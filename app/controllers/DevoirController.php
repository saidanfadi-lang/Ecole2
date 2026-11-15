<?php
class DevoirController extends CrudController {
    protected $table = 'devoirs', $slug = 'devoir', $title = 'Devoirs';
    protected $writeRoles = ['admin', 'enseignant'];
    protected $list = ['titre', 'matiere_id', 'classe_id', 'date_remise'];
    public function __construct() {
        $this->fields = [
            'titre' => ['label' => 'Titre', 'required' => 1],
            'description' => ['label' => 'Description', 'type' => 'textarea'],
            'matiere_id' => ['label' => 'Matière', 'required' => 1, 'ref' => ['table' => 'matieres', 'label' => 'nom']],
            'classe_id' => ['label' => 'Classe', 'required' => 1, 'ref' => ['table' => 'classes', 'label' => "CONCAT(niveau,' ',section)"]],
            'date_remise' => ['label' => 'Date de remise', 'type' => 'date', 'required' => 1],
        ];
    }
}
