<?php

require_once __DIR__ . '/../../includes/auth.php';

requireAdmin();

$id = $_GET['id'] ?? '';

if (!ctype_digit((string) $id) || (int) $id < 1) {
    header("Location: /PWL/admin/nilai/index.php");
    exit;
}

// Arahkan ke formulir edit mahasiswa yang sudah digabung
header("Location: /PWL/admin/mahasiswa/edit.php?id=" . (int) $id);
exit;
