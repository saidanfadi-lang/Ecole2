<h1>Super administration — toutes les écoles</h1>
<div class="card"><div class="table-wrap"><table>
<thead><tr><th>École</th><th>Ville</th><th>Comptes</th><th>Élèves (<?= e(ANNEE) ?>)</th><th>Statut</th></tr></thead><tbody>
<?php if (!$ecoles): ?><tr><td colspan="5" class="muted">Aucune école.</td></tr><?php endif; ?>
<?php foreach ($ecoles as $s): ?><tr><td><?= e($s['nom']) ?></td><td><?= e($s['ville']) ?></td><td><?= (int)$s['comptes'] ?></td><td><?= (int)$s['eleves'] ?></td><td><?= $s['actif'] ? 'Active' : 'Désactivée' ?></td></tr><?php endforeach; ?>
</tbody></table></div></div>
<p><a class="btn" href="<?= url('ecole/create') ?>">+ Nouvelle école</a> <a class="btn btn-light" href="<?= url('user/create') ?>">+ Compte administrateur</a></p>
