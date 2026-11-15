<div class="page-head"><h1>Publier une annonce</h1></div>
<div class="card"><form method="post" action="<?= url('annonce/store') ?>" class="form-grid">
  <?= csrf_field() ?>
  <div class="field full"><label>Titre *</label><input name="titre" required></div>
  <div class="field"><label>Visible par</label><select name="cible">
    <option value="tous">Tous</option><option value="parent">Parents</option><option value="enseignant">Enseignants</option><option value="eleve">Élèves</option></select></div>
  <div class="field full"><label>Contenu *</label><textarea name="corps" rows="6" required></textarea></div>
  <div class="full"><button class="btn">Publier</button> <a class="btn btn-light" href="<?= url('annonce') ?>">Annuler</a>
  <p class="muted small">Une annonce publiée ne peut être ni modifiée ni supprimée.</p></div>
</form></div>
