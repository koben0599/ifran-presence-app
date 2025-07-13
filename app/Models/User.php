<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        return $this->hasMany(\App\Models\Seance::class, 'enseignant_id');
    }

    public function presencesEtudiant()
    {
        return $this->hasMany(Presence::class, 'etudiant_id');
    }

    public function justificationsCoordinateur()
    {
        return $this->hasMany(Justification::class, 'coordinateur_id');
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }

    // Pour le parent
    public function enfants()
    {
        return $this->belongsToMany(User::class, 'parent_etudiant', 'parent_id', 'etudiant_id')
            ->where('role', 'etudiant');
    }

    // Pour l'étudiant
    public function parents()
    {
        return $this->belongsToMany(User::class, 'parent_etudiant', 'etudiant_id', 'parent_id')
            ->where('role', 'parent');
    }

    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(Classe::class, 'classe_user');
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

    public function isParent()
    {
        return $this->role === 'parent';
    }

    /**
     * Obtenir l'URL de la photo de profil ou les initiales
     */
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->photo && file_exists(storage_path('app/public/' . $this->photo))) {
            return asset('storage/' . $this->photo);
        }

        // Générer des initiales stylisées
        $initials = $this->initials;
        
        // Utiliser un service externe pour générer une image d'avatar
        return "https://ui-avatars.com/api/?name=" . urlencode($initials) . "&background=random&color=fff&rounded=true&size=200";
    }

    /**
     * Obtenir les initiales de l'utilisateur
     */
    public function getInitialsAttribute()
    {
        $firstInitial = substr($this->prenom ?? $this->name ?? '', 0, 1);
        $lastInitial = substr($this->nom ?? $this->lastname ?? '', 0, 1);
        
        return strtoupper($firstInitial . $lastInitial);
    }

    /**
     * Obtenir le nom complet de l'utilisateur
     */
    public function getFullNameAttribute()
    {
        if ($this->prenom && $this->nom) {
            return $this->prenom . ' ' . $this->nom;
        }
        
        return $this->name ?? $this->email;
    }

    /**
     * Obtenir le nom d'affichage (prénom ou nom complet)
     */
    public function getDisplayNameAttribute()
    {
        return $this->prenom ?? $this->name ?? $this->email;
    }

    /**
     * Vérifier si l'utilisateur a une photo de profil
     */
    public function hasProfilePhoto()
    {
        return $this->photo && file_exists(storage_path('app/public/' . $this->photo));
    }

    /**
     * Obtenir la couleur de rôle pour l'affichage
     */
    public function getRoleColorAttribute()
    {
        $colors = [
            'admin' => 'bg-red-100 text-red-800',
            'coordinateur' => 'bg-purple-100 text-purple-800',
            'enseignant' => 'bg-blue-100 text-blue-800',
            'etudiant' => 'bg-green-100 text-green-800',
            'parent' => 'bg-yellow-100 text-yellow-800',
        ];

        return $colors[$this->role] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Obtenir l'icône de rôle
     */
    public function getRoleIconAttribute()
    {
        $icons = [
            'admin' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>',
            'coordinateur' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>',
            'enseignant' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>',
            'etudiant' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>',
            'parent' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>',
        ];

        return $icons[$this->role] ?? '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>';
    }

    
}
