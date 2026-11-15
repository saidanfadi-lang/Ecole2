<!DOCTYPE html>
<html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Connexion - <?= e(APP_NAME) ?></title>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css"></head>
<body class="login-body">
<div class="bubbles"><i></i><i></i><i></i><i></i><i></i></div>
<form class="login-card" method="post" action="<?= url('auth/login') ?>">
  <div class="logo">🎓</div>
  <h1><?= e(APP_NAME) ?></h1>
  <p class="muted">Espace de gestion scolaire</p>
  <?php if ($error): ?><div class="alert alert-err"><?= e($error) ?></div><?php endif; ?>
  <?= csrf_field() ?>
  <label>E-mail</label><input type="email" name="email" required autofocus>
  <label>Mot de passe</label><input type="password" name="password" required>
  <button class="btn btn-block" type="submit">Se connecter</button>
  <p class="muted small">Démo : admin@ecole.tn / admin123 (école 1) — super@ecole.tn / super123</p>
</form>
</body></html>
