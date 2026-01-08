-- ============================================
-- SCRIPT DE CRÉATION DU SUPER ADMIN
-- ============================================
-- Ce script crée un utilisateur Super Admin qui a accès à tous les centres
-- Email: superadmin@mayelia.com
-- Password: SuperAdmin2026! (À changer après la première connexion)

INSERT INTO users (
    centre_id,
    nom,
    prenom,
    email,
    password,
    telephone,
    role,
    statut,
    created_at,
    updated_at
) VALUES (
    NULL, -- centre_id NULL = accès à tous les centres
    'SYSTÈME',
    'Super Admin',
    'superadmin@mayelia.com',
    '$2y$12$XXXXXXXXXXXXXXXXXX', -- Password haché ci-dessous
    '+225 00 00 00 00',
    'super_admin',
    'actif',
    NOW(),
    NOW()
);

-- ============================================
-- INSTRUCTIONS IMPORTANTES
-- ============================================
-- 1. Exécutez ce script dans votre base de données
-- 2. Le mot de passe par défaut est : SuperAdmin2026!
-- 3. Changez-le immédiatement après la première connexion
-- 4. Ce compte ne doit être utilisé que par les responsables systèmes

-- ============================================
-- CONNEXION
-- ============================================
-- URL : https://rendez-vous.mayeliamobilite.com/super-admin/select-centre
-- Email : superadmin@mayelia.com
-- Password : SuperAdmin2026!

-- ============================================
-- ALTERNATIVE : Créer le Super Admin via Artisan Tinker
-- ============================================
-- php artisan tinker
-- 
-- User::create([
--     'centre_id' => null,
--     'nom' => 'SYSTÈME',
--     'prenom' => 'Super Admin',
--     'email' => 'superadmin@mayelia.com',
--     'password' => bcrypt('SuperAdmin2026!'),
--     'telephone' => '+225 00 00 00 00',
--     'role' => 'super_admin',
--     'statut' => 'actif'
-- ]);
