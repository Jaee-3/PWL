<?php

$pageTitle = $pageTitle ?? 'Student Portal';
$navbarType = $navbarType ?? 'admin';

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($pageTitle) ?></title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="/PWL/assets/css/style.css?v=12">
</head>

<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">

    <div class="container">

        <a class="navbar-brand fw-bold"
           href="<?= $navbarType === 'student' ? '/PWL/mahasiswa/dashboard.php' : '/PWL/admin/dashboard.php' ?>">

            <i class="bi bi-mortarboard-fill me-2"></i>
            Student Portal

        </a>

        <!-- Tombol hamburger untuk layar kecil -->
        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarMenu"
            aria-controls="navbarMenu"
            aria-expanded="false"
            aria-label="Buka navigasi">

            <span class="navbar-toggler-icon"></span>

        </button>

        <div class="collapse navbar-collapse" id="navbarMenu">

            <div class="ms-auto d-flex flex-column flex-lg-row
                        align-items-lg-center gap-2 mt-3 mt-lg-0">

                <?php if ($navbarType === 'student'): ?>

                    <span class="text-white me-lg-2">

                        <i class="bi bi-person-circle me-1"></i>

                        <?= htmlspecialchars($_SESSION['mahasiswa_nama'] ?? 'Mahasiswa') ?>

                    </span>

                    <a href="/PWL/mahasiswa/ubah_password.php"
                       class="btn btn-outline-light btn-sm">

                        <i class="bi bi-key me-1"></i>
                        Ubah Password

                    </a>

                    <a href="/PWL/mahasiswa/logout.php"
                       class="btn btn-light btn-sm"
                       onclick="return confirm('Apakah kamu yakin ingin logout?')">

                        <i class="bi bi-box-arrow-right me-1"></i>
                        Logout

                    </a>

                <?php else: ?>

                    <span class="text-white me-lg-2">

                        <i class="bi bi-person-circle me-1"></i>

                        <?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?>

                    </span>

                    <a href="/PWL/logout.php"
                       class="btn btn-outline-light btn-sm">

                        <i class="bi bi-box-arrow-right me-1"></i>
                        Logout

                    </a>

                <?php endif; ?>

            </div>

        </div>

    </div>

</nav>
