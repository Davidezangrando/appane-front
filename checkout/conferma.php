<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken()) {
    redirect('/checkout/');
}

$carrello = $_SESSION['carrello'] ?? [];
$menu = getMenuAttivo();

if (empty($carrello) || !isMenuAperto($menu)) {
    setFlash('error', 'Impossibile completare l\'ordine.');
    redirect('/carrello/');
}

$nomeViaConsegna    = trim($_POST['nome_via_consegna'] ?? '');
$numeroCivicoConsegna = trim($_POST['numero_civico_consegna'] ?? '');
$capConsegna        = trim($_POST['cap_consegna'] ?? '');
$telefonoEmergenza  = trim($_POST['telefono_emergenza'] ?? '');
$indicazioni        = trim($_POST['indicazioni'] ?? '');

if (empty($nomeViaConsegna) || empty($numeroCivicoConsegna) || empty($capConsegna) || empty($telefonoEmergenza)) {
    setFlash('error', 'Compila tutti i campi obbligatori.');
    redirect('/checkout/');
}

$totale = getCarrelloTotale();
$db = getDB();

try {
    $db->beginTransaction();

    // Inserisci ordine in tOrdine
    $stmt = $db->prepare("
        INSERT INTO tOrdine (NomeViaConsegna, NumeroCivicoConsegna, CAPConsegna, IndicazioniUtente, TelefonoEmergenza, ImportoTotalePrevisto)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$nomeViaConsegna, $numeroCivicoConsegna, $capConsegna, $indicazioni ?: null, $telefonoEmergenza, $totale]);
    $ordineId = $db->lastInsertId();

    // Inserisci righe in tSelezione
    $stmt = $db->prepare("
        INSERT INTO tSelezione (idProdotto, idUtente, idOrdine, Quantita)
        VALUES (?, ?, ?, ?)
    ");
    foreach ($carrello as $prodottoId => $item) {
        $stmt->execute([$prodottoId, $_SESSION['user_id'], $ordineId, $item['quantita']]);
    }

    $db->commit();
    svuotaCarrello();

} catch (Exception $e) {
    $db->rollBack();
    setFlash('error', 'Errore nel salvataggio dell\'ordine. Riprova.');
    redirect('/checkout/');
}

// Pagina conferma
$pageTitle = 'Ordine Confermato';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="text-center py-5">
    <div style="font-size:4rem;color:#198754"><i class="bi bi-check-circle-fill"></i></div>
    <h1 class="mt-3">Ordine Confermato!</h1>
    <p class="lead text-muted">Il tuo ordine <strong>#<?= $ordineId ?></strong> è stato ricevuto.</p>
    <p>Totale previsto: <strong style="color:var(--bread-brown)"><?= formatPrezzo($totale) ?></strong></p>
    <p class="text-muted small">Il totale finale verrà confermato dal panificio.</p>

    <div class="mt-4 d-flex gap-2 justify-content-center">
        <a href="<?= SITE_URL ?>/dashboard/ordine_dettaglio.php?id=<?= $ordineId ?>" class="btn btn-bread">
            <i class="bi bi-eye"></i> Vedi ordine
        </a>
        <a href="<?= SITE_URL ?>/" class="btn btn-outline-secondary">
            <i class="bi bi-house"></i> Torna al menu
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
