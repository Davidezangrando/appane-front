<?php
$pageTitle = 'I miei Ordini';
require_once __DIR__ . '/../includes/header.php';
requireLogin();

$db = getDB();
$stmt = $db->prepare("
    SELECT DISTINCT o.idOrdine, o.ImportoTotalePrevisto, o.ImportoFinaleConfermato,
           o.NomeViaConsegna, o.NumeroCivicoConsegna, o.CAPConsegna
    FROM tOrdine o
    JOIN tSelezione ts ON o.idOrdine = ts.idOrdine
    WHERE ts.idUtente = ?
    ORDER BY o.idOrdine DESC
");
$stmt->execute([$_SESSION['user_id']]);
$ordini = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-bag"></i> I miei Ordini</h1>
    <a href="<?= SITE_URL ?>/dashboard/" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Dashboard
    </a>
</div>

<?php if (empty($ordini)): ?>
    <div class="text-center py-5">
        <i class="bi bi-bag-x" style="font-size:3rem;color:var(--bread-brown)"></i>
        <h4 class="mt-3">Nessun ordine ancora</h4>
        <p class="text-muted">Quando effettuerai il tuo primo ordine, lo vedrai qui.</p>
        <a href="<?= SITE_URL ?>/" class="btn btn-bread">Vai al menu</a>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Indirizzo consegna</th>
                    <th>Importo previsto</th>
                    <th>Importo confermato</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ordini as $o): ?>
                    <tr>
                        <td><strong><?= $o['idOrdine'] ?></strong></td>
                        <td><?= sanitize($o['NomeViaConsegna']) ?> <?= sanitize($o['NumeroCivicoConsegna']) ?>, <?= sanitize($o['CAPConsegna']) ?></td>
                        <td class="fw-bold"><?= formatPrezzo($o['ImportoTotalePrevisto']) ?></td>
                        <td>
                            <?php if ($o['ImportoFinaleConfermato'] !== null): ?>
                                <span class="fw-bold text-success"><?= formatPrezzo($o['ImportoFinaleConfermato']) ?></span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">In attesa</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= SITE_URL ?>/dashboard/ordine_dettaglio.php?id=<?= $o['idOrdine'] ?>" class="btn btn-sm btn-outline-bread">
                                Dettagli
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
