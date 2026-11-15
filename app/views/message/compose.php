<div class="page-head"><h1>Nouveau message</h1></div>
<div class="card">
<form method="post" action="<?= url('message/send') ?>" class="form-grid">
  <?= csrf_field() ?>
  <div class="field full"><label>Destinataire *</label>
    <select name="destinataire_id" required><option value="">— Choisir —</option>
      <?php foreach ($dest as $d): ?><option value="<?= $d['id'] ?>"><?= e($d['nom']) ?> (<?= e($d['role']) ?>)</option><?php endforeach; ?>
    </select></div>
  <div class="field full"><label>Objet *</label><input name="objet" required></div>
  <div class="field full"><label>Message *</label><textarea name="corps" rows="6" required></textarea></div>
  <div class="full"><button class="btn">Envoyer</button> <a class="btn btn-light" href="<?= url('message') ?>">Annuler</a>
    <p class="muted small">Un message envoyé ne peut plus être modifié ni supprimé par l'expéditeur.</p></div>
</form></div>
