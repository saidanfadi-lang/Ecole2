<?php
class AnnonceController extends Controller {
    public function index() {
        $role = $this->user()['role'];
        $rows = DB::all("SELECT a.*, u.nom AS auteur FROM annonces a JOIN users u ON u.id = a.auteur_id
                         WHERE a.id_ecole = ? AND (a.cible = 'tous' OR a.cible = ? OR ? = 'admin') ORDER BY a.id DESC", [school_id(), $role, $role]);
        $this->view('annonce/index', ['rows' => $rows, 'isAdmin' => $role === 'admin']);
    }
    public function create() { if ($this->user()['role'] !== 'admin') $this->deny(); $this->view('annonce/form'); }
    public function store() {
        if ($this->user()['role'] !== 'admin') $this->deny();
        $this->post();
        $t = trim($_POST['titre'] ?? ''); $c = trim($_POST['corps'] ?? ''); $cible = $_POST['cible'] ?? 'tous';
        if ($t === '' || $c === '' || !in_array($cible, ['tous','parent','enseignant','eleve'])) { $this->flash('err', 'Titre et contenu obligatoires.'); $this->redirect('annonce/create'); }
        DB::q('INSERT INTO annonces (id_ecole,auteur_id,titre,corps,cible,annee_scolaire) VALUES (?,?,?,?,?,?)', [school_id(), $this->user()['id'], $t, $c, $cible, ANNEE]);
        journal('annonces', DB::id(), 'publication');
        $this->flash('ok', 'Annonce publiée (non modifiable : en cas d\'erreur, publiez une annonce corrective).');
        $this->redirect('annonce');
    }
}
