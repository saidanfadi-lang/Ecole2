<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><title>Reçu</title>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css"></head>
<body class="paper"><div class="sheet">
<h1><?= e(ecole_nom()) ?></h1><h2>Reçu de paiement n° <?= str_pad($p['id'], 5, '0', STR_PAD_LEFT) ?></h2>
<table><tbody>
<tr><th>Élève</th><td><?= e($el['nom'].' '.$el['prenom']) ?> (<?= e($el['classe']) ?>)</td></tr>
<tr><th>Type</th><td><?= e($p['type']) ?></td></tr>
<tr><th>Montant</th><td><?= number_format($p['montant'], 2) ?> DT</td></tr>
<tr><th>Mode</th><td><?= e($p['mode']) ?></td></tr>
<tr><th>Date</th><td><?= e($p['date_paiement']) ?></td></tr>
<tr><th>Total payé cette année</th><td><?= number_format($paye, 2) ?> DT</td></tr>
<tr><th>Solde restant</th><td><?= number_format($reste, 2) ?> DT</td></tr>
</tbody></table>
<button class="btn no-print" onclick="window.print()">🖨 Imprimer / Enregistrer en PDF</button>
</div></body></html>
