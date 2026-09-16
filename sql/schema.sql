CREATE DATABASE IF NOT EXISTS reservation_rdv_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE reservation_rdv_db;

CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    duree_minutes INT NOT NULL,
    prix DECIMAL(6,2) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS rendez_vous (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_id INT NOT NULL,
    client_nom VARCHAR(100) NOT NULL,
    client_email VARCHAR(150) NOT NULL,
    date_rdv DATE NOT NULL,
    heure_debut TIME NOT NULL,
    heure_fin TIME NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(50) NOT NULL UNIQUE,
    mot_de_passe_hash VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

-- Quelques services de départ, à adapter/compléter depuis l'espace admin.
INSERT INTO services (nom, duree_minutes, prix) VALUES
    ('Coupe', 30, 25.00),
    ('Coupe + Barbe', 45, 35.00),
    ('Coloration', 90, 60.00);

-- Compte admin par défaut : login "admin" / mot de passe "admin1234".
-- Le hash ci-dessous correspond à "admin1234" (password_hash, bcrypt).
-- À changer une fois connecté, ce n'est qu'un compte de démarrage.
INSERT INTO admins (login, mot_de_passe_hash) VALUES
    ('admin', '$2y$10$U35gJYF0GJwnnLTQBR9hpugbo5fSfbcSz/O9202ewehXlSy0d7Lai');
