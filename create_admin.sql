-- Créer un administrateur
INSERT INTO users (nom, prenom, email, password, role, created_at, updated_at) 
VALUES (
    'Admin', 
    'Administrateur', 
    'admin@ifran.com', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password
    'admin', 
    NOW(), 
    NOW()
) ON DUPLICATE KEY UPDATE updated_at = NOW(); 