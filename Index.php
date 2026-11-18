<?php

require_once __DIR__ . '/includes/auth.php';

// Jika admin sudah login, arahkan ke dashboard admin
if (isAdminLoggedIn()) {
    header("Location: /PWL/admin/dashboard.php");
    exit;
}

// Jika mahasiswa sudah login, arahkan ke dashboard mahasiswa
if (isStudentLoggedIn()) {
    header("Location: /PWL/mahasiswa/dashboard.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student Portal</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
          rel="stylesheet">

    <link rel="stylesheet" href="/PWL/assets/css/style.css?v=12">
</head>

<body class="bg-light">

    <nav class="navbar navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/PWL/Index.php">
                <i class="bi bi-mortarboard-fill me-2"></i>
                Student Portal
            </a>
        </div>
    </nav>

    <main class="container py-5">

        <div class="row justify-content-center text-center">

            <div class="col-lg-9">

                <div class="py-5">

                    <i class="bi bi-mortarboard-fill text-primary"
                       style="font-size: 5rem;"></i>

                    <h1 class="fw-bold mt-4">
                        Selamat Datang di Student Portal
                    </h1>

                    <p class="text-secondary fs-5 mt-3">
                        Sistem informasi akademik untuk membantu mahasiswa
                        melihat informasi profil dan perkembangan nilai akademik.
                    </p>

                    <div class="row justify-content-center g-4 mt-4">

                        <div class="col-md-5">

                            <div class="card border-0 shadow-sm h-100">

                                <div class="card-body p-4">

                                    <i class="bi bi-person-fill-gear text-primary"
                                       style="font-size: 3rem;"></i>

                                    <h4 class="fw-bold mt-3">
                                        Admin
                                    </h4>

                                    <p class="text-secondary">
                                        Kelola data mahasiswa, program studi,
                                        dan nilai akademik.
                                    </p>

                                    <a href="/PWL/login.php"
                                       class="btn btn-primary w-100">
                                        Login Admin
                                    </a>

                                </div>

                            </div>

                        </div>

                        <div class="col-md-5">

                            <div class="card border-0 shadow-sm h-100">

                                <div class="card-body p-4">

                                    <i class="bi bi-person-circle text-primary"
                                       style="font-size: 3rem;"></i>

                                    <h4 class="fw-bold mt-3">
                                        Mahasiswa
                                    </h4>

                                    <p class="text-secondary">
                                        Lihat profil pribadi dan informasi
                                        nilai akademik.
                                    </p>

                                    <a href="/PWL/mahasiswa/login.php"
                                       class="btn btn-outline-primary w-100">
                                        Login Mahasiswa
                                    </a>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </main>

    <footer class="text-center text-secondary py-4 small">
        Student Portal &copy; <?= date('Y') ?>. All Rights Reserved.
    </footer>

</body>
</html>
