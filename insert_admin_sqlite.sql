-- Vérifier si l'admin existe déjà
SELECT COUNT(*) FROM users WHERE email = 'admin@ifran.com';

-- Insérer l'administrateur (exécuter seulement si le compte n'existe pas)
INSERT INTO users (nom, prenom, email, password, role, created_at, updated_at) 
VALUES (
    'Admin', 
    'Administrateur', 
    'admin@ifran.com', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin', 
    datetime('now'), 
    datetime('now')
);

-- Vérifier que l'admin a été créé
SELECT id, nom, prenom, email, role FROM users WHERE email = 'admin@ifran.com'; 