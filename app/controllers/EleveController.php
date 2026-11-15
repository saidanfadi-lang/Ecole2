<?php
class EleveController extends CrudController {
    protected $table = 'eleves', $slug = 'eleve', $title = 'Élèves';
    protected $list = ['nom', 'prenom', 'date_naissance', 'classe_id', 'parent_nom', 'parent_tel'];
    public function __construct() {
        $this->fields = [
            'nom' => ['label' => 'Nom', 'required' => 1],
            'prenom' => ['label' => 'Prénom', 'required' => 1],
            'date_naissance' => ['label' => 'Date de naissance', 'type' => 'date', 'required' => 1],
            'sexe' => ['label' => 'Sexe', 'type' => 'select', 'required' => 1, 'options' => ['M' => 'Garçon', 'F' => 'Fille']],
            'classe_id' => ['label' => 'Classe', 'ref' => ['table' => 'classes', 'label' => "CONCAT(niveau,' ',section)"]],
            'parent_user_id' => ['label' => 'Compte parent', 'ref' => ['table' => 'users', 'label' => 'nom', 'where' => "role='parent'"]],
            'eleve_user_id' => ['label' => 'Compte élève (connexion)', 'ref' => ['table' => 'users', 'label' => 'nom', 'where' => "role='eleve'"]],
            'parent_nom' => ['label' => 'Nom du parent / tuteur'],
            'parent_tel' => ['label' => 'Téléphone du parent'],
            'parent_email' => ['label' => 'E-mail du parent', 'type' => 'email'],
            'antecedents_medicaux' => ['label' => 'Antécédents médicaux', 'type' => 'textarea'],
        ];
    }
    protected function validateExtra($d, $id) {
        if ($d['classe_id']) {
            $c = DB::one('SELECT capacite FROM classes WHERE id = ?', [$d['classe_id']]);
            $n = DB::val('SELECT COUNT(*) FROM eleves WHERE classe_id = ? AND deleted_at IS NULL AND id <> ?', [$d['classe_id'], (int)$id]);
            if ($c && $n >= $c['capacite']) return 'La classe a atteint sa capacité maximale ('.$c['capacite'].').';
        }
        return null;
    }
    public function export() { // export CSV (ouvrable dans Excel)
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="eleves.csv"');
        echo "\xEF\xBB\xBF";
        $out = fopen('php://output', 'w');
        fputcsv($out, ['Nom', 'Prénom', 'Naissance', 'Sexe', 'Parent', 'Téléphone'], ';');
        foreach ($this->selectRows() as $r) fputcsv($out, [$r['nom'], $r['prenom'], $r['date_naissance'], $r['sexe'], $r['parent_nom'], $r['parent_tel']], ';');
        exit;
    }
}
