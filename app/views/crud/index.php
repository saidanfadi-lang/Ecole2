<div class="page-head">
  <h1><?= e($title) ?></h1>
  <div>
    <?php if ($slug === 'eleve'): ?><a class="btn btn-light" href="<?= url('eleve/export') ?>">⬇ Export CSV</a><?php endif; ?>
    <?php if ($canWrite): ?><a class="btn" href="<?= url($slug.'/create') ?>">+ Ajouter</a><?php endif; ?>
  </div>
</div>
<div class="card"><div class="table-wrap"><table>
  <thead><tr>
    <?php foreach ($list as $c): ?><th><?= e($fields[$c]['label']) ?></th><?php endforeach; ?>
    <th>Actions</th>
  </tr></thead>
  <tbody>
  <?php if (!$rows): ?><tr><td colspan="<?= count($list) + 1 ?>" class="muted">Aucun enregistrement.</td></tr><?php endif; ?>
  <?php foreach ($rows as $r): ?>
    <tr>
      <?php foreach ($list as $c): $v = $r[$c]; if (isset($maps[$c])) $v = $maps[$c][$v] ?? $v; ?>
        <td><?= e($v) ?></td>
      <?php endforeach; ?>
      <td class="actions">
        <?php foreach ($rowLinks as $l): ?><a class="btn btn-sm btn-light" target="_blank" href="<?= url(str_replace('{id}', $r['id'], $l[1])) ?>"><?= e($l[0]) ?></a><?php endforeach; ?>
        <?php if ($canWrite): ?>
          <a class="btn btn-sm" href="<?= url($slug.'/edit/'.$r['id']) ?>">Modifier</a>
          <form method="post" action="<?= url($slug.'/delete/'.$r['id']) ?>" onsubmit="return confirm('Supprimer cet enregistrement ?')">
            <?= csrf_field() ?><button class="btn btn-sm btn-danger">Supprimer</button>
          </form>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table></div></div>
