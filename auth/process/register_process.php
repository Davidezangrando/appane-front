<?php
session_start();
require_once __DIR__ . '/../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/auth/registrazione.php');
}

if (!verifyCsrfToken()) {
    setFlash('error', 'Token di sicurezza non valido.');
    redirect('/auth/registrazione.php');
}

$nome        = trim($_POST['nome'] ?? '');
$cognome     = trim($_POST['cognome'] ?? '');
$username    = trim($_POST['username'] ?? '');
$telefono    = trim($_POST['telefono'] ?? '');
$nomeVia     = trim($_POST['nome_via'] ?? '');
$numeroCivico = trim($_POST['numero_civico'] ?? '');
$cap         = trim($_POST['cap'] ?? '');
$password    = $_POST['password'] ?? '';
$passwordConfirm = $_POST['password_confirm'] ?? '';

// Validazione campi obbligatori
if (empty($nome) || empty($cognome) || empty($username) || empty($telefono) ||
    empty($nomeVia) || empty($numeroCivico) || empty($cap) || empty($password)) {
    setFlash('error', 'Compila tutti i campi obbligatori.');
    redirect('/auth/registrazione.php');
}

if ($password !== $passwordConfirm) {
    setFlash('error', 'Le password non coincidono.');
    redirect('/auth/registrazione.php');
}

$db = getDB();

// Verifica username univoco
$stmt = $db->prepare("SELECT idUtente FROM tUtente WHERE Username = ?");
$stmt->execute([$username]);
if ($stmt->fetch()) {
    setFlash('error', 'Questo username è già in uso. Scegli un altro.');
    redirect('/auth/registrazione.php');
}

// Inserimento
$stmt = $db->prepare("
    INSERT INTO tUtente (Nome, Cognome, NomeVia, NumeroCivico, CAP, NumeroTelefono, Username, Password)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt->execute([
    $nome, $cognome, $nomeVia, $numeroCivico, $cap, $telefono,
    $username, $password
]);

$userId = $db->lastInsertId();

// Login automatico
session_regenerate_id(true);
$_SESSION['user_id'] = $userId;
$_SESSION['is_admin'] = false;

setFlash('success', 'Registrazione completata! Benvenuto, ' . sanitize($nome) . '!');
redirect('/dashboard/');
