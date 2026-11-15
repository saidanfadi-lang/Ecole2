<?php
session_start();
define('ROOT', __DIR__);
require ROOT.'/config/config.php';
require ROOT.'/core/helpers.php';
require ROOT.'/core/Database.php';
require ROOT.'/core/Controller.php';
require ROOT.'/core/CrudController.php';
require ROOT.'/core/Cal.php';

if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));

$parts  = explode('/', trim($_GET['url'] ?? 'dashboard', '/'));
$name   = strtolower($parts[0]);
$action = $parts[1] ?? 'index';
$args   = array_slice($parts, 2);

if (!preg_match('/^[a-z]+$/', $name) || !file_exists(ROOT."/app/controllers/".ucfirst($name)."Controller.php")) {
    http_response_code(404); die('Page introuvable.');
}
if ($name !== 'auth' && empty($_SESSION['user'])) { header('Location: '.url('auth/login')); exit; }
if ($name !== 'auth' && !can($name)) { http_response_code(403); die('Accès refusé pour votre rôle.'); }

require ROOT."/app/controllers/".ucfirst($name)."Controller.php";
$class = ucfirst($name).'Controller';
$ctrl  = new $class();
if (!preg_match('/^[a-z]+$/i', $action) || !is_callable([$ctrl, $action]) || (new ReflectionMethod($ctrl, $action))->class === 'Controller') {
    http_response_code(404); die('Action introuvable.');
}
call_user_func_array([$ctrl, $action], $args);
