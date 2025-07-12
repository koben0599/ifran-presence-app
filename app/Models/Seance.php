<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Seance extends Model
{
    use HasFactory;
    
    protected $fillable = ['module_id', 'enseignant_id', 'date_debut', 'date_fin', 'type', 'classe', 'annule'];
    
    public function module()
    {
        return $this->belongsTo(Module::class);
    }
    
    public function enseignant()
    {
        return $this->belongsTo(User::class, 'enseignant_id');
    }
    
    public function presences()
    {
        return $this->hasMany(Presence::class);
    }
}
