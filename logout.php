<?php

require_once __DIR__ . '/includes/auth.php';

// Hapus seluruh data session
$_SESSION = [];

// Hapus session
session_destroy();

// Kembali ke halaman login
header("Location: /PWL/login.php");
exit;