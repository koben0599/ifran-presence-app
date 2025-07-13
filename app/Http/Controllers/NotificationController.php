<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Notification;
use App\Services\AlerteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $alerteService;

    public function __construct(AlerteService $alerteService)
    {
        $this->alerteService = $alerteService;
    }

    /**
     * Afficher les notifications de l'utilisateur
     */
    public function index()
    {
        $user = Auth::user();
        $notifications = $this->getNotificationsForUser($user);

        // Si c'est une requête AJAX, retourner JSON
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'notifications' => $notifications,
                'unread_count' => collect($notifications)->where('lu', false)->count()
            ]);
        }

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Marquer une notification comme lue
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($id);

        $notification->update(['lu' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('lu', false)
            ->update(['lu' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Supprimer une notification
     */
    public function destroy($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($id);

        $notification->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Obtenir le nombre de notifications non lues
     */
    public function getUnreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->where('lu', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Obtenir les notifications pour un utilisateur
     */
    private function getNotificationsForUser(User $user): array
    {
        $notifications = [];

        // Notifications système
        $notificationsSystem = $this->getSystemNotifications($user);
        $notifications = array_merge($notifications, $notificationsSystem);

        // Notifications d'alertes
        $notificationsAlertes = $this->getAlerteNotifications($user);
        $notifications = array_merge($notifications, $notificationsAlertes);

        // Notifications personnalisées
        $notificationsPerso = $this->getPersonalNotifications($user);
        $notifications = array_merge($notifications, $notificationsPerso);

        // Trier par date de création (plus récentes en premier)
        usort($notifications, function($a, $b) {
            return $b['created_at'] <=> $a['created_at'];
        });

        return $notifications;
    }

    /**
     * Obtenir les notifications système
     */
    private function getSystemNotifications(User $user): array
    {
        $notifications = [];

        // Notification de bienvenue pour les nouveaux utilisateurs
        if ($user->created_at->diffInDays(now()) < 7) {
            $notifications[] = [
                'id' => 'welcome_' . $user->id,
                'type' => 'system',
                'titre' => 'Bienvenue sur IFran Presence !',
                'message' => 'Votre compte a été créé avec succès. Explorez les fonctionnalités disponibles.',
                'niveau' => 'info',
                'lu' => false,
                'created_at' => $user->created_at->toISOString(),
                'action_url' => route('dashboard'),
                'action_text' => 'Voir le tableau de bord'
            ];
        }

        // Notification de maintenance (si applicable)
        if (config('app.maintenance_mode', false)) {
            $notifications[] = [
                'id' => 'maintenance',
                'type' => 'system',
                'titre' => 'Maintenance prévue',
                'message' => 'Une maintenance est prévue ce soir de 22h à 00h.',
                'niveau' => 'warning',
                'lu' => false,
                'created_at' => now()->toISOString(),
                'action_url' => null,
                'action_text' => null
            ];
        }

        return $notifications;
    }

    /**
     * Obtenir les notifications d'alertes
     */
    private function getAlerteNotifications(User $user): array
    {
        $notifications = [];

        if ($user->role === 'etudiant') {
            $alertes = $this->alerteService->getAlertesEtudiant($user->id);
            
            foreach ($alertes as $alerte) {
                $notifications[] = [
                    'id' => 'alerte_' . $user->id . '_' . $alerte['type'],
                    'type' => 'alerte',
                    'titre' => 'Alerte de présence',
                    'message' => $alerte['message'],
                    'niveau' => $alerte['niveau'],
                    'lu' => false,
                    'created_at' => now()->toISOString(),
                    'action_url' => route('etudiant.presences.index'),
                    'action_text' => 'Voir mes présences'
                ];
            }
        }

        if ($user->role === 'coordinateur') {
            $alertes = $this->alerteService->getAlertesClasse($user->classe_id);
            
            foreach ($alertes as $alerte) {
                $notifications[] = [
                    'id' => 'alerte_classe_' . $user->classe_id . '_' . $alerte['etudiant']->id,
                    'type' => 'alerte',
                    'titre' => 'Alerte étudiant',
                    'message' => 'Étudiant ' . $alerte['etudiant']->nom . ' : ' . $alerte['alertes'][0]['message'],
                    'niveau' => $alerte['alertes'][0]['niveau'],
                    'lu' => false,
                    'created_at' => now()->toISOString(),
                    'action_url' => route('coordinateur.etudiants.show', $alerte['etudiant']->id),
                    'action_text' => 'Voir l\'étudiant'
                ];
            }
        }

        if ($user->role === 'admin') {
            $alertes = $this->alerteService->getAlertesGlobales();
            
            foreach ($alertes as $alerte) {
                $notifications[] = [
                    'id' => 'alerte_global_' . $alerte['type'],
                    'type' => 'alerte',
                    'titre' => 'Alerte globale',
                    'message' => $alerte['message'],
                    'niveau' => $alerte['niveau'],
                    'lu' => false,
                    'created_at' => now()->toISOString(),
                    'action_url' => route('admin.statistiques.index'),
                    'action_text' => 'Voir les statistiques'
                ];
            }
        }

        return $notifications;
    }

    /**
     * Obtenir les notifications personnalisées
     */
    private function getPersonalNotifications(User $user): array
    {
        $notifications = [];

        // Notifications de nouvelles séances pour les enseignants
        if ($user->role === 'enseignant') {
            $nouvellesSeances = $user->seancesEnseignant()
                ->where('created_at', '>=', now()->subDays(1))
                ->count();

            if ($nouvellesSeances > 0) {
                $notifications[] = [
                    'id' => 'nouvelles_seances_' . $user->id,
                    'type' => 'seance',
                    'titre' => 'Nouvelles séances',
                    'message' => $nouvellesSeances . ' nouvelle(s) séance(s) ont été créée(s).',
                    'niveau' => 'info',
                    'lu' => false,
                    'created_at' => now()->toISOString(),
                    'action_url' => route('enseignant.seances.index'),
                    'action_text' => 'Voir mes séances'
                ];
            }
        }

        // Notifications de nouvelles présences pour les étudiants
        if ($user->role === 'etudiant') {
            $nouvellesPresences = $user->presences()
                ->where('created_at', '>=', now()->subDays(1))
                ->count();

            if ($nouvellesPresences > 0) {
                $notifications[] = [
                    'id' => 'nouvelles_presences_' . $user->id,
                    'type' => 'presence',
                    'titre' => 'Nouvelles présences',
                    'message' => $nouvellesPresences . ' nouvelle(s) présence(s) ont été enregistrée(s).',
                    'niveau' => 'info',
                    'lu' => false,
                    'created_at' => now()->toISOString(),
                    'action_url' => route('etudiant.presences.index'),
                    'action_text' => 'Voir mes présences'
                ];
            }
        }

        return $notifications;
    }

    /**
     * Créer une notification personnalisée
     */
    public static function createNotification(User $user, string $titre, string $message, string $type = 'info', string $actionUrl = null, string $actionText = null): void
    {
        Notification::create([
            'user_id' => $user->id,
            'titre' => $titre,
            'message' => $message,
            'type' => $type,
            'action_url' => $actionUrl,
            'action_text' => $actionText,
            'lu' => false,
        ]);
    }

    /**
     * Créer une notification pour tous les utilisateurs d'un rôle
     */
    public static function createNotificationForRole(string $role, string $titre, string $message, string $type = 'info', string $actionUrl = null, string $actionText = null): void
    {
        $users = User::where('role', $role)->get();
        
        foreach ($users as $user) {
            self::createNotification($user, $titre, $message, $type, $actionUrl, $actionText);
        }
    }
} 