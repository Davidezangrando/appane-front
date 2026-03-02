<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
redirect('/admin/ordini.php');
