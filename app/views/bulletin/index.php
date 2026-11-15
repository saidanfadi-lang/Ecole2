<div class="page-head"><h1>Bulletins</h1></div>
<div class="card"><form class="form-grid" onsubmit="location.href='<?= url('bulletin/show/') ?>'+this.eleve.value+'/'+this.trim.value;return false;">
  <div class="field"><label>Élève</label><select name="eleve" required>
    <?php foreach ($eleves as $e_): ?><option value="<?= $e_['id'] ?>"><?= e($e_['nom']) ?></option><?php endforeach; ?></select></div>
  <div class="field"><label>Trimestre</label><select name="trim"><option value="1">1er</option><option value="2">2e</option><option value="3">3e</option></select></div>
  <div class="full"><button class="btn">Afficher le bulletin</button></div>
</form></div>
