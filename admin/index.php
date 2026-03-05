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
    echo '<!DOCTYPE html><html lang="it"><head><meta charset="UTF-8"><title>Accesso negato</title></head><body><h1>403 — Accesso negato</h1><p>Non sei autorizzato.</p></body></html>';
    exit;
}

// ── Helper: upload immagine ───────────────────────────────────────────────
function gestisciUpload(array $file, string $uploadDir): string|false|null {
    // null  = nessun file inviato
    // false = errore di upload
    // string = path relativo salvato
    if (empty($file['tmp_name'])) return null;

    $tipiOk   = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $finfo    = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, $tipiOk, true))   return false;
    if ($file['size'] > 2 * 1024 * 1024)       return false;

    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $nomeFile = uniqid('prod_', true) . '.' . $ext;
    $destPath = $uploadDir . $nomeFile;

    if (!move_uploaded_file($file['tmp_name'], $destPath)) return false;

    return 'assets/img/prodotti/' . $nomeFile;
}

// ── Elaborazione POST ─────────────────────────────────────────────────────
$messaggio = '';
$errore    = '';
$uploadDir = __DIR__ . '/../assets/img/prodotti/';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $azione = $_POST['_azione'] ?? '';

    // ── ELIMINA ───────────────────────────────────────────────────────────
    if ($azione === 'elimina') {
        $id = (int)($_POST['id_prodotto'] ?? 0);
        if ($id > 0) {
            // Recupera immagine per eliminarla dal disco
            $s = $pdo->prepare("SELECT Immagine FROM tProdotto WHERE idProdotto = ?");
            $s->execute([$id]);
            $vecchia = $s->fetchColumn();

            $pdo->prepare("DELETE FROM tProdotto WHERE idProdotto = ?")->execute([$id]);

            if ($vecchia) {
                $filePath = __DIR__ . '/../' . $vecchia;
                if (file_exists($filePath)) unlink($filePath);
            }
            $messaggio = "Prodotto #$id eliminato.";
        }

    // ── MODIFICA ──────────────────────────────────────────────────────────
    } elseif ($azione === 'modifica') {
        $id          = (int)($_POST['id_prodotto'] ?? 0);
        $nome        = trim($_POST['nome_prodotto'] ?? '');
        $descrizione = trim($_POST['descrizione'] ?? '');
        $prezzo      = $_POST['prezzo'] ?? '';
        $vecchiaImg  = $_POST['immagine_attuale'] ?? null;

        if ($id <= 0 || $nome === '' || !is_numeric($prezzo) || (float)$prezzo <= 0) {
            $errore = 'Nome e prezzo validi sono obbligatori.';
        } else {
            $nuovaImg = gestisciUpload($_FILES['immagine'] ?? [], $uploadDir);

            if ($nuovaImg === false) {
                $errore = 'Formato non supportato o immagine troppo grande (max 2 MB).';
            } else {
                // Se c'è una nuova immagine, elimina quella vecchia
                if ($nuovaImg !== null && $vecchiaImg) {
                    $filePath = __DIR__ . '/../' . $vecchiaImg;
                    if (file_exists($filePath)) unlink($filePath);
                }

                $imgFinale = $nuovaImg ?? ($vecchiaImg ?: null);

                $pdo->prepare("UPDATE tProdotto SET NomeProdotto=?, Descrizione=?, Prezzo=?, Immagine=? WHERE idProdotto=?")
                    ->execute([$nome, $descrizione !== '' ? $descrizione : null, (float)$prezzo, $imgFinale, $id]);

                $messaggio = "Prodotto #$id aggiornato con successo.";
            }
        }

    // ── AGGIUNGI ──────────────────────────────────────────────────────────
    } elseif ($azione === 'aggiungi') {
        $nome        = trim($_POST['nome_prodotto'] ?? '');
        $descrizione = trim($_POST['descrizione'] ?? '');
        $prezzo      = $_POST['prezzo'] ?? '';

        if ($nome === '' || !is_numeric($prezzo) || (float)$prezzo <= 0) {
            $errore = 'Nome prodotto e prezzo (maggiore di zero) sono obbligatori.';
        } else {
            $nuovaImg = gestisciUpload($_FILES['immagine'] ?? [], $uploadDir);

            if ($nuovaImg === false) {
                $errore = 'Formato non supportato o immagine troppo grande (max 2 MB).';
            } else {
                $pdo->prepare("INSERT INTO tProdotto (NomeProdotto, Descrizione, Prezzo, Immagine) VALUES (?, ?, ?, ?)")
                    ->execute([$nome, $descrizione !== '' ? $descrizione : null, (float)$prezzo, $nuovaImg]);
                $messaggio = 'Prodotto "' . htmlspecialchars($nome) . '" aggiunto (ID: ' . $pdo->lastInsertId() . ').';
            }
        }
    }
}

// ── Modalità modifica (GET ?modifica=X) ──────────────────────────────────
$modificaId   = (int)($_GET['modifica'] ?? 0);
$prodottoEdit = null;

if ($modificaId > 0 && $messaggio === '' && $errore === '') {
    $s = $pdo->prepare("SELECT * FROM tProdotto WHERE idProdotto = ?");
    $s->execute([$modificaId]);
    $prodottoEdit = $s->fetch() ?: null;
}

// ── Lista prodotti ────────────────────────────────────────────────────────
$prodotti = $pdo->query("SELECT idProdotto, NomeProdotto, Descrizione, Prezzo, Immagine FROM tProdotto ORDER BY idProdotto DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — Gestione Prodotti</title>
    <style>
        body            { font-family: sans-serif; max-width: 960px; margin: 40px auto; padding: 0 20px; color: #222; }
        h1              { border-bottom: 2px solid #333; padding-bottom: 8px; }
        h2              { margin-top: 36px; }
        fieldset        { border: 1px solid #ccc; border-radius: 4px; padding: 14px 16px; margin: 0; }
        legend          { font-weight: bold; padding: 0 6px; font-size: .95rem; }
        label           { display: block; margin-top: 10px; font-weight: bold; font-size: .88rem; }
        input[type="text"],
        input[type="number"],
        textarea        { width: 100%; padding: 6px 8px; box-sizing: border-box; border: 1px solid #bbb; border-radius: 3px; font-size: .95rem; margin-top: 3px; }
        textarea        { resize: vertical; }
        input[type="file"] { margin-top: 5px; }
        .btn            { display: inline-block; padding: 7px 18px; border: none; border-radius: 3px; cursor: pointer; font-size: .9rem; margin-top: 14px; text-decoration: none; }
        .btn-dark       { background: #333; color: #fff; }
        .btn-dark:hover { background: #555; }
        .btn-warn       { background: #e0a800; color: #111; }
        .btn-warn:hover { background: #c69500; }
        .btn-danger     { background: #c0392b; color: #fff; }
        .btn-danger:hover { background: #962d22; }
        .btn-sm         { padding: 4px 10px; font-size: .82rem; margin-top: 0; }
        .msg-ok         { color: #186a2e; background: #d4edda; border: 1px solid #c3e6cb; padding: 8px 12px; border-radius: 3px; margin-bottom: 14px; }
        .msg-err        { color: #721c24; background: #f8d7da; border: 1px solid #f5c6cb; padding: 8px 12px; border-radius: 3px; margin-bottom: 14px; }
        table           { width: 100%; border-collapse: collapse; margin-top: 12px; font-size: .88rem; }
        th, td          { border: 1px solid #ccc; padding: 7px 10px; text-align: left; vertical-align: middle; }
        th              { background: #f0f0f0; font-weight: bold; }
        tr:nth-child(even) td { background: #fafafa; }
        tr.editing td   { background: #fff8e1 !important; }
        .thumb          { width: 52px; height: 52px; object-fit: cover; border-radius: 3px; }
        .no-img         { color: #aaa; font-size: .8rem; }
        .img-preview    { margin-top: 6px; }
        .img-preview img { width: 80px; height: 80px; object-fit: cover; border-radius: 4px; border: 1px solid #ccc; }
        .actions        { display: flex; gap: 6px; flex-wrap: wrap; }
    </style>
</head>
<body>

<h1>Admin — Gestione Prodotti</h1>

<?php if ($messaggio !== ''): ?>
    <p class="msg-ok"><?= htmlspecialchars($messaggio) ?></p>
<?php endif; ?>
<?php if ($errore !== ''): ?>
    <p class="msg-err"><?= htmlspecialchars($errore) ?></p>
<?php endif; ?>

<?php
// ── FORM: modifica o aggiunta ─────────────────────────────────────────────
$isEdit  = $prodottoEdit !== null;
$p       = $prodottoEdit ?? [];
$formTitle = $isEdit ? "Modifica prodotto #{$p['idProdotto']}" : 'Aggiungi nuovo prodotto';
?>

<form method="POST" action="" enctype="multipart/form-data">
    <input type="hidden" name="_azione"         value="<?= $isEdit ? 'modifica' : 'aggiungi' ?>">
    <?php if ($isEdit): ?>
        <input type="hidden" name="id_prodotto"     value="<?= (int)$p['idProdotto'] ?>">
        <input type="hidden" name="immagine_attuale" value="<?= htmlspecialchars($p['Immagine'] ?? '') ?>">
    <?php endif; ?>

    <fieldset>
        <legend><?= htmlspecialchars($formTitle) ?></legend>

        <label for="nome_prodotto">Nome prodotto *</label>
        <input type="text" id="nome_prodotto" name="nome_prodotto" maxlength="200" required
               value="<?= htmlspecialchars($p['NomeProdotto'] ?? '') ?>">

        <label for="descrizione">Descrizione</label>
        <textarea id="descrizione" name="descrizione" rows="3"><?= htmlspecialchars($p['Descrizione'] ?? '') ?></textarea>

        <label for="prezzo">Prezzo (€) *</label>
        <input type="number" id="prezzo" name="prezzo" step="0.01" min="0.01" max="9999.99" required
               value="<?= htmlspecialchars($p['Prezzo'] ?? '') ?>">

        <label for="immagine">
            Immagine prodotto (JPG, PNG, WebP, GIF — max 2 MB)
            <?= $isEdit ? '— lascia vuoto per mantenere quella attuale' : '' ?>
        </label>
        <input type="file" id="immagine" name="immagine" accept="image/jpeg,image/png,image/webp,image/gif">

        <?php if ($isEdit && !empty($p['Immagine'])): ?>
            <div class="img-preview">
                <small>Attuale:</small><br>
                <img src="/appane-frontend/<?= htmlspecialchars($p['Immagine']) ?>"
                     alt="immagine attuale">
            </div>
        <?php endif; ?>

        <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap">
            <button type="submit" class="btn btn-<?= $isEdit ? 'warn' : 'dark' ?>">
                <?= $isEdit ? '&#10003; Salva modifiche' : '+ Aggiungi prodotto' ?>
            </button>
            <?php if ($isEdit): ?>
                <a href="?" class="btn btn-dark" style="margin-top:14px">&#x2715; Annulla</a>
            <?php endif; ?>
        </div>
    </fieldset>
</form>

<h2>Prodotti presenti (<?= count($prodotti) ?>)</h2>

<?php if (empty($prodotti)): ?>
    <p>Nessun prodotto nel database.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Img</th>
                <th>Nome</th>
                <th>Descrizione</th>
                <th>Prezzo</th>
                <th>Azioni</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($prodotti as $prod): ?>
                <tr class="<?= ($isEdit && (int)$prod['idProdotto'] === (int)$p['idProdotto']) ? 'editing' : '' ?>">
                    <td><?= (int)$prod['idProdotto'] ?></td>
                    <td>
                        <?php if ($prod['Immagine']): ?>
                            <img src="/appane-frontend/<?= htmlspecialchars($prod['Immagine']) ?>"
                                 alt="<?= htmlspecialchars($prod['NomeProdotto']) ?>"
                                 class="thumb">
                        <?php else: ?>
                            <span class="no-img">—</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($prod['NomeProdotto']) ?></td>
                    <td><?= htmlspecialchars($prod['Descrizione'] ?? '') ?></td>
                    <td style="white-space:nowrap">€ <?= number_format((float)$prod['Prezzo'], 2, ',', '.') ?></td>
                    <td>
                        <div class="actions">
                            <a href="?modifica=<?= (int)$prod['idProdotto'] ?>"
                               class="btn btn-warn btn-sm">&#9998; Modifica</a>

                            <form method="POST" action="" style="margin:0"
                                  onsubmit="return confirm('Eliminare il prodotto #<?= (int)$prod['idProdotto'] ?> — <?= htmlspecialchars(addslashes($prod['NomeProdotto'])) ?>?')">
                                <input type="hidden" name="_azione"    value="elimina">
                                <input type="hidden" name="id_prodotto" value="<?= (int)$prod['idProdotto'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm">&#10005; Elimina</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>
