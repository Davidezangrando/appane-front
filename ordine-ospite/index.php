<?php
$pageTitle = 'Ordine Ospite';
require_once __DIR__ . '/../includes/header.php';

// Se già loggato, usa il checkout normale
if (isLoggedIn()) {
    redirect('/checkout/');
}

$carrello = $_SESSION['carrello'] ?? [];
$menu     = getMenuAttivo();

if (empty($carrello)) {
    setFlash('error', 'Il carrello è vuoto.');
    redirect('/');
}

if (!isMenuAperto($menu)) {
    setFlash('error', 'Nessun menu disponibile per gli ordini.');
    redirect('/carrello/');
}

$totale = getCarrelloTotale();
?>

<h1 class="mb-4"><i class="bi bi-credit-card"></i> Conferma Ordine — Ospite</h1>

<div class="alert alert-info">
    <i class="bi bi-info-circle"></i>
    Stai ordinando come ospite. <a href="<?= SITE_URL ?>/auth/login.php?redirect=checkout">Accedi</a> o
    <a href="<?= SITE_URL ?>/auth/registrazione.php">registrati</a> per tenere traccia dei tuoi ordini.
</div>

<div class="row g-4">
    <!-- Riepilogo carrello -->
    <div class="col-lg-7">
        <div class="card dash-card mb-3">
            <div class="card-body">
                <h5 class="card-title mb-3">Riepilogo prodotti</h5>
                <?php foreach ($carrello as $item): ?>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div>
                            <strong><?= sanitize($item['nome']) ?></strong>
                            <span class="text-muted">x<?= $item['quantita'] ?></span>
                        </div>
                        <span class="fw-bold"><?= formatPrezzo($item['prezzo'] * $item['quantita']) ?></span>
                    </div>
                <?php endforeach; ?>
                <div class="d-flex justify-content-between align-items-center pt-3">
                    <strong class="fs-5">Totale previsto</strong>
                    <strong class="fs-5" style="color:var(--bread-brown)"><?= formatPrezzo($totale) ?></strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Form dati ospite e consegna -->
    <div class="col-lg-5">
        <div class="card dash-card">
            <div class="card-body">
                <h5 class="card-title mb-3">I tuoi dati</h5>
                <form action="<?= SITE_URL ?>/ordine-ospite/conferma.php" method="POST">
                    <?= csrfField() ?>

                    <div class="mb-3">
                        <label class="form-label">Nome e Cognome *</label>
                        <input type="text" class="form-control" name="nome_ospite"
                               value="<?= sanitize($_POST['nome_ospite'] ?? '') ?>"
                               maxlength="200" required>
                    </div>

                    <hr class="my-3">

                    <h6 class="mb-3">Indirizzo di consegna</h6>

                    <div class="row g-2 mb-3">
                        <div class="col-8">
                            <label class="form-label">Via / Piazza *</label>
                            <input type="text" class="form-control" name="nome_via_consegna"
                                   value="<?= sanitize($_POST['nome_via_consegna'] ?? '') ?>" required>
                        </div>
                        <div class="col-4">
                            <label class="form-label">N. civico *</label>
                            <input type="text" class="form-control" name="numero_civico_consegna"
                                   value="<?= sanitize($_POST['numero_civico_consegna'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">CAP *</label>
                        <input type="text" class="form-control" name="cap_consegna"
                               value="<?= sanitize($_POST['cap_consegna'] ?? '') ?>"
                               maxlength="10" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Telefono di emergenza *</label>
                        <input type="tel" class="form-control" name="telefono_emergenza"
                               value="<?= sanitize($_POST['telefono_emergenza'] ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Indicazioni per la consegna</label>
                        <textarea class="form-control" name="indicazioni" rows="3"
                                  placeholder="Es: suonare al secondo piano, lasciare al portiere..."><?= sanitize($_POST['indicazioni'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-bread w-100 py-2">
                        <i class="bi bi-check-circle"></i> Conferma Ordine
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
