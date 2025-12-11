<?php
/**
 * CUSTOMER INDEX/DASHBOARD
 * File: index.php
 */
session_start(); // penting! harus di atas semua akses $_SESSION
require_once '../admin/koneksi.php'; // Asumsi: file koneksi database

// --- Fungsi Helper (Asumsi Model/Helper) ---
// Perlu dipastikan fungsi model('barang') ada dan dapat diakses.
// Untuk demonstrasi ini, saya hanya memanggilnya di bagian Homepage Content.
if (!function_exists('model')) {
    function model($modelName) {
        // Implementasi dummy atau pastikan file model dimuat
        // Contoh: return new BarangModel($koneksi); 
        // Anda harus memastikan kelas/fungsi ini tersedia.
        
        // Asumsi struktur $barang->tampil() dan getAllKategori()
        // Untuk menghindari error jika fungsi model() tidak ada:
        return (object)[
            'tampil' => function() use ($koneksi) {
                // Query database untuk daftar barang
                return []; // Ganti dengan hasil query sebenarnya
            },
            'getAllMerk' => function() use ($koneksi) {
                 return []; // Ganti dengan hasil query sebenarnya
            },
            'getAllKategori' => function() use ($koneksi) {
                 return []; // Ganti dengan hasil query sebenarnya
            },
        ];
    }
}
// ---------------------------------------------

// Cek login
$isLoggedIn = isset($_SESSION['id_cust']);

// Pesan alert
$message = "";
$alertType = "";
if (isset($_GET['msg']) && $_GET['msg'] === 'sewa_berhasil') {
    $message = "Sewa berhasil! Silakan cek riwayat sewa Anda.";
    $alertType = "success";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
        <style>
        #cartBadge[hidden] { display: none !important; }
        </style>
    <meta charset="UTF-8">
    <title>Customer - Dashboard | Sewa Kamera</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="asset/css/style.css">
</head>

<body>

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

                <li class="nav-item mx-2">
                    <a class="nav-link position-relative" href="?page=cart" id="cartLink">
                        <i class="bi bi-cart3"></i> Cart Sewa
                        <span id="cartBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">0</span>
                    </a>
                </li>

                <?php if ($isLoggedIn): ?>
                    <?php
                    // Ambil info user login
                    $conn = db();
                    $id_cust_nav = $_SESSION['id_cust'];
                    $user_nav = $conn->query("SELECT nama, foto FROM tb_customer WHERE id_cust='{$id_cust_nav}' LIMIT 1")->fetch_assoc();
                    $nama_nav = $user_nav ? $user_nav['nama'] : $_SESSION['username'];
                    $foto_nav = ($user_nav && !empty($user_nav['foto']) && file_exists('../admin/uploads/' . $user_nav['foto'])) ? '../admin/uploads/' . $user_nav['foto'] : null;
                    ?>
                    <li class="nav-item mx-2">
                        <a class="nav-link" href="?page=sewa">
                            <i class="bi bi-cash-stack"></i> Riwayat Sewa
                        </a>
                    </li>
                    <li class="nav-item mx-2 d-flex align-items-center">
                        <a class="nav-link d-flex align-items-center gap-2" href="?page=profile">
                            <?php if ($foto_nav): ?>
                                <img src="<?= $foto_nav ?>" alt="Avatar" style="width:28px;height:28px;object-fit:cover;border-radius:50%;border:1.5px solid #2575fc;">
                            <?php else: ?>
                                <span style="display:inline-block;width:28px;height:28px;border-radius:50%;background:#e0eafc;color:#2575fc;text-align:center;line-height:28px;font-weight:600;font-size:1.1rem;"><?= strtoupper(substr($nama_nav,0,1)) ?></span>
                            <?php endif; ?>
                            <span><?= htmlspecialchars($nama_nav) ?></span>
                        </a>
                    </li>
                    <li class="nav-item mx-2">
                        <a class="nav-link text-danger" href="logout.php">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                <?php else: ?>
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
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

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
            case "daftar_kamera": 
                // Asumsi: file daftar_kamera.php berisi tampilan daftar barang lengkap
                include "daftar_kamera.php"; 
                break;
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
            case "cart":
                include "cart.php"; 
                break;
            default: 
                echo '<div class="alert alert-danger text-center"><h3>Halaman tidak ditemukan</h3></div>';
        }
    } else {
        // --- Homepage Content: Tampilkan Daftar Barang ---
        $barang = model('barang');
        $daftarBarang = $barang->tampil();
        $daftarMerk = $barang->getAllMerk(); // Tidak terpakai di kode ini, tapi tetap dipanggil
        $daftarKategori = $barang->getAllKategori();
    ?>
    <div class="text-center mb-5">
        <h2 class="fw-bold mb-2">Selamat Datang di Sewa Kamera</h2>
        <p class="text-muted mb-3">Sewa kamera berkualitas dengan harga terjangkau</p>
    </div>

    <div class="row">
        <?php if (empty($daftarBarang)): ?>
            <div class="col-12">
                <div class="alert alert-info text-center"><h5>Belum ada barang tersedia</h5></div>
            </div>
        <?php else: ?>
            <?php 
            // Loop untuk menampilkan setiap barang
            foreach ($daftarBarang as $item):
                // Cari nama kategori
                $kategori_name = '';
                foreach ($daftarKategori as $k) {
                    if ($k['id_kategori'] == $item['kategori']) {
                        $kategori_name = $k['kategori'];
                        break;
                    }
                }
                $stok = isset($item['stok']) ? (int)$item['stok'] : 0;
                $stokBadgeClass = $stok > 0 ? 'bg-success' : 'bg-danger';
            ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm hover-card" style="transition: all 0.3s ease;">
                    
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
                            <span class="badge bg-light text-dark border"><?php echo $kategori_name; ?></span>
                            <span class="badge <?php echo $stokBadgeClass; ?> ms-1">Stok: <?php echo $stok; ?></span>
                        </div>
                        
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <p class="mb-0 text-muted small">Harga per Hari</p>
                                    <h6 class="fw-bold text-primary">Rp <?php echo number_format($item['harga_sewa_hari'], 0, ',', '.'); ?></h6>
                                </div>
                            </div>

                            <?php if ($stok == 0): ?>
                                <button class="btn btn-secondary btn-sm w-100" disabled>Stok Habis</button>
                            <?php else: ?>
                                <?php if ($isLoggedIn): ?>
                                    <button type="button" class="btn btn-primary btn-sm w-100" onclick="addToCart('<?php echo $item['id_barang']; ?>')">
                                        <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                                    </button>
                                <?php else: ?>
                                    <a href="login.php" class="btn btn-primary btn-sm w-100">
                                        <i class="bi bi-box-arrow-in-right"></i> Login untuk Sewa
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php } // End of !isset($_GET['page']) ?>

</div>

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
                    <button type="button" class="btn btn-primary" onclick="addToCartFromModal()">
                        <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let hargaPerHari = 0;

    /**
     * Mengatur ID, Nama, dan Harga barang di Modal Sewa
     * @param {string} id - ID Barang
     * @param {string} nama - Nama Barang
     * @param {number} harga - Harga Sewa per Hari
     */
    function setBarangId(id, nama, harga) {
        document.getElementById('id_barang').value = id;
        document.getElementById('nama_barang_display').value = nama;
        document.getElementById('harga_display').value = 'Rp ' + new Intl.NumberFormat('id-ID').format(harga);
        hargaPerHari = harga;
        calculateTotal();
    }

    /**
     * Menghitung Total Estimasi Sewa
     */
    function calculateTotal() {
        const tglMulaiEl = document.getElementById('tgl_mulai_sewa');
        const tglKembaliEl = document.getElementById('tgl_kembali_est');
        const jumlahEl = document.getElementById('jumlah');
        const totalDisplayEl = document.getElementById('total_display');

        if (!tglMulaiEl || !tglKembaliEl || !jumlahEl || !totalDisplayEl) return; // Guard clause

        const tglMulai = new Date(tglMulaiEl.value);
        const tglKembali = new Date(tglKembaliEl.value);
        const jumlah = parseInt(jumlahEl.value) || 1;

        if (tglMulai && tglKembali && tglKembali >= tglMulai) {
            // Hitung selisih hari (+1 hari karena sewa dihitung dari hari pertama hingga hari terakhir)
            const diffTime = tglKembali.getTime() - tglMulai.getTime();
            const hari = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; 
            const total = hargaPerHari * hari * jumlah;
            totalDisplayEl.value = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
        } else {
            totalDisplayEl.value = 'Rp 0';
        }
    }

    // Attach event listeners for calculation
    document.addEventListener('DOMContentLoaded', () => {
        const tglMulai = document.getElementById('tgl_mulai_sewa');
        const tglKembali = document.getElementById('tgl_kembali_est');
        const jumlah = document.getElementById('jumlah');
        
        if (tglMulai) tglMulai.addEventListener('change', calculateTotal);
        if (tglKembali) tglKembali.addEventListener('change', calculateTotal);
        if (jumlah) jumlah.addEventListener('change', calculateTotal);
    });

    /**
     * Menambahkan item ke keranjang (dari card di homepage)
     * @param {string} id - ID Barang
     */
    function addToCart(id) {
        fetch('cart_action.php', {
            method: 'POST',
            headers: {'Content-Type':'application/x-www-form-urlencoded'},
            body: new URLSearchParams({action:'add', id_barang: id, jumlah:1})
        }).then(r=>r.json()).then(res=>{
            if(res.success) { 
                updateCartBadge(); 
                alert('Berhasil ditambahkan ke keranjang!'); // Feedback instan
            }
            else alert('Gagal menambahkan: ' + (res.msg || 'Harap login atau coba lagi.'));
        });
    }

    /**
     * Menambahkan item ke keranjang (dari Modal Sewa)
     */
    function addToCartFromModal() {
        const id = document.getElementById('id_barang').value;
        const jumlah = parseInt(document.getElementById('jumlah').value) || 1;
        const tgl_mulai = document.getElementById('tgl_mulai_sewa').value;
        const tgl_kembali = document.getElementById('tgl_kembali_est').value;
        fetch('cart_action.php', {
            method: 'POST',
            headers: {'Content-Type':'application/x-www-form-urlencoded'},
            body: new URLSearchParams({
                action:'add',
                id_barang:id,
                jumlah:jumlah,
                tgl_mulai_sewa: tgl_mulai,
                tgl_kembali_est: tgl_kembali
            })
        }).then(r=>r.json()).then(res=>{
            if(res.success) { 
                updateCartBadge();
                alert('Berhasil ditambahkan ke keranjang!');
                var modalEl = document.getElementById('modalSewa');
                var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modal.hide();
            } else alert('Gagal menambahkan: ' + (res.msg || 'Harap lengkapi form dengan benar atau coba lagi.'));
        });
    }

    /**
     * Memperbarui angka di badge keranjang
     */
    function updateCartBadge() {
        fetch('cart_action.php', {
            method: 'POST',
            headers: {'Content-Type':'application/x-www-form-urlencoded'},
            body: new URLSearchParams({action:'info'})
        }).then(r=>r.json()).then(res=>{
            const badge = document.getElementById('cartBadge');
            if(badge && res.success) {
                const count = res.items.length || 0;
                badge.textContent = count;
                badge.hidden = count === 0;
            }
        }).catch(error => {
            console.error('Error fetching cart info:', error);
        });
    }

    document.addEventListener('DOMContentLoaded', updateCartBadge);
</script>

</body>
</html>