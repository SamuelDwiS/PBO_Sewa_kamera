<?php
require '../admin/koneksi.php';

// Check login status
$isLoggedIn = isset($_SESSION['id_cust']);

// Handle form sewa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sewa'])) {
    if (!$isLoggedIn) {
        header("Location: login.php");
        exit;
    }

    $id_cust = $_SESSION['id_cust'];
    $id_barang = $_POST['id_barang'] ?? '';
    $tgl_mulai_sewa = $_POST['tgl_mulai_sewa'] ?? '';
    $tgl_kembali_est = $_POST['tgl_kembali_est'] ?? '';
    $jumlah = $_POST['jumlah'] ?? 1;
    $catatan = $_POST['catatan'] ?? '';
    $status = $_POST['status'] ?? 'Menunggu Persetujuan';

    // Generate ID Sewa
    $db = db();
    $sql = "SELECT MAX(CAST(SUBSTRING(no_transaksi, 4) AS UNSIGNED)) as max_id FROM tb_sewa WHERE no_transaksi LIKE 'SEW%'";
    $result = $db->query($sql);
    $row = $result->fetch_assoc();
    $nextId = ($row['max_id'] ?? 0) + 1;
    $no_transaksi = 'SEW' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

    // Hitung tanggal dan total
    $date1 = new DateTime($tgl_mulai_sewa);
    $date2 = new DateTime($tgl_kembali_est);
    $hari = $date1->diff($date2)->days + 1;

    // Get harga dari barang
    $stmt = $db->prepare("SELECT harga_sewa_hari FROM tb_barang WHERE id_barang = ?");
    $stmt->bind_param("s", $id_barang);
    $stmt->execute();
    $result = $stmt->get_result();
    $barang = $result->fetch_assoc();
    $harga_per_hari = $barang['harga_sewa_hari'] ?? 0;
    $total_harga = $harga_per_hari * $hari * $jumlah;

    // Insert ke tb_sewa
    $stmt = $db->prepare("INSERT INTO tb_sewa (no_transaksi, id_cust, tgl_sewa, tgl_tenggat_pengembalian, status, jaminan, total_harga) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $jaminan = ""; // Bisa disesuaikan
    $stmt->bind_param("ssssssi", $no_transaksi, $id_cust, $tgl_mulai_sewa, $tgl_kembali_est, $status, $jaminan, $total_harga);
    
    if ($stmt->execute()) {
        // Insert detail sewa ke tb_detail_sewa
        $stmt_detail = $db->prepare("INSERT INTO tb_detail_sewa (no_transaksi, id_barang, id_kategori, jumlah, subtotal) VALUES (?, ?, ?, ?, ?)");
        $subtotal = $total_harga;
        $id_kategori = $item['kategori'] ?? '';
        
        // Ambil id_kategori dari barang jika belum ada
        if (empty($id_kategori)) {
            $stmt_cat = $db->prepare("SELECT kategori FROM tb_barang WHERE id_barang = ?");
            $stmt_cat->bind_param("s", $id_barang);
            $stmt_cat->execute();
            $result_cat = $stmt_cat->get_result();
            $barang_cat = $result_cat->fetch_assoc();
            $id_kategori = $barang_cat['kategori'] ?? '';
        }
        
        $stmt_detail->bind_param("sssii", $no_transaksi, $id_barang, $id_kategori, $jumlah, $subtotal);
        $stmt_detail->execute();
        
        // Update stok barang
        $barang_model = model('barang');
        $barang_model->updateStok($id_barang, $jumlah);
        
        header("Location: index.php?msg=sewa_berhasil");
        exit;
    }
}

// Get alert message
$message = "";
$alertType = "";
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'sewa_berhasil') {
        $message = "Sewa berhasil! Silakan cek riwayat sewa Anda.";
        $alertType = "success";
    }
}
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

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $alertType; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

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
            // Ambil data barang untuk ditampilkan di homepage
            $barang = model('barang');
            $daftarBarang = $barang->tampil();
            $daftarMerk = $barang->getAllMerk();
            $daftarKategori = $barang->getAllKategori();
            ?>
            <div class="text-center mb-5">
                <h2 class="fw-bold mb-2">Selamat Datang di Sewa Kamera</h2>
                <p class="text-muted mb-3">Sewa kamera berkualitas dengan harga terjangkau</p>
            </div>

            <!-- Daftar Barang dalam Card -->
            <div class="row">
                <?php if (empty($daftarBarang)): ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <h5>Belum ada barang tersedia</h5>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($daftarBarang as $item):
                        $kategori_name = '';
                        foreach ($daftarKategori as $k) {
                            if ($k['id_kategori'] == $item['kategori']) {
                                $kategori_name = $k['kategori'];
                                break;
                            }
                        }
                    ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 shadow-sm hover-card" style="transition: all 0.3s ease;">
                                <!-- Gambar -->
                                <div style="height: 250px; overflow: hidden; background-color: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                                    <?php if (!empty($item['gambar']) && file_exists('../admin/uploads/' . $item['gambar'])): ?>
                                        <img src="../admin/uploads/<?php echo $item['gambar']; ?>" alt="<?php echo $item['nama_barang']; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                    <?php else: ?>
                                        <i class="bi bi-image fs-1 text-muted"></i>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title fw-bold"><?php echo $item['nama_barang']; ?></h5>
                                    
                                    <p class="card-text text-muted small mb-2"><?php echo substr($item['deskripsi'], 0, 50); ?>...</p>
                                    
                                    <div class="mb-3">
                                        <span class="badge bg-light text-dark"><?php echo $kategori_name; ?></span>
                                        <span class="badge bg-secondary ms-1">Stok: <?php echo $item['stok']; ?></span>
                                    </div>
                                    
                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <p class="mb-0 text-muted small">Harga per Hari</p>
                                                <h6 class="fw-bold text-primary">Rp <?php echo number_format($item['harga_sewa_hari'], 0, ',', '.'); ?></h6>
                                            </div>
                                        </div>
                                        <?php if ($isLoggedIn): ?>
                                            <button type="button" class="btn btn-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#modalSewa" onclick="setBarangId('<?php echo $item['id_barang']; ?>', '<?php echo $item['nama_barang']; ?>', <?php echo $item['harga_sewa_hari']; ?>)">
                                                <i class="bi bi-cart-plus"></i> Sewa Sekarang
                                            </button>
                                        <?php else: ?>
                                            <a href="login.php" class="btn btn-primary btn-sm w-100">
                                                <i class="bi bi-box-arrow-in-right"></i> Login untuk Sewa
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="row mt-5 justify-content-center">
                <div class="col-md-4 mb-3">
                    <a href="?page=daftar_kamera" class="text-decoration-none">
                        <div class="card shadow-sm p-3 h-100 hover-card">
                            <i class="bi bi-camera fs-1 text-primary"></i>
                            <h5 class="mt-3">Lihat Semua Kamera</h5>
                            <p class="text-muted">Tampilkan koleksi lengkap</p>
                        </div>
                    </a>
                </div>
            </div>
            <?php
        }
        ?>

    </div>

    <!-- Modal Sewa -->
    <div class="modal fade" id="modalSewa" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Form Sewa Kamera</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="formSewa">
                    <div class="modal-body">
                        <input type="hidden" name="id_barang" id="id_barang" value="">
                        <input type="hidden" name="id_cust" value="<?php echo $_SESSION['id_cust'] ?? ''; ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Barang</label>
                            <input type="text" class="form-control" id="nama_barang_display" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Mulai Sewa</label>
                            <input type="date" class="form-control" name="tgl_mulai_sewa" id="tgl_mulai_sewa" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Kembali Estimasi</label>
                            <input type="date" class="form-control" name="tgl_kembali_est" id="tgl_kembali_est" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jumlah (unit)</label>
                            <input type="number" class="form-control" name="jumlah" id="jumlah" value="1" min="1" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Harga per Hari</label>
                            <input type="text" class="form-control" id="harga_display" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Total Estimasi</label>
                            <input type="text" class="form-control" id="total_display" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <input type="text" name="status" id="status" class="form-control" value="Menunggu Persetujuan" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control" name="catatan" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="sewa" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Konfirmasi Sewa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let hargaPerHari = 0;

        function setBarangId(id, nama, harga) {
            document.getElementById('id_barang').value = id;
            document.getElementById('nama_barang_display').value = nama;
            document.getElementById('harga_display').value = 'Rp ' + new Intl.NumberFormat('id-ID').format(harga);
            hargaPerHari = harga;
            calculateTotal();
        }

        function calculateTotal() {
            const tglMulai = new Date(document.getElementById('tgl_mulai_sewa').value);
            const tglKembali = new Date(document.getElementById('tgl_kembali_est').value);
            const jumlah = parseInt(document.getElementById('jumlah').value) || 1;

            if (tglMulai && tglKembali && tglKembali >= tglMulai) {
                const hari = Math.ceil((tglKembali - tglMulai) / (1000 * 60 * 60 * 24)) + 1;
                const total = hargaPerHari * hari * jumlah;
                document.getElementById('total_display').value = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
            }
        }

        document.getElementById('tgl_mulai_sewa').addEventListener('change', calculateTotal);
        document.getElementById('tgl_kembali_est').addEventListener('change', calculateTotal);
        document.getElementById('jumlah').addEventListener('change', calculateTotal);
    </script>

</body>

</html>
