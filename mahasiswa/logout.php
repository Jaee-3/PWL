<?php

require_once __DIR__ . '/../includes/auth.php';

// Menghapus seluruh data session
$_SESSION = [];

// Menghapus session
session_destroy();

// Kembali ke halaman login mahasiswa
header("Location: /PWL/mahasiswa/login.php");
exit;