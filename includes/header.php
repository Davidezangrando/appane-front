<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/auth.php';

restoreCarrelloDaCookie();
$carrelloCount = getCarrelloCount();
$currentUser = isLoggedIn() ? getUser() : null;
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? sanitize($pageTitle) . ' — ' : '' ?><?= SITE_NAME ?></title>
    <link href="<?= SITE_URL ?>/assets/css/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= SITE_URL ?>/assets/css/style.css?v=2" rel="stylesheet">
</head>
<body>
<nav class="navbar sticky-top">
    <div class="container">
        <a class="navbar-brand" href="<?= SITE_URL ?>/landing.php">
            <i class="bi bi-shop"></i> <?= SITE_NAME ?>
        </a>

        <div class="navbar-collapse" id="navMain">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?= SITE_URL ?>/">Menu della settimana</a>
                </li>
            </ul>
        </div>

        <div class="nav-actions">
            <a href="<?= SITE_URL ?>/carrello/" class="btn btn-outline-light btn-sm position-relative">
                <i class="bi bi-cart3"></i>
                <?php if ($carrelloCount > 0): ?>
                    <span class="nav-cart-badge"><?= $carrelloCount ?></span>
                <?php endif; ?>
            </a>

            <?php if (isLoggedIn()): ?>
            <?php $notifNonLette = getNotificheNonLette(); ?>
            <a href="<?= SITE_URL ?>/dashboard/notifiche.php" class="btn btn-outline-light btn-sm position-relative" title="Notifiche">
                <i class="bi bi-bell"></i>
                <?php if ($notifNonLette > 0): ?>
                    <span class="nav-cart-badge"><?= $notifNonLette ?></span>
                <?php endif; ?>
            </a>
            <?php endif; ?>

            <?php if (isLoggedIn()): ?>
                <div class="dropdown">
                    <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button">
                        <i class="bi bi-person-circle"></i>
                        <?= sanitize($currentUser['Nome'] ?? '') ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?= SITE_URL ?>/dashboard/"><i class="bi bi-speedometer2"></i>Dashboard</a></li>
                        <li><a class="dropdown-item" href="<?= SITE_URL ?>/dashboard/ordini.php"><i class="bi bi-bag"></i>I miei ordini</a></li>
                        <li><a class="dropdown-item" href="<?= SITE_URL ?>/dashboard/profilo.php"><i class="bi bi-gear"></i>Profilo</a></li>
                        <?php if (isAdmin()): ?>
                        <li><a class="dropdown-item" href="<?= SITE_URL ?>/admin/ordini.php"><i class="bi bi-shield-lock"></i>Admin</a></li>
                        <?php endif; ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?= SITE_URL ?>/auth/logout.php"><i class="bi bi-box-arrow-right"></i>Esci</a></li>
                    </ul>
                </div>
            <?php else: ?>
                <a href="<?= SITE_URL ?>/auth/login.php" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-person"></i> Accedi
                </a>
            <?php endif; ?>

            <button class="navbar-toggler" id="navToggler" type="button">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </div>
</nav>
<main class="container py-4">
    <?= renderFlash() ?>
