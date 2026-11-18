<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();

// Mengambil notifikasi
$flashMessage = $_SESSION['flash_message'] ?? '';
$flashType = $_SESSION['flash_type'] ?? 'success';

unset($_SESSION['flash_message'], $_SESSION['flash_type']);

// Mengambil data mahasiswa dan nilai akademik
$query = $db->query(
    "SELECT
        mahasiswa.id,
        mahasiswa.nim,
        mahasiswa.nama,
        program_studi.nama_prodi,
        mahasiswa.nilai_tugas,
        mahasiswa.nilai_uts,
        mahasiswa.nilai_uas,
        mahasiswa.kehadiran,
        (
            (mahasiswa.nilai_tugas * 0.30) +
            (mahasiswa.nilai_uts * 0.30) +
            (mahasiswa.nilai_uas * 0.40)
        ) AS nilai_akhir
     FROM mahasiswa
     INNER JOIN program_studi
        ON mahasiswa.id_prodi = program_studi.id_prodi
     ORDER BY mahasiswa.nama ASC"
);

$daftarNilai = $query->fetchAll(PDO::FETCH_ASSOC);

// Mengatur header
$pageTitle = 'Nilai Akademik - Student Portal';
$navbarType = 'admin';

require_once __DIR__ . '/../../includes/header.php';

?>

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

        <div>
            <h2>Nilai Akademik Mahasiswa</h2>

            <p class="text-muted mb-0">
                Kelola nilai tugas, UTS, UAS, dan kehadiran mahasiswa.
            </p>
        </div>

        <a href="/PWL/admin/dashboard.php"
           class="btn btn-outline-secondary">
            Kembali ke Dashboard
        </a>

    </div>

    <?php if ($flashMessage !== ''): ?>
        <div class="alert alert-<?= htmlspecialchars($flashType) ?> alert-dismissible fade show">

            <?= htmlspecialchars($flashMessage) ?>

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm">

        <div class="card-body p-4">

            <div class="mb-3">
                <h5>Daftar Nilai</h5>

                <small class="text-muted">
                    Total mahasiswa: <?= count($daftarNilai) ?>
                </small>
            </div>

            <div class="table-responsive">

                <table class="table table-hover table-bordered align-middle">

                    <thead class="table-light">

                        <tr>
                            <th>No</th>
                            <th>NIM</th>
                            <th>Nama Mahasiswa</th>
                            <th>Program Studi</th>
                            <th>Tugas</th>
                            <th>UTS</th>
                            <th>UAS</th>
                            <th>Kehadiran</th>
                            <th>Nilai Akhir</th>
                            <th>Aksi</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php if (count($daftarNilai) > 0): ?>

                            <?php foreach ($daftarNilai as $index => $mhs): ?>

                                <tr>

                                    <td><?= $index + 1 ?></td>

                                    <td>
                                        <?= htmlspecialchars($mhs['nim']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($mhs['nama']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($mhs['nama_prodi']) ?>
                                    </td>

                                    <td>
                                        <?= number_format((float) $mhs['nilai_tugas'], 2) ?>
                                    </td>

                                    <td>
                                        <?= number_format((float) $mhs['nilai_uts'], 2) ?>
                                    </td>

                                    <td>
                                        <?= number_format((float) $mhs['nilai_uas'], 2) ?>
                                    </td>

                                    <td>
                                        <?= number_format((float) $mhs['kehadiran'], 2) ?>%
                                    </td>

                                    <td class="fw-bold text-primary">
                                        <?= number_format((float) $mhs['nilai_akhir'], 2) ?>
                                    </td>

                                    <td>
                                        <a
                                            href="/PWL/admin/nilai/edit.php?id=<?= (int) $mhs['id'] ?>"
                                            class="btn btn-warning btn-sm">
                                            Edit Nilai
                                        </a>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                    Belum ada data mahasiswa.
                                </td>
                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

            <div class="alert alert-info mt-3 mb-0">

                <strong>Rumus Nilai Akhir:</strong>

                <p class="mb-0 mt-2">
                    (Tugas × 30%) + (UTS × 30%) + (UAS × 40%)
                </p>

                <small>
                    Kehadiran ditampilkan terpisah dan belum menjadi komponen nilai akhir.
                </small>

            </div>

        </div>
    </div>

</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
