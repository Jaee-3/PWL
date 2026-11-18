<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

// Koneksi database
$database = new Database();
$db = $database->getConnection();

// Menghitung jumlah mahasiswa
$queryMahasiswa = $db->query("SELECT COUNT(*) FROM mahasiswa");
$totalMahasiswa = $queryMahasiswa->fetchColumn();

// Menghitung jumlah program studi
$queryProdi = $db->query("SELECT COUNT(*) FROM program_studi");
$totalProdi = $queryProdi->fetchColumn();

// Mengambil daftar program studi
$queryDaftarProdi = $db->query(
    "SELECT kd_prodi, nama_prodi
     FROM program_studi
     ORDER BY nama_prodi ASC"
);

$daftarProdi = $queryDaftarProdi->fetchAll(PDO::FETCH_ASSOC);

// Pengaturan tampilan
$pageTitle = 'Dashboard Admin - Student Portal';
$navbarType = 'admin';

// Memanggil header
require_once __DIR__ . '/../includes/header.php';

?>

<!-- MAIN CONTENT -->
<div class="container py-5">

    <!-- HEADER DASHBOARD -->
    <div class="mb-4">

        <span class="badge bg-primary-subtle text-primary mb-2">
            ADMINISTRATOR
        </span>

        <h2 class="fw-bold mb-2">
            Dashboard Admin
        </h2>

        <p class="text-secondary mb-0">
            Selamat datang,
            <strong>
                <?= htmlspecialchars($_SESSION['admin_username']) ?>
            </strong>!
            Kelola data mahasiswa dan informasi akademik melalui halaman ini.
        </p>

    </div>

    <!-- STATISTIK -->
    <div class="row g-4 mb-4">

        <div class="col-md-6">

            <div class="stat-card">

                <div class="d-flex justify-content-between align-items-center">

                    <div>
                        <p>Total Mahasiswa</p>

                        <h3><?= $totalMahasiswa ?></h3>

                        <small class="text-secondary">
                            Mahasiswa terdaftar
                        </small>
                    </div>

                    <div class="rounded-circle bg-primary-subtle text-primary
                                d-flex align-items-center justify-content-center"
                         style="width: 60px; height: 60px;">

                        <i class="bi bi-people-fill fs-3"></i>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-md-6">

            <div class="stat-card">

                <div class="d-flex justify-content-between align-items-center">

                    <div>
                        <p>Total Program Studi</p>

                        <h3><?= $totalProdi ?></h3>

                        <small class="text-secondary">
                            Program studi tersedia
                        </small>
                    </div>

                    <div class="rounded-circle bg-success-subtle text-success
                                d-flex align-items-center justify-content-center"
                         style="width: 60px; height: 60px;">

                        <i class="bi bi-building-fill fs-3"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- MENU PENGELOLAAN -->
    <div class="mb-4">

        <h5 class="fw-bold mb-3">
            <i class="bi bi-grid-fill me-2 text-primary"></i>
            Menu Pengelolaan
        </h5>

        <div class="row g-3">

            <div class="col-md-6">

                <a href="/PWL/admin/mahasiswa/index.php"
                   class="text-decoration-none">

                    <div class="card h-100">

                        <div class="card-body d-flex align-items-center gap-3">

                            <div class="rounded-3 bg-primary-subtle text-primary
                                        d-flex align-items-center justify-content-center"
                                 style="width: 55px; height: 55px;">

                                <i class="bi bi-people-fill fs-3"></i>

                            </div>

                            <div>
                                <h6 class="fw-bold text-dark mb-1">
                                    Kelola Data Mahasiswa
                                </h6>

                                <p class="text-secondary small mb-0">
                                    Tambah, lihat, edit, dan hapus data mahasiswa.
                                </p>
                            </div>

                            <i class="bi bi-chevron-right ms-auto text-secondary"></i>

                        </div>

                    </div>

                </a>

            </div>

            <div class="col-md-6">

                <a href="/PWL/admin/nilai/index.php"
                   class="text-decoration-none">

                    <div class="card h-100">

                        <div class="card-body d-flex align-items-center gap-3">

                            <div class="rounded-3 bg-success-subtle text-success
                                        d-flex align-items-center justify-content-center"
                                 style="width: 55px; height: 55px;">

                                <i class="bi bi-journal-check fs-3"></i>

                            </div>

                            <div>
                                <h6 class="fw-bold text-dark mb-1">
                                    Kelola Nilai Akademik
                                </h6>

                                <p class="text-secondary small mb-0">
                                    Lihat dan perbarui nilai akademik mahasiswa.
                                </p>
                            </div>

                            <i class="bi bi-chevron-right ms-auto text-secondary"></i>

                        </div>

                    </div>

                </a>

            </div>

        </div>

    </div>

    <!-- DAFTAR PROGRAM STUDI -->
    <div class="card">

        <div class="card-header bg-white py-3">

            <h5 class="fw-bold mb-0">
                <i class="bi bi-mortarboard me-2 text-primary"></i>
                Daftar Program Studi
            </h5>

        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead>
                        <tr>
                            <th class="ps-4">Kode Prodi</th>
                            <th>Nama Program Studi</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php if (count($daftarProdi) > 0): ?>

                            <?php foreach ($daftarProdi as $prodi): ?>

                                <tr>

                                    <td class="ps-4 fw-semibold text-primary">
                                        <?= htmlspecialchars($prodi['kd_prodi']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($prodi['nama_prodi']) ?>
                                    </td>

                                    <td class="text-center">
                                        <span class="badge bg-success-subtle text-success">
                                            Aktif
                                        </span>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="3" class="text-center py-4 text-secondary">
                                    Belum ada program studi.
                                </td>
                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<?php

// Memanggil footer
require_once __DIR__ . '/../includes/footer.php';

?>
