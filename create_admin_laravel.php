<?php

// Charger Laravel
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

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
        'nom' => 'Admin',
        'prenom' => 'Administrateur',
        'email' => 'admin@ifran.com',
        'password' => Hash::make('password'),
        'role' => 'admin',
    ]);

    echo "✅ Administrateur créé avec succès!\n";
    echo "📧 Email: admin@ifran.com\n";
    echo "🔑 Mot de passe: password\n";
    echo "👤 Nom: Admin\n";
    echo "🔐 Rôle: Admin\n";
    echo "🆔 ID: {$admin->id}\n";

} catch (Exception $e) {
    echo "❌ Erreur lors de la création de l'administrateur: " . $e->getMessage() . "\n";
    exit(1);
} 