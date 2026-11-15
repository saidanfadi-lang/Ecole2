<?php
/* Calendrier scolaire : regroupe les échéances (événements + devoirs) de l'école de la session */
class Cal {
    const TYPES = ['Contrôle', 'Examen', 'Oral de synthèse', 'Réunion', 'Vacances / Férié', 'Autre'];
    const JOURS = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
    const MOIS  = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

    // Classe CSS (couleur) par type
    static function css($type) {
        $m = ['Devoir' => 'devoir', 'Contrôle' => 'controle', 'Examen' => 'examen', 'Oral de synthèse' => 'oral',
              'Réunion' => 'reunion', 'Vacances / Férié' => 'vacances'];
        return 'ev-'.($m[$type] ?? 'autre');
    }

    // null = aucune restriction (admin, enseignant, comptable) ; sinon liste des classes visibles (parent / élève)
    static function restriction() {
        $u = $_SESSION['user'];
        $col = ['parent' => 'parent_user_id', 'eleve' => 'eleve_user_id'][$u['role']] ?? null;
        if (!$col) return null;
        $ids = DB::q("SELECT DISTINCT classe_id FROM eleves WHERE $col = ? AND id_ecole = ? AND deleted_at IS NULL AND classe_id IS NOT NULL",
                     [$u['id'], school_id()])->fetchAll(PDO::FETCH_COLUMN);
        return array_map('intval', $ids);
    }

    // Classes proposées dans le filtre
    static function classes() {
        $res = self::restriction();
        $sql = "SELECT id, CONCAT(niveau,' ',section) AS lbl FROM classes WHERE deleted_at IS NULL AND id_ecole = ? AND annee_scolaire = ?";
        if ($res !== null) $sql .= $res ? ' AND id IN ('.implode(',', $res).')' : ' AND 1 = 0';
        return DB::q($sql.' ORDER BY niveau, section', [school_id(), ANNEE])->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    // Échéances entre deux dates (YYYY-MM-DD, incluses)
    static function range($from, $to, $classe = 0) {
        $sid = school_id(); $res = self::restriction(); $out = [];

        $c1 = ''; $c2 = '';
        if ($res !== null) {
            $c1 = $res ? ' AND (e.classe_id IS NULL OR e.classe_id IN ('.implode(',', $res).'))' : ' AND e.classe_id IS NULL';
            $c2 = $res ? ' AND d.classe_id IN ('.implode(',', $res).')' : ' AND 1 = 0';
        }
        if ($classe) { // filtre choisi (déjà validé par le contrôleur)
            $c1 .= ' AND (e.classe_id IS NULL OR e.classe_id = '.(int)$classe.')';
            $c2 .= ' AND d.classe_id = '.(int)$classe;
        }

        $ev = DB::all("SELECT e.*, CONCAT(c.niveau,' ',c.section) AS classe, m.nom AS matiere, s.nom AS salle
                       FROM evenements e LEFT JOIN classes c ON c.id = e.classe_id
                       LEFT JOIN matieres m ON m.id = e.matiere_id LEFT JOIN salles s ON s.id = e.salle_id
                       WHERE e.id_ecole = ? AND e.deleted_at IS NULL AND e.date_debut <= ? AND COALESCE(e.date_fin, e.date_debut) >= ?$c1",
                       [$sid, $to, $from]);
        foreach ($ev as $r) $out[] = [
            'source' => 'evenement', 'id' => $r['id'], 'titre' => $r['titre'], 'type' => $r['type'],
            'debut' => $r['date_debut'], 'fin' => $r['date_fin'] ?: $r['date_debut'],
            'h1' => $r['heure_debut'] ? substr($r['heure_debut'], 0, 5) : null,
            'h2' => $r['heure_fin'] ? substr($r['heure_fin'], 0, 5) : null,
            'classe' => $r['classe'], 'matiere' => $r['matiere'], 'salle' => $r['salle'], 'desc' => $r['description'],
        ];

        $dv = DB::all("SELECT d.*, CONCAT(c.niveau,' ',c.section) AS classe, m.nom AS matiere
                       FROM devoirs d JOIN classes c ON c.id = d.classe_id JOIN matieres m ON m.id = d.matiere_id
                       WHERE d.id_ecole = ? AND d.deleted_at IS NULL AND d.date_remise BETWEEN ? AND ?$c2", [$sid, $from, $to]);
        foreach ($dv as $r) $out[] = [
            'source' => 'devoir', 'id' => $r['id'], 'titre' => $r['titre'], 'type' => 'Devoir',
            'debut' => $r['date_remise'], 'fin' => $r['date_remise'], 'h1' => null, 'h2' => null,
            'classe' => $r['classe'], 'matiere' => $r['matiere'], 'salle' => null, 'desc' => $r['description'],
        ];

        usort($out, function ($a, $b) { return strcmp($a['debut'].($a['h1'] ?? '00:00'), $b['debut'].($b['h1'] ?? '00:00')); });
        return $out;
    }

    // Cours de l'emploi du temps d'une classe (affichés en grisé dans la vue semaine)
    static function lessons($classe) {
        if (!$classe) return [];
        return DB::all("SELECT e.jour, e.heure_debut, e.heure_fin, m.nom AS matiere, s.nom AS salle
                        FROM emplois e JOIN matieres m ON m.id = e.matiere_id JOIN salles s ON s.id = e.salle_id
                        WHERE e.id_ecole = ? AND e.classe_id = ? AND e.annee_scolaire = ? AND e.deleted_at IS NULL
                        ORDER BY e.heure_debut", [school_id(), (int)$classe, ANNEE]);
    }

    // Prochaines échéances (tableau de bord)
    static function upcoming($days = 21, $limit = 6) {
        $from = date('Y-m-d'); $to = date('Y-m-d', strtotime("+$days days"));
        return array_slice(self::range($from, $to), 0, $limit);
    }
}
