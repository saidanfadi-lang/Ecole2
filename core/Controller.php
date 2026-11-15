<?php
class Controller {
    protected function view($v, $d = []) {
        extract($d);
        ob_start();
        require ROOT."/app/views/$v.php";
        $content = ob_get_clean();
        require ROOT.'/app/views/layout.php';
    }
    protected function plain($v, $d = []) { extract($d); require ROOT."/app/views/$v.php"; }
    protected function redirect($p) { header('Location: '.url($p)); exit; }
    protected function flash($type, $msg) { $_SESSION['flash'] = [$type, $msg]; }
    protected function post() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); die('Méthode non autorisée'); }
        if (($_POST['_t'] ?? '') !== ($_SESSION['csrf'] ?? '')) { http_response_code(403); die('Jeton CSRF invalide'); }
    }
    protected function user() { return $_SESSION['user']; }
    protected function deny() { http_response_code(403); die('Accès refusé pour votre rôle.'); }
}
