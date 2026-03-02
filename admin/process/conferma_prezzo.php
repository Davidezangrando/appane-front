<?php
session_start();
require_once __DIR__ . '/../../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/admin/ordini.php');
}

if (!verifyCsrfToken()) {
    setFlash('error', 'Token di sicurezza non valido. Riprova.');
    redirect('/admin/ordini.php');
}

requireAdmin();

$ordineId = (int)($_POST['ordine_id'] ?? 0);
$importo  = (float)($_POST['importo'] ?? -1);

if (!$ordineId || $importo < 0) {
    setFlash('error', 'Dati non validi.');
    redirect('/admin/ordini.php');
}

$db = getDB();
$stmt = $db->prepare("UPDATE tOrdine SET ImportoFinaleConfermato = ? WHERE idOrdine = ?");
$stmt->execute([$importo, $ordineId]);

if ($stmt->rowCount() === 0) {
    setFlash('error', 'Ordine non trovato.');
    redirect('/admin/ordini.php');
}

setFlash('success', 'Importo confermato aggiornato.');
redirect('/admin/ordine_dettaglio.php?id=' . $ordineId);
