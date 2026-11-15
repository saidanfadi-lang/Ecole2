-- Base de données : gestion scolaire MULTI-ÉCOLES (installation neuve)
CREATE DATABASE IF NOT EXISTS ecole_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ecole_db;
SET FOREIGN_KEY_CHECKS=0;

CREATE TABLE schools (
  id_ecole INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(150) NOT NULL,
  ville VARCHAR(80) NULL, telephone VARCHAR(30) NULL, email VARCHAR(150) NULL,
  actif TINYINT(1) NOT NULL DEFAULT 1,
  deleted_at DATETIME NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_ecole INT NULL,                       -- NULL = superadmin (pas rattaché à une école)
  nom VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('superadmin','admin','enseignant','parent','eleve','comptable') NOT NULL,
  actif TINYINT(1) NOT NULL DEFAULT 1,
  deleted_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_ecole) REFERENCES schools(id_ecole)
) ENGINE=InnoDB;

CREATE TABLE enseignants (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_ecole INT NOT NULL,
  nom VARCHAR(80) NOT NULL, prenom VARCHAR(80) NOT NULL,
  email VARCHAR(150) NULL, telephone VARCHAR(30) NULL,
  annee_scolaire VARCHAR(9) NOT NULL,
  deleted_at DATETIME NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_ecole) REFERENCES schools(id_ecole)
) ENGINE=InnoDB;

CREATE TABLE classes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_ecole INT NOT NULL,
  niveau VARCHAR(20) NOT NULL, section VARCHAR(10) NOT NULL,
  capacite INT NOT NULL DEFAULT 30,
  enseignant_id INT NULL,
  annee_scolaire VARCHAR(9) NOT NULL,
  deleted_at DATETIME NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (enseignant_id) REFERENCES enseignants(id),
  FOREIGN KEY (id_ecole) REFERENCES schools(id_ecole)
) ENGINE=InnoDB;

CREATE TABLE matieres (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_ecole INT NOT NULL,
  nom VARCHAR(100) NOT NULL, coefficient DECIMAL(4,2) NOT NULL DEFAULT 1,
  niveau VARCHAR(20) NOT NULL,
  annee_scolaire VARCHAR(9) NOT NULL,
  deleted_at DATETIME NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_ecole) REFERENCES schools(id_ecole)
) ENGINE=InnoDB;

CREATE TABLE salles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_ecole INT NOT NULL,
  nom VARCHAR(60) NOT NULL, capacite INT NOT NULL DEFAULT 30,
  disponible TINYINT(1) NOT NULL DEFAULT 1,
  annee_scolaire VARCHAR(9) NOT NULL,
  deleted_at DATETIME NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_ecole) REFERENCES schools(id_ecole)
) ENGINE=InnoDB;

CREATE TABLE eleves (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_ecole INT NOT NULL,
  nom VARCHAR(80) NOT NULL, prenom VARCHAR(80) NOT NULL,
  date_naissance DATE NOT NULL, sexe ENUM('M','F') NOT NULL,
  classe_id INT NULL,
  parent_user_id INT NULL,
  eleve_user_id INT NULL,
  parent_nom VARCHAR(120) NULL, parent_tel VARCHAR(30) NULL, parent_email VARCHAR(150) NULL,
  antecedents_medicaux TEXT NULL,
  annee_scolaire VARCHAR(9) NOT NULL,
  deleted_at DATETIME NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (classe_id) REFERENCES classes(id),
  FOREIGN KEY (parent_user_id) REFERENCES users(id),
  FOREIGN KEY (eleve_user_id) REFERENCES users(id),
  FOREIGN KEY (id_ecole) REFERENCES schools(id_ecole)
) ENGINE=InnoDB;

CREATE TABLE emplois (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_ecole INT NOT NULL,
  classe_id INT NOT NULL, matiere_id INT NOT NULL, enseignant_id INT NOT NULL, salle_id INT NOT NULL,
  jour ENUM('Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi') NOT NULL,
  heure_debut TIME NOT NULL, heure_fin TIME NOT NULL,
  annee_scolaire VARCHAR(9) NOT NULL,
  deleted_at DATETIME NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (classe_id) REFERENCES classes(id),
  FOREIGN KEY (matiere_id) REFERENCES matieres(id),
  FOREIGN KEY (enseignant_id) REFERENCES enseignants(id),
  FOREIGN KEY (salle_id) REFERENCES salles(id),
  FOREIGN KEY (id_ecole) REFERENCES schools(id_ecole)
) ENGINE=InnoDB;

CREATE TABLE notes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_ecole INT NOT NULL,
  eleve_id INT NOT NULL, matiere_id INT NOT NULL,
  type ENUM('Contrôle continu','Examen') NOT NULL,
  trimestre TINYINT NOT NULL,
  valeur DECIMAL(4,2) NOT NULL,
  annee_scolaire VARCHAR(9) NOT NULL,
  deleted_at DATETIME NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (eleve_id) REFERENCES eleves(id),
  FOREIGN KEY (matiere_id) REFERENCES matieres(id),
  FOREIGN KEY (id_ecole) REFERENCES schools(id_ecole)
) ENGINE=InnoDB;

CREATE TABLE paiements (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_ecole INT NOT NULL,
  eleve_id INT NOT NULL,
  type ENUM('Inscription','Scolarité') NOT NULL,
  montant DECIMAL(10,2) NOT NULL,
  mode ENUM('Espèces','Virement','Mobile money') NOT NULL,
  date_paiement DATE NOT NULL,
  annee_scolaire VARCHAR(9) NOT NULL,
  deleted_at DATETIME NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (eleve_id) REFERENCES eleves(id),
  FOREIGN KEY (id_ecole) REFERENCES schools(id_ecole)
) ENGINE=InnoDB;

CREATE TABLE devoirs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_ecole INT NOT NULL,
  titre VARCHAR(150) NOT NULL, description TEXT NULL,
  matiere_id INT NOT NULL, classe_id INT NOT NULL,
  date_remise DATE NOT NULL,
  annee_scolaire VARCHAR(9) NOT NULL,
  deleted_at DATETIME NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (matiere_id) REFERENCES matieres(id),
  FOREIGN KEY (classe_id) REFERENCES classes(id),
  FOREIGN KEY (id_ecole) REFERENCES schools(id_ecole)
) ENGINE=InnoDB;

CREATE TABLE messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_ecole INT NOT NULL,
  expediteur_id INT NOT NULL, destinataire_id INT NOT NULL,
  objet VARCHAR(150) NOT NULL, corps TEXT NOT NULL,
  lu TINYINT(1) NOT NULL DEFAULT 0,
  masque_dest TINYINT(1) NOT NULL DEFAULT 0,
  annee_scolaire VARCHAR(9) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (expediteur_id) REFERENCES users(id),
  FOREIGN KEY (destinataire_id) REFERENCES users(id),
  FOREIGN KEY (id_ecole) REFERENCES schools(id_ecole)
) ENGINE=InnoDB;

CREATE TABLE annonces (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_ecole INT NOT NULL,
  auteur_id INT NOT NULL, titre VARCHAR(150) NOT NULL, corps TEXT NOT NULL,
  cible ENUM('tous','parent','enseignant','eleve') NOT NULL DEFAULT 'tous',
  annee_scolaire VARCHAR(9) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (auteur_id) REFERENCES users(id),
  FOREIGN KEY (id_ecole) REFERENCES schools(id_ecole)
) ENGINE=InnoDB;

CREATE TABLE evenements (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_ecole INT NOT NULL,
  titre VARCHAR(150) NOT NULL,
  type ENUM('Contrôle','Examen','Oral de synthèse','Réunion','Vacances / Férié','Autre') NOT NULL,
  classe_id INT NULL,                      -- NULL = toute l'école
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

CREATE TABLE journal (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_ecole INT NULL,
  user_id INT NULL, entite VARCHAR(50) NOT NULL, entite_id INT NULL,
  action VARCHAR(30) NOT NULL, anciennes TEXT NULL, nouvelles TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS=1;

-- Données de démonstration (mots de passe en clair, usage local uniquement)
INSERT INTO schools (nom,ville,telephone,email) VALUES
('École Abou Kacem Chebbi','Tozeur','76123456','contact@chebbi.tn'),
('École Ibn Khaldoun','Tunis','71234567','contact@ibnkhaldoun.tn');

INSERT INTO users (id_ecole,nom,email,password,role) VALUES
(1,'Directeur Ben Ali','admin@ecole.tn','admin123','admin'),
(1,'Mme Salma Trabelsi','prof@ecole.tn','prof123','enseignant'),
(1,'M. Karim Gharbi','parent@ecole.tn','parent123','parent'),
(1,'Mme Hela Jlassi','compta@ecole.tn','compta123','comptable'),
(1,'Yassine Gharbi','eleve@ecole.tn','eleve123','eleve'),
(NULL,'Super Administrateur','super@ecole.tn','super123','superadmin'),
(2,'Directrice Ben Salah','admin2@ecole2.tn','admin123','admin');

-- École 1
INSERT INTO enseignants (id_ecole,nom,prenom,email,telephone,annee_scolaire) VALUES
(1,'Trabelsi','Salma','prof@ecole.tn','20111222','2026-2027'),
(1,'Mansour','Ahmed','ahmed@ecole.tn','22333444','2026-2027');
INSERT INTO classes (id_ecole,niveau,section,capacite,enseignant_id,annee_scolaire) VALUES
(1,'CP','A',25,1,'2026-2027'),(1,'CE1','A',28,2,'2026-2027');
INSERT INTO matieres (id_ecole,nom,coefficient,niveau,annee_scolaire) VALUES
(1,'Arabe',3,'CP','2026-2027'),(1,'Mathématiques',3,'CP','2026-2027'),(1,'Français',2,'CP','2026-2027');
INSERT INTO salles (id_ecole,nom,capacite,disponible,annee_scolaire) VALUES
(1,'Salle 1',30,1,'2026-2027'),(1,'Salle 2',30,1,'2026-2027');
INSERT INTO eleves (id_ecole,nom,prenom,date_naissance,sexe,classe_id,parent_user_id,eleve_user_id,parent_nom,parent_tel,parent_email,annee_scolaire) VALUES
(1,'Gharbi','Yassine','2020-04-12','M',1,3,5,'Karim Gharbi','98123456','parent@ecole.tn','2026-2027');
INSERT INTO emplois (id_ecole,classe_id,matiere_id,enseignant_id,salle_id,jour,heure_debut,heure_fin,annee_scolaire) VALUES
(1,1,1,1,1,'Lundi','08:00','09:00','2026-2027'),
(1,1,2,1,1,'Mardi','09:00','10:00','2026-2027'),
(1,1,3,1,1,'Jeudi','10:00','11:00','2026-2027');
INSERT INTO devoirs (id_ecole,titre,description,matiere_id,classe_id,date_remise,annee_scolaire) VALUES
(1,'Exercices page 24','Faire les exercices 1 à 5',2,1,'2026-11-20','2026-2027');
INSERT INTO evenements (id_ecole,titre,type,classe_id,matiere_id,salle_id,date_debut,date_fin,heure_debut,heure_fin,description,annee_scolaire) VALUES
(1,'Contrôle de mathématiques','Contrôle',1,2,1,'2026-11-18',NULL,'09:00','10:00','Additions et soustractions','2026-2027'),
(1,'Examen du 1er trimestre','Examen',NULL,NULL,NULL,'2026-12-14','2026-12-18',NULL,NULL,'Examens de toutes les classes','2026-2027'),
(1,'Oral de synthèse','Oral de synthèse',1,3,2,'2026-12-10',NULL,'10:00','11:30','Présentation orale','2026-2027'),
(1,'Réunion parents-professeurs','Réunion',NULL,NULL,NULL,'2026-11-26',NULL,'17:00','18:30',NULL,'2026-2027'),
(1,'Vacances d''hiver','Vacances / Férié',NULL,NULL,NULL,'2026-12-28','2027-01-03',NULL,NULL,NULL,'2026-2027');

-- École 2 (données indépendantes de l'école 1)
INSERT INTO enseignants (id_ecole,nom,prenom,email,telephone,annee_scolaire) VALUES
(2,'Hamdi','Nadia','nadia@ecole2.tn','21555666','2026-2027');
INSERT INTO classes (id_ecole,niveau,section,capacite,enseignant_id,annee_scolaire) VALUES
(2,'CE2','B',30,3,'2026-2027');
INSERT INTO matieres (id_ecole,nom,coefficient,niveau,annee_scolaire) VALUES
(2,'Mathématiques',3,'CE2','2026-2027');
INSERT INTO salles (id_ecole,nom,capacite,disponible,annee_scolaire) VALUES
(2,'Salle A',30,1,'2026-2027');
INSERT INTO eleves (id_ecole,nom,prenom,date_naissance,sexe,classe_id,parent_nom,parent_tel,annee_scolaire) VALUES
(2,'Ben Salah','Amira','2018-09-03','F',3,'Sami Ben Salah','97111222','2026-2027');
INSERT INTO evenements (id_ecole,titre,type,classe_id,matiere_id,date_debut,heure_debut,heure_fin,annee_scolaire) VALUES
(2,'Contrôle de mathématiques','Contrôle',3,4,'2026-11-19','08:30','09:30','2026-2027');
