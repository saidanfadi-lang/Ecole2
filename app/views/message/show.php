<div class="page-head"><h1><?= e($m['objet']) ?></h1><a class="btn btn-light" href="<?= url('message') ?>">← Retour</a></div>
<div class="card">
  <p class="muted">De <strong><?= e($m['exp']) ?></strong> à <strong><?= e($m['dest']) ?></strong> — <?= e($m['created_at']) ?></p>
  <div class="msg-body"><?= nl2br(e($m['corps'])) ?></div>
  <?php if ($m['destinataire_id'] == $moi): ?>
  <form method="post" action="<?= url('message/hide/'.$m['id']) ?>" onsubmit="return confirm('Masquer ce message de votre boîte ?')">
    <?= csrf_field() ?><button class="btn btn-danger btn-sm">Masquer de ma boîte</button></form>
  <?php endif; ?>
</div>
