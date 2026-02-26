<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken()) {
    redirect('/carrello/');
}

svuotaCarrello();
setFlash('success', 'Carrello svuotato.');
redirect('/');
