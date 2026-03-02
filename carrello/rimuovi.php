<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken()) {
    redirect('/carrello/');
}

$prodottoId = (int)($_POST['prodotto_id'] ?? 0);
if (isset($_SESSION['carrello'][$prodottoId])) {
    unset($_SESSION['carrello'][$prodottoId]);
    setFlash('success', 'Prodotto rimosso dal carrello.');
}

saveCarrelloCookie();
redirect('/carrello/');
