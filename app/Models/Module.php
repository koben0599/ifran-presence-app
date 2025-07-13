<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    protected $fillable = [
        'nom',
        'code',
        'description'
    ];

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
     * Obtenir le nom complet du module
     */
    public function getNomCompletAttribute(): string
    {
        return $this->code . ' - ' . $this->nom;
    }

    /**
     * Obtenir le nombre d'heures totales du module
     */
    public function getHeuresTotalesAttribute(): int
    {
        return $this->emploisDuTemps()->sum(\DB::raw('TIME_TO_SEC(TIMEDIFF(heure_fin, heure_debut)) / 3600'));
    }
}
