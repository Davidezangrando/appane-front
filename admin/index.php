<?php
session_start();

// Connessione al database inline
try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=appane_zangrando;charset=utf8mb4',
        'root',
        '',
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die('Errore di connessione al database.');
}

// Controllo accesso: utente loggato?
if (empty($_SESSION['user_id'])) {
    header('Location: /appane-frontend/auth/login.php');
    exit;
}

// Controllo isAdmin direttamente da DB
$stmtAdmin = $pdo->prepare("SELECT isAdmin FROM tUtente WHERE idUtente = ?");
$stmtAdmin->execute([$_SESSION['user_id']]);
$isAdmin = (bool)$stmtAdmin->fetchColumn();

if (!$isAdmin) {
    http_response_code(403);
    echo '<!DOCTYPE html><html lang="it"><head><meta charset="UTF-8"><title>Accesso negato</title></head><body><h1>403 — Accesso negato</h1><p>Non sei autorizzato ad accedere a questa pagina.</p></body></html>';
    exit;
}

// Gestione form: aggiunta prodotto
$messaggio = '';
$errore    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomeProdotto = trim($_POST['nome_prodotto'] ?? '');
    $descrizione  = trim($_POST['descrizione'] ?? '');
    $prezzo       = $_POST['prezzo'] ?? '';

    if ($nomeProdotto === '' || !is_numeric($prezzo) || (float)$prezzo <= 0) {
        $errore = 'Nome prodotto e prezzo (maggiore di zero) sono obbligatori.';
    } else {
        $ins = $pdo->prepare("INSERT INTO tProdotto (NomeProdotto, Descrizione, Prezzo) VALUES (?, ?, ?)");
        $ins->execute([$nomeProdotto, $descrizione !== '' ? $descrizione : null, (float)$prezzo]);
        $messaggio = 'Prodotto "' . htmlspecialchars($nomeProdotto) . '" aggiunto con successo (ID: ' . $pdo->lastInsertId() . ').';
    }
}

// Carica lista prodotti
$prodotti = $pdo->query("SELECT idProdotto, NomeProdotto, Descrizione, Prezzo FROM tProdotto ORDER BY idProdotto DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — Aggiungi Prodotto</title>
    <style>
        body        { font-family: sans-serif; max-width: 860px; margin: 40px auto; padding: 0 20px; color: #222; }
        h1          { border-bottom: 2px solid #333; padding-bottom: 8px; }
        h2          { margin-top: 36px; }
        label       { display: block; margin-top: 12px; font-weight: bold; font-size: .9rem; }
        input[type="text"],
        input[type="number"],
        textarea    { width: 100%; padding: 6px 8px; box-sizing: border-box; border: 1px solid #bbb; border-radius: 3px; font-size: .95rem; }
        textarea    { resize: vertical; }
        button      { margin-top: 16px; padding: 8px 24px; background: #333; color: #fff; border: none; border-radius: 3px; cursor: pointer; font-size: .95rem; }
        button:hover { background: #555; }
        .msg-ok     { color: #186a2e; background: #d4edda; border: 1px solid #c3e6cb; padding: 8px 12px; border-radius: 3px; margin-bottom: 12px; }
        .msg-err    { color: #721c24; background: #f8d7da; border: 1px solid #f5c6cb; padding: 8px 12px; border-radius: 3px; margin-bottom: 12px; }
        table       { width: 100%; border-collapse: collapse; margin-top: 12px; font-size: .9rem; }
        th, td      { border: 1px solid #ccc; padding: 7px 10px; text-align: left; vertical-align: top; }
        th          { background: #f0f0f0; font-weight: bold; }
        tr:nth-child(even) td { background: #fafafa; }
        .prezzo     { white-space: nowrap; }
    </style>
</head>
<body>

<h1>Admin — Gestione Prodotti</h1>

<?php if ($messaggio !== ''): ?>
    <p class="msg-ok"><?= $messaggio ?></p>
<?php endif; ?>
<?php if ($errore !== ''): ?>
    <p class="msg-err"><?= htmlspecialchars($errore) ?></p>
<?php endif; ?>

<form method="POST" action="">
    <label for="nome_prodotto">Nome prodotto *</label>
    <input type="text" id="nome_prodotto" name="nome_prodotto" maxlength="200" required>

    <label for="descrizione">Descrizione</label>
    <textarea id="descrizione" name="descrizione" rows="3"></textarea>

    <label for="prezzo">Prezzo (€) *</label>
    <input type="number" id="prezzo" name="prezzo" step="0.01" min="0.01" max="9999.99" required>

    <button type="submit">Aggiungi prodotto</button>
</form>

<h2>Prodotti presenti (<?= count($prodotti) ?>)</h2>

<?php if (empty($prodotti)): ?>
    <p>Nessun prodotto nel database.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nome</th>
                <th>Descrizione</th>
                <th>Prezzo</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($prodotti as $p): ?>
                <tr>
                    <td><?= (int)$p['idProdotto'] ?></td>
                    <td><?= htmlspecialchars($p['NomeProdotto']) ?></td>
                    <td><?= htmlspecialchars($p['Descrizione'] ?? '') ?></td>
                    <td class="prezzo">€ <?= number_format((float)$p['Prezzo'], 2, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>
