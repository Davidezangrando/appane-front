<?php
session_start();
require_once __DIR__ . '/../../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/auth/login.php');
}

if (!verifyCsrfToken()) {
    setFlash('error', 'Token di sicurezza non valido.');
    redirect('/auth/login.php');
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$redirectTo = $_POST['redirect'] ?? '';

if (empty($username) || empty($password)) {
    setFlash('error', 'Compila tutti i campi.');
    redirect('/auth/login.php');
}

$db = getDB();
$stmt = $db->prepare("SELECT idUtente, Nome, Password, isAdmin FROM tUtente WHERE Username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if (!$user || $user['Password'] !== $password) {
    setFlash('error', 'Username o password non corretti.');
    redirect('/auth/login.php');
}

session_regenerate_id(true);
$_SESSION['user_id'] = $user['idUtente'];
$_SESSION['is_admin'] = (bool)($user['isAdmin'] ?? false);
clearCarrelloCookie();

setFlash('success', 'Benvenuto, ' . $user['Nome'] . '!');

if ($redirectTo === 'checkout') {
    redirect('/checkout/');
} else {
    redirect('/dashboard/');
}
