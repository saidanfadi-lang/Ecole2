<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><title>Bulletin</title>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css"></head>
<body class="paper"><div class="sheet">
<h1><?= e(ecole_nom()) ?></h1><h2>Bulletin — <?= $trim ?><?= $trim == 1 ? 'er' : 'e' ?> trimestre — <?= e(ANNEE) ?></h2>
<p><strong><?= e($el['nom'].' '.$el['prenom']) ?></strong> — Classe : <?= e($el['classe']) ?></p>
<table><thead><tr><th>Matière</th><th>Coef.</th><th>Moyenne /20</th></tr></thead><tbody>
<?php foreach ($lignes as $l): ?><tr><td><?= e($l['nom']) ?></td><td><?= e($l['coefficient']) ?></td><td><?= number_format($l['moyenne'], 2) ?></td></tr><?php endforeach; ?>
<?php if (!$lignes): ?><tr><td colspan="3">Aucune note pour ce trimestre.</td></tr><?php endif; ?>
</tbody></table>
<p class="total">Moyenne générale : <?= $generale === null ? '—' : number_format($generale, 2).' / 20' ?></p>
<button class="btn no-print" onclick="window.print()">🖨 Imprimer / Enregistrer en PDF</button>
</div></body></html>
