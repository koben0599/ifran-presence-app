<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmploiDuTemps extends Model
{
    use HasFactory;

    protected $fillable = [
        'classe',
        'module_id',
        'enseignant_id',
        'jour_semaine',
        'heure_debut',
        'heure_fin',
        'type_cours',
        'salle',
        'est_actif'
    ];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function enseignant()
    {
        return $this->belongsTo(User::class, 'enseignant_id');
    }

    public function seances()
    {
        return $this->hasMany(Seance::class);
    }
}
