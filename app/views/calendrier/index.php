<?php
$qs = function ($extra = []) use ($vue, $classe, $ref) {
    $p = array_merge(['vue' => $vue, 'date' => $ref, 'classe' => $classe ?: ''], $extra);
    return url('calendrier').'&'.http_build_query($p);
};
$today = date('Y-m-d');
$label = function ($ev) {
    $h = $ev['h1'] ? $ev['h1'].($ev['h2'] ? '–'.$ev['h2'] : '').' ' : '';
    return $h.$ev['titre'];
};
$tip = function ($ev) {
    $t = [$ev['type']];
    if ($ev['matiere']) $t[] = $ev['matiere'];
    if ($ev['classe']) $t[] = 'Classe '.$ev['classe'];
    if ($ev['salle']) $t[] = $ev['salle'];
    if ($ev['desc']) $t[] = $ev['desc'];
    return implode(' • ', $t);
};
?>
<div class="page-head">
  <h1>📅 Calendrier scolaire</h1>
  <div>
    <?php if ($canEdit): ?><a class="btn" href="<?= url('evenement/create') ?>">+ Ajouter une échéance</a>
    <a class="btn btn-light" href="<?= url('evenement') ?>">Gérer la liste</a><?php endif; ?>
  </div>
</div>

<div class="card cal-bar">
  <div class="cal-nav">
    <a class="btn btn-light btn-sm" href="<?= $qs(['date' => $prev]) ?>">◀</a>
    <strong class="cal-title"><?= e($titre) ?></strong>
    <a class="btn btn-light btn-sm" href="<?= $qs(['date' => $next]) ?>">▶</a>
    <a class="btn btn-light btn-sm" href="<?= $qs(['date' => $today]) ?>">Aujourd'hui</a>
  </div>
  <div class="cal-tools">
    <a class="btn btn-sm <?= $vue === 'mois' ? '' : 'btn-light' ?>" href="<?= $qs(['vue' => 'mois']) ?>">Mois</a>
    <a class="btn btn-sm <?= $vue === 'semaine' ? '' : 'btn-light' ?>" href="<?= $qs(['vue' => 'semaine']) ?>">Semaine</a>
    <form method="get" action="<?= BASE_URL ?>/index.php" class="cal-filter">
      <input type="hidden" name="url" value="calendrier"><input type="hidden" name="vue" value="<?= e($vue) ?>"><input type="hidden" name="date" value="<?= e($ref) ?>">
      <select name="classe" onchange="this.form.submit()">
        <?php if (!$restreint): ?><option value="">Toutes les classes</option><?php endif; ?>
        <?php foreach ($classes as $cid => $cl): ?><option value="<?= $cid ?>" <?= $cid == $classe ? 'selected' : '' ?>><?= e($cl) ?></option><?php endforeach; ?>
      </select>
    </form>
  </div>
</div>

<div class="cal-legend">
  <?php foreach (array_merge(['Devoir'], Cal::TYPES) as $t): ?><span class="chip <?= Cal::css($t) ?>"><?= e($t) ?></span><?php endforeach; ?>
  <?php if ($vue === 'semaine' && $classe): ?><span class="chip ev-cours">Cours (emploi du temps)</span><?php endif; ?>
</div>

<?php if ($vue === 'mois'): ?>
<div class="card cal-wrap"><div class="cal-month">
  <?php foreach (Cal::JOURS as $j): ?><div class="cal-dow"><?= substr($j, 0, 3) ?>.</div><?php endforeach; ?>
  <?php for ($d = clone $start; $d <= $end; $d->modify('+1 day')):
        $ds = $d->format('Y-m-d'); $autre = (int)$d->format('n') !== $moisRef; ?>
    <div class="cal-cell <?= $autre ? 'other' : '' ?> <?= $ds === $today ? 'today' : '' ?>">
      <div class="cal-num"><?= $d->format('j') ?></div>
      <?php foreach ($events as $ev): if ($ev['debut'] <= $ds && $ev['fin'] >= $ds): ?>
        <div class="ev <?= Cal::css($ev['type']) ?>" title="<?= e($tip($ev)) ?>">
          <?= e($label($ev)) ?><?php if ($ev['classe']): ?> <small>(<?= e($ev['classe']) ?>)</small><?php endif; ?>
          <?php if ($canEdit && $ev['source'] === 'evenement'): ?><a class="ev-edit" href="<?= url('evenement/edit/'.$ev['id']) ?>">✎</a><?php endif; ?>
        </div>
      <?php endif; endforeach; ?>
    </div>
  <?php endfor; ?>
</div></div>

<?php else:
  // ---- Vue semaine : grille horaire façon emploi du temps ----
  $heures = range(8, 17);
  $jours = [];
  for ($d = clone $start, $i = 0; $i < 7; $i++, $d->modify('+1 day')) $jours[$i] = clone $d;
  $cellEv = function ($ds, $h) use ($events) {
      $r = [];
      foreach ($events as $ev) if ($ev['h1'] && $ev['debut'] <= $ds && $ev['fin'] >= $ds && (int)substr($ev['h1'], 0, 2) === $h) $r[] = $ev;
      return $r;
  };
?>
<div class="card cal-wrap"><div class="table-wrap"><table class="cal-week">
  <thead><tr><th class="hcol"></th>
    <?php foreach ($jours as $i => $j): ?><th class="<?= $j->format('Y-m-d') === $today ? 'today' : '' ?>"><?= Cal::JOURS[$i] ?><br><small><?= $j->format('d/m') ?></small></th><?php endforeach; ?>
  </tr></thead>
  <tbody>
    <tr class="allday"><th class="hcol">Journée</th>
      <?php foreach ($jours as $j): $ds = $j->format('Y-m-d'); ?><td>
        <?php foreach ($events as $ev): if (!$ev['h1'] && $ev['debut'] <= $ds && $ev['fin'] >= $ds): ?>
          <div class="ev <?= Cal::css($ev['type']) ?>" title="<?= e($tip($ev)) ?>"><?= e($ev['titre']) ?><?php if ($ev['classe']): ?> <small>(<?= e($ev['classe']) ?>)</small><?php endif; ?>
          <?php if ($canEdit && $ev['source'] === 'evenement'): ?><a class="ev-edit" href="<?= url('evenement/edit/'.$ev['id']) ?>">✎</a><?php endif; ?></div>
        <?php endif; endforeach; ?></td><?php endforeach; ?>
    </tr>
    <?php foreach ($heures as $h): ?>
    <tr><th class="hcol"><?= sprintf('%02d:00', $h) ?></th>
      <?php foreach ($jours as $i => $j): $ds = $j->format('Y-m-d'); ?><td>
        <?php foreach ($lessons as $l): if ($l['jour'] === Cal::JOURS[$i] && (int)substr($l['heure_debut'], 0, 2) === $h): ?>
          <div class="ev ev-cours" title="<?= e($l['salle']) ?>"><?= substr($l['heure_debut'], 0, 5) ?>–<?= substr($l['heure_fin'], 0, 5) ?> <?= e($l['matiere']) ?></div>
        <?php endif; endforeach; ?>
        <?php foreach ($cellEv($ds, $h) as $ev): ?>
          <div class="ev <?= Cal::css($ev['type']) ?>" title="<?= e($tip($ev)) ?>"><?= e($label($ev)) ?><?php if ($ev['classe']): ?> <small>(<?= e($ev['classe']) ?>)</small><?php endif; ?>
          <?php if ($canEdit && $ev['source'] === 'evenement'): ?><a class="ev-edit" href="<?= url('evenement/edit/'.$ev['id']) ?>">✎</a><?php endif; ?></div>
        <?php endforeach; ?></td><?php endforeach; ?>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table></div>
<?php if (!$classe && !$restreint): ?><p class="muted small">Choisissez une classe pour voir aussi ses cours de l'emploi du temps.</p><?php endif; ?>
</div>
<?php endif; ?>

<div class="card">
  <h3 style="margin-top:0">Échéances de la période</h3>
  <?php if (!$events): ?><p class="muted">Aucune échéance sur cette période.</p><?php endif; ?>
  <div class="table-wrap"><table><tbody>
  <?php foreach ($events as $ev): ?>
    <tr>
      <td style="white-space:nowrap"><?= date('d/m/Y', strtotime($ev['debut'])) ?><?= $ev['fin'] !== $ev['debut'] ? ' → '.date('d/m/Y', strtotime($ev['fin'])) : '' ?><?= $ev['h1'] ? '<br><small class="muted">'.e($ev['h1']).($ev['h2'] ? ' – '.e($ev['h2']) : '').'</small>' : '' ?></td>
      <td><span class="chip <?= Cal::css($ev['type']) ?>"><?= e($ev['type']) ?></span></td>
      <td><strong><?= e($ev['titre']) ?></strong><?= $ev['matiere'] ? ' — '.e($ev['matiere']) : '' ?><?= $ev['desc'] ? '<br><small class="muted">'.e($ev['desc']).'</small>' : '' ?></td>
      <td><?= $ev['classe'] ? e($ev['classe']) : '<span class="muted">Toute l\'école</span>' ?></td>
      <td><?= e($ev['salle'] ?? '') ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody></table></div>
</div>
