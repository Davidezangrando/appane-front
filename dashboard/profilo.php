<?php
$pageTitle = 'Profilo';
require_once __DIR__ . '/../includes/header.php';
requireLogin();

$db = getDB();
$user = getUser();

// Aggiornamento profilo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrfToken()) {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_profile') {
        $nome         = trim($_POST['nome'] ?? '');
        $cognome      = trim($_POST['cognome'] ?? '');
        $telefono     = trim($_POST['telefono'] ?? '');
        $nomeVia      = trim($_POST['nome_via'] ?? '');
        $numeroCivico = trim($_POST['numero_civico'] ?? '');
        $cap          = trim($_POST['cap'] ?? '');

        if (empty($nome) || empty($cognome) || empty($telefono) || empty($nomeVia) || empty($numeroCivico) || empty($cap)) {
            setFlash('error', 'Tutti i campi sono obbligatori.');
            redirect('/dashboard/profilo.php');
        }

        $stmt = $db->prepare("UPDATE tUtente SET Nome=?, Cognome=?, NumeroTelefono=?, NomeVia=?, NumeroCivico=?, CAP=? WHERE idUtente=?");
        $stmt->execute([$nome, $cognome, $telefono, $nomeVia, $numeroCivico, $cap, $_SESSION['user_id']]);
        setFlash('success', 'Profilo aggiornato con successo!');
        redirect('/dashboard/profilo.php');

    } elseif ($action === 'change_password') {
        $currentPw = $_POST['current_password'] ?? '';
        $newPw     = $_POST['new_password'] ?? '';
        $confirmPw = $_POST['confirm_password'] ?? '';

        // Verifica password attuale
        $stmt = $db->prepare("SELECT Password FROM tUtente WHERE idUtente = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $hash = $stmt->fetchColumn();

        if ($currentPw !== $hash) {
            setFlash('error', 'Password attuale non corretta.');
            redirect('/dashboard/profilo.php');
        }

        if ($newPw !== $confirmPw) {
            setFlash('error', 'Le password non coincidono.');
            redirect('/dashboard/profilo.php');
        }

        $stmt = $db->prepare("UPDATE tUtente SET Password = ? WHERE idUtente = ?");
        $stmt->execute([$newPw, $_SESSION['user_id']]);
        setFlash('success', 'Password cambiata con successo!');
        redirect('/dashboard/profilo.php');
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-gear"></i> Profilo</h1>
    <a href="<?= SITE_URL ?>/dashboard/" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Dashboard
    </a>
</div>

<div class="row g-4">
    <!-- Dati personali -->
    <div class="col-lg-7">
        <div class="card dash-card">
            <div class="card-body">
                <h5 class="card-title mb-3">Dati personali</h5>
                <form method="POST">
                    <?= csrfField() ?>
                    <input type="hidden" name="action" value="update_profile">

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label">Nome *</label>
                            <input type="text" class="form-control" name="nome" value="<?= sanitize($user['Nome']) ?>" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Cognome *</label>
                            <input type="text" class="form-control" name="cognome" value="<?= sanitize($user['Cognome']) ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" value="<?= sanitize($user['Username']) ?>" disabled>
                        <div class="form-text">L'username non può essere modificato.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Numero di Telefono *</label>
                        <input type="tel" class="form-control" name="telefono" value="<?= sanitize($user['NumeroTelefono']) ?>" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-8">
                            <label class="form-label">Via / Piazza *</label>
                            <input type="text" class="form-control" name="nome_via" value="<?= sanitize($user['NomeVia']) ?>" required>
                        </div>
                        <div class="col-4">
                            <label class="form-label">N. civico *</label>
                            <input type="text" class="form-control" name="numero_civico" value="<?= sanitize($user['NumeroCivico']) ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">CAP *</label>
                        <input type="text" class="form-control" name="cap" value="<?= sanitize($user['CAP']) ?>" maxlength="10" required>
                    </div>

                    <button type="submit" class="btn btn-bread">
                        <i class="bi bi-check"></i> Salva modifiche
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Cambio password -->
    <div class="col-lg-5">
        <div class="card dash-card">
            <div class="card-body">
                <h5 class="card-title mb-3">Cambio Password</h5>
                <form method="POST">
                    <?= csrfField() ?>
                    <input type="hidden" name="action" value="change_password">

                    <div class="mb-3">
                        <label class="form-label">Password attuale *</label>
                        <input type="password" class="form-control" name="current_password" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nuova password *</label>
                        <input type="password" class="form-control" name="new_password" minlength="8" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Conferma nuova password *</label>
                        <input type="password" class="form-control" name="confirm_password" required>
                    </div>

                    <button type="submit" class="btn btn-outline-bread">
                        <i class="bi bi-key"></i> Cambia password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
