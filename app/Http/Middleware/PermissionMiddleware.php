<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $permission = null): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Vérifier les permissions spécifiques
        if ($permission && !$this->hasPermission($user, $permission)) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier les permissions par rôle
        if (!$this->checkRolePermissions($user, $request)) {
            abort(403, 'Accès non autorisé pour votre rôle');
        }

        return $next($request);
    }

    /**
     * Vérifier si l'utilisateur a une permission spécifique
     */
    private function hasPermission($user, string $permission): bool
    {
        $permissions = [
            'admin' => [
                'gestion_utilisateurs',
                'gestion_classes',
                'gestion_modules',
                'gestion_emplois_temps',
                'gestion_seances',
                'gestion_presences',
                'gestion_justifications',
                'export_donnees',
                'statistiques_globales',
                'gestion_systeme'
            ],
            'coordinateur' => [
                'gestion_etudiants',
                'gestion_enseignants',
                'gestion_emplois_temps',
                'gestion_seances',
                'validation_justifications',
                'statistiques_classe',
                'export_classe'
            ],
            'enseignant' => [
                'saisie_presences',
                'consultation_seances',
                'statistiques_cours',
                'gestion_justifications_cours'
            ],
            'etudiant' => [
                'consultation_presences',
                'consultation_emplois_temps',
                'soumission_justification'
            ],
            'parent' => [
                'consultation_presences_enfant',
                'consultation_emplois_temps_enfant'
            ]
        ];

        return in_array($permission, $permissions[$user->role] ?? []);
    }

    /**
     * Vérifier les permissions par rôle
     */
    private function checkRolePermissions($user, Request $request): bool
    {
        $route = $request->route();
        $routeName = $route->getName();

        // Permissions par route
        $routePermissions = [
            // Routes admin
            'admin.dashboard' => ['admin'],
            'admin.users.*' => ['admin'],
            'admin.classes.*' => ['admin'],
            'admin.modules.*' => ['admin'],
            'admin.emplois.*' => ['admin'],
            'admin.seances.*' => ['admin'],
            'admin.presences.*' => ['admin'],
            'admin.justifications.*' => ['admin'],
            'admin.exports.*' => ['admin'],
            'admin.statistiques.*' => ['admin'],

            // Routes coordinateur
            'coordinateur.dashboard' => ['coordinateur', 'admin'],
            'coordinateur.etudiants.*' => ['coordinateur', 'admin'],
            'coordinateur.enseignants.*' => ['coordinateur', 'admin'],
            'coordinateur.emplois.*' => ['coordinateur', 'admin'],
            'coordinateur.seances.*' => ['coordinateur', 'admin'],
            'coordinateur.justifications.*' => ['coordinateur', 'admin'],
            'coordinateur.statistiques.*' => ['coordinateur', 'admin'],

            // Routes enseignant
            'enseignant.dashboard' => ['enseignant', 'admin'],
            'enseignant.seances.*' => ['enseignant', 'admin'],
            'enseignant.presences.*' => ['enseignant', 'admin'],
            'enseignant.statistiques.*' => ['enseignant', 'admin'],
            'enseignant.justifications.*' => ['enseignant', 'admin'],

            // Routes étudiant
            'etudiant.dashboard' => ['etudiant', 'admin'],
            'etudiant.presences.*' => ['etudiant', 'admin'],
            'etudiant.emplois.*' => ['etudiant', 'admin'],
            'etudiant.justifications.*' => ['etudiant', 'admin'],

            // Routes parent
            'parent.dashboard' => ['parent', 'admin'],
            'parent.presences.*' => ['parent', 'admin'],
            'parent.emplois.*' => ['parent', 'admin'],
        ];

        if (isset($routePermissions[$routeName])) {
            return in_array($user->role, $routePermissions[$routeName]);
        }

        // Permissions par préfixe de route
        $prefixPermissions = [
            'admin' => ['admin'],
            'coordinateur' => ['coordinateur', 'admin'],
            'enseignant' => ['enseignant', 'admin'],
            'etudiant' => ['etudiant', 'admin'],
            'parent' => ['parent', 'admin'],
        ];

        foreach ($prefixPermissions as $prefix => $roles) {
            if (str_starts_with($routeName, $prefix . '.')) {
                return in_array($user->role, $roles);
            }
        }

        return true; // Par défaut, autoriser l'accès
    }

    /**
     * Vérifier l'accès aux ressources spécifiques
     */
    public function checkResourceAccess($user, $resource, $resourceId): bool
    {
        switch ($resource) {
            case 'classe':
                return $this->checkClasseAccess($user, $resourceId);
            case 'seance':
                return $this->checkSeanceAccess($user, $resourceId);
            case 'presence':
                return $this->checkPresenceAccess($user, $resourceId);
            case 'justification':
                return $this->checkJustificationAccess($user, $resourceId);
            default:
                return true;
        }
    }

    /**
     * Vérifier l'accès à une classe
     */
    private function checkClasseAccess($user, $classeId): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'coordinateur') {
            return $user->classe_id == $classeId;
        }

        if ($user->role === 'enseignant') {
            return $user->seancesEnseignant()->where('classe_id', $classeId)->exists();
        }

        if ($user->role === 'etudiant') {
            return $user->classe_id == $classeId;
        }

        return false;
    }

    /**
     * Vérifier l'accès à une séance
     */
    private function checkSeanceAccess($user, $seanceId): bool
    {
        $seance = \App\Models\Seance::find($seanceId);
        if (!$seance) {
            return false;
        }

        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'enseignant') {
            return $seance->enseignant_id == $user->id;
        }

        if ($user->role === 'etudiant') {
            return $seance->classe_id == $user->classe_id;
        }

        if ($user->role === 'coordinateur') {
            return $seance->classe->coordinateur_id == $user->id;
        }

        return false;
    }

    /**
     * Vérifier l'accès à une présence
     */
    private function checkPresenceAccess($user, $presenceId): bool
    {
        $presence = \App\Models\Presence::with('seance')->find($presenceId);
        if (!$presence) {
            return false;
        }

        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'enseignant') {
            return $presence->seance->enseignant_id == $user->id;
        }

        if ($user->role === 'etudiant') {
            return $presence->etudiant_id == $user->id;
        }

        if ($user->role === 'coordinateur') {
            return $presence->seance->classe->coordinateur_id == $user->id;
        }

        return false;
    }

    /**
     * Vérifier l'accès à une justification
     */
    private function checkJustificationAccess($user, $justificationId): bool
    {
        $justification = \App\Models\Justification::with('presence.seance')->find($justificationId);
        if (!$justification) {
            return false;
        }

        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'etudiant') {
            return $justification->presence->etudiant_id == $user->id;
        }

        if ($user->role === 'coordinateur') {
            return $justification->presence->seance->classe->coordinateur_id == $user->id;
        }

        if ($user->role === 'enseignant') {
            return $justification->presence->seance->enseignant_id == $user->id;
        }

        return false;
    }
} 