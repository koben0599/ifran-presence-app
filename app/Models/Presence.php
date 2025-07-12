<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Presence extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'etudiant_id',
        'seance_id',
        'statut',
        'justifie'
    ];
    
    public function etudiant()
    {
        return $this->belongsTo(User::class, 'etudiant_id');
    }
    
    public function seance()
    {
        return $this->belongsTo(Seance::class);
    }
}
