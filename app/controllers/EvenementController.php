<?php
/* Gestion des échéances du calendrier : contrôles, examens, oraux de synthèse, réunions, vacances... */
class EvenementController extends CrudController {
    protected $table = 'evenements', $slug = 'evenement', $title = 'Échéances du calendrier';
    protected $writeRoles = ['admin', 'enseignant'];
    protected $list = ['titre', 'type', 'classe_id', 'matiere_id', 'date_debut', 'heure_debut'];
    public function __construct() {
        $types = array_combine(Cal::TYPES, Cal::TYPES);
        $this->fields = [
            'titre' => ['label' => 'Titre', 'required' => 1],
            'type' => ['label' => 'Type', 'type' => 'select', 'required' => 1, 'options' => $types],
            'classe_id' => ['label' => 'Classe (vide = toute l\'école)', 'ref' => ['table' => 'classes', 'label' => "CONCAT(niveau,' ',section)"]],
            'matiere_id' => ['label' => 'Matière', 'ref' => ['table' => 'matieres', 'label' => 'nom']],
            'date_debut' => ['label' => 'Date (ou début)', 'type' => 'date', 'required' => 1],
            'date_fin' => ['label' => 'Date de fin (si plusieurs jours)', 'type' => 'date'],
            'heure_debut' => ['label' => 'Heure de début', 'type' => 'time'],
            'heure_fin' => ['label' => 'Heure de fin', 'type' => 'time'],
            'salle_id' => ['label' => 'Salle', 'ref' => ['table' => 'salles', 'label' => 'nom']],
            'description' => ['label' => 'Description', 'type' => 'textarea'],
        ];
    }
    protected function validateExtra($d, $id) {
        if ($d['date_fin'] && $d['date_fin'] < $d['date_debut']) return 'La date de fin doit être après la date de début.';
        if ($d['heure_fin'] && !$d['heure_debut']) return 'Indiquez aussi l\'heure de début.';
        if ($d['heure_debut'] && $d['heure_fin'] && $d['heure_fin'] <= $d['heure_debut']) return 'L\'heure de fin doit être après l\'heure de début.';
        // Conflit de salle le même jour sur un créneau qui se chevauche
        if ($d['salle_id'] && $d['heure_debut'] && $d['heure_fin']) {
            $n = DB::val("SELECT COUNT(*) FROM evenements WHERE id_ecole = ? AND salle_id = ? AND deleted_at IS NULL AND id <> ?
                          AND date_debut <= COALESCE(?, ?) AND COALESCE(date_fin, date_debut) >= ?
                          AND heure_debut < ? AND heure_fin > ?",
                [school_id(), $d['salle_id'], (int)$id, $d['date_fin'], $d['date_debut'], $d['date_debut'], $d['heure_fin'], $d['heure_debut']]);
            if ($n > 0) return 'Conflit : cette salle est déjà réservée pour une autre échéance sur ce créneau.';
        }
        return null;
    }
}
