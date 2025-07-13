<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vérifier si l'admin existe déjà
        if (User::where('email', 'admin@ifran.com')->exists()) {
            $this->command->info('Un administrateur avec l\'email admin@ifran.com existe déjà!');
            return;
        }

        // Créer l'administrateur
        User::create([
            'nom' => 'Admin',
            'prenom' => 'Administrateur',
            'email' => 'admin@ifran.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $this->command->info('✅ Administrateur créé avec succès!');
        $this->command->info('📧 Email: admin@ifran.com');
        $this->command->info('🔑 Mot de passe: password');
    }
} 