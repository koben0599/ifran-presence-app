<?php

namespace Database\Seeders;

use App\Models\User;
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
        User::create([
            'nom' => 'Admin',
            'prenom' => 'System',
            'email' => 'admin@ifran.edu',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);

        // Coordinateur
        User::create([
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'email' => 'jean.dupont@ifran.edu',
            'password' => Hash::make('password'),
            'role' => 'coordinateur'
        ]);

        // Enseignant
        User::create([
            'nom' => 'Martin',
            'prenom' => 'Sophie',
            'email' => 'sophie.martin@ifran.edu',
            'password' => Hash::make('password'),
            'role' => 'enseignant'
        ]);

        // Étudiants
        $classes = ['B3DEV', 'B2DEV', 'B3CREA'];
        for ($i = 1; $i <= 30; $i++) {
            User::create([
                'nom' => 'Etudiant' . $i,
                'prenom' => 'Prénom' . $i,
                'email' => 'etudiant' . $i . '@ifran.edu',
                'password' => Hash::make('password'),
                'role' => 'etudiant',
                'classe' => $classes[array_rand($classes)]
            ]);
        }
    }
}
