<?php
date_default_timezone_set('Europe/Rome');

define('DB_HOST', 'localhost');
define('DB_NAME', 'appane_zangrando');
define('DB_USER', 'root');
define('DB_PASS', '');

define('SITE_NAME', 'AppPane');
define('SITE_URL', '/appane-frontend');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            die('Errore di connessione al database.');
        }
    }
    return $pdo;
}
