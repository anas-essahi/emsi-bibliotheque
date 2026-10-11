

CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    type ENUM('etudiant','enseignant','admin') NOT NULL,
    quota_max INT DEFAULT 3,
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE livres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(200) NOT NULL,
    auteur VARCHAR(150) NOT NULL,
    isbn VARCHAR(20) UNIQUE,
    categorie VARCHAR(80),
    type ENUM('papier','ebook') NOT NULL,
    exemplaires_total INT DEFAULT 1,
    exemplaires_disponibles INT DEFAULT 1,
    couverture VARCHAR(255),
    fichier_pdf VARCHAR(255),
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE emprunts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    livre_id INT NOT NULL,
    date_emprunt DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_retour_prevue DATE NOT NULL,
    date_retour_effective DATETIME NULL,
    statut ENUM('en_cours', 'retourne', 'en_retard') DEFAULT 'en_cours',
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (livre_id) REFERENCES livres(id)
);

INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, type, quota_max) VALUES
('Alaoui', 'Sara', 'sara.alaoui@emsi.ma', '$2y$10$92IXUNpkjO0rOQ5by6RSAOexBpKGwR8DlvVLUQ0EYqhxxmDMcqU3y', 'etudiant', 3),
('Bennani', 'Youssef', 'youssef.bennani@emsi.ma', '$2y$10$92IXUNpkjO0rOQ5by6RSAOexBpKGwR8DlvVLUQ0EYqhxxmDMcqU3y', 'etudiant', 3),
('Zahid', 'Achraf', 'a.zahid@emsi.ma', '$2y$10$92IXUNpkjO0rOQ5by6RSAOexBpKGwR8DlvVLUQ0EYqhxxmDMcqU3y', 'enseignant', 5),
('Admin', 'Système', 'admin@bibliotheque.ma', '$2y$10$92IXUNpkjO0rOQ5by6RSAOexBpKGwR8DlvVLUQ0EYqhxxmDMcqU3y', 'admin', 0);


INSERT INTO livres (titre, auteur, isbn, categorie, type, exemplaires_total, exemplaires_disponibles, couverture, fichier_pdf) VALUES
('Clean Code', 'Robert C. Martin', '9780132350884', 'Informatique', 'papier', 3, 3, NULL, NULL),
('Design Patterns', 'Gang of Four', '9780201633610', 'Informatique', 'papier', 2, 2, NULL, NULL),
('Le Petit Prince', 'Antoine de Saint-Exupéry', '9782070408504', 'Roman', 'papier', 4, 4, NULL, NULL),
('Introduction to Algorithms', 'Cormen, Leiserson, Rivest', '9780262033848', 'Informatique', 'papier', 2, 2, NULL, NULL),
('PHP & MySQL: Novice to Ninja', 'Kevin Yank', '9780994336595', 'Informatique', 'ebook', 1, 1, NULL, 'php_mysql_ninja.pdf'),
('1984', 'George Orwell', '9780451524935', 'Roman', 'papier', 3, 3, NULL, NULL);


INSERT INTO emprunts (utilisateur_id, livre_id, date_emprunt, date_retour_prevue, statut) VALUES
(1, 1, '2026-09-01 10:00:00', '2026-09-15', 'en_retard'),
(2, 3, '2026-09-10 14:30:00', '2026-09-24', 'en_cours');

