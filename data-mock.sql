-- ============================================
-- APP PANE — Dati di test (mock)
-- Da eseguire DOPO setup.sql.
-- Tutti gli indirizzi sono vie reali di Trieste.
-- Password di tutti gli utenti: password123
-- ============================================

USE appane_zangrando;

-- ── Utenti ───────────────────────────────────
-- isAdmin = 1 solo per admin@appane

INSERT INTO tUtente (Nome, Cognome, NomeVia, NumeroCivico, CAP, NumeroTelefono, Username, Password, isAdmin) VALUES
('Admin',    'AppPane',  'Via Carducci',        '1',  '34122', '0000000000', 'admin',           'admin123',    1),
('Luca',     'Ferrario', 'Via Carducci',        '12', '34122', '3401234567', 'luca.ferrario',   'password123', 0),
('Giulia',   'Tamaro',   'Corso Italia',        '3',  '34122', '3479876543', 'giulia.tamaro',   'password123', 0),
('Marco',    'Sartori',  'Via San Nicolò',      '8',  '34121', '3337654321', 'marco.sartori',   'password123', 0),
('Elena',    'Cosulich', 'Via Mazzini',         '21', '34121', '3491122334', 'elena.cosulich',  'password123', 0),
('Paolo',    'Pieri',    'Viale XX Settembre',  '5',  '34121', '3665544332', 'paolo.pieri',     'password123', 0),
('Stefania', 'Vidali',   'Via Fabio Severo',    '44', '34127', '3388776655', 'stefania.vidali', 'password123', 0),
('Roberto',  'Musco',    "Via dell'Istria",     '7',  '34125', '3451234567', 'roberto.musco',   'password123', 0),
('Chiara',   'Benussi',  'Via Geppa',           '16', '34121', '3209988776', 'chiara.benussi',  'password123', 0);

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
--
-- Regola: il menu viene pubblicato il mercoledì (DataPubblicazione).
-- Gli ordini sono aperti da quel giorno fino al GIOVEDÌ 23:59:59,
-- cioè finché NOW() < DataPubblicazione + INTERVAL 2 DAY.
-- Dal venerdì 00:00 in poi il menu è visibile ma non ordinabile.
--
-- ┌─────────────────────────────────────────────────────────────────┐
-- │  SCENARI DI TEST (eseguire UNO dei due UPDATE qui sotto):       │
-- │                                                                 │
-- │  A) ORDINABILE (simula mercoledì o giovedì entro le 23:59):     │
-- │     UPDATE tMenu SET DataPubblicazione = DATE_SUB(CURDATE(), INTERVAL 1 DAY) WHERE idMenu = 3; │
-- │                                                                 │
-- │  B) NON ORDINABILE (simula venerdì 00:01 in poi):               │
-- │     UPDATE tMenu SET DataPubblicazione = DATE_SUB(CURDATE(), INTERVAL 2 DAY) WHERE idMenu = 3; │
-- └─────────────────────────────────────────────────────────────────┘

INSERT INTO tMenu (DataPubblicazione) VALUES
('2026-01-14'),                                       -- Menu 1 (idMenu=1) — storico
('2026-02-04'),                                       -- Menu 2 (idMenu=2) — storico
(DATE_SUB(CURDATE(), INTERVAL 1 DAY));                -- Menu 3 (idMenu=3) — corrente: ORDINABILE

-- Per passare a NON ORDINABILE esegui:
-- UPDATE tMenu SET DataPubblicazione = DATE_SUB(CURDATE(), INTERVAL 2 DAY) WHERE idMenu = 3;

-- ── Produzione (prodotti disponibili per menu) ─

INSERT INTO tProduzione (idProdotto, idMenu) VALUES
-- Menu 1 (gennaio): pagnotta, filone integrale, pane di semola, focaccia
(1, 1), (2, 1), (3, 1), (4, 1),
-- Menu 2 (febbraio W1): filone integrale, pane di semola, pane alle olive, filone ai semi
(2, 2), (3, 2), (5, 2), (6, 2),
-- Menu 3 (corrente): tutti e 6 i prodotti
(1, 3), (2, 3), (3, 3), (4, 3), (5, 3), (6, 3);

-- ── Ordini ───────────────────────────────────
-- ImportoFinaleConfermato = NULL → non ancora confermato dall'admin.

INSERT INTO tOrdine (NomeViaConsegna, NumeroCivicoConsegna, CAPConsegna, IndicazioniUtente, TelefonoEmergenza, ImportoTotalePrevisto, ImportoFinaleConfermato) VALUES
-- Ordine 1 — luca.ferrario
('Via Carducci',        '12',  '34122', NULL,                                          '3401234567', 10.80, NULL),
-- Ordine 2 — giulia.tamaro
('Corso Italia',        '3',   '34122', 'Lasciare in portineria',                      '3479876543', 14.00, NULL),
-- Ordine 3 — marco.sartori — confermato
('Via San Nicolò',      '8',   '34121', 'Citofono SARTORI, secondo piano',             '3337654321', 14.70, 14.70),
-- Ordine 4 — elena.cosulich — confermato
('Via Mazzini',         '21',  '34121', NULL,                                          '3491122334', 12.80, 12.80),
-- Ordine 5 — luca.ferrario — secondo ordine, confermato
('Via Carducci',        '12',  '34122', NULL,                                          '3401234567', 12.50, 12.50),
-- Ordine 6 — paolo.pieri — confermato
('Viale XX Settembre',  '5',   '34121', 'Piano terra, campanello PIERI',               '3665544332', 15.30, 15.30),
-- Ordine 7 — stefania.vidali — in attesa
('Via Fabio Severo',    '44',  '34127', 'Suonare al citofono 7',                       '3388776655', 14.20, NULL),
-- Ordine 8 — roberto.musco — confermato
('Via Valdirivo',       '30',  '34122', 'Ufficio al primo piano, chiedere di Roberto', '3451234567', 11.00, 11.00),
-- Ordine 9 — ospite (NomeOspite valorizzato)
('Via Coroneo',         '15',  '34133', NULL,                                          '3331122334',  8.30, NULL);

-- ── Selezione (righe d'ordine) ───────────────
-- Nota: idUtente = 1 → admin, idUtente = 2 → luca.ferrario, ecc.
-- Gli utenti sono stati inseriti con isAdmin in testa, quindi gli indici shiftano di 1.

INSERT INTO tSelezione (idProdotto, idUtente, idOrdine, Quantita) VALUES
-- Ordine 1 — luca.ferrario (idUtente=2) — prodotti del Menu 1: 2×3.50 + 1×3.80 = 10.80
(1, 2, 1, 2),   -- Pagnotta ×2
(4, 2, 1, 1),   -- Focaccia al rosmarino ×1

-- Ordine 2 — giulia.tamaro (idUtente=3) — prodotti del Menu 2: 1×4.00 + 2×5.00 = 14.00
(2, 3, 2, 1),   -- Filone integrale ×1
(5, 3, 2, 2),   -- Pane alle olive ×2

-- Ordine 3 — marco.sartori (idUtente=4) — prodotti del Menu 3: 3×3.50 + 1×4.20 = 14.70
(1, 4, 3, 3),   -- Pagnotta ×3
(6, 4, 3, 1),   -- Filone ai semi ×1

-- Ordine 4 — elena.cosulich (idUtente=5) — prodotti del Menu 1: 2×4.50 + 1×3.80 = 12.80
(3, 5, 4, 2),   -- Pane di semola ×2
(4, 5, 4, 1),   -- Focaccia al rosmarino ×1

-- Ordine 5 — luca.ferrario (idUtente=2) — prodotti del Menu 2: 2×4.00 + 1×4.50 = 12.50
(2, 2, 5, 2),   -- Filone integrale ×2
(3, 2, 5, 1),   -- Pane di semola ×1

-- Ordine 6 — paolo.pieri (idUtente=6) — prodotti del Menu 3: 1×3.50 + 2×3.80 + 1×4.20 = 15.30
(1, 6, 6, 1),   -- Pagnotta ×1
(4, 6, 6, 2),   -- Focaccia al rosmarino ×2
(6, 6, 6, 1),   -- Filone ai semi ×1

-- Ordine 7 — stefania.vidali (idUtente=7) — prodotti del Menu 3: 2×5.00 + 1×4.20 = 14.20
(5, 7, 7, 2),   -- Pane alle olive ×2
(6, 7, 7, 1),   -- Filone ai semi ×1

-- Ordine 8 — roberto.musco (idUtente=8) — prodotti del Menu 1: 2×3.50 + 1×4.00 = 11.00
(1, 8, 8, 2),   -- Pagnotta ×2
(2, 8, 8, 1),   -- Filone integrale ×1

-- Ordine 9 — ospite (idUtente=NULL): 1×3.50 + 1×4.80 = 8.30... pagnotta + filone ai semi
(1, NULL, 9, 1),  -- Pagnotta ×1
(6, NULL, 9, 1);  -- Filone ai semi ×1
