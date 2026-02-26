<?php
require_once __DIR__ . '/../config/database.php';

// --- CSRF ---
function generateCsrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(generateCsrfToken()) . '">';
}

function verifyCsrfToken(): bool {
    if (empty($_POST['csrf_token']) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
}

// --- Sanitizzazione ---
function sanitize(string $value): string {
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

// --- Flash messages ---
function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function renderFlash(): string {
    $flash = getFlash();
    if (!$flash) return '';
    $type = $flash['type'] === 'error' ? 'danger' : $flash['type'];
    return '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">'
        . sanitize($flash['message'])
        . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
}

// --- Menu attivo ---
function getMenuAttivo(): ?array {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM tMenu WHERE DataPubblicazione <= CURDATE() ORDER BY DataPubblicazione DESC LIMIT 1");
    $stmt->execute();
    $menu = $stmt->fetch();
    return $menu ?: null;
}

function isMenuAperto(?array $menu = null): bool {
    if (!$menu) $menu = getMenuAttivo();
    return $menu !== null;
}

// --- Prodotti del menu ---
function getProdottiMenu(int $menuId): array {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT p.*
        FROM tProduzione pr
        JOIN tProdotto p ON pr.idProdotto = p.idProdotto
        WHERE pr.idMenu = ?
        ORDER BY p.NomeProdotto
    ");
    $stmt->execute([$menuId]);
    return $stmt->fetchAll();
}

// --- Carrello ---
function getCarrelloCount(): int {
    if (empty($_SESSION['carrello'])) return 0;
    $count = 0;
    foreach ($_SESSION['carrello'] as $item) {
        $count += $item['quantita'];
    }
    return $count;
}

function getCarrelloTotale(): float {
    if (empty($_SESSION['carrello'])) return 0;
    $totale = 0;
    foreach ($_SESSION['carrello'] as $item) {
        $totale += $item['prezzo'] * $item['quantita'];
    }
    return $totale;
}

function svuotaCarrello(): void {
    $_SESSION['carrello'] = [];
    unset($_SESSION['carrello_menu_id']);
}

// Svuota carrello se il menu è cambiato
function checkCarrelloMenu(?array $menu): void {
    if (!$menu) {
        svuotaCarrello();
        return;
    }
    if (isset($_SESSION['carrello_menu_id']) && $_SESSION['carrello_menu_id'] !== $menu['idMenu']) {
        svuotaCarrello();
    }
}

// --- Formattazione ---
function formatPrezzo(float $prezzo): string {
    return number_format($prezzo, 2, ',', '.') . ' €';
}

function formatData(string $data): string {
    return date('d/m/Y', strtotime($data));
}

function formatDataOra(string $data): string {
    return date('d/m/Y H:i', strtotime($data));
}

function redirect(string $url): void {
    header('Location: ' . SITE_URL . $url);
    exit;
}
