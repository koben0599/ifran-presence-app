<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'role',
        'photo',
        'classe'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relations selon le rôle
    public function seancesEnseignant()
    {
        return $this->hasMany(Seance::class, 'enseignant_id');
    }

    public function presencesEtudiant()
    {
        return $this->hasMany(Presence::class, 'etudiant_id');
    }

    public function justificationsCoordinateur()
    {
        return $this->hasMany(Justification::class, 'coordinateur_id');
    }

    // Méthodes pour vérifier les rôles
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isEnseignant()
    {
        return $this->role === 'enseignant';
    }

    public function isCoordinateur()
    {
        return $this->role === 'coordinateur';
    }

    public function isEtudiant()
    {
        return $this->role === 'etudiant';
    }
}
