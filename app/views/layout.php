<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e(ecole_nom()) ?></title>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<?php
$menu = [
  ['dashboard','Tableau de bord','🏠'], ['eleve','Élèves','🎒'], ['classe','Classes','🏫'], ['matiere','Matières','📚'],
  ['enseignant','Enseignants','👩‍🏫'], ['salle','Salles','🚪'], ['emploi','Emploi du temps','🗓️'], ['note','Notes','📝'],
  ['bulletin','Bulletins','📄'], ['paiement','Paiements','💰'], ['devoir','Devoirs','✏️'], ['calendrier','Calendrier scolaire','📅'], ['evenement','Échéances','⏰'], ['message','Messagerie','✉️'],
  ['annonce','Annonces','📢'], ['ecole','Écoles','🏢'], ['user','Comptes','🔐'],
];
?>
<aside class="sidebar">
  <div class="brand"><span>🎓</span><div><?= e(ecole_nom()) ?><small><?= is_super() ? 'Super administration' : 'Année '.e(ANNEE) ?></small></div></div>
  <nav>
    <?php foreach ($menu as $m): if (!can($m[0]) || ($m[0] === 'ecole' && !is_super())) continue; ?>
      <a href="<?= url($m[0]) ?>"><span><?= $m[2] ?></span> <?= e($m[1]) ?></a>
    <?php endforeach; ?>
  </nav>
</aside>
<main class="main">
  <header class="topbar">
    <div>Bonjour, <strong><?= e($_SESSION['user']['nom']) ?></strong> <span class="badge"><?= e($_SESSION['user']['role']) ?></span></div>
    <a class="btn btn-light" href="<?= url('auth/logout') ?>">Déconnexion</a>
  </header>
  <section class="content">
    <?php if (!empty($_SESSION['flash'])): list($t, $msg) = $_SESSION['flash']; unset($_SESSION['flash']); ?>
      <div class="alert <?= $t === 'ok' ? 'alert-ok' : 'alert-err' ?>"><?= e($msg) ?></div>
    <?php endif; ?>
    <?= $content ?>
  </section>
</main>
</body>
</html>
