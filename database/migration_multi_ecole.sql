-- MIGRATION : ancienne base mono-école  ->  multi-écoles + calendrier scolaire
-- À exécuter UNE SEULE FOIS sur la base ecole_db existante (phpMyAdmin > ecole_db > onglet SQL).
-- Méthode anti-erreur #1452 : on ajoute la colonne NULLABLE, on remplit, puis seulement on ajoute la clé étrangère.
USE ecole_db;
SET FOREIGN_KEY_CHECKS=0;

CREATE TABLE schools (
  id_ecole INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(150) NOT NULL,
  ville VARCHAR(80) NULL, telephone VARCHAR(30) NULL, email VARCHAR(150) NULL,
  actif TINYINT(1) NOT NULL DEFAULT 1,
  deleted_at DATETIME NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Toutes les données actuelles deviennent l'école n° 1
INSERT INTO schools (id_ecole, nom) VALUES (1, 'École Abou Kacem Chebbi');

ALTER TABLE enseignants ADD COLUMN id_ecole INT NULL AFTER id;
UPDATE enseignants SET id_ecole = 1;
ALTER TABLE enseignants MODIFY id_ecole INT NOT NULL;
ALTER TABLE enseignants ADD CONSTRAINT fk_enseignants_ecole FOREIGN KEY (id_ecole) REFERENCES schools(id_ecole);

ALTER TABLE classes ADD COLUMN id_ecole INT NULL AFTER id;
UPDATE classes SET id_ecole = 1;
ALTER TABLE classes MODIFY id_ecole INT NOT NULL;
ALTER TABLE classes ADD CONSTRAINT fk_classes_ecole FOREIGN KEY (id_ecole) REFERENCES schools(id_ecole);

ALTER TABLE matieres ADD COLUMN id_ecole INT NULL AFTER id;
UPDATE matieres SET id_ecole = 1;
ALTER TABLE matieres MODIFY id_ecole INT NOT NULL;
ALTER TABLE matieres ADD CONSTRAINT fk_matieres_ecole FOREIGN KEY (id_ecole) REFERENCES schools(id_ecole);

ALTER TABLE salles ADD COLUMN id_ecole INT NULL AFTER id;
UPDATE salles SET id_ecole = 1;
ALTER TABLE salles MODIFY id_ecole INT NOT NULL;
ALTER TABLE salles ADD CONSTRAINT fk_salles_ecole FOREIGN KEY (id_ecole) REFERENCES schools(id_ecole);

ALTER TABLE eleves ADD COLUMN id_ecole INT NULL AFTER id;
UPDATE eleves SET id_ecole = 1;
ALTER TABLE eleves MODIFY id_ecole INT NOT NULL;
ALTER TABLE eleves ADD CONSTRAINT fk_eleves_ecole FOREIGN KEY (id_ecole) REFERENCES schools(id_ecole);

ALTER TABLE emplois ADD COLUMN id_ecole INT NULL AFTER id;
UPDATE emplois SET id_ecole = 1;
ALTER TABLE emplois MODIFY id_ecole INT NOT NULL;
ALTER TABLE emplois ADD CONSTRAINT fk_emplois_ecole FOREIGN KEY (id_ecole) REFERENCES schools(id_ecole);

ALTER TABLE notes ADD COLUMN id_ecole INT NULL AFTER id;
UPDATE notes SET id_ecole = 1;
ALTER TABLE notes MODIFY id_ecole INT NOT NULL;
ALTER TABLE notes ADD CONSTRAINT fk_notes_ecole FOREIGN KEY (id_ecole) REFERENCES schools(id_ecole);

ALTER TABLE paiements ADD COLUMN id_ecole INT NULL AFTER id;
UPDATE paiements SET id_ecole = 1;
ALTER TABLE paiements MODIFY id_ecole INT NOT NULL;
ALTER TABLE paiements ADD CONSTRAINT fk_paiements_ecole FOREIGN KEY (id_ecole) REFERENCES schools(id_ecole);

ALTER TABLE devoirs ADD COLUMN id_ecole INT NULL AFTER id;
UPDATE devoirs SET id_ecole = 1;
ALTER TABLE devoirs MODIFY id_ecole INT NOT NULL;
ALTER TABLE devoirs ADD CONSTRAINT fk_devoirs_ecole FOREIGN KEY (id_ecole) REFERENCES schools(id_ecole);

ALTER TABLE messages ADD COLUMN id_ecole INT NULL AFTER id;
UPDATE messages SET id_ecole = 1;
ALTER TABLE messages MODIFY id_ecole INT NOT NULL;
ALTER TABLE messages ADD CONSTRAINT fk_messages_ecole FOREIGN KEY (id_ecole) REFERENCES schools(id_ecole);

ALTER TABLE annonces ADD COLUMN id_ecole INT NULL AFTER id;
UPDATE annonces SET id_ecole = 1;
ALTER TABLE annonces MODIFY id_ecole INT NOT NULL;
ALTER TABLE annonces ADD CONSTRAINT fk_annonces_ecole FOREIGN KEY (id_ecole) REFERENCES schools(id_ecole);

ALTER TABLE users ADD COLUMN id_ecole INT NULL AFTER id;
UPDATE users SET id_ecole = 1;
ALTER TABLE users ADD CONSTRAINT fk_users_ecole FOREIGN KEY (id_ecole) REFERENCES schools(id_ecole);

-- Journal : colonne simple (sans clé étrangère, pour garder l'historique)
ALTER TABLE journal ADD COLUMN id_ecole INT NULL AFTER id;
UPDATE journal SET id_ecole = 1;

-- Rôle superadmin + compte élève lié à la fiche élève
ALTER TABLE users MODIFY role ENUM('superadmin','admin','enseignant','parent','eleve','comptable') NOT NULL;
ALTER TABLE eleves ADD COLUMN eleve_user_id INT NULL AFTER parent_user_id;
ALTER TABLE eleves ADD CONSTRAINT fk_eleves_eleve_user FOREIGN KEY (eleve_user_id) REFERENCES users(id);
UPDATE eleves SET eleve_user_id = (SELECT id FROM users WHERE email = 'eleve@ecole.tn' LIMIT 1) WHERE nom = 'Gharbi' AND prenom = 'Yassine';

-- Calendrier scolaire
CREATE TABLE evenements (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_ecole INT NOT NULL,
  titre VARCHAR(150) NOT NULL,
  type ENUM('Contrôle','Examen','Oral de synthèse','Réunion','Vacances / Férié','Autre') NOT NULL,
  classe_id INT NULL,
  matiere_id INT NULL, salle_id INT NULL,
  date_debut DATE NOT NULL, date_fin DATE NULL,
  heure_debut TIME NULL, heure_fin TIME NULL,
  description TEXT NULL,
  annee_scolaire VARCHAR(9) NOT NULL,
  deleted_at DATETIME NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_ecole) REFERENCES schools(id_ecole),
  FOREIGN KEY (classe_id) REFERENCES classes(id),
  FOREIGN KEY (matiere_id) REFERENCES matieres(id),
  FOREIGN KEY (salle_id) REFERENCES salles(id)
) ENGINE=InnoDB;

-- Compte superadmin (gère les écoles) — id_ecole reste NULL
INSERT INTO users (id_ecole, nom, email, password, role) VALUES (NULL, 'Super Administrateur', 'super@ecole.tn', 'super123', 'superadmin');

SET FOREIGN_KEY_CHECKS=1;
