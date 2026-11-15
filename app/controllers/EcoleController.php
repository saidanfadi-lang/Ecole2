<?php
/* Gestion des écoles (réservé au superadmin) — table schools, clé primaire id_ecole */
class EcoleController extends CrudController {
    protected $table = 'schools', $pk = 'id_ecole', $slug = 'ecole', $title = 'Écoles';
    protected $hasYear = false, $scoped = false;
    protected $writeRoles = ['superadmin'];
    protected $list = ['nom', 'ville', 'telephone', 'email', 'actif'];
    public function __construct() {
        if (!is_super()) $this->deny();   // l'admin d'une école ne voit jamais les autres écoles
        $this->fields = [
            'nom' => ['label' => 'Nom de l\'école', 'required' => 1],
            'ville' => ['label' => 'Ville'],
            'telephone' => ['label' => 'Téléphone'],
            'email' => ['label' => 'E-mail', 'type' => 'email'],
            'actif' => ['label' => 'École active', 'type' => 'select', 'required' => 1, 'options' => ['1' => 'Oui', '0' => 'Non (connexion bloquée)']],
        ];
    }
    protected function beforeDelete($id) {
        $n = DB::val('SELECT COUNT(*) FROM users WHERE id_ecole = ? AND deleted_at IS NULL', [$id]);
        return $n > 0 ? "Impossible : $n compte(s) utilisateur sont rattachés à cette école. Désactivez l'école à la place." : null;
    }
}
