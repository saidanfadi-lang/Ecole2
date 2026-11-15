<div class="page-head"><h1>Annonces</h1><?php if ($isAdmin): ?><a class="btn" href="<?= url('annonce/create') ?>">+ Publier</a><?php endif; ?></div>
<?php if (!$rows): ?><div class="card muted">Aucune annonce.</div><?php endif; ?>
<?php foreach ($rows as $a): ?>
<div class="card annonce"><h3><?= e($a['titre']) ?> <span class="badge"><?= e($a['cible']) ?></span></h3>
<p><?= nl2br(e($a['corps'])) ?></p><p class="muted small">Par <?= e($a['auteur']) ?> — <?= e($a['created_at']) ?></p></div>
<?php endforeach; ?>
