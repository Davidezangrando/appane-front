<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/auth.php';

$carrelloCount = getCarrelloCount();
$currentUser = isLoggedIn() ? getUser() : null;
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? sanitize($pageTitle) . ' — ' : '' ?><?= SITE_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= SITE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-bread sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= SITE_URL ?>/">
            <i class="bi bi-shop"></i> <?= SITE_NAME ?>
        </a>
        <div class="d-flex align-items-center order-lg-last gap-2">
            <a href="<?= SITE_URL ?>/carrello/" class="btn btn-outline-light btn-sm position-relative">
                <i class="bi bi-cart3"></i>
                <?php if ($carrelloCount > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        <?= $carrelloCount ?>
                    </span>
                <?php endif; ?>
            </a>
            <?php if (isLoggedIn()): ?>
                <div class="dropdown">
                    <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i>
                        <?= sanitize($currentUser['Nome'] ?? '') ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?= SITE_URL ?>/dashboard/"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                        <li><a class="dropdown-item" href="<?= SITE_URL ?>/dashboard/ordini.php"><i class="bi bi-bag me-2"></i>I miei ordini</a></li>
                        <li><a class="dropdown-item" href="<?= SITE_URL ?>/dashboard/profilo.php"><i class="bi bi-gear me-2"></i>Profilo</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?= SITE_URL ?>/auth/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Esci</a></li>
                    </ul>
                </div>
            <?php else: ?>
                <a href="<?= SITE_URL ?>/auth/login.php" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-person"></i> Accedi
                </a>
            <?php endif; ?>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?= SITE_URL ?>/">Menu della settimana</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<main class="container py-4">
    <?= renderFlash() ?>
