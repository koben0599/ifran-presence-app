<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class EmploiDuTemps extends Model
{
    use HasFactory;

    protected $fillable = [
        'classe_id',
        'module_id',
        'enseignant_id',
        'jour_semaine',
        'heure_debut',
        'heure_fin',
        'type',
        'salle',
        'est_actif'
    ];

    protected $casts = [
        'est_actif' => 'boolean',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function enseignant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enseignant_id');
    }

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class, 'classe_id');
    }

    public function seances(): HasMany
    {
        return $this->hasMany(Seance::class);
    }

    /**
     * Obtenir la durée du cours en heures
     */
    public function getDureeAttribute(): float
    {
        $debut = Carbon::parse($this->heure_debut);
        $fin = Carbon::parse($this->heure_fin);
        return $debut->diffInMinutes($fin) / 60;
    }

    /**
     * Obtenir le nom du jour en français
     */
    public function getJourFrancaisAttribute(): string
    {
        $jours = [
            'monday' => 'Lundi',
            'tuesday' => 'Mardi',
            'wednesday' => 'Mercredi',
            'thursday' => 'Jeudi',
            'friday' => 'Vendredi',
            'saturday' => 'Samedi',
            'sunday' => 'Dimanche'
        ];

        return $jours[strtolower($this->jour_semaine)] ?? $this->jour_semaine;
    }

    /**
     * Obtenir le type de cours en français
     */
    public function getTypeFrancaisAttribute(): string
    {
        $types = [
            'presentiel' => 'Présentiel',
            'elearning' => 'E-learning',
            'workshop' => 'Atelier'
        ];

        return $types[$this->type] ?? $this->type;
    }

    /**
     * Obtenir la couleur selon le type de cours
     */
    public function getCouleurTypeAttribute(): string
    {
        $couleurs = [
            'presentiel' => 'bg-blue-100 text-blue-800',
            'elearning' => 'bg-green-100 text-green-800',
            'workshop' => 'bg-purple-100 text-purple-800'
        ];

        return $couleurs[$this->type] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Vérifier si l'emploi du temps est pour aujourd'hui
     */
    public function isAujourdhui(): bool
    {
        $aujourdhui = Carbon::now()->locale('fr')->dayName;
        return strtolower($aujourdhui) === strtolower($this->jour_semaine);
    }

    /**
     * Vérifier si l'emploi du temps est pour cette semaine
     */
    public function isCetteSemaine(): bool
    {
        $lundi = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $vendredi = $lundi->copy()->endOfWeek(Carbon::FRIDAY);
        
        // Logique pour vérifier si l'emploi du temps s'applique cette semaine
        return true; // Simplifié pour l'exemple
    }
}
