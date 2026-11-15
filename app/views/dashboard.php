<h1>Tableau de bord</h1>
<div class="stats">
  <?php $i = 0; foreach ($stats as $k => $v): ?>
    <div class="stat" style="animation-delay:<?= $i++ * 0.1 ?>s"><div class="num"><?= (int)$v ?></div><div><?= e($k) ?></div></div>
  <?php endforeach; ?>
  <?php if (can('paiement')): ?>
    <div class="stat" style="animation-delay:.4s"><div class="num"><?= number_format($total, 0, ',', ' ') ?> DT</div><div>Paiements encaissés</div></div>
  <?php endif; ?>
  <div class="stat" style="animation-delay:.5s"><div class="num"><?= $nonLus ?></div><div>Messages non lus</div></div>
</div>
<?php if (!empty($echeances)): ?>
<div class="card" style="margin-top:18px">
  <div class="page-head"><h3 style="margin:0">⏰ Prochaines échéances</h3><a class="btn btn-light btn-sm" href="<?= url('calendrier') ?>">Voir le calendrier</a></div>
  <?php foreach ($echeances as $ev): ?>
    <div class="up-row"><span class="chip <?= Cal::css($ev['type']) ?>"><?= e($ev['type']) ?></span>
      <strong><?= e($ev['titre']) ?></strong><?= $ev['classe'] ? ' <span class="muted">('.e($ev['classe']).')</span>' : '' ?>
      <span class="muted"> — <?= date('d/m/Y', strtotime($ev['debut'])) ?><?= $ev['h1'] ? ' à '.e($ev['h1']) : '' ?></span></div>
  <?php endforeach; ?>
</div>
<?php endif; ?>
