<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

setFlash('error', 'Per ordinare è necessario essere registrati. Crea un account o accedi.');
redirect('/auth/registrazione.php');
