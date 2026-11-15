<?php
class BulletinController extends Controller {
    public function index() {
        $eleves = DB::all("SELECT id, CONCAT(nom,' ',prenom) AS nom FROM eleves WHERE deleted_at IS NULL AND id_ecole = ? AND annee_scolaire = ? ORDER BY nom", [school_id(), ANNEE]);
        $this->view('bulletin/index', compact('eleves'));
    }
    public function show($eleveId = 0, $trim = 1) {
        $el = DB::one('SELECT e.*, CONCAT(c.niveau," ",c.section) AS classe FROM eleves e LEFT JOIN classes c ON c.id = e.classe_id WHERE e.id = ? AND e.id_ecole = ? AND e.deleted_at IS NULL', [(int)$eleveId, school_id()]);
        if (!$el) { http_response_code(404); die('Élève introuvable.'); }
        $lignes = DB::all('SELECT m.nom, m.coefficient, AVG(n.valeur) AS moyenne FROM notes n JOIN matieres m ON m.id = n.matiere_id
                           WHERE n.eleve_id = ? AND n.trimestre = ? AND n.annee_scolaire = ? AND n.deleted_at IS NULL GROUP BY m.id, m.nom, m.coefficient',
                           [$el['id'], (int)$trim, ANNEE]);
        $somme = 0; $coefs = 0;
        foreach ($lignes as $l) { $somme += $l['moyenne'] * $l['coefficient']; $coefs += $l['coefficient']; }
        $generale = $coefs > 0 ? round($somme / $coefs, 2) : null;
        $this->plain('bulletin/show', ['el' => $el, 'lignes' => $lignes, 'generale' => $generale, 'trim' => (int)$trim]);
    }
}
