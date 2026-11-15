<?php
class PaiementController extends CrudController {
    protected $table = 'paiements', $slug = 'paiement', $title = 'Paiements';
    protected $writeRoles = ['admin', 'comptable'];
    protected $list = ['eleve_id', 'type', 'montant', 'mode', 'date_paiement'];
    protected $rowLinks = [['Reçu', 'paiement/recu/{id}']];
    public function __construct() {
        $this->fields = [
            'eleve_id' => ['label' => 'Élève', 'required' => 1, 'ref' => ['table' => 'eleves', 'label' => "CONCAT(nom,' ',prenom)"]],
            'type' => ['label' => 'Type', 'type' => 'select', 'required' => 1, 'options' => ['Inscription' => 'Inscription', 'Scolarité' => 'Scolarité']],
            'montant' => ['label' => 'Montant (DT)', 'type' => 'number', 'required' => 1, 'min' => 1],
            'mode' => ['label' => 'Mode', 'type' => 'select', 'required' => 1, 'options' => ['Espèces' => 'Espèces', 'Virement' => 'Virement', 'Mobile money' => 'Mobile money']],
            'date_paiement' => ['label' => 'Date', 'type' => 'date', 'required' => 1],
        ];
    }
    private function solde($eleveId) {
        $paye = (float)DB::val('SELECT COALESCE(SUM(montant),0) FROM paiements WHERE eleve_id = ? AND id_ecole = ? AND annee_scolaire = ? AND deleted_at IS NULL', [$eleveId, school_id(), ANNEE]);
        return [$paye, TARIF_ANNUEL - $paye];
    }
    // Notification automatique du parent après paiement (messagerie interne + journal)
    protected function afterStore($id, $d) {
        $el = DB::one('SELECT * FROM eleves WHERE id = ? AND id_ecole = ?', [$d['eleve_id'], school_id()]);
        if (!$el || !$el['parent_user_id']) return;
        list($paye, $reste) = $this->solde($d['eleve_id']);
        $corps = "Paiement reçu pour {$el['prenom']} {$el['nom']}.\nMontant : {$d['montant']} DT\nDate : {$d['date_paiement']}\nMode : {$d['mode']}\nSolde restant : $reste DT\nReçu : ".$_SERVER['HTTP_HOST'].url('paiement/recu/'.$id);
        DB::q('INSERT INTO messages (id_ecole,expediteur_id,destinataire_id,objet,corps,annee_scolaire) VALUES (?,?,?,?,?,?)',
            [school_id(), $_SESSION['user']['id'], $el['parent_user_id'], 'Confirmation de paiement', $corps, ANNEE]);
        journal('messages', DB::id(), 'notification paiement envoyée', null, ['destinataire' => $el['parent_user_id'], 'statut' => 'envoyé']);
    }
    public function recu($id = 0) {
        $p = $this->find($id);
        $el = DB::one('SELECT e.*, CONCAT(c.niveau," ",c.section) AS classe FROM eleves e LEFT JOIN classes c ON c.id = e.classe_id WHERE e.id = ? AND e.id_ecole = ?', [$p['eleve_id'], school_id()]);
        list($paye, $reste) = $this->solde($p['eleve_id']);
        $this->plain('paiement/recu', compact('p', 'el', 'paye', 'reste'));
    }
}
