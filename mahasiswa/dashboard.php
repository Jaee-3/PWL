<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

// Memastikan mahasiswa sudah login
requireStudent();

// Koneksi database
$database = new Database();
$db = $database->getConnection();

// Mengambil data mahasiswa
$query = $db->prepare(
    "SELECT
        mahasiswa.id,
        mahasiswa.nim,
        mahasiswa.nama,
        mahasiswa.email,
        mahasiswa.nilai_tugas,
        mahasiswa.nilai_uts,
        mahasiswa.nilai_uas,
        mahasiswa.kehadiran,
        program_studi.kd_prodi,
        program_studi.nama_prodi
    FROM mahasiswa
    INNER JOIN program_studi
        ON mahasiswa.id_prodi = program_studi.id_prodi
    WHERE mahasiswa.id = :id
    LIMIT 1"
);

$query->execute([
    ':id' => $_SESSION['mahasiswa_id']
]);

$mahasiswa = $query->fetch(PDO::FETCH_ASSOC);

// Jika data tidak ditemukan, akhiri session
if (!$mahasiswa) {
    $_SESSION = [];
    session_destroy();

    header("Location: /PWL/mahasiswa/login.php");
    exit;
}

// Mengubah nilai menjadi angka
$nilaiTugas = (float) $mahasiswa['nilai_tugas'];
$nilaiUTS = (float) $mahasiswa['nilai_uts'];
$nilaiUAS = (float) $mahasiswa['nilai_uas'];
$kehadiran = (float) $mahasiswa['kehadiran'];

// Menghitung nilai akhir
$nilaiAkhir =
    ($nilaiTugas * 0.30) +
    ($nilaiUTS * 0.30) +
    ($nilaiUAS * 0.40);

// Menentukan predikat
if ($nilaiAkhir >= 85) {
    $predikat = "A";
    $statusNilai = "Sangat Baik";
    $warnaPredikat = "success";
} elseif ($nilaiAkhir >= 75) {
    $predikat = "B";
    $statusNilai = "Baik";
    $warnaPredikat = "primary";
} elseif ($nilaiAkhir >= 65) {
    $predikat = "C";
    $statusNilai = "Cukup";
    $warnaPredikat = "info";
} elseif ($nilaiAkhir >= 50) {
    $predikat = "D";
    $statusNilai = "Kurang";
    $warnaPredikat = "warning";
} else {
    $predikat = "E";
    $statusNilai = "Sangat Kurang";
    $warnaPredikat = "danger";
}

// Membatasi progress bar
$progressKehadiran = max(0, min(100, $kehadiran));

// Pengaturan tampilan
$pageTitle = 'Dashboard Mahasiswa - Student Portal';
$navbarType = 'student';

// Memanggil header
require_once __DIR__ . '/../includes/header.php';

?>

<!-- KONTEN DASHBOARD -->
<div class="container py-5">

    <!-- JUDUL -->
    <div class="mb-4">

        <span class="badge text-bg-primary mb-2">
            <i class="bi bi-house-door me-1"></i>
            Dashboard
        </span>

        <h2 class="fw-bold mb-2">
            Selamat Datang,
            <?= htmlspecialchars($mahasiswa['nama']) ?>!
        </h2>

        <p class="text-secondary mb-0">
            Berikut adalah informasi profil dan perkembangan akademik kamu.
        </p>

    </div>

    <!-- PROFIL MAHASISWA -->
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white border-0 py-3">

            <h5 class="fw-bold mb-0">
                <i class="bi bi-person-vcard text-primary me-2"></i>
                Profil Mahasiswa
            </h5>

        </div>

        <div class="card-body p-4">

            <div class="row g-4">

                <!-- NIM -->
                <div class="col-md-6">

                    <div class="d-flex align-items-start gap-3">

                        <div class="rounded-3 bg-primary-subtle text-primary
                                    d-flex align-items-center justify-content-center"
                             style="width: 44px; height: 44px;">

                            <i class="bi bi-upc-scan fs-5"></i>

                        </div>

                        <div>
                            <small class="text-secondary">
                                Nomor Induk Mahasiswa
                            </small>

                            <div class="fw-semibold">
                                <?= htmlspecialchars($mahasiswa['nim']) ?>
                            </div>
                        </div>

                    </div>

                </div>

                <!-- NAMA -->
                <div class="col-md-6">

                    <div class="d-flex align-items-start gap-3">

                        <div class="rounded-3 bg-primary-subtle text-primary
                                    d-flex align-items-center justify-content-center"
                             style="width: 44px; height: 44px;">

                            <i class="bi bi-person fs-5"></i>

                        </div>

                        <div>
                            <small class="text-secondary">
                                Nama Lengkap
                            </small>

                            <div class="fw-semibold">
                                <?= htmlspecialchars($mahasiswa['nama']) ?>
                            </div>
                        </div>

                    </div>

                </div>

                <!-- EMAIL -->
                <div class="col-md-6">

                    <div class="d-flex align-items-start gap-3">

                        <div class="rounded-3 bg-primary-subtle text-primary
                                    d-flex align-items-center justify-content-center"
                             style="width: 44px; height: 44px;">

                            <i class="bi bi-envelope fs-5"></i>

                        </div>

                        <div>
                            <small class="text-secondary">
                                Email
                            </small>

                            <div class="fw-semibold">
                                <?= htmlspecialchars($mahasiswa['email']) ?>
                            </div>
                        </div>

                    </div>

                </div>

                <!-- KODE PRODI -->
                <div class="col-md-6">

                    <div class="d-flex align-items-start gap-3">

                        <div class="rounded-3 bg-primary-subtle text-primary
                                    d-flex align-items-center justify-content-center"
                             style="width: 44px; height: 44px;">

                            <i class="bi bi-building fs-5"></i>

                        </div>

                        <div>
                            <small class="text-secondary">
                                Kode Program Studi
                            </small>

                            <div class="fw-semibold">
                                <?= htmlspecialchars($mahasiswa['kd_prodi']) ?>
                            </div>
                        </div>

                    </div>

                </div>

                <!-- PROGRAM STUDI -->
                <div class="col-12">

                    <div class="d-flex align-items-start gap-3">

                        <div class="rounded-3 bg-primary-subtle text-primary
                                    d-flex align-items-center justify-content-center"
                             style="width: 44px; height: 44px;">

                            <i class="bi bi-mortarboard fs-5"></i>

                        </div>

                        <div>
                            <small class="text-secondary">
                                Program Studi
                            </small>

                            <div class="fw-semibold">
                                <?= htmlspecialchars($mahasiswa['nama_prodi']) ?>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- RINGKASAN AKADEMIK -->
    <div class="mb-3">

        <h5 class="fw-bold mb-1">
            <i class="bi bi-journal-check text-primary me-2"></i>
            Ringkasan Akademik
        </h5>

        <p class="text-secondary small mb-0">
            Informasi nilai berdasarkan data yang diperbarui administrator.
        </p>

    </div>

    <!-- KARTU NILAI -->
    <div class="row g-3 mb-4">

        <!-- TUGAS -->
        <div class="col-6 col-lg-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body p-3 p-md-4">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-secondary small">Nilai Tugas</span>
                        <i class="bi bi-file-earmark-text text-primary fs-4"></i>
                    </div>

                    <h3 class="fw-bold text-primary mb-1">
                        <?= number_format($nilaiTugas, 2) ?>
                    </h3>

                    <small class="text-secondary">Bobot 30%</small>

                </div>

            </div>

        </div>

        <!-- UTS -->
        <div class="col-6 col-lg-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body p-3 p-md-4">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-secondary small">Nilai UTS</span>
                        <i class="bi bi-pencil-square text-primary fs-4"></i>
                    </div>

                    <h3 class="fw-bold text-primary mb-1">
                        <?= number_format($nilaiUTS, 2) ?>
                    </h3>

                    <small class="text-secondary">Bobot 30%</small>

                </div>

            </div>

        </div>

        <!-- UAS -->
        <div class="col-6 col-lg-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body p-3 p-md-4">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-secondary small">Nilai UAS</span>
                        <i class="bi bi-journal-bookmark text-primary fs-4"></i>
                    </div>

                    <h3 class="fw-bold text-primary mb-1">
                        <?= number_format($nilaiUAS, 2) ?>
                    </h3>

                    <small class="text-secondary">Bobot 40%</small>

                </div>

            </div>

        </div>

        <!-- KEHADIRAN -->
        <div class="col-6 col-lg-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body p-3 p-md-4">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-secondary small">Kehadiran</span>
                        <i class="bi bi-calendar-check text-success fs-4"></i>
                    </div>

                    <h3 class="fw-bold text-success mb-1">
                        <?= number_format($kehadiran, 2) ?>%
                    </h3>

                    <small class="text-secondary">
                        Persentase kehadiran
                    </small>

                </div>

            </div>

        </div>

    </div>

    <!-- NILAI AKHIR -->
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body p-4 p-md-5">

            <div class="row align-items-center g-4">

                <div class="col-md-7">

                    <span class="badge text-bg-primary mb-3">
                        Hasil Akademik
                    </span>

                    <h4 class="fw-bold mb-2">
                        Nilai Akhir Semester
                    </h4>

                    <p class="text-secondary mb-3">
                        Nilai akhir dihitung dari gabungan nilai tugas,
                        UTS, dan UAS sesuai bobot penilaian.
                    </p>

                    <div class="d-flex flex-wrap gap-2">

                        <span class="badge text-bg-light border text-dark">
                            Tugas 30%
                        </span>

                        <span class="badge text-bg-light border text-dark">
                            UTS 30%
                        </span>

                        <span class="badge text-bg-light border text-dark">
                            UAS 40%
                        </span>

                    </div>

                </div>

                <div class="col-md-5 text-md-end">

                    <div class="display-4 fw-bold text-primary mb-2">
                        <?= number_format($nilaiAkhir, 2) ?>
                    </div>

                    <span class="badge text-bg-<?= $warnaPredikat ?> fs-6 px-3 py-2">
                        Predikat <?= $predikat ?>
                    </span>

                    <p class="fw-semibold mt-2 mb-0">
                        <?= htmlspecialchars($statusNilai) ?>
                    </p>

                </div>

            </div>

        </div>

    </div>

    <!-- KEHADIRAN -->
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center mb-3">

                <div>
                    <h5 class="fw-bold mb-1">
                        <i class="bi bi-calendar-check text-success me-2"></i>
                        Kehadiran Mahasiswa
                    </h5>

                    <small class="text-secondary">
                        Persentase kehadiran yang tercatat.
                    </small>
                </div>

                <h4 class="fw-bold text-success mb-0">
                    <?= number_format($kehadiran, 2) ?>%
                </h4>

            </div>

            <div
                class="progress"
                role="progressbar"
                aria-label="Persentase kehadiran"
                aria-valuenow="<?= $progressKehadiran ?>"
                aria-valuemin="0"
                aria-valuemax="100"
                style="height: 12px;"
            >

                <div
                    class="progress-bar bg-success"
                    style="width: <?= $progressKehadiran ?>%;"
                ></div>

            </div>

        </div>

    </div>

    <!-- INFORMASI -->
    <div class="alert alert-info border-0 shadow-sm">

        <div class="d-flex align-items-start gap-2">

            <i class="bi bi-info-circle-fill fs-5"></i>

            <div>

                <strong>Informasi Akademik</strong>

                <p class="mb-0 mt-1">
                    Nilai akademik dan kehadiran ditampilkan berdasarkan
                    data yang dimasukkan dan diperbarui oleh administrator.
                    Jika terdapat ketidaksesuaian, silakan hubungi administrator.
                </p>

            </div>

        </div>

    </div>

</div>

<?php

// Memanggil footer
require_once __DIR__ . '/../includes/footer.php';

?>
