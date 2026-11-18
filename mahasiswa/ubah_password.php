<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

requireStudent();

$database = new Database();
$db = $database->getConnection();

$error = "";
$success = "";

// Membuat token keamanan
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Memproses perubahan password
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $passwordLama = $_POST['password_lama'] ?? '';
    $passwordBaru = $_POST['password_baru'] ?? '';
    $konfirmasiPassword = $_POST['konfirmasi_password'] ?? '';
    $token = $_POST['csrf_token'] ?? '';

    if (
        empty($token) ||
        !hash_equals($_SESSION['csrf_token'], $token)
    ) {
        $error = "Permintaan tidak valid. Silakan muat ulang halaman.";

    } elseif (
        $passwordLama === '' ||
        $passwordBaru === '' ||
        $konfirmasiPassword === ''
    ) {
        $error = "Semua kolom wajib diisi.";

    } elseif (strlen($passwordBaru) < 8) {
        $error = "Password baru minimal 8 karakter.";

    } elseif ($passwordBaru !== $konfirmasiPassword) {
        $error = "Konfirmasi password baru tidak cocok.";

    } elseif ($passwordLama === $passwordBaru) {
        $error = "Password baru harus berbeda dari password lama.";

    } else {

        // Mengambil password lama dari database
        $query = $db->prepare(
            "SELECT password
             FROM mahasiswa
             WHERE id = :id"
        );

        $query->execute([
            ':id' => $_SESSION['mahasiswa_id']
        ]);

        $data = $query->fetch(PDO::FETCH_ASSOC);

        if (!$data || !password_verify($passwordLama, $data['password'])) {

            $error = "Password lama yang kamu masukkan salah.";

        } else {

            // Mengenkripsi password baru
            $passwordHash = password_hash(
                $passwordBaru,
                PASSWORD_DEFAULT
            );

            // Memperbarui password di database
            $update = $db->prepare(
                "UPDATE mahasiswa
                 SET password = :password
                 WHERE id = :id"
            );

            $update->execute([
                ':password' => $passwordHash,
                ':id' => $_SESSION['mahasiswa_id']
            ]);

            $success = "Password berhasil diperbarui.";

            // Mengganti token setelah berhasil
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }
}

// Pengaturan halaman
$pageTitle = 'Ubah Password - Student Portal';
$navbarType = 'student';

require_once __DIR__ . '/../includes/header.php';

?>

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-md-6">

            <div class="card border-0 shadow-sm">

                <div class="card-body p-4">

                    <h3 class="mb-2">Ubah Password</h3>

                    <p class="text-muted mb-4">
                        Perbarui password akun mahasiswa kamu.
                    </p>

                    <?php if ($error !== ''): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($success !== ''): ?>
                        <div class="alert alert-success">
                            <?= htmlspecialchars($success) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>"
                        >

                        <div class="mb-3">
                            <label class="form-label">
                                Password Lama
                            </label>

                            <input
                                type="password"
                                name="password_lama"
                                class="form-control"
                                autocomplete="current-password"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Password Baru
                            </label>

                            <input
                                type="password"
                                name="password_baru"
                                class="form-control"
                                minlength="8"
                                autocomplete="new-password"
                                required
                            >

                            <small class="text-muted">
                                Minimal 8 karakter.
                            </small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">
                                Konfirmasi Password Baru
                            </label>

                            <input
                                type="password"
                                name="konfirmasi_password"
                                class="form-control"
                                minlength="8"
                                autocomplete="new-password"
                                required
                            >
                        </div>

                        <button
                            type="submit"
                            class="btn btn-primary w-100"
                        >
                            Simpan Password Baru
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
