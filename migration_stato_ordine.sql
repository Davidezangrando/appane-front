-- ============================================
-- MIGRAZIONE: Aggiunge colonna Stato a tOrdine
-- Eseguire su database già esistenti.
-- ============================================

USE appane_zangrando;

ALTER TABLE tOrdine
    ADD COLUMN Stato ENUM('in_attesa', 'confermato') NOT NULL DEFAULT 'in_attesa'
    AFTER ImportoFinaleConfermato;
