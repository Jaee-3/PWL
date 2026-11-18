<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAdmin();

// Penghapusan hanya boleh melalui POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /PWL/admin/mahasiswa/index.php");
    exit;
}

// Memeriksa ID mahasiswa
$id = $_POST['id'] ?? '';

if (!ctype_digit((string) $id) || (int) $id < 1) {
    $_SESSION['flash_message'] = "ID mahasiswa tidak valid.";
    $_SESSION['flash_type'] = "danger";

    header("Location: /PWL/admin/mahasiswa/index.php");
    exit;
}

// Memeriksa token keamanan
$token = $_POST['csrf_token'] ?? '';

if (
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $token)
) {
    $_SESSION['flash_message'] = "Permintaan tidak valid. Silakan coba lagi.";
    $_SESSION['flash_type'] = "danger";

    header("Location: /PWL/admin/mahasiswa/index.php");
    exit;
}

try {

    $database = new Database();
    $db = $database->getConnection();

    // Menghapus mahasiswa berdasarkan ID
    $query = $db->prepare(
        "DELETE FROM mahasiswa WHERE id = :id"
    );

    $query->execute([
        ':id' => $id
    ]);

    if ($query->rowCount() > 0) {
        $_SESSION['flash_message'] = "Data mahasiswa berhasil dihapus.";
        $_SESSION['flash_type'] = "success";
    } else {
        $_SESSION['flash_message'] = "Data mahasiswa tidak ditemukan.";
        $_SESSION['flash_type'] = "warning";
    }

} catch (PDOException $e) {

    $_SESSION['flash_message'] = "Terjadi kesalahan saat menghapus data.";
    $_SESSION['flash_type'] = "danger";

}

header("Location: /PWL/admin/mahasiswa/index.php");
exit;

