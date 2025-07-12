<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;



use App\Models\Etudiant;
use App\Models\Module;


class EtudiantDroppe
{
    use Dispatchable, SerializesModels;

    public $etudiant;
    public $module;
    public $taux;

    public function __construct(Etudiant $etudiant, Module $module, $taux)
    {
        $this->etudiant = $etudiant;
        $this->module = $module;
        $this->taux = $taux;
    }
}