<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/../includes/header.php';
requireLogin();

$user = getUser();
$db = getDB();

// Ultimo ordine
$stmt = $db->prepare("
    SELECT DISTINCT o.idOrdine, o.ImportoTotalePrevisto, o.ImportoFinaleConfermato, o.Stato
    FROM tOrdine o
    JOIN tSelezione ts ON o.idOrdine = ts.idOrdine
    WHERE ts.idUtente = ?
    ORDER BY o.idOrdine DESC
    LIMIT 1
");
$stmt->execute([$_SESSION['user_id']]);
$ultimoOrdine = $stmt->fetch();

// Conteggio ordini
$stmt = $db->prepare("SELECT COUNT(DISTINCT idOrdine) FROM tSelezione WHERE idUtente = ?");
$stmt->execute([$_SESSION['user_id']]);
$totaleOrdini = (int)$stmt->fetchColumn();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Ciao, <?= sanitize($user['Nome']) ?>! 👋</h1>
    <a href="<?= SITE_URL ?>/auth/logout.php" class="btn btn-outline-danger btn-sm">
        <i class="bi bi-box-arrow-right"></i> Esci
    </a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card dash-card text-center">
            <div class="card-body">
                <i class="bi bi-bag-check" style="font-size:2rem;color:var(--bread-brown)"></i>
                <h3 class="mt-2 mb-0"><?= $totaleOrdini ?></h3>
                <p class="text-muted mb-0">Ordini effettuati</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card dash-card text-center">
            <div class="card-body">
                <i class="bi bi-basket2" style="font-size:2rem;color:#198754"></i>
                <h3 class="mt-2 mb-0"><?= getCarrelloCount() ?></h3>
                <p class="text-muted mb-0">Prodotti nel carrello</p>
            </div>
        </div>
    </div>
</div>

<?php if ($ultimoOrdine): ?>
    <div class="card dash-card mb-4">
        <div class="card-body">
            <h5 class="card-title">Ultimo ordine</h5>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong>Ordine #<?= $ultimoOrdine['idOrdine'] ?></strong>
                    <br>
                    <span class="fw-bold" style="color:var(--bread-brown)"><?= formatPrezzo($ultimoOrdine['ImportoTotalePrevisto']) ?></span>
                    <?php if ($ultimoOrdine['Stato'] === 'confermato'): ?>
                        <?php if ($ultimoOrdine['ImportoFinaleConfermato'] !== null): ?>
                            <span class="ms-2 text-muted small">(confermato: <?= formatPrezzo($ultimoOrdine['ImportoFinaleConfermato']) ?>)</span>
                        <?php else: ?>
                            <span class="ms-2 badge bg-success">Confermato</span>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="ms-2 badge bg-warning text-dark">In attesa di conferma</span>
                    <?php endif; ?>
                </div>
                <a href="<?= SITE_URL ?>/dashboard/ordine_dettaglio.php?id=<?= $ultimoOrdine['idOrdine'] ?>" class="btn btn-outline-bread btn-sm">
                    Dettagli <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>

<h5 class="mb-3">Accesso rapido</h5>
<div class="row g-3">
    <div class="col-6 col-md-4">
        <a href="<?= SITE_URL ?>/" class="card dash-card text-decoration-none text-dark h-100">
            <div class="card-body text-center">
                <i class="bi bi-shop" style="font-size:1.5rem;color:var(--bread-brown)"></i>
                <div class="mt-1 small">Menu settimanale</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-4">
        <a href="<?= SITE_URL ?>/dashboard/ordini.php" class="card dash-card text-decoration-none text-dark h-100">
            <div class="card-body text-center">
                <i class="bi bi-bag" style="font-size:1.5rem;color:var(--bread-brown)"></i>
                <div class="mt-1 small">I miei ordini</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-4">
        <a href="<?= SITE_URL ?>/dashboard/profilo.php" class="card dash-card text-decoration-none text-dark h-100">
            <div class="card-body text-center">
                <i class="bi bi-gear" style="font-size:1.5rem;color:var(--bread-brown)"></i>
                <div class="mt-1 small">Profilo</div>
            </div>
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
