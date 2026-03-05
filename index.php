<?php
$pageTitle = 'Menu della Settimana';
require_once __DIR__ . '/includes/header.php';

$menu = getMenuAttivo();
checkCarrelloMenu($menu);
$menuAperto = isMenuAperto($menu);
?>

<?php if (!$menu): ?>
    <!-- Nessun menu pubblicato -->
    <div class="hero-section text-center">
        <h1><i class="bi bi-clock-history"></i> Torna presto!</h1>
        <p class="lead mb-0">Il menu settimanale non è ancora disponibile.<br>Controlla di nuovo a breve!</p>
    </div>

<?php else: ?>
    <!-- Hero con info menu -->
    <div class="hero-section">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1><i class="bi bi-basket2-fill"></i> Menu della Settimana</h1>
                <p class="lead mb-0">Pubblicato il <strong><?= formatData($menu['DataPubblicazione']) ?></strong></p>
            </div>
        </div>
    </div>

    <?php if (!$menuAperto): ?>
        <div class="alert alert-warning mt-3 d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-lock-fill fs-5"></i>
            <span><strong>Non ordinabile.</strong> Il periodo di ordinazione per questo menu è terminato. Puoi consultare i prodotti ma non è più possibile effettuare ordini.</span>
        </div>
    <?php endif; ?>

    <?php
    $prodotti = getProdottiMenu($menu['idMenu']);
    ?>

    <?php if (empty($prodotti)): ?>
        <div class="alert alert-info mt-4">
            <i class="bi bi-info-circle"></i> Nessun prodotto disponibile in questo menu.
        </div>
    <?php else: ?>
        <div class="row g-3 mt-2">
            <?php foreach ($prodotti as $prod): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card card-prodotto h-100">
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center position-relative" style="height:160px; overflow:hidden">
                            <?php if (!empty($prod['Immagine'])): ?>
                                <img src="<?= SITE_URL ?>/<?= sanitize($prod['Immagine']) ?>"
                                     alt="<?= sanitize($prod['NomeProdotto']) ?>"
                                     style="width:100%;height:100%;object-fit:cover;position:absolute;inset:0">
                            <?php else: ?>
                                <i class="bi bi-basket2 text-muted" style="font-size:3rem"></i>
                            <?php endif; ?>
                            <?php if (!$menuAperto): ?>
                                <span class="position-absolute top-0 end-0 m-2 badge bg-secondary" style="z-index:1">
                                    <i class="bi bi-lock-fill"></i> Non ordinabile
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title mb-1"><?= sanitize($prod['NomeProdotto']) ?></h6>
                            <?php if ($prod['Descrizione']): ?>
                                <p class="card-text text-muted small flex-grow-1"><?= sanitize($prod['Descrizione']) ?></p>
                            <?php endif; ?>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="prezzo"><?= formatPrezzo($prod['Prezzo']) ?></span>
                            </div>
                            <?php if ($menuAperto): ?>
                                <form action="<?= SITE_URL ?>/carrello/aggiungi.php" method="POST" class="mt-2">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="prodotto_id" value="<?= $prod['idProdotto'] ?>">
                                    <input type="hidden" name="menu_id" value="<?= $menu['idMenu'] ?>">
                                    <div class="d-flex gap-1">
                                        <div class="qty-control flex-grow-1">
                                            <button type="button" class="btn btn-sm btn-outline-secondary qty-minus">-</button>
                                            <input type="number" name="quantita" value="1" min="1" max="99" data-max="99">
                                            <button type="button" class="btn btn-sm btn-outline-secondary qty-plus">+</button>
                                        </div>
                                        <button type="submit" class="btn btn-sm btn-bread btn-aggiungi" title="Aggiungi al carrello">
                                            <i class="bi bi-cart-plus"></i>
                                        </button>
                                    </div>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
