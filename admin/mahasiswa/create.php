<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();

$error = "";

// Membuat CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Mengambil daftar program studi
$queryProdi = $db->query(
    "SELECT id_prodi, kd_prodi, nama_prodi
     FROM program_studi
     ORDER BY nama_prodi ASC"
);

$daftarProdi = $queryProdi->fetchAll(PDO::FETCH_ASSOC);

// Memproses formulir
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nomorMahasiswa = trim($_POST['nim'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $id_prodi = $_POST['id_prodi'] ?? '';
    $csrfToken = $_POST['csrf_token'] ?? '';

    // Validasi CSRF
    if (!hash_equals($_SESSION['csrf_token'], $csrfToken)) {

        $error = "Permintaan tidak valid. Silakan muat ulang halaman.";

    } elseif (
        $nomorMahasiswa === '' ||
        $nama === '' ||
        $email === '' ||
        $password === '' ||
        $id_prodi === ''
    ) {

        $error = "Semua kolom wajib diisi.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Format email tidak valid.";

    } elseif (strlen($password) < 8) {

        $error = "Password minimal harus 8 karakter.";

    } elseif (!ctype_digit($nomorMahasiswa)) {

        $error = "Nomor mahasiswa hanya boleh berisi angka.";

    } elseif (strlen($nomorMahasiswa) !== 7) {

        $error = "Nomor mahasiswa harus terdiri dari 7 digit.";

    } elseif (!ctype_digit((string) $id_prodi)) {

        $error = "Program studi tidak valid.";

    } else {

        // Memeriksa program studi
        $cekProdi = $db->prepare(
            "SELECT kd_prodi
             FROM program_studi
             WHERE id_prodi = :id_prodi"
        );

        $cekProdi->execute([
            ':id_prodi' => $id_prodi
        ]);

        $prodi = $cekProdi->fetch(PDO::FETCH_ASSOC);

        if (!$prodi) {

            $error = "Program studi tidak valid.";

        } else {

            try {

                // Membuat NIM otomatis
                $nim = $prodi['kd_prodi'] . $nomorMahasiswa;

                // Mengamankan password
                $passwordHash = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                // Menyimpan mahasiswa
                $query = $db->prepare(
                    "INSERT INTO mahasiswa
                    (nim, nama, email, password, id_prodi)
                    VALUES
                    (:nim, :nama, :email, :password, :id_prodi)"
                );

                $query->execute([
                    ':nim' => $nim,
                    ':nama' => $nama,
                    ':email' => $email,
                    ':password' => $passwordHash,
                    ':id_prodi' => $id_prodi
                ]);

                // Notifikasi berhasil
                $_SESSION['flash_message'] =
                    "Mahasiswa berhasil ditambahkan.";

                $_SESSION['flash_type'] = "success";

                header("Location: /PWL/admin/mahasiswa/index.php");
                exit;

            } catch (PDOException $e) {

                if ($e->getCode() === '23000') {
                    $error = "NIM atau email sudah terdaftar.";
                } else {
                    $error = "Terjadi kesalahan saat menyimpan data.";
                }

            }
        }
    }
}

// Pengaturan halaman
$pageTitle = 'Tambah Mahasiswa - Student Portal';
$navbarType = 'admin';

// Memanggil header bersama
require_once __DIR__ . '/../../includes/header.php';

?>

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-8 col-xl-7">

            <!-- Judul halaman -->
            <div class="mb-4">

                <div class="d-flex align-items-center gap-2 mb-2">

                    <span class="badge text-bg-primary">
                        <i class="bi bi-person-plus-fill me-1"></i>
                        Administrasi
                    </span>

                </div>

                <h2 class="fw-bold mb-2">
                    Tambah Mahasiswa
                </h2>

                <p class="text-secondary mb-0">
                    Lengkapi informasi berikut untuk mendaftarkan
                    mahasiswa baru ke dalam Student Portal.
                </p>

                <a href="/PWL/admin/mahasiswa/index.php"
                   class="btn btn-outline-secondary btn-sm mt-3">

                    <i class="bi bi-arrow-left me-1"></i>
                    Kembali ke Data Mahasiswa

                </a>

            </div>

            <!-- Formulir -->
            <div class="card border-0 shadow-sm">

                <div class="card-body p-4 p-md-5">

                    <div class="d-flex align-items-center gap-3 mb-4">

                        <div class="rounded-3 bg-primary-subtle text-primary
                                    d-flex align-items-center justify-content-center"
                             style="width: 48px; height: 48px;">

                            <i class="bi bi-person-vcard fs-4"></i>

                        </div>

                        <div>

                            <h5 class="fw-bold mb-1">
                                Informasi Mahasiswa
                            </h5>

                            <small class="text-secondary">
                                Isi seluruh kolom yang tersedia.
                            </small>

                        </div>

                    </div>

                    <?php if ($error !== ""): ?>

                        <div class="alert alert-danger d-flex align-items-center"
                             role="alert">

                            <i class="bi bi-exclamation-triangle-fill me-2"></i>

                            <?= htmlspecialchars($error) ?>

                        </div>

                    <?php endif; ?>

                    <form method="POST">

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>"
                        >

                        <!-- NIM -->
                        <div class="mb-4">

                            <label for="nim" class="form-label fw-semibold">
                                Nomor Mahasiswa
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="bi bi-upc-scan"></i>
                                </span>

                                <input
                                    type="text"
                                    name="nim"
                                    id="nim"
                                    class="form-control"
                                    maxlength="7"
                                    pattern="[0-9]{7}"
                                    inputmode="numeric"
                                    placeholder="Contoh: 2026001"
                                    value="<?= htmlspecialchars($_POST['nim'] ?? '') ?>"
                                    required
                                >

                            </div>

                            <small class="text-secondary">
                                Masukkan 7 digit nomor mahasiswa.
                                Kode program studi akan ditambahkan otomatis.
                            </small>

                        </div>

                        <!-- Preview NIM -->
                        <div class="mb-4">

                            <label class="form-label fw-semibold">
                                Preview NIM Lengkap
                            </label>

                            <div class="form-control bg-light"
                                 id="previewNim">

                                Pilih program studi terlebih dahulu.

                            </div>

                        </div>

                        <!-- Nama -->
                        <div class="mb-4">

                            <label for="nama" class="form-label fw-semibold">
                                Nama Lengkap
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="bi bi-person"></i>
                                </span>

                                <input
                                    type="text"
                                    name="nama"
                                    id="nama"
                                    class="form-control"
                                    placeholder="Masukkan nama lengkap"
                                    value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>"
                                    required
                                >

                            </div>

                        </div>

                        <!-- Email -->
                        <div class="mb-4">

                            <label for="email" class="form-label fw-semibold">
                                Email
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="bi bi-envelope"></i>
                                </span>

                                <input
                                    type="email"
                                    name="email"
                                    id="email"
                                    class="form-control"
                                    placeholder="nama@email.com"
                                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                    required
                                >

                            </div>

                        </div>

                        <!-- Password -->
                        <div class="mb-4">

                            <label for="password" class="form-label fw-semibold">
                                Password Mahasiswa
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="bi bi-lock"></i>
                                </span>

                                <input
                                    type="password"
                                    name="password"
                                    id="password"
                                    class="form-control"
                                    placeholder="Minimal 8 karakter"
                                    minlength="8"
                                    required
                                >

                                <button
                                    class="btn btn-outline-secondary"
                                    type="button"
                                    id="togglePassword"
                                    aria-label="Tampilkan password"
                                >

                                    <i class="bi bi-eye" id="passwordIcon"></i>

                                </button>

                            </div>

                            <small class="text-secondary">
                                Password akan disimpan dalam bentuk hash
                                untuk keamanan.
                            </small>

                        </div>

                        <!-- Program Studi -->
                        <div class="mb-4">

                            <label for="id_prodi" class="form-label fw-semibold">
                                Program Studi
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="bi bi-building"></i>
                                </span>

                                <select
                                    name="id_prodi"
                                    id="id_prodi"
                                    class="form-select"
                                    required
                                >

                                    <option value="">
                                        -- Pilih Program Studi --
                                    </option>

                                    <?php foreach ($daftarProdi as $prodi): ?>

                                        <option
                                            value="<?= htmlspecialchars($prodi['id_prodi']) ?>"
                                            data-kode="<?= htmlspecialchars($prodi['kd_prodi']) ?>"
                                            <?= (($_POST['id_prodi'] ?? '') == $prodi['id_prodi']) ? 'selected' : '' ?>
                                        >

                                            <?= htmlspecialchars($prodi['kd_prodi']) ?>
                                            -
                                            <?= htmlspecialchars($prodi['nama_prodi']) ?>

                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>

                            <small class="text-secondary">
                                Kode program studi digunakan sebagai awalan NIM.
                            </small>

                        </div>

                        <hr class="my-4">

                        <!-- Tombol -->
                        <div class="d-flex flex-column flex-sm-row gap-2">

                            <button
                                type="submit"
                                class="btn btn-primary px-4"
                            >

                                <i class="bi bi-save me-2"></i>
                                Simpan Mahasiswa

                            </button>

                            <a
                                href="/PWL/admin/mahasiswa/index.php"
                                class="btn btn-outline-secondary px-4"
                            >

                                <i class="bi bi-x-circle me-2"></i>
                                Batal

                            </a>

                        </div>

                    </form>

                </div>

            </div>

            <p class="text-secondary text-center small mt-4">

                <i class="bi bi-shield-check me-1"></i>
                Pastikan informasi mahasiswa sudah benar sebelum disimpan.

            </p>

        </div>

    </div>

</div>

<!-- JavaScript khusus halaman tambah mahasiswa -->
<script>

// Preview NIM otomatis
const nimInput = document.getElementById('nim');
const prodiSelect = document.getElementById('id_prodi');
const previewNim = document.getElementById('previewNim');

function updatePreviewNim() {

    const nomor = nimInput.value.trim();

    const selectedOption =
        prodiSelect.options[prodiSelect.selectedIndex];

    const kodeProdi = selectedOption.dataset.kode || '';

    if (kodeProdi !== '' && nomor !== '') {

        previewNim.textContent = kodeProdi + nomor;

    } else if (kodeProdi !== '') {

        previewNim.textContent = kodeProdi + '.......';

    } else {

        previewNim.textContent =
            'Pilih program studi terlebih dahulu.';

    }

}

nimInput.addEventListener('input', updatePreviewNim);
prodiSelect.addEventListener('change', updatePreviewNim);

// Tampilkan atau sembunyikan password
const togglePassword = document.getElementById('togglePassword');
const passwordInput = document.getElementById('password');
const passwordIcon = document.getElementById('passwordIcon');

togglePassword.addEventListener('click', function () {

    if (passwordInput.type === 'password') {

        passwordInput.type = 'text';
        passwordIcon.className = 'bi bi-eye-slash';

        togglePassword.setAttribute(
            'aria-label',
            'Sembunyikan password'
        );

    } else {

        passwordInput.type = 'password';
        passwordIcon.className = 'bi bi-eye';

        togglePassword.setAttribute(
            'aria-label',
            'Tampilkan password'
        );

    }

});

// Menampilkan preview saat halaman pertama kali dibuka
updatePreviewNim();

</script>

<?php

// Memanggil footer bersama
require_once __DIR__ . '/../../includes/footer.php';

?>
