-- ============================================
-- APP PANE — Dati di test (mock)
-- Da eseguire DOPO setup.sql.
-- Tutti gli indirizzi sono vie reali di Trieste.
-- Password di tutti gli utenti: password123
-- ============================================

USE appane_zangrando;

-- ── Utenti ───────────────────────────────────
-- Hash bcrypt di "password123"

INSERT INTO tUtente (Nome, Cognome, NomeVia, NumeroCivico, CAP, NumeroTelefono, Username, Password) VALUES
('Luca',     'Ferrario', 'Via Carducci',        '12', '34122', '3401234567', 'luca.ferrario',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Giulia',   'Tamaro',   'Corso Italia',         '3',  '34122', '3479876543', 'giulia.tamaro',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Marco',    'Sartori',  'Via San Nicolò',       '8',  '34121', '3337654321', 'marco.sartori',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Elena',    'Cosulich', 'Via Mazzini',          '21', '34121', '3491122334', 'elena.cosulich',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Paolo',    'Pieri',    'Viale XX Settembre',   '5',  '34121', '3665544332', 'paolo.pieri',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Stefania', 'Vidali',   'Via Fabio Severo',     '44', '34127', '3388776655', 'stefania.vidali', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Roberto',  'Musco',    "Via dell'Istria",      '7',  '34125', '3451234567', 'roberto.musco',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Chiara',   'Benussi',  'Via Geppa',            '16', '34121', '3209988776', 'chiara.benussi',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- ── Ingredienti ─────────────────────────────

INSERT INTO tIngrediente (NomeIngrediente, Descrizione) VALUES
('Farina tipo 0',                     'Farina di grano tenero tipo 0'),
('Farina integrale macinata a pietra','Farina integrale ad alta fibra'),
('Semola di grano duro rimacinata',   'Semola fine per pane pugliese'),
('Acqua',                             'Acqua naturale a temperatura ambiente'),
('Lievito madre',                     'Pasta madre naturale a lunga maturazione'),
('Sale marino',                       'Sale marino fino di Sicilia'),
('Olio extra vergine di oliva',       'Olio EVO monocultivar'),
('Rosmarino fresco',                  'Rosmarino raccolto fresco'),
('Olive taggiasche denocciolate',     'Olive taggiasche conservate in olio EVO'),
('Mix di semi',                       'Semi di lino, girasole e sesamo tostati'),
('Gherigli di noce',                  'Noci tostate a secco'),
('Miele millefiori',                  'Miele locale di produzione artigianale');

-- ── Prodotti ────────────────────────────────

INSERT INTO tProdotto (NomeProdotto, Descrizione, Prezzo) VALUES
('Pagnotta con lievito madre', 'Pagnotta tradizionale a lunga lievitazione, crosta croccante e mollica alveolata',      3.50),
('Filone integrale',           'Filone con farina integrale macinata a pietra, ricco di fibre e dal sapore rustico',    4.00),
('Pane di semola',             'Pane dorato con semola rimacinata di grano duro, profumato e dalla crosta spessa',      4.50),
('Focaccia al rosmarino',      'Focaccia soffice con rosmarino fresco e abbondante olio EVO, sale grosso in superficie',3.80),
('Pane alle olive',            'Pane morbido con olive taggiasche denocciolate, ideale con formaggi e salumi',          5.00),
('Filone ai semi misti',       'Filone integrale con mix di semi tostati (lino, girasole, sesamo), croccante',          4.20);

-- ── Ricette ─────────────────────────────────

INSERT INTO tRicetta (idIngrediente, idProdotto, Quantita) VALUES
-- (1) Pagnotta con lievito madre
(1, 1, '500g'),  (4, 1, '320ml'), (5, 1, '80g'),  (6, 1, '8g'),
-- (2) Filone integrale
(2, 2, '500g'),  (4, 2, '380ml'), (5, 2, '100g'), (6, 2, '10g'), (12, 2, '1 cucchiaio'),
-- (3) Pane di semola
(3, 3, '500g'),  (4, 3, '330ml'), (5, 3, '90g'),  (6, 3, '10g'), (7, 3, '20ml'),
-- (4) Focaccia al rosmarino
(1, 4, '400g'),  (4, 4, '280ml'), (5, 4, '80g'),  (6, 4, '8g'),  (7, 4, '60ml'), (8, 4, 'q.b.'),
-- (5) Pane alle olive
(1, 5, '400g'),  (4, 5, '280ml'), (5, 5, '80g'),  (6, 5, '8g'),  (9, 5, '150g'),
-- (6) Filone ai semi misti
(2, 6, '450g'),  (4, 6, '330ml'), (5, 6, '80g'),  (6, 6, '8g'),  (10, 6, '80g');

-- ── Menu settimanali ─────────────────────────

INSERT INTO tMenu (DataPubblicazione) VALUES
('2026-01-14'),   -- Menu 1 (idMenu=1)
('2026-02-04'),   -- Menu 2 (idMenu=2)
('2026-02-18');   -- Menu 3 (idMenu=3) — settimana corrente

-- ── Produzione (prodotti disponibili per menu) ─

INSERT INTO tProduzione (idProdotto, idMenu) VALUES
-- Menu 1 (gennaio): pagnotta, filone integrale, pane di semola, focaccia
(1, 1), (2, 1), (3, 1), (4, 1),
-- Menu 2 (febbraio W1): filone integrale, pane di semola, pane alle olive, filone ai semi
(2, 2), (3, 2), (5, 2), (6, 2),
-- Menu 3 (febbraio W3 — corrente): tutti e 6 i prodotti
(1, 3), (2, 3), (3, 3), (4, 3), (5, 3), (6, 3);

-- ── Ordini ───────────────────────────────────
-- I totali sono calcolati dalla somma delle righe in tSelezione (Prezzo × Quantita).
-- ImportoFinaleConfermato = NULL → ordine non ancora confermato dal backoffice.

INSERT INTO tOrdine (NomeViaConsegna, NumeroCivicoConsegna, CAPConsegna, IndicazioniUtente, TelefonoEmergenza, ImportoTotalePrevisto, ImportoFinaleConfermato) VALUES
-- Ordine 1 — luca.ferrario — consegna a casa
('Via Carducci',        '12',  '34122', NULL,                              '3401234567', 10.80, NULL),
-- Ordine 2 — giulia.tamaro
('Corso Italia',        '3',   '34122', 'Lasciare in portineria',          '3479876543', 14.00, NULL),
-- Ordine 3 — marco.sartori — confermato
('Via San Nicolò',      '8',   '34121', 'Citofono SARTORI, secondo piano', '3337654321', 14.70, 14.70),
-- Ordine 4 — elena.cosulich — confermato
('Via Mazzini',         '21',  '34121', NULL,                              '3491122334', 12.80, 12.80),
-- Ordine 5 — luca.ferrario — secondo ordine, confermato
('Via Carducci',        '12',  '34122', NULL,                              '3401234567', 12.50, 12.50),
-- Ordine 6 — paolo.pieri — confermato
('Viale XX Settembre',  '5',   '34121', 'Piano terra, campanello PIERI',   '3665544332', 15.30, 15.30),
-- Ordine 7 — stefania.vidali — in attesa
('Via Fabio Severo',    '44',  '34127', 'Suonare al citofono 7',           '3388776655', 14.20, NULL),
-- Ordine 8 — roberto.musco — consegna in ufficio, confermato
('Via Valdirivo',       '30',  '34122', 'Ufficio al primo piano, chiedere di Roberto', '3451234567', 11.00, 11.00);

-- ── Selezione (righe d'ordine) ───────────────
-- Colonne: idProdotto, idUtente, idOrdine, Quantita
-- Ogni prodotto può comparire una sola volta per ordine (PK composita).
-- I prodotti di ogni ordine sono coerenti con il menu di riferimento.

INSERT INTO tSelezione (idProdotto, idUtente, idOrdine, Quantita) VALUES
-- Ordine 1 — luca.ferrario (idUtente=1) — prodotti del Menu 1
-- 2 × 3.50 + 1 × 3.80 = 10.80
(1, 1, 1, 2),   -- Pagnotta ×2
(4, 1, 1, 1),   -- Focaccia al rosmarino ×1

-- Ordine 2 — giulia.tamaro (idUtente=2) — prodotti del Menu 2
-- 1 × 4.00 + 2 × 5.00 = 14.00
(2, 2, 2, 1),   -- Filone integrale ×1
(5, 2, 2, 2),   -- Pane alle olive ×2

-- Ordine 3 — marco.sartori (idUtente=3) — prodotti del Menu 3
-- 3 × 3.50 + 1 × 4.20 = 14.70
(1, 3, 3, 3),   -- Pagnotta ×3
(6, 3, 3, 1),   -- Filone ai semi ×1

-- Ordine 4 — elena.cosulich (idUtente=4) — prodotti del Menu 1
-- 2 × 4.50 + 1 × 3.80 = 12.80
(3, 4, 4, 2),   -- Pane di semola ×2
(4, 4, 4, 1),   -- Focaccia al rosmarino ×1

-- Ordine 5 — luca.ferrario (idUtente=1) — prodotti del Menu 2
-- 2 × 4.00 + 1 × 4.50 = 12.50
(2, 1, 5, 2),   -- Filone integrale ×2
(3, 1, 5, 1),   -- Pane di semola ×1

-- Ordine 6 — paolo.pieri (idUtente=5) — prodotti del Menu 3
-- 1 × 3.50 + 2 × 3.80 + 1 × 4.20 = 15.30
(1, 5, 6, 1),   -- Pagnotta ×1
(4, 5, 6, 2),   -- Focaccia al rosmarino ×2
(6, 5, 6, 1),   -- Filone ai semi ×1

-- Ordine 7 — stefania.vidali (idUtente=6) — prodotti del Menu 3
-- 2 × 5.00 + 1 × 4.20 = 14.20
(5, 6, 7, 2),   -- Pane alle olive ×2
(6, 6, 7, 1),   -- Filone ai semi ×1

-- Ordine 8 — roberto.musco (idUtente=7) — prodotti del Menu 1
-- 2 × 3.50 + 1 × 4.00 = 11.00
(1, 7, 8, 2),   -- Pagnotta ×2
(2, 7, 8, 1);   -- Filone integrale ×1
