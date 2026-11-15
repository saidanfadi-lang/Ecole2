<?php
class MessageController extends Controller {
    // Qui peut écrire à qui (RBAC)
    private $allowed = [
        'admin' => ['enseignant','parent','eleve','comptable','admin'],
        'enseignant' => ['parent','admin'],
        'parent' => ['enseignant','admin'],
        'eleve' => ['admin'],
        'comptable' => ['parent','admin'],
    ];
    public function index() {
        $rows = DB::all('SELECT m.*, u.nom AS autre FROM messages m JOIN users u ON u.id = m.expediteur_id
                         WHERE m.id_ecole = ? AND m.destinataire_id = ? AND m.masque_dest = 0 ORDER BY m.id DESC', [school_id(), $this->user()['id']]);
        $this->view('message/list', ['rows' => $rows, 'box' => 'Boîte de réception']);
    }
    public function sent() {
        $rows = DB::all('SELECT m.*, u.nom AS autre FROM messages m JOIN users u ON u.id = m.destinataire_id
                         WHERE m.id_ecole = ? AND m.expediteur_id = ? ORDER BY m.id DESC', [school_id(), $this->user()['id']]);
        $this->view('message/list', ['rows' => $rows, 'box' => 'Messages envoyés']);
    }
    public function compose() {
        $in = "'".implode("','", $this->allowed[$this->user()['role']])."'";
        $dest = DB::all("SELECT id, nom, role FROM users WHERE id_ecole = ? AND role IN ($in) AND id <> ? AND actif = 1 AND deleted_at IS NULL ORDER BY nom", [school_id(), $this->user()['id']]);
        $this->view('message/compose', ['dest' => $dest, 'error' => null]);
    }
    public function send() {
        $this->post();
        $to = DB::one('SELECT * FROM users WHERE id = ? AND id_ecole = ? AND actif = 1 AND deleted_at IS NULL', [(int)($_POST['destinataire_id'] ?? 0), school_id()]);
        $objet = trim($_POST['objet'] ?? ''); $corps = trim($_POST['corps'] ?? '');
        if (!$to || !in_array($to['role'], $this->allowed[$this->user()['role']]) || $objet === '' || $corps === '') {
            $this->flash('err', 'Message invalide ou destinataire non autorisé pour votre rôle.');
            $this->redirect('message/compose');
        }
        DB::q('INSERT INTO messages (id_ecole,expediteur_id,destinataire_id,objet,corps,annee_scolaire) VALUES (?,?,?,?,?,?)',
            [school_id(), $this->user()['id'], $to['id'], $objet, $corps, ANNEE]);
        journal('messages', DB::id(), 'envoi', null, ['destinataire' => $to['id']]);
        $this->flash('ok', 'Message envoyé (il ne pourra plus être modifié ni supprimé).');
        $this->redirect('message/sent');
    }
    public function show($id = 0) {
        $uid = $this->user()['id'];
        $m = DB::one('SELECT m.*, e.nom AS exp, d.nom AS dest FROM messages m
                      JOIN users e ON e.id = m.expediteur_id JOIN users d ON d.id = m.destinataire_id WHERE m.id = ? AND m.id_ecole = ?', [(int)$id, school_id()]);
        if (!$m || ($m['expediteur_id'] != $uid && $m['destinataire_id'] != $uid) || ($m['destinataire_id'] == $uid && $m['masque_dest'])) $this->deny();
        if ($m['destinataire_id'] == $uid && !$m['lu']) DB::q('UPDATE messages SET lu = 1 WHERE id = ?', [$m['id']]);
        journal('messages', $m['id'], 'consultation');   // traçabilité des accès
        $this->view('message/show', ['m' => $m, 'moi' => $uid]);
    }
    public function hide($id = 0) { // suppression côté destinataire uniquement
        $this->post();
        DB::q('UPDATE messages SET masque_dest = 1 WHERE id = ? AND id_ecole = ? AND destinataire_id = ?', [(int)$id, school_id(), $this->user()['id']]);
        $this->redirect('message');
    }
}
