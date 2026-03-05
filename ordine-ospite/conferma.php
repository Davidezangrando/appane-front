<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';

// Se già loggato, usa il checkout normale
if (isLoggedIn()) {
    redirect('/checkout/');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken()) {
    redirect('/ordine-ospite/');
}

$carrello = $_SESSION['carrello'] ?? [];
$menu     = getMenuAttivo();

if (empty($carrello) || !isMenuAperto($menu)) {
    setFlash('error', 'Impossibile completare l\'ordine.');
    redirect('/carrello/');
}

$nomeOspite           = trim($_POST['nome_ospite'] ?? '');
$nomeViaConsegna      = trim($_POST['nome_via_consegna'] ?? '');
$numeroCivicoConsegna = trim($_POST['numero_civico_consegna'] ?? '');
$capConsegna          = trim($_POST['cap_consegna'] ?? '');
$telefonoEmergenza    = trim($_POST['telefono_emergenza'] ?? '');
$indicazioni          = trim($_POST['indicazioni'] ?? '');

if (empty($nomeOspite) || empty($nomeViaConsegna) || empty($numeroCivicoConsegna) || empty($capConsegna) || empty($telefonoEmergenza)) {
    setFlash('error', 'Compila tutti i campi obbligatori.');
    redirect('/ordine-ospite/');
}

$totale = getCarrelloTotale();
$db     = getDB();

// Determina stato in base al CAP:
// 34xxx → confermato subito; tutto il resto (incluso 31xxx) → in attesa
$stato = preg_match('/^34/', $capConsegna) ? 'confermato' : 'in_attesa';

try {
    $db->beginTransaction();

    // Inserisci ordine in tOrdine (NomeOspite valorizzato = ordine guest)
    $stmt = $db->prepare("
        INSERT INTO tOrdine (NomeOspite, NomeViaConsegna, NumeroCivicoConsegna, CAPConsegna, IndicazioniUtente, TelefonoEmergenza, ImportoTotalePrevisto, Stato)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$nomeOspite, $nomeViaConsegna, $numeroCivicoConsegna, $capConsegna, $indicazioni ?: null, $telefonoEmergenza, $totale, $stato]);
    $ordineId = $db->lastInsertId();

    // Inserisci righe in tSelezione con idUtente NULL (ordine ospite)
    $stmt = $db->prepare("
        INSERT INTO tSelezione (idProdotto, idUtente, idOrdine, Quantita)
        VALUES (?, NULL, ?, ?)
    ");
    foreach ($carrello as $prodottoId => $item) {
        $stmt->execute([$prodottoId, $ordineId, $item['quantita']]);
    }

    $db->commit();
    svuotaCarrello();
    clearCarrelloCookie();

} catch (Exception $e) {
    $db->rollBack();
    setFlash('error', 'Errore nel salvataggio dell\'ordine. Riprova.');
    redirect('/ordine-ospite/');
}

// Pagina conferma
$pageTitle = $stato === 'confermato' ? 'Ordine Confermato' : 'Ordine in Attesa';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="text-center py-5">
<?php if ($stato === 'confermato'): ?>
    <div style="font-size:4rem;color:#198754"><i class="bi bi-check-circle-fill"></i></div>
    <h1 class="mt-3">Ordine Confermato!</h1>
    <p class="lead text-muted">Il tuo ordine <strong>#<?= $ordineId ?></strong> è stato accettato e confermato.</p>
<?php else: ?>
    <div style="font-size:4rem;color:#ffc107"><i class="bi bi-hourglass-split"></i></div>
    <h1 class="mt-3">Ordine in Attesa</h1>
    <p class="lead text-muted">Il tuo ordine <strong>#<?= $ordineId ?></strong> è stato ricevuto ed è in attesa di conferma da parte del panificio.</p>
<?php endif; ?>
    <p>Totale previsto: <strong style="color:var(--bread-brown)"><?= formatPrezzo($totale) ?></strong></p>
    <p class="text-muted small">Il totale finale verrà confermato dal panificio.</p>
    <p class="text-muted">Conserva il numero ordine <strong>#<?= $ordineId ?></strong> per eventuali comunicazioni.</p>

    <div class="mt-4 d-flex gap-2 justify-content-center flex-wrap">
        <a href="<?= SITE_URL ?>/auth/registrazione.php" class="btn btn-bread">
            <i class="bi bi-person-plus"></i> Crea un account
        </a>
        <a href="<?= SITE_URL ?>/" class="btn btn-outline-secondary">
            <i class="bi bi-house"></i> Torna al menu
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
