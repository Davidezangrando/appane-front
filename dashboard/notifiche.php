<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

setFlash('error', 'Le notifiche non sono disponibili in questa versione.');
redirect('/dashboard/');
