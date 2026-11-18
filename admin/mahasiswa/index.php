<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();

// Membuat token keamanan
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Mengambil pesan notifikasi
$flashMessage = $_SESSION['flash_message'] ?? '';
$flashType = $_SESSION['flash_type'] ?? 'success';

unset($_SESSION['flash_message'], $_SESSION['flash_type']);

// Mengambil seluruh data mahasiswa
$query = $db->query(
    "SELECT
        mahasiswa.id,
        mahasiswa.nim,
        mahasiswa.nama,
        mahasiswa.email,
        program_studi.kd_prodi,
        program_studi.nama_prodi
     FROM mahasiswa
     INNER JOIN program_studi
        ON mahasiswa.id_prodi = program_studi.id_prodi
     ORDER BY mahasiswa.id DESC"
);

$daftarMahasiswa = $query->fetchAll(PDO::FETCH_ASSOC);

// Pengaturan halaman
$pageTitle = 'Data Mahasiswa - Student Portal';
$navbarType = 'admin';

// Memanggil header bersama
require_once __DIR__ . '/../../includes/header.php';

?>

<div class="container py-5">

    <!-- HEADER HALAMAN -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

        <div>
            <span class="badge bg-primary-subtle text-primary mb-2">
                ADMINISTRATOR
            </span>

            <h2 class="fw-bold mb-2">
                Data Mahasiswa
            </h2>

            <p class="text-secondary mb-0">
                Kelola seluruh data mahasiswa Student Portal.
            </p>
        </div>

        <div class="d-flex gap-2 flex-wrap">

            <a href="/PWL/admin/dashboard.php"
               class="btn btn-outline-secondary">

                <i class="bi bi-house-door me-1"></i>
                Dashboard

            </a>

            <a href="/PWL/admin/mahasiswa/create.php"
               class="btn btn-primary">

                <i class="bi bi-person-plus-fill me-1"></i>
                Tambah Mahasiswa

            </a>

        </div>

    </div>

    <!-- NOTIFIKASI -->
    <?php if ($flashMessage !== ''): ?>

        <div class="alert alert-<?= htmlspecialchars($flashType) ?> alert-dismissible fade show">

            <i class="bi bi-info-circle-fill me-2"></i>

            <?= htmlspecialchars($flashMessage) ?>

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    <?php endif; ?>

    <!-- STATISTIK SINGKAT -->
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="stat-card">

                <div class="d-flex align-items-center justify-content-between">

                    <div>
                        <p>Total Mahasiswa</p>
                        <h3><?= count($daftarMahasiswa) ?></h3>
                    </div>

                    <div class="rounded-circle bg-primary-subtle text-primary
                                d-flex align-items-center justify-content-center"
                         style="width: 55px; height: 55px;">

                        <i class="bi bi-people-fill fs-3"></i>

                    </div>

                </div>

            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card">

                <div class="d-flex align-items-center justify-content-between">

                    <div>
                        <p>Status Pengelolaan</p>
                        <h3 class="text-success">Aktif</h3>
                    </div>

                    <div class="rounded-circle bg-success-subtle text-success
                                d-flex align-items-center justify-content-center"
                         style="width: 55px; height: 55px;">

                        <i class="bi bi-check-circle-fill fs-3"></i>

                    </div>

                </div>

            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card">

                <div class="d-flex align-items-center justify-content-between">

                    <div>
                        <p>Menu Pengelolaan</p>
                        <h3>Data</h3>
                    </div>

                    <div class="rounded-circle bg-warning-subtle text-warning
                                d-flex align-items-center justify-content-center"
                         style="width: 55px; height: 55px;">

                        <i class="bi bi-database-fill fs-3"></i>

                    </div>

                </div>

            </div>
        </div>

    </div>

    <!-- TABEL MAHASISWA -->
    <div class="card">

        <div class="card-header bg-white py-3">

            <div class="row align-items-center g-3">

                <div class="col-md-6">

                    <h5 class="fw-bold mb-1">
                        <i class="bi bi-table me-2 text-primary"></i>
                        Daftar Mahasiswa
                    </h5>

                    <small class="text-secondary">
                        Total <?= count($daftarMahasiswa) ?> mahasiswa terdaftar.
                    </small>

                </div>

                <div class="col-md-6">

                    <div class="position-relative">

                        <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>

                        <input
                            type="text"
                            id="search"
                            class="form-control ps-5"
                            placeholder="Cari NIM, nama, email, atau prodi...">

                    </div>

                </div>

            </div>

        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0"
                       id="dataMahasiswa">

                    <thead>
                        <tr>
                            <th class="ps-4">No.</th>
                            <th>NIM</th>
                            <th>Nama Mahasiswa</th>
                            <th>Email</th>
                            <th>Program Studi</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php if (count($daftarMahasiswa) > 0): ?>

                            <?php foreach ($daftarMahasiswa as $index => $mhs): ?>

                                <tr>

                                    <td class="ps-4 text-secondary">
                                        <?= $index + 1 ?>
                                    </td>

                                    <td class="fw-semibold text-primary">
                                        <?= htmlspecialchars($mhs['nim']) ?>
                                    </td>

                                    <td class="fw-semibold">
                                        <?= htmlspecialchars($mhs['nama']) ?>
                                    </td>

                                    <td class="text-secondary">
                                        <?= htmlspecialchars($mhs['email']) ?>
                                    </td>

                                    <td>

                                        <span class="badge bg-primary-subtle text-primary">
                                            <?= htmlspecialchars($mhs['kd_prodi']) ?>
                                        </span>

                                        <span class="ms-1">
                                            <?= htmlspecialchars($mhs['nama_prodi']) ?>
                                        </span>

                                    </td>

                                    <td>

                                        <div class="d-flex justify-content-center gap-2">

                                            <a
                                                href="/PWL/admin/mahasiswa/edit.php?id=<?= (int) $mhs['id'] ?>"
                                                class="btn btn-warning btn-sm">

                                                <i class="bi bi-pencil-square me-1"></i>
                                                Edit

                                            </a>

                                            <form
                                                method="POST"
                                                action="/PWL/admin/mahasiswa/delete.php">

                                                <input
                                                    type="hidden"
                                                    name="id"
                                                    value="<?= (int) $mhs['id'] ?>">

                                                <input
                                                    type="hidden"
                                                    name="csrf_token"
                                                    value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                                                <button
                                                    type="submit"
                                                    class="btn btn-danger btn-sm"
                                                    data-confirm="Apakah kamu yakin ingin menghapus mahasiswa ini?">

                                                    <i class="bi bi-trash-fill me-1"></i>
                                                    Hapus

                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr id="emptyData">
                                <td colspan="6"
                                    class="text-center text-secondary py-5">

                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Belum ada data mahasiswa.

                                </td>
                            </tr>

                        <?php endif; ?>

                        <tr id="noResult" style="display: none;">
                            <td colspan="6"
                                class="text-center text-secondary py-4">

                                <i class="bi bi-search fs-4 d-block mb-2"></i>
                                Data mahasiswa tidak ditemukan.

                            </td>
                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

        <div class="card-footer bg-white text-secondary small py-3">

            <i class="bi bi-info-circle me-1"></i>
            Gunakan fitur pencarian untuk menemukan data mahasiswa dengan cepat.

        </div>

    </div>

</div>

<?php

// Memanggil footer bersama
require_once __DIR__ . '/../../includes/footer.php';

?>
