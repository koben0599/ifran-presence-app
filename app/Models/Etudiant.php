<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Events\EtudiantDroppe;
use App\Models\Module;
use App\Models\Presence;
use App\Models\Justification;

class Etudiant extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['nom', 'prenom', 'photo', 'classe', 'email', 'password'];

    public function presences()
    {
        return $this->hasMany(Presence::class);
    }

    public function justifications()
    {
        return $this->hasMany(Justification::class);
    }
    public function verifierPresenceModule(Module $module)
{
    $seancesModule = $module->seances()->pluck('id');
    $totalSeances = $seancesModule->count();
    
    if ($totalSeances === 0) return;
    
    $presences = $this->presences()
        ->whereIn('seance_id', $seancesModule)
        ->get();
        
    $presentCount = $presences->where('statut', 'present')->count();
    $taux = ($presentCount / $totalSeances) * 100;
    
    if ($taux <= 25) {
        event(new EtudiantDroppe($this, $module, $taux));
    }
}
}
