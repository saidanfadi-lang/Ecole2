<?php
class EmploiController extends CrudController {
    protected $table = 'emplois', $slug = 'emploi', $title = 'Emploi du temps';
    protected $list = ['jour', 'heure_debut', 'heure_fin', 'classe_id', 'matiere_id', 'enseignant_id', 'salle_id'];
    public function __construct() {
        $j = array_combine(['Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'], ['Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi']);
        $this->fields = [
            'jour' => ['label' => 'Jour', 'type' => 'select', 'required' => 1, 'options' => $j],
            'heure_debut' => ['label' => 'Début', 'type' => 'time', 'required' => 1],
            'heure_fin' => ['label' => 'Fin', 'type' => 'time', 'required' => 1],
            'classe_id' => ['label' => 'Classe', 'required' => 1, 'ref' => ['table' => 'classes', 'label' => "CONCAT(niveau,' ',section)"]],
            'matiere_id' => ['label' => 'Matière', 'required' => 1, 'ref' => ['table' => 'matieres', 'label' => 'nom']],
            'enseignant_id' => ['label' => 'Enseignant', 'required' => 1, 'ref' => ['table' => 'enseignants', 'label' => "CONCAT(prenom,' ',nom)"]],
            'salle_id' => ['label' => 'Salle', 'required' => 1, 'ref' => ['table' => 'salles', 'label' => 'nom']],
        ];
    }
    protected function validateExtra($d, $id) {
        if ($d['heure_fin'] <= $d['heure_debut']) return "L'heure de fin doit être après l'heure de début.";
        foreach (['enseignant_id' => 'Cet enseignant', 'salle_id' => 'Cette salle'] as $col => $txt) {
            $n = DB::val("SELECT COUNT(*) FROM emplois WHERE id_ecole = ? AND $col = ? AND jour = ? AND annee_scolaire = ? AND deleted_at IS NULL
                          AND heure_debut < ? AND heure_fin > ? AND id <> ?",
                [school_id(), $d[$col], $d['jour'], ANNEE, $d['heure_fin'], $d['heure_debut'], (int)$id]);
            if ($n > 0) return "Conflit : $txt est déjà occupé(e) sur ce créneau.";
        }
        return null;
    }
}
