<?php

namespace App\Listeners;

use App\Events\EtudiantDroppe;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;


use App\Models\User;

class NotifierEtudiantDroppe
{
    public function handle(EtudiantDroppe $event)
    {
        // Notification pour l'étudiant
        Notification::create([
            'user_id' => $event->etudiant->id,
            'titre' => 'Module droppé',
            'message' => "Vous avez été droppé du module {$event->module->nom} (taux de présence: {$event->taux}%)",
            'type' => 'alerte'
        ]);

        // Notifications pour les coordinateurs
        $coordinateurs = User::where('role', 'coordinateur')->get();
        foreach ($coordinateurs as $coordinateur) {
            Notification::create([
                'user_id' => $coordinateur->id,
                'titre' => 'Étudiant droppé',
                'message' => "L'étudiant {$event->etudiant->prenom} {$event->etudiant->nom} a été droppé du module {$event->module->nom}",
                'type' => 'alerte'
            ]);
        }

        // Notification pour l'enseignant du module
        if ($event->module->enseignant) {
            Notification::create([
                'user_id' => $event->module->enseignant->id,
                'titre' => 'Étudiant droppé',
                'message' => "L'étudiant {$event->etudiant->prenom} {$event->etudiant->nom} a été droppé de votre module {$event->module->nom}",
                'type' => 'alerte'
            ]);
        }
    }
}