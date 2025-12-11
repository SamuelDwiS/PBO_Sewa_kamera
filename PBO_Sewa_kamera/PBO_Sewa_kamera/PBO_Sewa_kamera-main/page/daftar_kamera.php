<?php
/**
 * DAFTAR KAMERA - Halaman untuk menampilkan daftar kamera dengan card
 */
require '../admin/koneksi.php';

// Koneksi database
$conn = db();

// Query dengan JOIN untuk ambil nama merek dan kategori
$sql = "SELECT b.*, m.merk AS merk, k.kategori AS kategori
        FROM tb_barang b
        LEFT JOIN tb_merk m ON b.merk = m.id_merk
        LEFT JOIN tb_kategori k ON b.kategori = k.id_kategori";

$result = $conn->query($sql);

$daftarKamera = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $daftarKamera[] = $row;
    }
}

$isLoggedIn = isset($_SESSION['id_cust']);
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h2 class="fw-bold mb-4">
            <i class="bi bi-camera"></i> Daftar Kamera
        </h2>
    </div>
</div>

<?php if (empty($daftarKamera)) { ?>
    <div class="alert alert-info text-center">
        <p>Tidak ada kamera yang tersedia saat ini</p>
    </div>
<?php } else { ?>
    <div class="row">
        <?php foreach ($daftarKamera as $kamera) { 
            $stok = intval($kamera['stok']);
        ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100 hover-card">

                    <!-- Gambar Kamera -->
                    <div style="height: 250px; overflow: hidden; background-color: #f0f0f0;">
                        <?php
                        $gambarPath = "../admin/uploads/" . $kamera['gambar'];
                        if (!empty($kamera['gambar']) && file_exists($gambarPath)) {
                            echo '<img src="' . $gambarPath . '" alt="' . htmlspecialchars($kamera['nama_barang'], ENT_QUOTES) . '" class="card-img-top" style="width: 100%; height: 100%; object-fit: cover;">';
                        } else {
                            echo '<div class="d-flex align-items-center justify-content-center h-100"><i class="bi bi-image fs-1 text-muted"></i></div>';
                        }
                        ?>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body">
                        <h5 class="card-title fw-bold"><?php echo htmlspecialchars($kamera['nama_barang']); ?></h5>
                        <div class="mb-2">
                            <div class="fw-bold mb-1" style="font-size:1rem;">Spesifikasi:</div>
                            <table class="table table-borderless table-sm mb-0" style="font-size:0.97rem;">
                                <tbody>
                                    <tr>
                                        <td class="ps-0 pe-2 text-muted" style="width: 80px;">Merek</td>
                                        <td class="fw-semibold text-dark">: <?php echo htmlspecialchars($kamera['merk']); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="ps-0 pe-2 text-muted">Kategori</td>
                                        <td class="fw-semibold text-dark">: <?php echo htmlspecialchars($kamera['kategori']); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="ps-0 pe-2 text-muted">Jenis</td>
                                        <td class="fw-semibold text-dark">: <?php echo htmlspecialchars($kamera['jenis_kamera'] ?? '-'); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="ps-0 pe-2 text-muted">Harga/Hari</td>
                                        <td class="fw-semibold text-success">: Rp <?php echo number_format($kamera['harga_sewa_hari'], 0, ',', '.'); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="ps-0 pe-2 text-muted">Stok</td>
                                        <td class="fw-semibold <?php echo ($stok > 0 ? 'text-dark' : 'text-danger'); ?>">: <?php echo ($stok > 0 ? $stok . ' tersedia' : 'Habis'); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Card Footer -->
                    <div class="card-footer bg-white">
                        <!-- Tombol Sewa Sekarang dihilangkan sesuai permintaan -->
                    </div>

                </div>
            </div>
        <?php } ?>
    </div>
<?php } ?>

<!-- Modal Form Sewa -->
<div class="modal fade" id="modalSewa" tabindex="-1" aria-labelledby="modalSewaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalSewaLabel">
                    <i class="bi bi-cash-stack"></i> Form Penyewaan Kamera
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="POST" action="proses_sewa.php">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nama Kamera</label>
                            <input type="text" class="form-control" id="nama_barang" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">ID Kamera</label>
                            <input type="hidden" name="id_barang" id="id_barang">
                            <input type="text" class="form-control" id="id_barang_display" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Harga/Hari (Rp)</label>
                            <input type="text" class="form-control" id="harga_sewa_hari" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Jumlah yang Disewa</label>
                            <input type="number" name="jumlah" id="jumlah" class="form-control" value="1" min="1" required onchange="hitungTotal()">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tanggal Mulai Sewa</label>
                            <input type="date" name="tgl_sewa" id="tgl_sewa" class="form-control" required onchange="hitungTotal()">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Durasi Sewa (Hari)</label>
                            <input type="number" name="durasi_sewa" id="durasi_sewa" class="form-control" value="1" min="1" required onchange="hitungTotal()">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Total Harga (Rp)</label>
                            <input type="text" class="form-control fs-5 fw-bold text-success" id="total_harga" readonly>
                            <input type="hidden" name="total_sewa" id="total_sewa">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Catatan (Opsional)</label>
                        <textarea name="catatan" id="catatan" class="form-control" rows="3" placeholder="Masukkan catatan..."></textarea>
                    </div>

                    <div class="alert alert-info">
                        <p class="mb-0">
                            <strong>Nama:</strong> <?php echo htmlspecialchars($_SESSION['nama']); ?><br>
                            <strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?>
                        </p>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Proses Sewa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
function setKameraData(idBarang, namaBarang, hargaSewa) {
    document.getElementById('id_barang').value = idBarang;
    document.getElementById('id_barang_display').value = idBarang;
    document.getElementById('nama_barang').value = namaBarang;
    document.getElementById('harga_sewa_hari').value = hargaSewa;
    document.getElementById('jumlah').value = 1;
    document.getElementById('durasi_sewa').value = 1;

    const today = new Date().toISOString().split('T')[0];
    document.getElementById('tgl_sewa').value = today;

    hitungTotal();
}

function hitungTotal() {
    const jumlah = parseInt(document.getElementById('jumlah').value) || 1;
    const durasi = parseInt(document.getElementById('durasi_sewa').value) || 1;
    const harga = parseInt(document.getElementById('harga_sewa_hari').value) || 0;

    const total = jumlah * durasi * harga;

    const totalFormatted = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR'
    }).format(total);

    document.getElementById('total_harga').value = totalFormatted;
    document.getElementById('total_sewa').value = total;
}
</script>
