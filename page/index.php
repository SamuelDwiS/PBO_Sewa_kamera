<?php

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Customer - Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icon -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="asset/css/style.css">

</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-primary shadow-sm py-3">
        <div class="container">

            <a class="navbar-brand fw-bold" href="index.php">
                <i class="bi bi-camera"></i> Sewa Kamera
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
                <i class="bi bi-list"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarMenu">

                <ul class="navbar-nav ms-auto">

                    <li class="nav-item mx-2">
                        <a class="nav-link" href="?page=sewa">
                            <i class="bi bi-cash-stack"></i> Sewa
                        </a>
                    </li>

                    <li class="nav-item mx-2">
                        <a class="nav-link" href="?page=pengembalian">
                            <i class="bi bi-receipt"></i> Pengembalian
                        </a>
                    </li>

                    <li class="nav-item mx-2">
                        <a class="nav-link" href="?page=profile">
                            <i class="bi bi-person-circle"></i> Profil
                        </a>
                    </li>

                    <li class="nav-item mx-2">
                        <a class="nav-link text-danger" href="logout.php">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>

                </ul>

            </div>

        </div>
    </nav>


    <!-- Main Content -->
    <div class="container py-5">

        <?php
        if (isset($_GET['page'])) {
            switch ($_GET['page']) {
                case "layanan": include "customer_layanan.php"; break;
                case "transaksi": include "customer_transaksi.php"; break;
                case "profile": include "customer_profile.php"; break;
                default: 
                    ?>
                    <div class="alert alert-danger text-center">
                        <h3>Halaman tidak ditemukan</h3>
                    </div>
                    <?php
            }
        } else {
            ?>
            <div class="text-center">
                <h2 class="fw-bold">Selamat Datang di Sewa Kamera</h2>

                <div class="row mt-5 justify-content-center">
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm p-3">
                            <i class="bi bi-camera fs-1 text-primary"></i>
                            <h5 class="mt-3">Kamera</h5>
                            <p class="text-muted">Pilih tipe kamera sesuai kebutuhan</p>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm p-3">
                            <i class="bi bi-receipt fs-1 text-primary"></i>
                            <h5 class="mt-3">Peminjaman</h5>
                            <p class="text-muted">Layanan Peminjaman Kamera </p>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
