-- ============================================
-- MIGRAZIONE: Supporto ordini ospite
-- Eseguire su database già esistenti.
-- ============================================

USE appane_zangrando;

-- 1. Aggiunge colonna NomeOspite a tOrdine (NULL = ordine utente registrato)
ALTER TABLE tOrdine
    ADD COLUMN NomeOspite VARCHAR(200) DEFAULT NULL
    AFTER idOrdine;

-- 2. Rende idUtente nullable in tSelezione per supportare ordini ospite
--    (prima si rimuove il vecchio FK, poi si modifica la colonna, poi si riaggunge il FK)
ALTER TABLE tSelezione
    DROP FOREIGN KEY tSelezione_ibfk_2;

ALTER TABLE tSelezione
    MODIFY COLUMN idUtente INT DEFAULT NULL;

ALTER TABLE tSelezione
    ADD CONSTRAINT fk_selezione_utente
    FOREIGN KEY (idUtente) REFERENCES tUtente(idUtente);
