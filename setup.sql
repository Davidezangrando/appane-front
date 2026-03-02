-- ============================================
-- APP PANE — Setup Database
-- Eseguire questo script per creare la struttura.
-- Per i dati di test eseguire data-mock.sql dopo.
-- ============================================

DROP DATABASE IF EXISTS appane_zangrando;

CREATE DATABASE appane_zangrando
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE appane_zangrando;

-- ── Tabelle entità ──────────────────────────

CREATE TABLE tUtente (
    idUtente INT AUTO_INCREMENT PRIMARY KEY,
    Nome VARCHAR(100) NOT NULL,
    Cognome VARCHAR(100) NOT NULL,
    NomeVia VARCHAR(255) NOT NULL,
    NumeroCivico VARCHAR(10) NOT NULL,
    CAP VARCHAR(10) NOT NULL,
    NumeroTelefono VARCHAR(20) NOT NULL,
    Username VARCHAR(100) UNIQUE NOT NULL,
    Password VARCHAR(255) NOT NULL,
    isAdmin TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE tOrdine (
    idOrdine INT AUTO_INCREMENT PRIMARY KEY,
    NomeOspite VARCHAR(200) DEFAULT NULL,           
    NomeViaConsegna VARCHAR(255) NOT NULL,
    NumeroCivicoConsegna VARCHAR(10) NOT NULL,
    CAPConsegna VARCHAR(10) NOT NULL,
    IndicazioniUtente TEXT,
    TelefonoEmergenza VARCHAR(20) NOT NULL,
    ImportoTotalePrevisto DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    ImportoFinaleConfermato DECIMAL(8,2) DEFAULT NULL
) ENGINE=InnoDB;

CREATE TABLE tProdotto (
    idProdotto INT AUTO_INCREMENT PRIMARY KEY,
    NomeProdotto VARCHAR(200) NOT NULL,
    Descrizione TEXT,
    Prezzo DECIMAL(6,2) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE tIngrediente (
    idIngrediente INT AUTO_INCREMENT PRIMARY KEY,
    NomeIngrediente VARCHAR(200) NOT NULL,
    Descrizione TEXT
) ENGINE=InnoDB;

CREATE TABLE tMenu (
    idMenu INT AUTO_INCREMENT PRIMARY KEY,
    -- Il menu viene pubblicato il mercoledì.
    -- Gli ordini sono accettati da DataPubblicazione fino al giovedì sera (23:59:59),
    -- cioè mentre NOW() < DataPubblicazione + INTERVAL 2 DAY.
    -- Dal venerdì 00:00 il menu è visibile ma non ordinabile.
    DataPubblicazione DATE NOT NULL
) ENGINE=InnoDB;

-- ── Tabelle ponte (relazioni) ───────────────

CREATE TABLE tSelezione (
    idProdotto INT NOT NULL,
    idUtente INT DEFAULT NULL,              -- NULL per ordini ospite
    idOrdine INT NOT NULL,
    Quantita INT NOT NULL,
    PRIMARY KEY (idProdotto, idOrdine),
    FOREIGN KEY (idProdotto) REFERENCES tProdotto(idProdotto),
    FOREIGN KEY (idUtente) REFERENCES tUtente(idUtente),
    FOREIGN KEY (idOrdine) REFERENCES tOrdine(idOrdine)
) ENGINE=InnoDB;

CREATE TABLE tRicetta (
    idIngrediente INT NOT NULL,
    idProdotto INT NOT NULL,
    Quantita VARCHAR(100) NOT NULL,
    PRIMARY KEY (idIngrediente, idProdotto),
    FOREIGN KEY (idIngrediente) REFERENCES tIngrediente(idIngrediente),
    FOREIGN KEY (idProdotto) REFERENCES tProdotto(idProdotto)
) ENGINE=InnoDB;

CREATE TABLE tProduzione (
    idProdotto INT NOT NULL,
    idMenu INT NOT NULL,
    PRIMARY KEY (idProdotto, idMenu),
    FOREIGN KEY (idProdotto) REFERENCES tProdotto(idProdotto),
    FOREIGN KEY (idMenu) REFERENCES tMenu(idMenu)
) ENGINE=InnoDB;
