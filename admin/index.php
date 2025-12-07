<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental Kamera - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="asset/css/style.css">
</head>

<body>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar">
                <h4 class="text-white text-center mb-4">Rental Kamera</h4>
                <a href="index.php" class="<?php echo (!isset($_GET['page'])) ? 'active' : ''; ?>">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a href="index.php?page=transaksi" class="<?php echo (isset($_GET['page']) && $_GET['page']=='transaksi') ? 'active' : ''; ?>">
                    <i class="bi bi-cash-coin"></i> Sewa
                </a>
                <a href="index.php?page=sewa" class="<?php echo (isset($_GET['page']) && $_GET['page']=='sewa') ? 'active' : ''; ?>">
                    <i class="bi bi-cart-check"></i> Data Sewa
                </a>
                <a href="index.php?page=data_transaksi" class="<?php echo (isset($_GET['page']) && $_GET['page']=='data_pengembalian') ? 'active' : ''; ?>">
                    <i class="bi bi-cash-coin"></i> Data Pengembalian
                </a>
                <a href="index.php?page=anggota" class="<?php echo (isset($_GET['page']) && $_GET['page']=='anggota') ? 'active' : ''; ?>">
                    <i class="bi bi-people"></i> Data Customer
                </a>
                <a href="index.php?page=barang" class="<?php echo (isset($_GET['page']) && $_GET['page']=='barang') ? 'active' : ''; ?>">
                    <i class="bi bi-recycle"></i> Barang
                </a>
                <a href="logout.php">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 content">
                <?php
                if (isset($_GET['page'])) {
                    $page = $_GET['page'];
                    if ($page == "anggota") {
                        include "anggota.php";
                    } elseif ($page == "transaksi") {
                        include "transaksi.php";
                    } elseif ($page == "barang") {
                        include "barang.php";
                    } elseif ($page == "sewa") {
                        include "sewa.php";
                    } elseif ($page == "data_transaksi") {
                        include "data_transaksi.php";
                    } elseif ($page == "laporan") {
                        include "laporan.php";
                    } else {
                        echo "<h2>Halaman tidak ditemukan</h2>";
                    }
                } else {
                    echo "<h2>Halaman Dashboard Rental Kamera</h2>";
                }
                ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>