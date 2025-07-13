<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

try {
    // Vérifier si l'admin existe déjà
    if (User::where('email', 'admin@ifran.com')->exists()) {
        echo "❌ Un administrateur avec l'email admin@ifran.com existe déjà!\n";
        exit(1);
    }

    // Créer l'administrateur
    $admin = User::create([
        'name' => 'Admin',
        'email' => 'admin@ifran.com',
        'password' => Hash::make('password'),
        'role' => 'admin',
        'nom' => 'Admin',
        'prenom' => 'Administrateur',
    ]);

    echo "✅ Administrateur créé avec succès!\n";
    echo "📧 Email: admin@ifran.com\n";
    echo "🔑 Mot de passe: password\n";
    echo "👤 Nom: Admin\n";
    echo "🔐 Rôle: Admin\n";

} catch (Exception $e) {
    echo "❌ Erreur lors de la création de l'administrateur: " . $e->getMessage() . "\n";
    exit(1);
} 