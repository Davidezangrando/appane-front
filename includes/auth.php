<?php
require_once __DIR__ . '/functions.php';

function isLoggedIn(): bool {
    return !empty($_SESSION['user_id']);
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        setFlash('error', 'Devi effettuare il login per accedere a questa pagina.');
        redirect('/auth/login.php');
    }
}

function getUser(): ?array {
    if (!isLoggedIn()) return null;
    $db = getDB();
    $stmt = $db->prepare("SELECT idUtente, Nome, Cognome, NomeVia, NumeroCivico, CAP, NumeroTelefono, Username FROM tUtente WHERE idUtente = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch() ?: null;
}
