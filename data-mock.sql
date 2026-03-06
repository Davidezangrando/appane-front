-- ============================================
-- APP PANE — Dati di test (mock)
-- Da eseguire DOPO setup.sql (già aggiornato con tutte le colonne).
-- Tutti gli indirizzi di Trieste: CAP 34xxx → Stato='confermato' automatico.
-- Ordini 2 e 7 usano Udine (CAP 33100) → Stato='in_attesa' per testare entrambi i flussi.
-- Password di tutti gli utenti: password123
-- ============================================

USE appane_zangrando;

-- ── Utenti ───────────────────────────────────
-- isAdmin = 1 solo per admin

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
-- Immagine = NULL (nessuna immagine caricata nei dati di test)

INSERT INTO tProdotto (NomeProdotto, Descrizione, Prezzo, Immagine) VALUES
('Pagnotta con lievito madre', 'Pagnotta tradizionale a lunga lievitazione, crosta croccante e mollica alveolata',      3.50, NULL),
('Filone integrale',           'Filone con farina integrale macinata a pietra, ricco di fibre e dal sapore rustico',    4.00, NULL),
('Pane di semola',             'Pane dorato con semola rimacinata di grano duro, profumato e dalla crosta spessa',      4.50, NULL),
('Focaccia al rosmarino',      'Focaccia soffice con rosmarino fresco e abbondante olio EVO, sale grosso in superficie',3.80, NULL),
('Pane alle olive',            'Pane morbido con olive taggiasche denocciolate, ideale con formaggi e salumi',          5.00, NULL),
('Filone ai semi misti',       'Filone integrale con mix di semi tostati (lino, girasole, sesamo), croccante',          4.20, NULL);

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
-- Il menu viene pubblicato il mercoledì (DataPubblicazione).
-- Ordinabile mentre NOW() < DataPubblicazione + 2 giorni (= venerdì 00:00).
--
-- ┌─────────────────────────────────────────────────────────────────┐
-- │  SCENARI DI TEST per il Menu 3 (eseguire UNO degli UPDATE):     │
-- │                                                                 │
-- │  A) ORDINABILE (finestra d'ordine ancora aperta):               │
-- │     UPDATE tMenu SET DataPubblicazione = DATE_SUB(CURDATE(), INTERVAL 1 DAY) WHERE idMenu = 3; │
-- │                                                                 │
-- │  B) NON ORDINABILE (finestra chiusa, menu solo visibile):       │
-- │     UPDATE tMenu SET DataPubblicazione = DATE_SUB(CURDATE(), INTERVAL 3 DAY) WHERE idMenu = 3; │
-- └─────────────────────────────────────────────────────────────────┘

INSERT INTO tMenu (DataPubblicazione) VALUES
('2026-01-14'),                                       -- Menu 1 (idMenu=1) — storico gennaio
('2026-02-04'),                                       -- Menu 2 (idMenu=2) — storico febbraio
(DATE_SUB(CURDATE(), INTERVAL 1 DAY));                -- Menu 3 (idMenu=3) — corrente: ORDINABILE

-- ── Produzione (prodotti disponibili per menu) ─

INSERT INTO tProduzione (idProdotto, idMenu) VALUES
-- Menu 1 (gennaio): pagnotta, filone integrale, pane di semola, focaccia
(1, 1), (2, 1), (3, 1), (4, 1),
-- Menu 2 (febbraio): filone integrale, pane di semola, pane alle olive, filone ai semi
(2, 2), (3, 2), (5, 2), (6, 2),
-- Menu 3 (corrente): tutti e 6 i prodotti
(1, 3), (2, 3), (3, 3), (4, 3), (5, 3), (6, 3);

-- ── Ordini ───────────────────────────────────
--
-- Logica Stato (replicata da checkout/conferma.php):
--   CAP inizia con '34' → Stato = 'confermato'  (consegna in area Trieste)
--   Altri CAP          → Stato = 'in_attesa'    (da confermare manualmente)
--
-- Ordini 2 e 7 hanno indirizzo a Udine (CAP 33100) → in_attesa, per testare entrambi i flussi.
-- NomeOspite = NULL per utenti registrati; valorizzato solo per ordini ospite.
--
-- Colonne: NomeOspite, NomeViaConsegna, NumeroCivicoConsegna, CAPConsegna,
--          IndicazioniUtente, TelefonoEmergenza, ImportoTotalePrevisto, ImportoFinaleConfermato, Stato

INSERT INTO tOrdine (NomeOspite, NomeViaConsegna, NumeroCivicoConsegna, CAPConsegna, IndicazioniUtente, TelefonoEmergenza, ImportoTotalePrevisto, ImportoFinaleConfermato, Stato) VALUES
-- Ordine 1 — luca.ferrario — Trieste → confermato | prezzo già impostato dall'admin
-- Prodotti Menu 1: 2×pagnotta (3.50) + 1×focaccia (3.80) = 10.80
(NULL, 'Via Carducci',       '12', '34122', NULL,                                          '3401234567', 10.80, 10.80, 'confermato'),

-- Ordine 2 — giulia.tamaro — Udine → in_attesa | nessun prezzo confermato
-- Prodotti Menu 2: 1×filone integrale (4.00) + 2×pane alle olive (5.00) = 14.00
(NULL, 'Via Mercatovecchio', '5',  '33100', 'Citofono TAMARO, secondo piano',              '3479876543', 14.00, NULL, 'in_attesa'),

-- Ordine 3 — marco.sartori — Trieste → confermato | prezzo già impostato dall'admin
-- Prodotti Menu 3: 3×pagnotta (3.50) + 1×filone ai semi (4.20) = 14.70
(NULL, 'Via San Nicolò',     '8',  '34121', 'Citofono SARTORI, secondo piano',             '3337654321', 14.70, 14.70, 'confermato'),

-- Ordine 4 — elena.cosulich — Trieste → confermato | prezzo già impostato dall'admin
-- Prodotti Menu 1: 2×pane di semola (4.50) + 1×focaccia (3.80) = 12.80
(NULL, 'Via Mazzini',        '21', '34121', NULL,                                          '3491122334', 12.80, 12.80, 'confermato'),

-- Ordine 5 — luca.ferrario (2° ordine) — Trieste → confermato | prezzo NON ancora confermato dall'admin
-- Prodotti Menu 2: 2×filone integrale (4.00) + 1×pane di semola (4.50) = 12.50
(NULL, 'Via Carducci',       '12', '34122', NULL,                                          '3401234567', 12.50, NULL, 'confermato'),

-- Ordine 6 — paolo.pieri — Trieste → confermato | prezzo già impostato dall'admin
-- Prodotti Menu 3: 1×pagnotta (3.50) + 2×focaccia (3.80) + 1×filone ai semi (4.20) = 15.30
(NULL, 'Viale XX Settembre', '5',  '34121', 'Piano terra, campanello PIERI',               '3665544332', 15.30, 15.30, 'confermato'),

-- Ordine 7 — stefania.vidali — Udine → in_attesa | nessun prezzo confermato
-- Prodotti Menu 3: 2×pane alle olive (5.00) + 1×filone ai semi (4.20) = 14.20
(NULL, 'Via Giuseppe Verdi', '44', '33100', 'Suonare al citofono 7',                       '3388776655', 14.20, NULL, 'in_attesa'),

-- Ordine 8 — roberto.musco — Trieste → confermato | prezzo già impostato dall'admin
-- Prodotti Menu 1: 2×pagnotta (3.50) + 1×filone integrale (4.00) = 11.00
(NULL, 'Via Valdirivo',      '30', '34122', 'Ufficio al primo piano, chiedere di Roberto', '3451234567', 11.00, 11.00, 'confermato'),

-- Ordine 9 — OSPITE (Giovanni Russi) — Trieste → confermato | prezzo NON ancora confermato dall'admin
-- Per testare traccia.php: numero ordine = 9, telefono = 3331122334
-- Prodotti Menu 3: 1×pagnotta (3.50) + 1×filone ai semi (4.20) = 7.70
('Giovanni Russi', 'Via Coroneo', '15', '34133', NULL,                                     '3331122334',  7.70, NULL, 'confermato');

-- ── Selezione (righe d'ordine) ───────────────

INSERT INTO tSelezione (idProdotto, idUtente, idOrdine, Quantita) VALUES
-- Ordine 1 — luca.ferrario (idUtente=2): 2×pagnotta + 1×focaccia = 10.80
(1, 2, 1, 2),   -- Pagnotta con lievito madre ×2
(4, 2, 1, 1),   -- Focaccia al rosmarino ×1

-- Ordine 2 — giulia.tamaro (idUtente=3): 1×filone integrale + 2×pane alle olive = 14.00
(2, 3, 2, 1),   -- Filone integrale ×1
(5, 3, 2, 2),   -- Pane alle olive ×2

-- Ordine 3 — marco.sartori (idUtente=4): 3×pagnotta + 1×filone ai semi = 14.70
(1, 4, 3, 3),   -- Pagnotta con lievito madre ×3
(6, 4, 3, 1),   -- Filone ai semi misti ×1

-- Ordine 4 — elena.cosulich (idUtente=5): 2×pane di semola + 1×focaccia = 12.80
(3, 5, 4, 2),   -- Pane di semola ×2
(4, 5, 4, 1),   -- Focaccia al rosmarino ×1

-- Ordine 5 — luca.ferrario (idUtente=2): 2×filone integrale + 1×pane di semola = 12.50
(2, 2, 5, 2),   -- Filone integrale ×2
(3, 2, 5, 1),   -- Pane di semola ×1

-- Ordine 6 — paolo.pieri (idUtente=6): 1×pagnotta + 2×focaccia + 1×filone ai semi = 15.30
(1, 6, 6, 1),   -- Pagnotta con lievito madre ×1
(4, 6, 6, 2),   -- Focaccia al rosmarino ×2
(6, 6, 6, 1),   -- Filone ai semi misti ×1

-- Ordine 7 — stefania.vidali (idUtente=7): 2×pane alle olive + 1×filone ai semi = 14.20
(5, 7, 7, 2),   -- Pane alle olive ×2
(6, 7, 7, 1),   -- Filone ai semi misti ×1

-- Ordine 8 — roberto.musco (idUtente=8): 2×pagnotta + 1×filone integrale = 11.00
(1, 8, 8, 2),   -- Pagnotta con lievito madre ×2
(2, 8, 8, 1),   -- Filone integrale ×1

-- Ordine 9 — ospite (idUtente=NULL): 1×pagnotta + 1×filone ai semi = 7.70
(1, NULL, 9, 1),  -- Pagnotta con lievito madre ×1
(6, NULL, 9, 1);  -- Filone ai semi misti ×1

-- ── Notifiche ────────────────────────────────
-- Simula le notifiche create automaticamente da checkout/conferma.php.
-- Letto = 1 → già lette; Letto = 0 → non lette (badge visibile in header).

INSERT INTO tNotifica (idUtente, Messaggio, Letto, CreatoIl) VALUES
-- luca.ferrario (idUtente=2): ordine #1 confermato (letto), ordine #5 confermato (non letto → badge)
(2, 'Il tuo ordine #1 è stato confermato!',                    1, DATE_SUB(NOW(), INTERVAL 45 DAY)),
(2, 'Il tuo ordine #5 è stato confermato!',                    0, DATE_SUB(NOW(), INTERVAL 2  DAY)),

-- giulia.tamaro (idUtente=3): ordine #2 in attesa (non letto → badge)
(3, 'Il tuo ordine #2 è in attesa di conferma dal panificio.', 0, DATE_SUB(NOW(), INTERVAL 30 DAY)),

-- marco.sartori (idUtente=4): ordine #3 confermato (letto)
(4, 'Il tuo ordine #3 è stato confermato!',                    1, DATE_SUB(NOW(), INTERVAL 20 DAY)),

-- elena.cosulich (idUtente=5): ordine #4 confermato (letto)
(5, 'Il tuo ordine #4 è stato confermato!',                    1, DATE_SUB(NOW(), INTERVAL 15 DAY)),

-- paolo.pieri (idUtente=6): ordine #6 confermato (letto)
(6, 'Il tuo ordine #6 è stato confermato!',                    1, DATE_SUB(NOW(), INTERVAL 10 DAY)),

-- stefania.vidali (idUtente=7): ordine #7 in attesa (non letto → badge)
(7, 'Il tuo ordine #7 è in attesa di conferma dal panificio.', 0, DATE_SUB(NOW(), INTERVAL 5  DAY)),

-- roberto.musco (idUtente=8): ordine #8 confermato (letto)
(8, 'Il tuo ordine #8 è stato confermato!',                    1, DATE_SUB(NOW(), INTERVAL 3  DAY));
