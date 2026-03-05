<?php
$pageTitle = 'Notifiche';
require_once __DIR__ . '/../includes/header.php';
requireLogin();

$db = getDB();

// Marca tutte come lette all'apertura della pagina
$db->prepare("UPDATE tNotifica SET Letto = 1 WHERE idUtente = ? AND Letto = 0")
   ->execute([$_SESSION['user_id']]);

// Carica tutte le notifiche dell'utente
$stmt = $db->prepare("SELECT * FROM tNotifica WHERE idUtente = ? ORDER BY CreatoIl DESC");
$stmt->execute([$_SESSION['user_id']]);
$notifiche = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-bell"></i> Notifiche</h1>
    <a href="<?= SITE_URL ?>/dashboard/" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Dashboard
    </a>
</div>

<?php if (empty($notifiche)): ?>
    <div class="text-center py-5">
        <i class="bi bi-bell-slash" style="font-size:3rem;color:var(--muted)"></i>
        <h4 class="mt-3">Nessuna notifica</h4>
        <p class="text-muted">Non hai ancora ricevuto notifiche.</p>
    </div>
<?php else: ?>
    <div class="d-flex flex-column gap-2">
        <?php foreach ($notifiche as $n): ?>
            <div class="card dash-card" style="<?= !$n['Letto'] ? 'border-left: 3px solid var(--brown)' : '' ?>">
                <div class="card-body d-flex justify-content-between align-items-start gap-3" style="padding: 0.9rem 1.2rem">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-bell-fill" style="color:var(--brown); margin-top:2px; flex-shrink:0"></i>
                        <span><?= sanitize($n['Messaggio']) ?></span>
                    </div>
                    <span class="text-muted small" style="white-space:nowrap; flex-shrink:0">
                        <?= formatDataOra($n['CreatoIl']) ?>
                    </span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
