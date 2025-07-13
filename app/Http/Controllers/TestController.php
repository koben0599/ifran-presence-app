<?php

namespace App\Http\Controllers;

use App\Http\Controllers\NotificationController;
use App\Models\User;
use Illuminate\Http\Request;

class TestController extends Controller
{
    /**
     * Créer une notification de test
     */
    public function createTestNotification()
    {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json(['error' => 'Utilisateur non connecté']);
        }

        // Créer une notification de test
        NotificationController::createNotification(
            $user,
            'Test de notification',
            'Ceci est une notification de test pour vérifier le système.',
            'info',
            route('dashboard'),
            'Voir le tableau de bord'
        );

        return response()->json([
            'success' => true,
            'message' => 'Notification de test créée avec succès !'
        ]);
    }

    /**
     * Créer plusieurs notifications de test
     */
    public function createMultipleNotifications()
    {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json(['error' => 'Utilisateur non connecté']);
        }

        $notifications = [
            [
                'titre' => 'Nouvelle séance programmée',
                'message' => 'Une nouvelle séance de Développement Web a été programmée pour demain.',
                'type' => 'seance',
                'action_url' => route('enseignant.seances.index'),
                'action_text' => 'Voir mes séances'
            ],
            [
                'titre' => 'Alerte de présence',
                'message' => 'Votre taux de présence est en baisse. Veuillez être plus assidu.',
                'type' => 'alerte',
                'action_url' => route('etudiant.presences.index'),
                'action_text' => 'Voir mes présences'
            ],
            [
                'titre' => 'Maintenance prévue',
                'message' => 'Une maintenance est prévue ce soir de 22h à 00h.',
                'type' => 'system',
                'action_url' => null,
                'action_text' => null
            ]
        ];

        foreach ($notifications as $notification) {
            NotificationController::createNotification(
                $user,
                $notification['titre'],
                $notification['message'],
                $notification['type'],
                $notification['action_url'],
                $notification['action_text']
            );
        }

        return response()->json([
            'success' => true,
            'message' => count($notifications) . ' notifications de test créées avec succès !'
        ]);
    }

    /**
     * Page de test des notifications
     */
    public function testPage()
    {
        return view('test.notifications');
    }
} 