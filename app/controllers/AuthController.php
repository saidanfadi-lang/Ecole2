<?php
class AuthController extends Controller {
    public function login() {
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->post();
            // Version simplifiée : comparaison directe du mot de passe (usage local uniquement)
            $u = DB::one('SELECT u.*, s.nom AS ecole_nom FROM users u LEFT JOIN schools s ON s.id_ecole = u.id_ecole
                          WHERE u.email = ? AND u.password = ? AND u.actif = 1 AND u.deleted_at IS NULL
                          AND (u.id_ecole IS NULL OR (s.actif = 1 AND s.deleted_at IS NULL))',
                [trim($_POST['email'] ?? ''), $_POST['password'] ?? '']);
            if ($u) {
                session_regenerate_id(true);
                $_SESSION['user'] = ['id' => $u['id'], 'nom' => $u['nom'], 'role' => $u['role'],
                                     'id_ecole' => $u['id_ecole'], 'ecole' => $u['ecole_nom'] ?: APP_NAME];
                $_SESSION['csrf'] = bin2hex(random_bytes(16));
                journal('users', $u['id'], 'connexion');
                $this->redirect('dashboard');
            }
            $error = 'E-mail ou mot de passe incorrect (ou école désactivée).';
        }
        $this->plain('auth/login', ['error' => $error]);
    }
    public function logout() { session_destroy(); header('Location: '.url('auth/login')); exit; }
}
