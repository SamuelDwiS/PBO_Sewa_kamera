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
                <nav class="col-md-3 col-lg-2 d-md-block sidebar py-4 px-3">
                    <h4 class="text-white text-center mb-4">Rental Kamera</h4>
                    <div class="nav flex-column">
                        <a href="index.php" class="nav-link <?php echo (!isset($_GET['page'])) ? 'active' : ''; ?>">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                        <a href="index.php?page=transaksi" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page']=='transaksi') ? 'active' : ''; ?>">
                            <i class="bi bi-cash-coin"></i> Sewa
                        </a>
                        <a href="index.php?page=sewa" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page']=='sewa') ? 'active' : ''; ?>">
                            <i class="bi bi-cart-check"></i> Data Sewa
                        </a>
                        <a href="index.php?page=data_transaksi" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page']=='data_pengembalian') ? 'active' : ''; ?>">
                            <i class="bi bi-cash-coin"></i> Data Pengembalian
                        </a>
                        <a href="index.php?page=anggota" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page']=='anggota') ? 'active' : ''; ?>">
                            <i class="bi bi-people"></i> Data Customer
                        </a>
                        <a href="index.php?page=barang" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page']=='barang') ? 'active' : ''; ?>">
                            <i class="bi bi-camera"></i> Barang
                        </a>
                        <a href="index.php?page=laporan" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page']=='laporan') ? 'active' : ''; ?>">
                            <i class="bi bi-file-earmark-text"></i> Laporan
                        </a>
                        <a href="logout.php" class="nav-link">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </div>
                </nav>

                <!-- Main Content -->
                <main class="col-md-9 ms-sm-auto col-lg-10 content">
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
                        // Dashboard utama dinamis
                        include_once "koneksi.php";
                        $koneksi = Database::getInstance()->getConnection();
                        $totalKamera = $koneksi->query("SELECT COUNT(*) FROM tb_barang")->fetch_row()[0];
                        $totalCustomer = $koneksi->query("SELECT COUNT(*) FROM tb_customer")->fetch_row()[0];
                        $totalSewa = $koneksi->query("SELECT COUNT(*) FROM tb_sewa")->fetch_row()[0];
                        $totalBarangHabis = $koneksi->query("SELECT COUNT(*) FROM tb_barang WHERE stok = '0' OR stok = 0")->fetch_row()[0];
                        $totalKategori = $koneksi->query("SELECT COUNT(*) FROM tb_kategori")->fetch_row()[0];
                        $totalMerk = $koneksi->query("SELECT COUNT(*) FROM tb_merk")->fetch_row()[0];
                        echo '<div class="card p-4 shadow-sm mb-4">';
                        echo '<h3 class="mb-3">Selamat Datang di Dashboard Admin Rental Kamera!</h3>';
                        echo '<p class="mb-0">Kelola data kamera, customer, transaksi sewa, dan pengembalian dengan mudah melalui menu di samping kiri.</p>';
                        echo '</div>';
                        echo '<div class="row row-cols-1 row-cols-md-4 g-4 mb-2 justify-content-center">';
                        echo '  <div class="col">';
                        echo '    <div class="card shadow-sm text-center h-100">';
                        echo '      <div class="card-body">';
                        echo '        <i class="bi bi-camera display-4 text-primary mb-2"></i>';
                        echo '        <h5 class="card-title">Total Kamera</h5>';
                        echo '        <p class="card-text fs-4">' . $totalKamera . '</p>';
                        echo '      </div>';
                        echo '    </div>';
                        echo '  </div>';
                        echo '  <div class="col">';
                        echo '    <div class="card shadow-sm text-center h-100">';
                        echo '      <div class="card-body">';
                        echo '        <i class="bi bi-people display-4 text-success mb-2"></i>';
                        echo '        <h5 class="card-title">Total Customer</h5>';
                        echo '        <p class="card-text fs-4">' . $totalCustomer . '</p>';
                        echo '      </div>';
                        echo '    </div>';
                        echo '  </div>';
                        echo '  <div class="col">';
                        echo '    <div class="card shadow-sm text-center h-100">';
                        echo '      <div class="card-body">';
                        echo '        <i class="bi bi-cart-check display-4 text-warning mb-2"></i>';
                        echo '        <h5 class="card-title">Total Sewa</h5>';
                        echo '        <p class="card-text fs-4">' . $totalSewa . '</p>';
                        echo '      </div>';
                        echo '    </div>';
                        echo '  </div>';
                        echo '  <div class="col">';
                        echo '    <div class="card shadow-sm text-center h-100">';
                        echo '      <div class="card-body">';
                        echo '        <i class="bi bi-exclamation-triangle display-4 text-danger mb-2"></i>';
                        echo '        <h5 class="card-title">Barang Habis</h5>';
                        echo '        <p class="card-text fs-4">' . $totalBarangHabis . '</p>';
                        echo '      </div>';
                        echo '    </div>';
                        echo '  </div>';
                        echo '</div>';
                        echo '<div class="row row-cols-1 row-cols-md-2 g-4 mb-4 justify-content-center">';
                        echo '  <div class="col">';
                        echo '    <div class="card shadow-sm text-center h-100">';
                        echo '      <div class="card-body">';
                        echo '        <i class="bi bi-tags display-4 text-info mb-2"></i>';
                        echo '        <h5 class="card-title">Total Kategori</h5>';
                        echo '        <p class="card-text fs-4">' . $totalKategori . '</p>';
                        echo '      </div>';
                        echo '    </div>';
                        echo '  </div>';
                        echo '  <div class="col">';
                        echo '    <div class="card shadow-sm text-center h-100">';
                        echo '      <div class="card-body">';
                        echo '        <i class="bi bi-bookmark-star display-4 text-secondary mb-2"></i>';
                        echo '        <h5 class="card-title">Total Merk</h5>';
                        echo '        <p class="card-text fs-4">' . $totalMerk . '</p>';
                        echo '      </div>';
                        echo '    </div>';
                        echo '  </div>';
                        echo '</div>';
                    }
                    ?>
                </main>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>

    </html>