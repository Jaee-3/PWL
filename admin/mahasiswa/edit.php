<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();

$error = "";

// Memeriksa ID mahasiswa
$id = $_GET['id'] ?? '';

if (!ctype_digit((string) $id) || (int) $id < 1) {
    header("Location: /PWL/admin/mahasiswa/index.php");
    exit;
}

// Mengambil daftar program studi
$queryProdi = $db->query(
    "SELECT id_prodi, kd_prodi, nama_prodi
     FROM program_studi
     ORDER BY nama_prodi ASC"
);

$daftarProdi = $queryProdi->fetchAll(PDO::FETCH_ASSOC);

// Mengambil data mahasiswa
$query = $db->prepare(
    "SELECT *
     FROM mahasiswa
     WHERE id = :id
     LIMIT 1"
);

$query->execute([':id' => $id]);

$mahasiswa = $query->fetch(PDO::FETCH_ASSOC);

if (!$mahasiswa) {
    header("Location: /PWL/admin/mahasiswa/index.php");
    exit;
}

// Membuat CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Mengambil kode program studi mahasiswa
$kodeProdiAwal = '';

foreach ($daftarProdi as $itemProdi) {
    if ((string) $itemProdi['id_prodi'] === (string) $mahasiswa['id_prodi']) {
        $kodeProdiAwal = $itemProdi['kd_prodi'];
        break;
    }
}

// Menyiapkan data formulir
$form = [
    'nomor_mahasiswa' => $kodeProdiAwal !== ''
        ? substr($mahasiswa['nim'], strlen($kodeProdiAwal))
        : $mahasiswa['nim'],
    'nama' => $mahasiswa['nama'],
    'email' => $mahasiswa['email'],
    'id_prodi' => $mahasiswa['id_prodi'],
    'nilai_tugas' => $mahasiswa['nilai_tugas'],
    'nilai_uts' => $mahasiswa['nilai_uts'],
    'nilai_uas' => $mahasiswa['nilai_uas'],
    'kehadiran' => $mahasiswa['kehadiran']
];

// Memproses formulir
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $token = $_POST['csrf_token'] ?? '';

    $form['nomor_mahasiswa'] = trim($_POST['nomor_mahasiswa'] ?? '');
    $form['nama'] = trim($_POST['nama'] ?? '');
    $form['email'] = trim($_POST['email'] ?? '');
    $form['id_prodi'] = $_POST['id_prodi'] ?? '';

    $form['nilai_tugas'] = trim($_POST['nilai_tugas'] ?? '');
    $form['nilai_uts'] = trim($_POST['nilai_uts'] ?? '');
    $form['nilai_uas'] = trim($_POST['nilai_uas'] ?? '');
    $form['kehadiran'] = trim($_POST['kehadiran'] ?? '');

    $password = $_POST['password'] ?? '';

    // Validasi CSRF
    if (
        empty($token) ||
        !hash_equals($_SESSION['csrf_token'], $token)
    ) {

        $error = "Permintaan tidak valid. Silakan muat ulang halaman.";

    } elseif (
        $form['nomor_mahasiswa'] === '' ||
        $form['nama'] === '' ||
        $form['email'] === '' ||
        $form['id_prodi'] === ''
    ) {

        $error = "Data identitas mahasiswa wajib diisi.";

    } elseif (
        !ctype_digit($form['nomor_mahasiswa']) ||
        strlen($form['nomor_mahasiswa']) !== 7
    ) {

        $error = "Nomor mahasiswa harus terdiri dari 7 digit angka.";

    } elseif (!filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {

        $error = "Format email tidak valid.";

    } elseif ($password !== '' && strlen($password) < 8) {

        $error = "Password baru minimal harus 8 karakter.";

    } elseif (!ctype_digit((string) $form['id_prodi'])) {

        $error = "Program studi tidak valid.";

    } elseif (
        !is_numeric($form['nilai_tugas']) ||
        !is_numeric($form['nilai_uts']) ||
        !is_numeric($form['nilai_uas']) ||
        !is_numeric($form['kehadiran'])
    ) {

        $error = "Nilai akademik dan kehadiran harus berupa angka.";

    } elseif (
        $form['nilai_tugas'] < 0 || $form['nilai_tugas'] > 100 ||
        $form['nilai_uts'] < 0 || $form['nilai_uts'] > 100 ||
        $form['nilai_uas'] < 0 || $form['nilai_uas'] > 100 ||
        $form['kehadiran'] < 0 || $form['kehadiran'] > 100
    ) {

        $error = "Nilai akademik dan kehadiran harus berada antara 0 sampai 100.";

    } else {

        // Memeriksa program studi
        $cekProdi = $db->prepare(
            "SELECT kd_prodi
             FROM program_studi
             WHERE id_prodi = :id_prodi"
        );

        $cekProdi->execute([
            ':id_prodi' => $form['id_prodi']
        ]);

        $prodi = $cekProdi->fetch(PDO::FETCH_ASSOC);

        if (!$prodi) {

            $error = "Program studi tidak valid.";

        } else {

            // Membentuk NIM lengkap
            $nim = $prodi['kd_prodi'] . $form['nomor_mahasiswa'];

            try {

                // Jika password diisi, password ikut diperbarui
                if ($password !== '') {

                    $passwordHash = password_hash(
                        $password,
                        PASSWORD_DEFAULT
                    );

                    $update = $db->prepare(
                        "UPDATE mahasiswa SET
                            nim = :nim,
                            nama = :nama,
                            email = :email,
                            password = :password,
                            id_prodi = :id_prodi,
                            nilai_tugas = :nilai_tugas,
                            nilai_uts = :nilai_uts,
                            nilai_uas = :nilai_uas,
                            kehadiran = :kehadiran
                         WHERE id = :id"
                    );

                    $update->execute([
                        ':nim' => $nim,
                        ':nama' => $form['nama'],
                        ':email' => $form['email'],
                        ':password' => $passwordHash,
                        ':id_prodi' => $form['id_prodi'],
                        ':nilai_tugas' => $form['nilai_tugas'],
                        ':nilai_uts' => $form['nilai_uts'],
                        ':nilai_uas' => $form['nilai_uas'],
                        ':kehadiran' => $form['kehadiran'],
                        ':id' => $id
                    ]);

                } else {

                    // Jika kosong, password lama tetap digunakan
                    $update = $db->prepare(
                        "UPDATE mahasiswa SET
                            nim = :nim,
                            nama = :nama,
                            email = :email,
                            id_prodi = :id_prodi,
                            nilai_tugas = :nilai_tugas,
                            nilai_uts = :nilai_uts,
                            nilai_uas = :nilai_uas,
                            kehadiran = :kehadiran
                         WHERE id = :id"
                    );

                    $update->execute([
                        ':nim' => $nim,
                        ':nama' => $form['nama'],
                        ':email' => $form['email'],
                        ':id_prodi' => $form['id_prodi'],
                        ':nilai_tugas' => $form['nilai_tugas'],
                        ':nilai_uts' => $form['nilai_uts'],
                        ':nilai_uas' => $form['nilai_uas'],
                        ':kehadiran' => $form['kehadiran'],
                        ':id' => $id
                    ]);
                }

                $_SESSION['flash_message'] =
                    "Data mahasiswa dan nilai akademik berhasil diperbarui.";

                $_SESSION['flash_type'] = "success";

                header("Location: /PWL/admin/mahasiswa/index.php");
                exit;

            } catch (PDOException $e) {

                if ($e->getCode() === '23000') {
                    $error = "NIM atau email sudah digunakan mahasiswa lain.";
                } else {
                    $error = "Terjadi kesalahan saat memperbarui data.";
                }

            }
        }
    }
}

// Menentukan kode prodi yang sedang dipilih
$kodeProdiTerpilih = '';

foreach ($daftarProdi as $itemProdi) {
    if ((string) $itemProdi['id_prodi'] === (string) $form['id_prodi']) {
        $kodeProdiTerpilih = $itemProdi['kd_prodi'];
        break;
    }
}

// Menghitung nilai akhir awal
$nilaiAkhir =
    ((float) $form['nilai_tugas'] * 0.30) +
    ((float) $form['nilai_uts'] * 0.30) +
    ((float) $form['nilai_uas'] * 0.40);

// Pengaturan halaman
$pageTitle = 'Edit Mahasiswa - Student Portal';
$navbarType = 'admin';

// Memanggil header bersama
require_once __DIR__ . '/../../includes/header.php';

?>

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-10 col-xl-9">

            <!-- Judul -->
            <div class="mb-4">

                <span class="badge text-bg-primary mb-2">
                    <i class="bi bi-pencil-square me-1"></i>
                    Administrasi
                </span>

                <h2 class="fw-bold mb-2">
                    Edit Data Mahasiswa
                </h2>

                <p class="text-secondary mb-0">
                    Perbarui identitas dan informasi akademik mahasiswa.
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

                    <?php if ($error !== ''): ?>

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

                        <!-- BAGIAN A -->
                        <div class="d-flex align-items-center gap-3 mb-4">

                            <div class="rounded-3 bg-primary-subtle text-primary
                                        d-flex align-items-center justify-content-center"
                                 style="width: 48px; height: 48px;">

                                <i class="bi bi-person-vcard fs-4"></i>

                            </div>

                            <div>
                                <h5 class="fw-bold mb-1">
                                    A. Identitas Mahasiswa
                                </h5>

                                <small class="text-secondary">
                                    Informasi pribadi dan akun mahasiswa.
                                </small>
                            </div>

                        </div>

                        <!-- NIM -->
                        <div class="mb-4">

                            <label for="nomor_mahasiswa"
                                   class="form-label fw-semibold">

                                Nomor Mahasiswa

                            </label>

                            <div class="input-group">

                                <span class="input-group-text" id="kodeProdi">
                                    <?= htmlspecialchars($kodeProdiTerpilih) ?>
                                </span>

                                <input
                                    type="text"
                                    name="nomor_mahasiswa"
                                    id="nomor_mahasiswa"
                                    class="form-control"
                                    value="<?= htmlspecialchars($form['nomor_mahasiswa']) ?>"
                                    maxlength="7"
                                    pattern="[0-9]{7}"
                                    inputmode="numeric"
                                    required
                                >

                            </div>

                            <small class="text-secondary">
                                Nomor mahasiswa terdiri dari 7 digit.
                                Kode program studi ditambahkan otomatis.
                            </small>

                        </div>

                        <!-- Preview NIM -->
                        <div class="mb-4">

                            <label class="form-label fw-semibold">
                                Preview NIM Lengkap
                            </label>

                            <div class="form-control bg-light" id="previewNim">
                                <?= htmlspecialchars($kodeProdiTerpilih . $form['nomor_mahasiswa']) ?>
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
                                    value="<?= htmlspecialchars($form['nama']) ?>"
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
                                    value="<?= htmlspecialchars($form['email']) ?>"
                                    required
                                >

                            </div>

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

                                    <?php foreach ($daftarProdi as $prodiItem): ?>

                                        <option
                                            value="<?= (int) $prodiItem['id_prodi'] ?>"
                                            data-kode="<?= htmlspecialchars($prodiItem['kd_prodi']) ?>"
                                            <?= (string) $form['id_prodi'] === (string) $prodiItem['id_prodi'] ? 'selected' : '' ?>
                                        >

                                            <?= htmlspecialchars($prodiItem['kd_prodi']) ?>
                                            -
                                            <?= htmlspecialchars($prodiItem['nama_prodi']) ?>

                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>

                        </div>

                        <!-- Password -->
                        <div class="mb-4">

                            <label for="password" class="form-label fw-semibold">
                                Password Baru
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
                                    minlength="8"
                                    placeholder="Kosongkan jika tidak ingin mengubah password"
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
                                Password lama tetap digunakan jika kolom ini dikosongkan.
                                Password baru minimal 8 karakter.
                            </small>

                        </div>

                        <hr class="my-5">

                        <!-- BAGIAN B -->
                        <div class="d-flex align-items-center gap-3 mb-4">

                            <div class="rounded-3 bg-success-subtle text-success
                                        d-flex align-items-center justify-content-center"
                                 style="width: 48px; height: 48px;">

                                <i class="bi bi-journal-check fs-4"></i>

                            </div>

                            <div>
                                <h5 class="fw-bold mb-1">
                                    B. Nilai Akademik
                                </h5>

                                <small class="text-secondary">
                                    Perbarui nilai dan persentase kehadiran.
                                </small>
                            </div>

                        </div>

                        <div class="row">

                            <!-- Tugas -->
                            <div class="col-md-6 mb-4">

                                <label for="nilai_tugas"
                                       class="form-label fw-semibold">
                                    Nilai Tugas
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </span>

                                    <input
                                        type="number"
                                        name="nilai_tugas"
                                        id="nilai_tugas"
                                        class="form-control"
                                        min="0"
                                        max="100"
                                        step="0.01"
                                        value="<?= htmlspecialchars((string) $form['nilai_tugas']) ?>"
                                        required
                                    >

                                </div>

                            </div>

                            <!-- UTS -->
                            <div class="col-md-6 mb-4">

                                <label for="nilai_uts"
                                       class="form-label fw-semibold">
                                    Nilai UTS
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-pencil"></i>
                                    </span>

                                    <input
                                        type="number"
                                        name="nilai_uts"
                                        id="nilai_uts"
                                        class="form-control"
                                        min="0"
                                        max="100"
                                        step="0.01"
                                        value="<?= htmlspecialchars((string) $form['nilai_uts']) ?>"
                                        required
                                    >

                                </div>

                            </div>

                            <!-- UAS -->
                            <div class="col-md-6 mb-4">

                                <label for="nilai_uas"
                                       class="form-label fw-semibold">
                                    Nilai UAS
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-journal-bookmark"></i>
                                    </span>

                                    <input
                                        type="number"
                                        name="nilai_uas"
                                        id="nilai_uas"
                                        class="form-control"
                                        min="0"
                                        max="100"
                                        step="0.01"
                                        value="<?= htmlspecialchars((string) $form['nilai_uas']) ?>"
                                        required
                                    >

                                </div>

                            </div>

                            <!-- Kehadiran -->
                            <div class="col-md-6 mb-4">

                                <label for="kehadiran"
                                       class="form-label fw-semibold">
                                    Kehadiran (%)
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-calendar-check"></i>
                                    </span>

                                    <input
                                        type="number"
                                        name="kehadiran"
                                        id="kehadiran"
                                        class="form-control"
                                        min="0"
                                        max="100"
                                        step="0.01"
                                        value="<?= htmlspecialchars((string) $form['kehadiran']) ?>"
                                        required
                                    >

                                </div>

                            </div>

                        </div>

                        <!-- Preview Nilai Akhir -->
                        <div class="card border-0 bg-primary-subtle mb-4">

                            <div class="card-body p-4">

                                <div class="d-flex align-items-center justify-content-between">

                                    <div>

                                        <p class="text-primary fw-semibold mb-1">
                                            Preview Nilai Akhir
                                        </p>

                                        <small class="text-secondary">
                                            Dihitung otomatis berdasarkan nilai terbaru.
                                        </small>

                                    </div>

                                    <div class="text-end">

                                        <h2 class="fw-bold text-primary mb-0"
                                            id="nilaiAkhir">

                                            <?= number_format($nilaiAkhir, 2) ?>

                                        </h2>

                                        <small class="text-secondary">
                                            Skala 0–100
                                        </small>

                                    </div>

                                </div>

                                <hr>

                                <small class="text-secondary">
                                    Rumus: Tugas 30% + UTS 30% + UAS 40%.
                                    Kehadiran dihitung secara terpisah.
                                </small>

                            </div>

                        </div>

                        <!-- Tombol -->
                        <div class="d-flex flex-column flex-sm-row gap-2">

                            <button
                                type="submit"
                                class="btn btn-primary px-4"
                            >

                                <i class="bi bi-save me-2"></i>
                                Simpan Perubahan

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
                Pastikan seluruh perubahan sudah benar sebelum disimpan.

            </p>

        </div>

    </div>

</div>

<script>

// ==========================================
// PREVIEW NIM OTOMATIS
// ==========================================

const nomorMahasiswa = document.getElementById('nomor_mahasiswa');
const prodiSelect = document.getElementById('id_prodi');
const kodeProdi = document.getElementById('kodeProdi');
const previewNim = document.getElementById('previewNim');

function updatePreviewNim() {

    const selectedOption =
        prodiSelect.options[prodiSelect.selectedIndex];

    const kode = selectedOption.dataset.kode || '';

    kodeProdi.textContent = kode;

    if (kode !== '') {
        previewNim.textContent =
            kode + nomorMahasiswa.value;
    } else {
        previewNim.textContent =
            'Pilih program studi terlebih dahulu.';
    }

}

nomorMahasiswa.addEventListener('input', updatePreviewNim);
prodiSelect.addEventListener('change', updatePreviewNim);

updatePreviewNim();


// ==========================================
// PREVIEW NILAI AKHIR OTOMATIS
// ==========================================

const inputTugas = document.getElementById('nilai_tugas');
const inputUTS = document.getElementById('nilai_uts');
const inputUAS = document.getElementById('nilai_uas');
const hasilNilaiAkhir = document.getElementById('nilaiAkhir');

function updateNilaiAkhir() {

    const tugas = parseFloat(inputTugas.value) || 0;
    const uts = parseFloat(inputUTS.value) || 0;
    const uas = parseFloat(inputUAS.value) || 0;

    const hasil =
        (tugas * 0.30) +
        (uts * 0.30) +
        (uas * 0.40);

    hasilNilaiAkhir.textContent = hasil.toFixed(2);

}

inputTugas.addEventListener('input', updateNilaiAkhir);
inputUTS.addEventListener('input', updateNilaiAkhir);
inputUAS.addEventListener('input', updateNilaiAkhir);


// ==========================================
// TAMPILKAN / SEMBUNYIKAN PASSWORD
// ==========================================

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

</script>

<?php

// Memanggil footer bersama
require_once __DIR__ . '/../../includes/footer.php';

?>
