<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Seance;
use Illuminate\Auth\Access\HandlesAuthorization;

class SeancePolicy
{
    use HandlesAuthorization;

    public function view(User $user, Seance $seance)
    {
        return $user->role === 'admin' || 
               ($user->role === 'enseignant' && $user->id === $seance->enseignant_id) ||
               ($user->role === 'coordinateur');
    }

    public function update(User $user, Seance $seance)
    {
        return $user->role === 'admin' || 
               ($user->role === 'enseignant' && $user->id === $seance->enseignant_id);
    }

    public function saisirPresence(User $user, Seance $seance)
    {
        // Vérifie que la séance est en cours ou récente (2 semaines max)
        $dateLimite = now()->subWeeks(2);
        return $seance->date_debut >= $dateLimite && 
               ($user->role === 'enseignant' && $user->id === $seance->enseignant_id ||
                $user->role === 'coordinateur');
    }
}