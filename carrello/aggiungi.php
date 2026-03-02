<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/');
}

if (!verifyCsrfToken()) {
    setFlash('error', 'Token di sicurezza non valido. Riprova.');
    redirect('/');
}

$menuId     = (int)($_POST['menu_id'] ?? 0);
$prodottoId = (int)($_POST['prodotto_id'] ?? 0);
$quantita   = max(1, (int)($_POST['quantita'] ?? 1));

if (!$menuId || !$prodottoId) {
    setFlash('error', 'Dati non validi.');
    redirect('/');
}

// Verifica menu aperto
$menu = getMenuAttivo();
if (!$menu || $menu['idMenu'] !== $menuId || !isMenuAperto($menu)) {
    setFlash('error', 'Il menu non è più disponibile per gli ordini.');
    redirect('/');
}

// Verifica prodotto nel menu
$db = getDB();
$stmt = $db->prepare("
    SELECT p.idProdotto, p.NomeProdotto, p.Prezzo
    FROM tProduzione pr
    JOIN tProdotto p ON pr.idProdotto = p.idProdotto
    WHERE pr.idMenu = ? AND pr.idProdotto = ?
");
$stmt->execute([$menuId, $prodottoId]);
$prodotto = $stmt->fetch();

if (!$prodotto) {
    setFlash('error', 'Prodotto non trovato nel menu.');
    redirect('/');
}

// Aggiungi al carrello
$_SESSION['carrello_menu_id'] = $menuId;
if (!isset($_SESSION['carrello'])) {
    $_SESSION['carrello'] = [];
}

if (isset($_SESSION['carrello'][$prodottoId])) {
    $_SESSION['carrello'][$prodottoId]['quantita'] += $quantita;
} else {
    $_SESSION['carrello'][$prodottoId] = [
        'prodotto_id' => $prodottoId,
        'nome'        => $prodotto['NomeProdotto'],
        'prezzo'      => (float)$prodotto['Prezzo'],
        'quantita'    => $quantita,
    ];
}

setFlash('success', sanitize($prodotto['NomeProdotto']) . ' aggiunto al carrello!');
saveCarrelloCookie();
redirect('/');
