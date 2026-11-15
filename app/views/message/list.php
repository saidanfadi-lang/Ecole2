<div class="page-head"><h1><?= e($box) ?></h1>
  <div><a class="btn btn-light" href="<?= url('message') ?>">Réception</a> <a class="btn btn-light" href="<?= url('message/sent') ?>">Envoyés</a> <a class="btn" href="<?= url('message/compose') ?>">+ Nouveau message</a></div></div>
<div class="card"><div class="table-wrap"><table>
<thead><tr><th><?= $box === 'Messages envoyés' ? 'Destinataire' : 'Expéditeur' ?></th><th>Objet</th><th>Date</th><th></th></tr></thead><tbody>
<?php if (!$rows): ?><tr><td colspan="4" class="muted">Aucun message.</td></tr><?php endif; ?>
<?php foreach ($rows as $m): ?>
<tr class="<?= ($box !== 'Messages envoyés' && !$m['lu']) ? 'unread' : '' ?>">
  <td><?= e($m['autre']) ?></td><td><?= e($m['objet']) ?></td><td><?= e($m['created_at']) ?></td>
  <td class="actions"><a class="btn btn-sm" href="<?= url('message/show/'.$m['id']) ?>">Lire</a></td>
</tr>
<?php endforeach; ?></tbody></table></div></div>
