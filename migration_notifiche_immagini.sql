-- ============================================
-- MIGRAZIONE: Notifiche + immagine prodotto
-- Eseguire su database già esistenti.
-- ============================================

USE appane_zangrando;

-- 1. Immagine prodotto (percorso relativo al file caricato)
ALTER TABLE tProdotto
    ADD COLUMN Immagine VARCHAR(500) DEFAULT NULL;

-- 2. Tabella notifiche per utenti registrati
CREATE TABLE tNotifica (
    idNotifica  INT AUTO_INCREMENT PRIMARY KEY,
    idUtente    INT NOT NULL,
    Messaggio   VARCHAR(500) NOT NULL,
    Letto       TINYINT(1) NOT NULL DEFAULT 0,
    CreatoIl    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idUtente) REFERENCES tUtente(idUtente)
) ENGINE=InnoDB;
