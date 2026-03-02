<?php
$pageTitle = 'Admin — Dettaglio Ordine';
session_start();
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$ordineId = (int)($_GET['id'] ?? 0);
if (!$ordineId) {
    setFlash('error', 'Ordine non valido.');
    redirect('/admin/ordini.php');
}

$db = getDB();

$stmt = $db->prepare("
    SELECT o.*,
           COALESCE(o.NomeOspite, CONCAT(u.Nome, ' ', u.Cognome)) AS NomeCliente,
           IF(o.NomeOspite IS NOT NULL, 'Ospite', 'Registrato') AS TipoCliente
    FROM tOrdine o
    LEFT JOIN tSelezione s ON s.idOrdine = o.idOrdine
    LEFT JOIN tUtente u ON s.idUtente = u.idUtente
    WHERE o.idOrdine = ?
    GROUP BY o.idOrdine
");
$stmt->execute([$ordineId]);
$ordine = $stmt->fetch();

if (!$ordine) {
    setFlash('error', 'Ordine non trovato.');
    redirect('/admin/ordini.php');
}

$stmtProd = $db->prepare("
    SELECT p.NomeProdotto, p.Prezzo, s.Quantita,
           (p.Prezzo * s.Quantita) AS Subtotale
    FROM tSelezione s
    JOIN tProdotto p ON s.idProdotto = p.idProdotto
    WHERE s.idOrdine = ?
    ORDER BY p.NomeProdotto
");
$stmtProd->execute([$ordineId]);
$prodotti = $stmtProd->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-receipt"></i> Ordine #<?= $ordineId ?></h1>
    <a href="<?= SITE_URL ?>/admin/ordini.php" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Lista ordini
    </a>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header fw-semibold">Dati cliente</div>
            <div class="card-body">
                <p class="mb-1"><strong>Cliente:</strong> <?= sanitize($ordine['NomeCliente'] ?? '') ?></p>
                <p class="mb-1"><strong>Tipo:</strong>
                    <?php if ($ordine['TipoCliente'] === 'Ospite'): ?>
                        <span class="badge bg-secondary">Ospite</span>
                    <?php else: ?>
                        <span class="badge bg-primary">Registrato</span>
                    <?php endif; ?>
                </p>
                <p class="mb-1"><strong>Consegna:</strong>
                    <?= sanitize($ordine['NomeViaConsegna']) ?> <?= sanitize($ordine['NumeroCivicoConsegna']) ?>, <?= sanitize($ordine['CAPConsegna']) ?>
                </p>
                <p class="mb-1"><strong>Telefono:</strong> <?= sanitize($ordine['TelefonoEmergenza']) ?></p>
                <?php if ($ordine['IndicazioniUtente']): ?>
                    <p class="mb-0"><strong>Indicazioni:</strong> <?= sanitize($ordine['IndicazioniUtente']) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header fw-semibold">Importi</div>
            <div class="card-body">
                <p class="mb-1"><strong>Importo previsto:</strong> <?= formatPrezzo($ordine['ImportoTotalePrevisto']) ?></p>
                <p class="mb-3"><strong>Importo confermato:</strong>
                    <?php if ($ordine['ImportoFinaleConfermato'] !== null): ?>
                        <span class="text-success fw-bold"><?= formatPrezzo($ordine['ImportoFinaleConfermato']) ?></span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark">In attesa di conferma</span>
                    <?php endif; ?>
                </p>

                <?php if ($ordine['ImportoFinaleConfermato'] === null): ?>
                <form action="<?= SITE_URL ?>/admin/process/conferma_prezzo.php" method="post">
                    <?= csrfField() ?>
                    <input type="hidden" name="ordine_id" value="<?= $ordineId ?>">
                    <div class="input-group">
                        <span class="input-group-text">€</span>
                        <input type="number" name="importo" class="form-control"
                               step="0.01" min="0"
                               placeholder="Importo finale"
                               value="<?= number_format($ordine['ImportoTotalePrevisto'], 2, '.', '') ?>"
                               required>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-lg"></i> Conferma
                        </button>
                    </div>
                </form>
                <?php else: ?>
                <form action="<?= SITE_URL ?>/admin/process/conferma_prezzo.php" method="post">
                    <?= csrfField() ?>
                    <input type="hidden" name="ordine_id" value="<?= $ordineId ?>">
                    <div class="input-group">
                        <span class="input-group-text">€</span>
                        <input type="number" name="importo" class="form-control"
                               step="0.01" min="0"
                               value="<?= number_format($ordine['ImportoFinaleConfermato'], 2, '.', '') ?>"
                               required>
                        <button type="submit" class="btn btn-outline-success">
                            <i class="bi bi-pencil"></i> Aggiorna
                        </button>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header fw-semibold">Prodotti ordinati</div>
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Prodotto</th>
                    <th class="text-end">Prezzo unit.</th>
                    <th class="text-end">Quantità</th>
                    <th class="text-end">Subtotale</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prodotti as $p): ?>
                    <tr>
                        <td><?= sanitize($p['NomeProdotto']) ?></td>
                        <td class="text-end"><?= formatPrezzo($p['Prezzo']) ?></td>
                        <td class="text-end"><?= $p['Quantita'] ?></td>
                        <td class="text-end fw-semibold"><?= formatPrezzo($p['Subtotale']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <td colspan="3" class="text-end fw-bold">Totale previsto</td>
                    <td class="text-end fw-bold"><?= formatPrezzo($ordine['ImportoTotalePrevisto']) ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
