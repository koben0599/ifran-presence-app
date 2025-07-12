<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classe extends Model
{
    use HasFactory;

    public function users() {
        return $this->hasMany(User::class);
    }

    public function seances() {
        return $this->hasMany(Seance::class);
    }

    public function emploisDuTemps() {
        return $this->hasMany(EmploiDuTemps::class);
    }

    public function classes()
{
    return $this->hasMany(Classe::class);
}

}
