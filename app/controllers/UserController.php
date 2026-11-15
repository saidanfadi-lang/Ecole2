<?php
class UserController extends CrudController {
    protected $table = 'users', $slug = 'user', $title = 'Comptes utilisateurs';
    protected $hasYear = false;
    protected $writeRoles = ['admin', 'superadmin'];
    protected $list = ['nom', 'email', 'role', 'actif'];
    public function __construct() {
        $roles = ['admin' => 'Administrateur', 'enseignant' => 'Enseignant', 'parent' => 'Parent', 'eleve' => 'Élève', 'comptable' => 'Comptable'];
        if (is_super()) $roles = ['superadmin' => 'Super administrateur'] + $roles;
        $this->fields = [
            'nom' => ['label' => 'Nom complet', 'required' => 1],
            'email' => ['label' => 'E-mail', 'type' => 'email', 'required' => 1],
            'password' => ['label' => 'Mot de passe', 'required' => 1],
            'role' => ['label' => 'Rôle', 'type' => 'select', 'required' => 1, 'options' => $roles],
            'actif' => ['label' => 'Compte actif', 'type' => 'select', 'required' => 1, 'options' => ['1' => 'Oui', '0' => 'Non']],
        ];
        if (is_super()) { // le superadmin rattache chaque compte à une école (vide = superadmin)
            $this->fields = ['id_ecole' => ['label' => 'École', 'ref' => ['table' => 'schools', 'pk' => 'id_ecole', 'label' => 'nom', 'unscoped' => 1]]] + $this->fields;
            $this->list = ['nom', 'email', 'role', 'id_ecole', 'actif'];
        }
    }
    protected function validateExtra($d, $id) {
        if (is_super() && $d['role'] !== 'superadmin' && !$d['id_ecole']) return 'Choisissez l\'école de ce compte.';
        $n = DB::val('SELECT COUNT(*) FROM users WHERE email = ? AND id <> ?', [$d['email'], (int)$id]);
        return $n > 0 ? 'Cet e-mail est déjà utilisé.' : null;
    }
    protected function beforeDelete($id) {
        return $id == $_SESSION['user']['id'] ? 'Vous ne pouvez pas supprimer votre propre compte.' : null;
    }
}
