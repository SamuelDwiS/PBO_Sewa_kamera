<?php
require '../admin/koneksi.php';

// Check login status
$isLoggedIn = isset($_SESSION['id_cust']);
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
                        <a class="nav-link" href="?page=daftar_kamera">
                            <i class="bi bi-camera"></i> Daftar Kamera
                        </a>
                    </li>

                    <?php if ($isLoggedIn) { ?>
                        <li class="nav-item mx-2">
                            <a class="nav-link" href="?page=sewa">
                                <i class="bi bi-cash-stack"></i> Riwayat Sewa
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
                    <?php } else { ?>
                        <li class="nav-item mx-2">
                            <a class="nav-link" href="login.php">
                                <i class="bi bi-box-arrow-in-right"></i> Login
                            </a>
                        </li>

                        <li class="nav-item mx-2">
                            <a class="nav-link" href="registrasi.php">
                                <i class="bi bi-person-plus"></i> Registrasi
                            </a>
                        </li>
                    <?php } ?>

                </ul>

            </div>

        </div>
    </nav>


    <!-- Main Content -->
    <div class="container py-5">

        <?php
        if (isset($_GET['page'])) {
            switch ($_GET['page']) {
                case "daftar_kamera": include "daftar_kamera.php"; break;
                case "sewa": 
                    if ($isLoggedIn) {
                        include "riwayat_sewa.php";
                    } else {
                        echo '<div class="alert alert-warning text-center"><h4>Silakan login terlebih dahulu</h4><a href="login.php" class="btn btn-primary">Login</a></div>';
                    }
                    break;
                case "profile": 
                    if ($isLoggedIn) {
                        include "profile.php";
                    } else {
                        echo '<div class="alert alert-warning text-center"><h4>Silakan login terlebih dahulu</h4><a href="login.php" class="btn btn-primary">Login</a></div>';
                    }
                    break;
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
                <h2 class="fw-bold mb-2">Selamat Datang di Sewa Kamera</h2>
                <p class="text-muted mb-5">Sewa kamera berkualitas dengan harga terjangkau</p>

                <div class="row mt-5 justify-content-center">
                    <div class="col-md-4 mb-3">
                        <a href="?page=daftar_kamera" class="text-decoration-none">
                            <div class="card shadow-sm p-3 h-100 hover-card">
                                <i class="bi bi-camera fs-1 text-primary"></i>
                                <h5 class="mt-3">Daftar Kamera</h5>
                                <p class="text-muted">Lihat koleksi kamera kami</p>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-4 mb-3">
                        <a href="login.php" class="text-decoration-none">
                            <div class="card shadow-sm p-3 h-100 hover-card">
                                <i class="bi bi-box-arrow-in-right fs-1 text-primary"></i>
                                <h5 class="mt-3">Login</h5>
                                <p class="text-muted">Masuk untuk melakukan penyewaan</p>
                            </div>
                        </a>
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
