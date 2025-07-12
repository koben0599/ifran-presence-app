<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Justification extends Model
{
    use HasFactory;

    protected $fillable = [
        'presence_id',
        'coordinateur_id',
        'raison',
        'fichier_justificatif'
    ];

    public function presence()
    {
        return $this->belongsTo(Presence::class);
    }

    public function coordinateur()
    {
        return $this->belongsTo(User::class, 'coordinateur_id');
    }
}
