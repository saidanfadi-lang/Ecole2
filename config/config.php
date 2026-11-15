<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'ecole_db');
define('DB_USER', 'root');
define('DB_PASS', '');            // XAMPP : mot de passe vide par défaut
define('BASE_URL', '/ecole');     // dossier dans htdocs
define('ANNEE', '2026-2027');     // année scolaire courante
define('TARIF_ANNUEL', 1500);     // total à payer par élève (DT) pour calculer le solde
define('APP_NAME', 'Gestion scolaire multi-écoles'); // le nom de chaque école vient de la table schools

// Contrôleurs autorisés par rôle (RBAC)
$PERMS = [
  'superadmin' => ['dashboard','ecole','user','auth'],   // gère les écoles et leurs administrateurs
  'admin'      => ['*'],
  'enseignant' => ['dashboard','note','devoir','emploi','calendrier','evenement','message','annonce','bulletin','auth'],
  'comptable'  => ['dashboard','paiement','calendrier','message','annonce','auth'],
  'parent'     => ['dashboard','calendrier','message','annonce','auth'],
  'eleve'      => ['dashboard','calendrier','message','annonce','auth'],
];
