<?php

try {
    // Connexion à la base de données SQLite
    $pdo = new PDO('sqlite:database/database.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vérifier si l'admin existe déjà
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute(['admin@ifran.com']);
    $exists = $stmt->fetchColumn();

    if ($exists > 0) {
        echo "❌ Un administrateur avec l'email admin@ifran.com existe déjà!\n";
        exit(1);
    }

    // Hash du mot de passe (password)
    $hashedPassword = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

    // Insérer l'administrateur
    $stmt = $pdo->prepare("
        INSERT INTO users (nom, prenom, email, password, role, created_at, updated_at) 
        VALUES (?, ?, ?, ?, ?, datetime('now'), datetime('now'))
    ");

    $stmt->execute([
        'Admin',
        'Administrateur',
        'admin@ifran.com',
        $hashedPassword,
        'admin'
    ]);

    echo "✅ Administrateur créé avec succès!\n";
    echo "📧 Email: admin@ifran.com\n";
    echo "🔑 Mot de passe: password\n";
    echo "👤 Nom: Admin\n";
    echo "🔐 Rôle: Admin\n";

} catch (PDOException $e) {
    echo "❌ Erreur lors de la création de l'administrateur: " . $e->getMessage() . "\n";
    exit(1);
} 