<div class="page-head"><h1><?= $id ? 'Modifier' : 'Ajouter' ?> — <?= e($title) ?></h1></div>
<div class="card">
<?php if ($error): ?><div class="alert alert-err"><?= e($error) ?></div><?php endif; ?>
<form method="post" action="<?= url($slug.($id ? '/update/'.$id : '/store')) ?>" class="form-grid">
  <?= csrf_field() ?>
  <?php foreach ($fields as $k => $f): $val = $row[$k] ?? ''; $type = $f['type'] ?? 'text'; $req = !empty($f['required']) ? 'required' : ''; ?>
    <div class="field <?= $type === 'textarea' ? 'full' : '' ?>">
      <label><?= e($f['label']) ?><?= $req ? ' *' : '' ?></label>
      <?php if (isset($f['ref']) || $type === 'select'): ?>
        <select name="<?= $k ?>" <?= $req ?>>
          <option value="">— Choisir —</option>
          <?php foreach ($maps[$k] as $ov => $ol): ?>
            <option value="<?= e($ov) ?>" <?= (string)$val === (string)$ov ? 'selected' : '' ?>><?= e($ol) ?></option>
          <?php endforeach; ?>
        </select>
      <?php elseif ($type === 'textarea'): ?>
        <textarea name="<?= $k ?>" rows="4"><?= e($val) ?></textarea>
      <?php else: ?>
        <input type="<?= $type ?>" name="<?= $k ?>" value="<?= e($val) ?>" <?= $req ?>
          <?= $type === 'number' ? 'step="0.01"' : '' ?>
          <?= isset($f['min']) ? 'min="'.$f['min'].'"' : '' ?> <?= isset($f['max']) ? 'max="'.$f['max'].'"' : '' ?>>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
  <div class="full"><button class="btn" type="submit">Enregistrer</button> <a class="btn btn-light" href="<?= url($slug) ?>">Annuler</a></div>
</form>
</div>
