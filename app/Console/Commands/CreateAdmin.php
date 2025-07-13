<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create {--name=Admin} {--email=admin@ifran.com} {--password=password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Créer un utilisateur administrateur';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->option('name');
        $email = $this->option('email');
        $password = $this->option('password');

        // Vérifier si l'utilisateur existe déjà
        if (User::where('email', $email)->exists()) {
            $this->error("Un utilisateur avec l'email {$email} existe déjà!");
            return 1;
        }

        // Créer l'administrateur
        $admin = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
            'nom' => $name,
            'prenom' => 'Administrateur',
        ]);

        $this->info("✅ Administrateur créé avec succès!");
        $this->info("📧 Email: {$email}");
        $this->info("🔑 Mot de passe: {$password}");
        $this->info("👤 Nom: {$name}");
        $this->info("🔐 Rôle: Admin");

        return 0;
    }
} 