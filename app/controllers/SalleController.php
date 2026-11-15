<?php
class SalleController extends CrudController {
    protected $table = 'salles', $slug = 'salle', $title = 'Salles';
    protected $list = ['nom', 'capacite', 'disponible'];
    public function __construct() {
        $this->fields = [
            'nom' => ['label' => 'Nom', 'required' => 1],
            'capacite' => ['label' => 'Capacité', 'type' => 'number', 'required' => 1, 'min' => 1],
            'disponible' => ['label' => 'Disponibilité', 'type' => 'select', 'required' => 1, 'options' => ['1' => 'Disponible', '0' => 'Indisponible']],
        ];
    }
    protected function beforeDelete($id) {
        return DB::val('SELECT COUNT(*) FROM emplois WHERE salle_id = ? AND deleted_at IS NULL', [$id]) > 0
            ? 'Impossible : cette salle est utilisée dans l\'emploi du temps.' : null;
    }
}
