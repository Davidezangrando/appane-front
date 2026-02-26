<?php
$pageTitle = 'Registrazione';
require_once __DIR__ . '/../includes/header.php';

if (isLoggedIn()) {
    redirect('/dashboard/');
}
?>

<div class="auth-card">
    <div class="card dash-card">
        <div class="card-body p-4">
            <h2 class="text-center mb-4"><i class="bi bi-person-plus"></i> Registrati</h2>

            <form action="<?= SITE_URL ?>/auth/process/register_process.php" method="POST">
                <?= csrfField() ?>

                <div class="row g-3">
                    <div class="col-6">
                        <label for="nome" class="form-label">Nome *</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                    <div class="col-6">
                        <label for="cognome" class="form-label">Cognome *</label>
                        <input type="text" class="form-control" id="cognome" name="cognome" required>
                    </div>
                </div>

                <div class="mb-3 mt-3">
                    <label for="username" class="form-label">Username *</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>

                <div class="mb-3">
                    <label for="telefono" class="form-label">Numero di Telefono *</label>
                    <input type="tel" class="form-control" id="telefono" name="telefono" required>
                </div>

                <hr>
                <p class="form-label fw-semibold mb-2">Indirizzo di residenza</p>

                <div class="row g-2 mb-3">
                    <div class="col-8">
                        <label for="nome_via" class="form-label">Via / Piazza *</label>
                        <input type="text" class="form-control" id="nome_via" name="nome_via" placeholder="Es: Via Roma" required>
                    </div>
                    <div class="col-4">
                        <label for="numero_civico" class="form-label">N. civico *</label>
                        <input type="text" class="form-control" id="numero_civico" name="numero_civico" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="cap" class="form-label">CAP *</label>
                    <input type="text" class="form-control" id="cap" name="cap" maxlength="10" required>
                </div>

                <div class="mb-3 mt-3">
                    <label for="password" class="form-label">Password *</label>
                    <input type="password" class="form-control" id="password" name="password" minlength="8" required>
                    <div class="form-text">Minimo 8 caratteri</div>
                </div>

                <div class="mb-3">
                    <label for="password_confirm" class="form-label">Conferma Password *</label>
                    <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                </div>

                <button type="submit" class="btn btn-bread w-100 py-2">
                    <i class="bi bi-person-plus"></i> Registrati
                </button>
            </form>

            <hr>
            <p class="text-center mb-0">
                Hai già un account? <a href="<?= SITE_URL ?>/auth/login.php">Accedi</a>
            </p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
