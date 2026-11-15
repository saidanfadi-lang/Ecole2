<?php
class ClasseController extends CrudController {
    protected $table = 'classes', $slug = 'classe', $title = 'Classes';
    protected $list = ['niveau', 'section', 'capacite', 'enseignant_id'];
    public function __construct() {
        $this->fields = [
            'niveau' => ['label' => 'Niveau', 'type' => 'select', 'required' => 1, 'options' => array_combine(['7eme','8eme','9eme'], ['7eme','8eme','9eme'])],
            'section' => ['label' => 'Section', 'required' => 1],
            'capacite' => ['label' => 'Capacité max', 'type' => 'number', 'required' => 1, 'min' => 1],
            'enseignant_id' => ['label' => 'Enseignant titulaire', 'ref' => ['table' => 'enseignants', 'label' => "CONCAT(prenom,' ',nom)"]],
        ];
    }
    protected function beforeDelete($id) {
        $n = DB::val('SELECT COUNT(*) FROM eleves WHERE classe_id = ? AND deleted_at IS NULL', [$id]);
        return $n > 0 ? "Impossible : $n élève(s) sont encore affectés à cette classe. Réaffectez-les d'abord." : null;
    }
}
