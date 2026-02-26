<?php
$pageTitle = 'Carrello';
require_once __DIR__ . '/../includes/header.php';

$carrello = $_SESSION['carrello'] ?? [];
$totale = getCarrelloTotale();
$menu = getMenuAttivo();
$menuAperto = isMenuAperto($menu);
?>

<h1 class="mb-4"><i class="bi bi-cart3"></i> Il tuo Carrello</h1>

<?php if (empty($carrello)): ?>
    <div class="text-center py-5">
        <i class="bi bi-cart-x" style="font-size:4rem;color:var(--bread-brown)"></i>
        <h3 class="mt-3">Il carrello è vuoto</h3>
        <p class="text-muted">Aggiungi qualcosa dal menu settimanale!</p>
        <a href="<?= SITE_URL ?>/" class="btn btn-bread"><i class="bi bi-arrow-left"></i> Torna al Menu</a>
    </div>
<?php else: ?>

    <?php if (!$menuAperto): ?>
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle"></i> Nessun menu disponibile al momento. Il carrello verrà svuotato al prossimo menu.
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-carrello align-middle">
            <thead class="table-light">
                <tr>
                    <th>Prodotto</th>
                    <th class="text-center" style="width:180px">Quantità</th>
                    <th class="text-end">Prezzo</th>
                    <th class="text-end">Subtotale</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($carrello as $id => $item): ?>
                    <tr>
                        <td>
                            <strong><?= sanitize($item['nome']) ?></strong>
                        </td>
                        <td>
                            <form action="<?= SITE_URL ?>/carrello/aggiorna.php" method="POST" class="d-flex justify-content-center">
                                <?= csrfField() ?>
                                <input type="hidden" name="prodotto_id" value="<?= $id ?>">
                                <div class="qty-control">
                                    <button type="button" class="btn btn-sm btn-outline-secondary qty-minus">-</button>
                                    <input type="number" name="quantita" value="<?= $item['quantita'] ?>" min="1" max="99" data-max="99"
                                        onchange="this.form.submit()">
                                    <button type="button" class="btn btn-sm btn-outline-secondary qty-plus">+</button>
                                </div>
                            </form>
                        </td>
                        <td class="text-end"><?= formatPrezzo($item['prezzo']) ?></td>
                        <td class="text-end fw-bold"><?= formatPrezzo($item['prezzo'] * $item['quantita']) ?></td>
                        <td class="text-end">
                            <form action="<?= SITE_URL ?>/carrello/rimuovi.php" method="POST" class="d-inline">
                                <?= csrfField() ?>
                                <input type="hidden" name="prodotto_id" value="<?= $id ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger" data-confirm="Rimuovere questo prodotto?">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="table-light">
                    <td colspan="3" class="text-end fw-bold fs-5">Totale:</td>
                    <td class="text-end fw-bold fs-5 text-bread"><?= formatPrezzo($totale) ?></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="d-flex flex-wrap gap-2 justify-content-between mt-3">
        <div class="d-flex gap-2">
            <a href="<?= SITE_URL ?>/" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Continua acquisti
            </a>
            <form action="<?= SITE_URL ?>/carrello/svuota.php" method="POST" class="d-inline">
                <?= csrfField() ?>
                <button type="submit" class="btn btn-outline-danger" data-confirm="Svuotare tutto il carrello?">
                    <i class="bi bi-trash"></i> Svuota
                </button>
            </form>
        </div>
        <?php if ($menuAperto): ?>
            <?php if (isLoggedIn()): ?>
                <a href="<?= SITE_URL ?>/checkout/" class="btn btn-bread btn-lg">
                    <i class="bi bi-credit-card"></i> Procedi all'ordine
                </a>
            <?php else: ?>
                <a href="<?= SITE_URL ?>/auth/login.php?redirect=checkout" class="btn btn-bread btn-lg">
                    <i class="bi bi-person"></i> Accedi per ordinare
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </div>

<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
