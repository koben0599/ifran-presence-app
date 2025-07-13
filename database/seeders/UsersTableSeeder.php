<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Classe;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        if (!User::where('email', 'admin@ifran.edu')->exists()) {
            User::create([
                'nom' => 'Admin',
                'prenom' => 'System',
                'email' => 'admin@ifran.edu',
                'password' => Hash::make('password'),
                'role' => 'admin'
            ]);
        }

        // Enseignants spécialisés
        $enseignants = [
            ['nom' => 'Martin', 'prenom' => 'Sophie', 'email' => 'sophie.martin@ifran.edu', 'specialite' => 'Développement Web'],
            ['nom' => 'Bernard', 'prenom' => 'Pierre', 'email' => 'pierre.bernard@ifran.edu', 'specialite' => 'Développement Mobile'],
            ['nom' => 'Dubois', 'prenom' => 'Marie', 'email' => 'marie.dubois@ifran.edu', 'specialite' => 'Base de données'],
            ['nom' => 'Leroy', 'prenom' => 'Thomas', 'email' => 'thomas.leroy@ifran.edu', 'specialite' => 'Design UI/UX'],
            ['nom' => 'Moreau', 'prenom' => 'Julie', 'email' => 'julie.moreau@ifran.edu', 'specialite' => 'Marketing Digital'],
            ['nom' => 'Simon', 'prenom' => 'Laurent', 'email' => 'laurent.simon@ifran.edu', 'specialite' => 'Gestion de projet'],
            ['nom' => 'Michel', 'prenom' => 'Claire', 'email' => 'claire.michel@ifran.edu', 'specialite' => 'Infographie'],
            ['nom' => 'Petit', 'prenom' => 'Nicolas', 'email' => 'nicolas.petit@ifran.edu', 'specialite' => 'Vidéo et Animation'],
            ['nom' => 'Robert', 'prenom' => 'Isabelle', 'email' => 'isabelle.robert@ifran.edu', 'specialite' => 'Programmation avancée'],
            ['nom' => 'Richard', 'prenom' => 'François', 'email' => 'francois.richard@ifran.edu', 'specialite' => 'Architecture logicielle'],
        ];

        foreach ($enseignants as $enseignant) {
            if (!User::where('email', $enseignant['email'])->exists()) {
                User::create([
                    'nom' => $enseignant['nom'],
                    'prenom' => $enseignant['prenom'],
                    'email' => $enseignant['email'],
                    'password' => Hash::make('password'),
                    'role' => 'enseignant'
                ]);
            }
        }

        // Coordinateurs
        $coordinateurs = [
            ['nom' => 'Dupont', 'prenom' => 'Jean', 'email' => 'jean.dupont@ifran.edu'],
            ['nom' => 'Durand', 'prenom' => 'Anne', 'email' => 'anne.durand@ifran.edu'],
            ['nom' => 'Lefebvre', 'prenom' => 'Marc', 'email' => 'marc.lefebvre@ifran.edu'],
            ['nom' => 'Garcia', 'prenom' => 'Elena', 'email' => 'elena.garcia@ifran.edu'],
        ];

        foreach ($coordinateurs as $coordinateur) {
            if (!User::where('email', $coordinateur['email'])->exists()) {
                User::create([
                    'nom' => $coordinateur['nom'],
                    'prenom' => $coordinateur['prenom'],
                    'email' => $coordinateur['email'],
                    'password' => Hash::make('password'),
                    'role' => 'coordinateur'
                ]);
            }
        }

        // Récupérer les classes existantes
        $classes = Classe::all();
        
        // Étudiants
        $nomsEtudiants = [
            'Dupont', 'Martin', 'Bernard', 'Thomas', 'Petit', 'Robert', 'Richard', 'Durand',
            'Leroy', 'Moreau', 'Simon', 'Laurent', 'Lefebvre', 'Michel', 'Garcia', 'David',
            'Bertrand', 'Roux', 'Vincent', 'Fournier', 'Morel', 'Girard', 'Andre', 'Lefevre',
            'Mercier', 'Dupuis', 'Lambert', 'Bonnet', 'Francois', 'Martinez'
        ];

        $prenomsEtudiants = [
            'Lucas', 'Emma', 'Hugo', 'Léa', 'Jules', 'Chloé', 'Adam', 'Jade', 'Louis', 'Alice',
            'Paul', 'Inès', 'Antoine', 'Lola', 'Nathan', 'Eva', 'Arthur', 'Louise', 'Raphaël', 'Jules',
            'Tom', 'Agathe', 'Théo', 'Camille', 'Ethan', 'Sarah', 'Enzo', 'Zoé', 'Axel', 'Nina'
        ];

        for ($i = 0; $i < 30; $i++) {
            $email = 'etudiant' . ($i + 1) . '@ifran.edu';
            if (!User::where('email', $email)->exists()) {
                $classe = $classes->random();
                User::create([
                    'nom' => $nomsEtudiants[$i],
                    'prenom' => $prenomsEtudiants[$i],
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'role' => 'etudiant',
                    'classe_id' => $classe->id
                ]);
            }
        }

        // Parents
        for ($i = 1; $i <= 15; $i++) {
            $email = 'parent' . $i . '@ifran.edu';
            if (!User::where('email', $email)->exists()) {
                User::create([
                    'nom' => 'Parent' . $i,
                    'prenom' => 'Prénom' . $i,
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'role' => 'parent'
                ]);
            }
        }
    }
}
