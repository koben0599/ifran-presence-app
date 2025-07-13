<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classe extends Model
{
    protected $fillable = ['nom', 'coordinateur_id'];

    /**
     * Relation avec le coordinateur
     */
    public function coordinateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coordinateur_id');
    }

    /**
     * Relation avec les étudiants
     */
    public function etudiants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'classe_user')
                   ->where('role', 'etudiant');
    }

    /**
     * Relation avec les séances
     */
    public function seances(): HasMany
    {
        return $this->hasMany(Seance::class);
    }

    /**
     * Relation avec les emplois du temps
     */
    public function emploisDuTemps(): HasMany
    {
        return $this->hasMany(EmploiDuTemps::class);
    }

    /**
     * Obtenir le niveau de la classe (B1, B2, B3)
     */
    public function getNiveauAttribute(): string
    {
        return substr($this->nom, 0, 2);
    }

    /**
     * Obtenir la spécialité de la classe (DEV, CREA)
     */
    public function getSpecialiteAttribute(): string
    {
        return substr($this->nom, 2);
    }

    /**
     * Obtenir le nombre d'étudiants
     */
    public function getNombreEtudiantsAttribute(): int
    {
        return $this->etudiants()->count();
    }

    /**
     * Obtenir le taux de présence moyen de la classe
     */
    public function getTauxPresenceAttribute(): float
    {
        $totalPresences = Presence::whereHas('seance', function($query) {
            $query->where('classe_id', $this->id);
        })->count();

        $presents = Presence::whereHas('seance', function($query) {
            $query->where('classe_id', $this->id);
        })->where('statut', 'present')->count();

        return $totalPresences > 0 ? round(($presents / $totalPresences) * 100, 2) : 0;
    }
}
