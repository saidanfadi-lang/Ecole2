<?php
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function url($p = '') { return BASE_URL.'/index.php?url='.$p; }
function csrf_field() { return '<input type="hidden" name="_t" value="'.e($_SESSION['csrf']).'">'; }
function can($ctrl) {
    global $PERMS;
    if (empty($_SESSION['user'])) return false;
    $p = $PERMS[$_SESSION['user']['role']] ?? [];
    return in_array('*', $p) || in_array($ctrl, $p);
}
// Multi-écoles : l'école vient TOUJOURS de la session (jamais de l'URL ni d'un champ de formulaire)
function school_id() { return (int)($_SESSION['user']['id_ecole'] ?? 0); }
function is_super() { return ($_SESSION['user']['role'] ?? '') === 'superadmin'; }
function ecole_nom() { return $_SESSION['user']['ecole'] ?? APP_NAME; }
function journal($entite, $id, $action, $old = null, $new = null) {
    DB::q('INSERT INTO journal (id_ecole,user_id,entite,entite_id,action,anciennes,nouvelles) VALUES (?,?,?,?,?,?,?)', [
        school_id() ?: null,
        $_SESSION['user']['id'] ?? null, $entite, $id, $action,
        $old === null ? null : json_encode($old, JSON_UNESCAPED_UNICODE),
        $new === null ? null : json_encode($new, JSON_UNESCAPED_UNICODE),
    ]);
}
