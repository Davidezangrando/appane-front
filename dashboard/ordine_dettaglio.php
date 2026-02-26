<?php
$pageTitle = 'Dettaglio Ordine';
require_once __DIR__ . '/../includes/header.php';
requireLogin();

$ordineId = (int)($_GET['id'] ?? 0);
if (!$ordineId) redirect('/dashboard/ordini.php');

$db = getDB();

// Verifica che l'ordine appartenga all'utente
$stmt = $db->prepare("
    SELECT o.*
    FROM tOrdine o
    WHERE o.idOrdine = ?
      AND EXISTS (
          SELECT 1 FROM tSelezione ts
          WHERE ts.idOrdine = ? AND ts.idUtente = ?
      )
");
$stmt->execute([$ordineId, $ordineId, $_SESSION['user_id']]);
$ordine = $stmt->fetch();

if (!$ordine) {
    setFlash('error', 'Ordine non trovato.');
    redirect('/dashboard/ordini.php');
}

// Prodotti dell'ordine
$stmt = $db->prepare("
    SELECT ts.Quantita, p.NomeProdotto, p.Prezzo
    FROM tSelezione ts
    JOIN tProdotto p ON ts.idProdotto = p.idProdotto
    WHERE ts.idOrdine = ?
");
$stmt->execute([$ordineId]);
$dettagli = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-receipt"></i> Ordine #<?= $ordine['idOrdine'] ?></h1>
    <a href="<?= SITE_URL ?>/dashboard/ordini.php" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Tutti gli ordini
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <!-- Prodotti ordinati -->
        <div class="card dash-card mb-3">
            <div class="card-body">
                <h5 class="card-title mb-3">Prodotti ordinati</h5>
                <?php foreach ($dettagli as $d): ?>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <div>
                            <strong><?= sanitize($d['NomeProdotto']) ?></strong>
                            <span class="text-muted">x<?= $d['Quantita'] ?></span>
                        </div>
                        <span><?= formatPrezzo($d['Prezzo'] * $d['Quantita']) ?></span>
                    </div>
                <?php endforeach; ?>
                <div class="d-flex justify-content-between pt-3">
                    <strong class="fs-5">Totale previsto</strong>
                    <strong class="fs-5" style="color:var(--bread-brown)"><?= formatPrezzo($ordine['ImportoTotalePrevisto']) ?></strong>
                </div>
                <?php if ($ordine['ImportoFinaleConfermato'] !== null): ?>
                    <div class="d-flex justify-content-between pt-2">
                        <strong class="fs-5">Totale confermato</strong>
                        <strong class="fs-5 text-success"><?= formatPrezzo($ordine['ImportoFinaleConfermato']) ?></strong>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Info consegna -->
        <div class="card dash-card">
            <div class="card-body">
                <h5 class="card-title mb-3">Consegna</h5>
                <p class="mb-1">
                    <i class="bi bi-geo-alt"></i>
                    <?= sanitize($ordine['NomeViaConsegna']) ?> <?= sanitize($ordine['NumeroCivicoConsegna']) ?>, <?= sanitize($ordine['CAPConsegna']) ?>
                </p>
                <p class="mb-1">
                    <i class="bi bi-telephone"></i> <?= sanitize($ordine['TelefonoEmergenza']) ?>
                </p>
                <?php if ($ordine['IndicazioniUtente']): ?>
                    <p class="mb-0 text-muted">
                        <i class="bi bi-chat-text"></i> <?= sanitize($ordine['IndicazioniUtente']) ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Stato conferma -->
    <div class="col-lg-4">
        <div class="card dash-card">
            <div class="card-body">
                <h5 class="card-title mb-3">Stato ordine</h5>
                <?php if ($ordine['ImportoFinaleConfermato'] !== null): ?>
                    <div class="alert alert-success mb-0">
                        <i class="bi bi-check-circle"></i> Ordine confermato dal panificio
                        <div class="mt-1 fw-bold"><?= formatPrezzo($ordine['ImportoFinaleConfermato']) ?></div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning mb-0">
                        <i class="bi bi-hourglass-split"></i> In attesa di conferma
                        <div class="mt-1 text-muted small">Il panificio verificherà e confermerà l'ordine a breve.</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
