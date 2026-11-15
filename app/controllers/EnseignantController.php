<?php
class EnseignantController extends CrudController {
    protected $table = 'enseignants', $slug = 'enseignant', $title = 'Enseignants';
    protected $list = ['nom', 'prenom', 'email', 'telephone'];
    public function __construct() {
        $this->fields = [
            'nom' => ['label' => 'Nom', 'required' => 1],
            'prenom' => ['label' => 'Prénom', 'required' => 1],
            'email' => ['label' => 'E-mail', 'type' => 'email'],
            'telephone' => ['label' => 'Téléphone'],
        ];
    }
    protected function beforeDelete($id) {
        $n = DB::val('SELECT COUNT(*) FROM classes WHERE enseignant_id = ? AND deleted_at IS NULL', [$id])
           + DB::val('SELECT COUNT(*) FROM emplois WHERE enseignant_id = ? AND deleted_at IS NULL', [$id]);
        return $n > 0 ? 'Impossible : cet enseignant est affecté à une classe ou à des cours.' : null;
    }
}
