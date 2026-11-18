
<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

// Jika mahasiswa sudah login, arahkan ke dashboard
redirectIfStudentLoggedIn();

$error = "";

// Membuat CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Memproses login mahasiswa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nim = trim($_POST['nim'] ?? '');
    $password = $_POST['password'] ?? '';
    $token = $_POST['csrf_token'] ?? '';

    if (
        empty($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $token)
    ) {

        $error = "Permintaan tidak valid. Silakan muat ulang halaman.";

    } elseif ($nim === '' || $password === '') {

        $error = "NIM dan password wajib diisi.";

    } else {

        $database = new Database();
        $db = $database->getConnection();

        $query = $db->prepare(
            "SELECT id, nim, nama, password
             FROM mahasiswa
             WHERE nim = :nim
             LIMIT 1"
        );

        $query->execute([
            ':nim' => $nim
        ]);

        $mahasiswa = $query->fetch(PDO::FETCH_ASSOC);

        if ($mahasiswa && password_verify($password, $mahasiswa['password'])) {

            session_regenerate_id(true);

            $_SESSION['mahasiswa_id'] = $mahasiswa['id'];
            $_SESSION['mahasiswa_nim'] = $mahasiswa['nim'];
            $_SESSION['mahasiswa_nama'] = $mahasiswa['nama'];

            // Membuat token baru setelah login
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            header("Location: /PWL/mahasiswa/dashboard.php");
            exit;

        } else {

            $error = "NIM atau password yang kamu masukkan salah.";

        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login Mahasiswa - Student Portal</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >

    <link
        href="/PWL/assets/css/style.css?v=12"
        rel="stylesheet"
    >

    <style>

        body {
            min-height: 100vh;
            background: #f1f5f9 !important;
        }

        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 15px;
        }

        .login-card {
            width: 100%;
            max-width: 950px;
            background: #ffffff;
            border-radius: 18px;
            overflow: hidden;
            border: none;
            box-shadow: 0 15px 45px rgba(15, 23, 42, 0.10);
        }

        .login-brand {
            background: linear-gradient(145deg, #0d6efd, #1746a2);
            color: white;
            padding: 55px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 520px;
        }

        .brand-icon {
            width: 75px;
            height: 75px;
            background: rgba(255,255,255,0.15);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 35px;
            margin-bottom: 25px;
        }

        .login-form-area {
            padding: 55px 45px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-input {
            padding: 12px 14px;
            border-radius: 9px;
        }

        .login-input:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.12);
        }

        .login-button {
            padding: 12px;
            border-radius: 9px;
            font-weight: 600;
        }

        .password-wrapper {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: #64748b;
            cursor: pointer;
        }

        .admin-link {
            color: #0d6efd;
            font-weight: 600;
        }

        @media (max-width: 767px) {

            .login-brand {
                min-height: auto;
                padding: 35px 25px;
            }

            .login-form-area {
                padding: 35px 25px;
            }

        }

    </style>

</head>

<body>

<div class="login-wrapper">

    <div class="login-card">

        <div class="row g-0">

            <div class="col-md-5">

                <div class="login-brand">

                    <div class="brand-icon">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>

                    <h2 class="fw-bold mb-3">
                        Student Portal
                    </h2>

                    <p class="mb-4 text-white-50">
                        Sistem Informasi Akademik Mahasiswa
                    </p>

                    <hr class="border-light opacity-25">

                    <div class="mt-3">

                        <div class="d-flex align-items-center gap-3 mb-3">
                            <i class="bi bi-person-badge fs-4"></i>
                            <span>Akses akun mahasiswa</span>
                        </div>

                        <div class="d-flex align-items-center gap-3 mb-3">
                            <i class="bi bi-journal-check fs-4"></i>
                            <span>Melihat informasi nilai akademik</span>
                        </div>

                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-calendar-check fs-4"></i>
                            <span>Memantau informasi kehadiran</span>
                        </div>

                    </div>

                </div>

            </div>

            <div class="col-md-7">

                <div class="login-form-area">

                    <div class="mb-4">

                        <span class="badge text-bg-primary mb-3">
                            Portal Mahasiswa
                        </span>

                        <h3 class="fw-bold mb-2">
                            Selamat Datang!
                        </h3>

                        <p class="text-secondary mb-0">
                            Silakan masuk menggunakan NIM dan password
                            untuk melihat informasi akademik kamu.
                        </p>

                    </div>

                    <?php if ($error !== ''): ?>

                        <div class="alert alert-danger d-flex align-items-center gap-2">

                            <i class="bi bi-exclamation-circle-fill"></i>

                            <div>
                                <?= htmlspecialchars($error) ?>
                            </div>

                        </div>

                    <?php endif; ?>

                    <form method="POST" action="">

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>"
                        >

                        <div class="mb-3">

                            <label for="nim" class="form-label fw-semibold">
                                Nomor Induk Mahasiswa (NIM)
                            </label>

                            <div class="input-group">

                                <span class="input-group-text bg-white">
                                    <i class="bi bi-person-vcard text-secondary"></i>
                                </span>

                                <input
                                    type="text"
                                    class="form-control login-input"
                                    id="nim"
                                    name="nim"
                                    placeholder="Masukkan NIM"
                                    value="<?= htmlspecialchars($_POST['nim'] ?? '') ?>"
                                    autocomplete="username"
                                    required
                                    autofocus
                                >

                            </div>

                        </div>

                        <div class="mb-4">

                            <label for="password" class="form-label fw-semibold">
                                Password
                            </label>

                            <div class="password-wrapper">

                                <input
                                    type="password"
                                    class="form-control login-input pe-5"
                                    id="password"
                                    name="password"
                                    placeholder="Masukkan password"
                                    autocomplete="current-password"
                                    required
                                >

                                <button
                                    type="button"
                                    class="password-toggle"
                                    id="togglePassword"
                                    aria-label="Tampilkan password"
                                >
                                    <i class="bi bi-eye" id="passwordIcon"></i>
                                </button>

                            </div>

                        </div>

                        <button
                            type="submit"
                            class="btn btn-primary w-100 login-button"
                        >
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            Login Mahasiswa
                        </button>

                    </form>

                    <div class="text-center mt-4">

                        <p class="text-secondary mb-2">
                            Administrator?
                        </p>

                        <a
                            href="/PWL/login.php"
                            class="text-decoration-none admin-link"
                        >
                            <i class="bi bi-shield-lock me-1"></i>
                            Login sebagai Admin
                        </a>

                    </div>

                    <div class="text-center mt-4">

                        <small class="text-secondary">
                            Student Portal &copy; <?= date('Y') ?>
                        </small>

                        <p class="text-secondary small mt-1 mb-0">
                            Sistem Informasi Akademik
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<script>

    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');
    const passwordIcon = document.getElementById('passwordIcon');

    togglePassword.addEventListener('click', function () {

        if (passwordInput.type === 'password') {

            passwordInput.type = 'text';

            passwordIcon.classList.remove('bi-eye');
            passwordIcon.classList.add('bi-eye-slash');

            togglePassword.setAttribute(
                'aria-label',
                'Sembunyikan password'
            );

        } else {

            passwordInput.type = 'password';

            passwordIcon.classList.remove('bi-eye-slash');
            passwordIcon.classList.add('bi-eye');

            togglePassword.setAttribute(
                'aria-label',
                'Tampilkan password'
            );

        }

    });

</script>

</body>
</html>
