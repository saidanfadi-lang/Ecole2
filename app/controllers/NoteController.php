<?php
class NoteController extends CrudController {
    protected $table = 'notes', $slug = 'note', $title = 'Notes';
    protected $writeRoles = ['admin', 'enseignant'];
    protected $list = ['eleve_id', 'matiere_id', 'type', 'trimestre', 'valeur'];
    public function __construct() {
        $this->fields = [
            'eleve_id' => ['label' => 'Élève', 'required' => 1, 'ref' => ['table' => 'eleves', 'label' => "CONCAT(nom,' ',prenom)"]],
            'matiere_id' => ['label' => 'Matière', 'required' => 1, 'ref' => ['table' => 'matieres', 'label' => 'nom']],
            'type' => ['label' => 'Type', 'type' => 'select', 'required' => 1, 'options' => ['Contrôle continu' => 'Contrôle continu', 'Examen' => 'Examen']],
            'trimestre' => ['label' => 'Trimestre', 'type' => 'select', 'required' => 1, 'options' => ['1' => '1er trimestre', '2' => '2e trimestre', '3' => '3e trimestre']],
            'valeur' => ['label' => 'Note (/20)', 'type' => 'number', 'required' => 1, 'min' => 0, 'max' => 20],
        ];
    }
}
