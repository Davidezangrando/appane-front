<?php
$pageTitle = 'Admin — Tutti gli Ordini';
session_start();
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$db = getDB();

$filtro = $_GET['tipo'] ?? 'tutti';
$where  = match($filtro) {
    'ospite'     => 'WHERE o.NomeOspite IS NOT NULL',
    'registrato' => 'WHERE o.NomeOspite IS NULL',
    default      => '',
};

$stmt = $db->query("
    SELECT o.*,
           COALESCE(o.NomeOspite, CONCAT(u.Nome, ' ', u.Cognome)) AS NomeCliente,
           IF(o.NomeOspite IS NOT NULL, 'Ospite', 'Registrato') AS TipoCliente
    FROM tOrdine o
    LEFT JOIN tSelezione s ON s.idOrdine = o.idOrdine
    LEFT JOIN tUtente u ON s.idUtente = u.idUtente
    $where
    GROUP BY o.idOrdine
    ORDER BY o.idOrdine DESC
");
$ordini = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-shield-lock"></i> Tutti gli Ordini</h1>
</div>

<div class="mb-3 d-flex gap-2">
    <a href="?tipo=tutti" class="btn btn-sm <?= $filtro === 'tutti' ? 'btn-bread' : 'btn-outline-secondary' ?>">Tutti</a>
    <a href="?tipo=registrato" class="btn btn-sm <?= $filtro === 'registrato' ? 'btn-bread' : 'btn-outline-secondary' ?>">Registrati</a>
    <a href="?tipo=ospite" class="btn btn-sm <?= $filtro === 'ospite' ? 'btn-bread' : 'btn-outline-secondary' ?>">Ospiti</a>
</div>

<?php if (empty($ordini)): ?>
    <div class="text-center py-5">
        <i class="bi bi-bag-x" style="font-size:3rem;color:var(--bread-brown)"></i>
        <h4 class="mt-3">Nessun ordine trovato</h4>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Cliente</th>
                    <th>Tipo</th>
                    <th>Via consegna</th>
                    <th>Importo previsto</th>
                    <th>Importo confermato</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ordini as $o): ?>
                    <tr>
                        <td><strong><?= $o['idOrdine'] ?></strong></td>
                        <td><?= sanitize($o['NomeCliente'] ?? '') ?></td>
                        <td>
                            <?php if ($o['TipoCliente'] === 'Ospite'): ?>
                                <span class="badge bg-secondary">Ospite</span>
                            <?php else: ?>
                                <span class="badge bg-primary">Registrato</span>
                            <?php endif; ?>
                        </td>
                        <td><?= sanitize($o['NomeViaConsegna']) ?> <?= sanitize($o['NumeroCivicoConsegna']) ?>, <?= sanitize($o['CAPConsegna']) ?></td>
                        <td><?= formatPrezzo($o['ImportoTotalePrevisto']) ?></td>
                        <td>
                            <?php if ($o['ImportoFinaleConfermato'] !== null): ?>
                                <span class="fw-bold text-success"><?= formatPrezzo($o['ImportoFinaleConfermato']) ?></span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">In attesa</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= SITE_URL ?>/admin/ordine_dettaglio.php?id=<?= $o['idOrdine'] ?>" class="btn btn-sm btn-outline-bread">
                                Dettagli
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
