<?php
class DashboardController extends Controller {
    public function index() {
        if (is_super()) { // vue du superadmin : toutes les écoles
            $ecoles = DB::all("SELECT s.nom, s.ville, s.actif,
                (SELECT COUNT(*) FROM users u WHERE u.id_ecole = s.id_ecole AND u.deleted_at IS NULL) AS comptes,
                (SELECT COUNT(*) FROM eleves e WHERE e.id_ecole = s.id_ecole AND e.deleted_at IS NULL AND e.annee_scolaire = ?) AS eleves
                FROM schools s WHERE s.deleted_at IS NULL ORDER BY s.nom", [ANNEE]);
            return $this->view('dashboard_super', compact('ecoles'));
        }
        $sid = school_id();
        $n = function ($t) use ($sid) { return (int)DB::val("SELECT COUNT(*) FROM $t WHERE deleted_at IS NULL AND id_ecole = ? AND annee_scolaire = ?", [$sid, ANNEE]); };
        $stats = [
            'Élèves' => $n('eleves'), 'Classes' => $n('classes'), 'Enseignants' => $n('enseignants'), 'Devoirs' => $n('devoirs'),
        ];
        $total = (float)DB::val('SELECT COALESCE(SUM(montant),0) FROM paiements WHERE deleted_at IS NULL AND id_ecole = ? AND annee_scolaire = ?', [$sid, ANNEE]);
        $nonLus = (int)DB::val('SELECT COUNT(*) FROM messages WHERE id_ecole = ? AND destinataire_id = ? AND lu = 0 AND masque_dest = 0', [$sid, $this->user()['id']]);
        $echeances = can('calendrier') ? Cal::upcoming(21, 6) : [];
        $this->view('dashboard', compact('stats', 'total', 'nonLus', 'echeances'));
    }
}
