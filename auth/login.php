<?php
$pageTitle = 'Accedi';
require_once __DIR__ . '/../includes/header.php';

if (isLoggedIn()) {
    redirect('/dashboard/');
}

$redirect = sanitize($_GET['redirect'] ?? '');
?>

<div class="auth-card">
    <div class="card dash-card">
        <div class="card-body p-4">
            <h2 class="text-center mb-4"><i class="bi bi-person-circle"></i> Accedi</h2>

            <form action="<?= SITE_URL ?>/auth/process/login_process.php" method="POST">
                <?= csrfField() ?>
                <?php if ($redirect): ?>
                    <input type="hidden" name="redirect" value="<?= $redirect ?>">
                <?php endif; ?>

                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required autofocus>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-bread w-100 py-2">
                    <i class="bi bi-box-arrow-in-right"></i> Accedi
                </button>
            </form>

            <hr>
            <p class="text-center mb-0">
                Non hai un account? <a href="<?= SITE_URL ?>/auth/registrazione.php">Registrati</a>
            </p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
