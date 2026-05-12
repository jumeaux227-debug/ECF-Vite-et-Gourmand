USE vite_et_gourmand;

-- 1. Insertion des Rôles
INSERT INTO role (libelle) VALUES ('administrateur'), ('employé'), ('utilisateur');

-- 2. Insertion des Thèmes et Régimes
INSERT INTO theme (libelle) VALUES ('Végétarien'), ('Carnivore'), ('Gastronomique');
INSERT INTO regime (libelle) VALUES ('Sans gluten'), ('Classique'), ('Vegan');

-- 3. Insertion d'utilisateurs de test
-- Note: les mots de passe sont en clair ici pour le test, mais en PHP on utilisera password_hash()
INSERT INTO utilisateur (email, password, prenom, nom, role_id) VALUES 
('admin@test.fr', 'admin123', 'Jean', 'Admin', 1),
('employe@test.fr', 'emp123', 'Marie', 'Employé', 2),
('client@test.fr', 'client123', 'Pierre', 'Client', 3);

-- 4. Insertion d'Horaires
INSERT INTO horaire (jour, heure_ouverture, heure_fermeture) VALUES 
('Lundi', '08:00', '20:00'),
('Mardi', '08:00', '20:00'),
('Mercredi', '08:00', '20:00');

-- 5. Insertion de quelques Plats
INSERT INTO plat (titre_plat) VALUES ('Salade composée'), ('Entrecôte grillée'), ('Fondant au chocolat');

-- 6. Insertion d'un Menu
INSERT INTO menu (titre, nombre_personne_minimum, prix_par_personne, description, regime_id, theme_id) 
VALUES ('Menu Découverte', 2, 25.50, 'Un menu varié pour tous les goûts', 2, 1);