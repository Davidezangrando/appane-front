<?php
$pageTitle = 'Traccia Ordine';
require_once __DIR__ . '/../includes/header.php';

// Se loggato, manda alla dashboard ordini
if (isLoggedIn()) {
    redirect('/dashboard/ordini.php');
}

$ordine   = null;
$cercato  = false;
$errore   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cercato   = true;
    $ordineId  = (int)($_POST['ordine_id'] ?? 0);
    $telefono  = trim($_POST['telefono'] ?? '');

    if (!$ordineId || $telefono === '') {
        $errore = 'Inserisci sia il numero ordine che il telefono.';
    } else {
        $db   = getDB();
        $stmt = $db->prepare("
            SELECT idOrdine, NomeOspite, NomeViaConsegna, NumeroCivicoConsegna,
                   CAPConsegna, Stato, ImportoTotalePrevisto, ImportoFinaleConfermato
            FROM tOrdine
            WHERE idOrdine = ?
              AND TelefonoEmergenza = ?
              AND NomeOspite IS NOT NULL
        ");
        $stmt->execute([$ordineId, $telefono]);
        $ordine = $stmt->fetch();

        if (!$ordine) {
            $errore = 'Nessun ordine trovato con questi dati. Verifica il numero ordine e il telefono usati al momento dell\'acquisto.';
        }
    }
}
?>

<div class="auth-card">
    <h1 class="mb-1"><i class="bi bi-search"></i> Traccia il tuo Ordine</h1>
    <p class="text-muted mb-4">Inserisci il numero ordine e il telefono che hai usato durante l'acquisto.</p>

    <div class="card dash-card mb-4">
        <div class="card-body">
            <form method="POST" action="">
                <?= csrfField() ?>
                <div class="mb-3">
                    <label class="form-label">Numero ordine *</label>
                    <input type="number" class="form-control" name="ordine_id"
                           value="<?= (int)($_POST['ordine_id'] ?? 0) ?: '' ?>"
                           min="1" required placeholder="Es: 42">
                </div>
                <div class="mb-3">
                    <label class="form-label">Telefono usato all'ordine *</label>
                    <input type="tel" class="form-control" name="telefono"
                           value="<?= sanitize($_POST['telefono'] ?? '') ?>"
                           required placeholder="Es: 3471234567">
                </div>
                <button type="submit" class="btn btn-bread w-100">
                    <i class="bi bi-search"></i> Cerca ordine
                </button>
            </form>
        </div>
    </div>

    <?php if ($cercato && $errore): ?>
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-circle"></i> <?= sanitize($errore) ?>
        </div>
    <?php endif; ?>

    <?php if ($ordine): ?>
        <div class="card dash-card">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="bi bi-receipt"></i> Ordine #<?= $ordine['idOrdine'] ?>
                </h5>

                <!-- Stato -->
                <div class="mb-3">
                    <?php if ($ordine['Stato'] === 'confermato'): ?>
                        <div class="alert alert-success mb-0">
                            <i class="bi bi-check-circle-fill"></i>
                            <strong>Confermato</strong> — Il tuo ordine è stato accettato.
                            <?php if ($ordine['ImportoFinaleConfermato'] !== null): ?>
                                <div class="mt-1">Importo finale: <strong><?= formatPrezzo($ordine['ImportoFinaleConfermato']) ?></strong></div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning mb-0">
                            <i class="bi bi-hourglass-split"></i>
                            <strong>In attesa</strong> — Il panificio sta verificando il tuo ordine.
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Riepilogo indirizzo -->
                <p class="mb-1">
                    <i class="bi bi-person"></i> <strong><?= sanitize($ordine['NomeOspite']) ?></strong>
                </p>
                <p class="mb-1">
                    <i class="bi bi-geo-alt"></i>
                    <?= sanitize($ordine['NomeViaConsegna']) ?> <?= sanitize($ordine['NumeroCivicoConsegna']) ?>,
                    <?= sanitize($ordine['CAPConsegna']) ?>
                </p>
                <p class="mb-0">
                    <i class="bi bi-currency-euro"></i>
                    Importo previsto: <strong><?= formatPrezzo($ordine['ImportoTotalePrevisto']) ?></strong>
                </p>
            </div>
        </div>

        <p class="text-muted small mt-3 text-center">
            Vuoi tenere traccia in modo più comodo?
            <a href="<?= SITE_URL ?>/auth/registrazione.php">Crea un account</a>
        </p>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
