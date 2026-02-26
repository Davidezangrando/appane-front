<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken()) {
    redirect('/carrello/');
}

$prodottoId = (int)($_POST['prodotto_id'] ?? 0);
$quantita   = max(1, (int)($_POST['quantita'] ?? 1));

if (!$prodottoId || !isset($_SESSION['carrello'][$prodottoId])) {
    redirect('/carrello/');
}

$_SESSION['carrello'][$prodottoId]['quantita'] = $quantita;
setFlash('success', 'Quantità aggiornata.');
redirect('/carrello/');
