<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\EmploiDuTemps;
use Carbon\Carbon;

class CreneauDisponible implements Rule
{
    protected $salle;
    protected $jour;
    protected $ignoreId;

    public function __construct($salle, $jour, $ignoreId = null)
    {
        $this->salle = $salle;
        $this->jour = $jour;
        $this->ignoreId = $ignoreId;
    }

    public function passes($attribute, $value)
    {
        $heureDebut = Carbon::parse($value);
        $heureFin = Carbon::parse(request()->heure_fin);

        $query = EmploiDuTemps::where('salle', $this->salle)
            ->where('jour_semaine', $this->jour)
            ->where(function($q) use ($heureDebut, $heureFin) {
                $q->whereBetween('heure_debut', [$heureDebut->format('H:i'), $heureFin->format('H:i')])
                  ->orWhereBetween('heure_fin', [$heureDebut->format('H:i'), $heureFin->format('H:i')])
                  ->orWhere(function($q) use ($heureDebut, $heureFin) {
                      $q->where('heure_debut', '<=', $heureDebut->format('H:i'))
                        ->where('heure_fin', '>=', $heureFin->format('H:i'));
                  });
            });
            
        if ($this->ignoreId) {
            $query->where('id', '!=', $this->ignoreId);
        }

        return $query->count() === 0;
    }

    public function message()
    {
        return 'La salle est déjà occupée pendant ce créneau horaire.';
    }
} 