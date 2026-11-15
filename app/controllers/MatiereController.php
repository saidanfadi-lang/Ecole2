<?php
class MatiereController extends CrudController {
    protected $table = 'matieres', $slug = 'matiere', $title = 'Matières';
    protected $list = ['nom', 'coefficient', 'niveau'];
    public function __construct() {
        $niv = array_combine(['CP','CE1','CE2','CM1','CM2'], ['CP','CE1','CE2','CM1','CM2']);
        $this->fields = [
            'nom' => ['label' => 'Nom', 'required' => 1],
            'coefficient' => ['label' => 'Coefficient', 'type' => 'number', 'required' => 1, 'min' => 0.5],
            'niveau' => ['label' => 'Niveau', 'type' => 'select', 'required' => 1, 'options' => $niv],
        ];
    }
    protected function beforeDelete($id) {
        $n = DB::val('SELECT COUNT(*) FROM notes WHERE matiere_id = ? AND deleted_at IS NULL', [$id])
           + DB::val('SELECT COUNT(*) FROM emplois WHERE matiere_id = ? AND deleted_at IS NULL', [$id]);
        return $n > 0 ? 'Impossible : des notes ou des cours sont rattachés à cette matière.' : null;
    }
}
