<?php
require 'koneksi.php';

$db = db();

// Handle update status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $no_transaksi = $_POST['no_transaksi'] ?? '';
    $status_baru = $_POST['status_baru'] ?? '';
    
    $stmt = $db->prepare("UPDATE tb_sewa SET status = ? WHERE no_transaksi = ?");
    $stmt->bind_param("ss", $status_baru, $no_transaksi);
    
    if ($stmt->execute()) {
        // Jika status disetujui, ubah stok barang ke "Sedang Disewa"
        if ($status_baru === 'Disetujui') {
            // Bisa tambahkan logic lainnya di sini
        }
        header("Location: index.php?page=sewa&msg=updated");
        exit;
    }
}

// Ambil data sewa dengan join ke customer dan barang
$sql = "SELECT 
            ts.no_transaksi,
            ts.id_cust,
            tc.nama as nama_customer,
            tds.id_barang,
            tb.nama_barang,
            ts.tgl_sewa,
            ts.tgl_tenggat_pengembalian,
            tds.jumlah,
            ts.total_harga,
            ts.status
        FROM tb_sewa ts
        JOIN tb_customer tc ON ts.id_cust = tc.id_cust
        JOIN tb_detail_sewa tds ON ts.no_transaksi = tds.no_transaksi
        JOIN tb_barang tb ON tds.id_barang = tb.id_barang
        ORDER BY ts.no_transaksi DESC";

$result = $db->query($sql);
$daftarSewa = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

// Handle hapus
if (isset($_GET['hapus'])) {
    $no_transaksi = $_GET['hapus'];
    
    // Get jumlah untuk restore stok
    $stmt = $db->prepare("SELECT tds.id_barang, tds.jumlah FROM tb_detail_sewa tds WHERE tds.no_transaksi = ?");
    $stmt->bind_param("s", $no_transaksi);
    $stmt->execute();
    $detail_result = $stmt->get_result();
    
    while ($detail = $detail_result->fetch_assoc()) {
        $barang_model = model('barang');
        $barang_model->restoreStok($detail['id_barang'], $detail['jumlah']);
    }
    
    // Delete dari tb_detail_sewa
    $stmt = $db->prepare("DELETE FROM tb_detail_sewa WHERE no_transaksi = ?");
    $stmt->bind_param("s", $no_transaksi);
    $stmt->execute();
    
    // Delete dari tb_sewa
    $stmt = $db->prepare("DELETE FROM tb_sewa WHERE no_transaksi = ?");
    $stmt->bind_param("s", $no_transaksi);
    if ($stmt->execute()) {
        header("Location: index.php?page=sewa&msg=deleted");
        exit;
    }
}

// Get alert message
$message = "";
$alertType = "";
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'deleted') {
        $message = "Data sewa berhasil dihapus!";
        $alertType = "warning";
    } elseif ($_GET['msg'] === 'updated') {
        $message = "Status sewa berhasil diubah!";
        $alertType = "success";
    }
}
?>

<div class="container-fluid mt-4">
    <!-- Alert -->
    <?php if ($message): ?>
        <div class="alert alert-<?php echo $alertType; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-cart-check"></i> Data Sewa Kamera</h2>
    </div>

    <!-- Tabel Sewa -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No Transaksi</th>
                        <th>Customer</th>
                        <th>Barang</th>
                        <th>Jumlah</th>
                        <th>Tgl Mulai</th>
                        <th>Tgl Kembali</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($daftarSewa)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">Belum ada data sewa</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($daftarSewa as $item): ?>
                            <tr>
                                <td><span class="badge bg-primary"><?php echo $item['no_transaksi']; ?></span></td>
                                <td>
                                    <strong><?php echo $item['nama_customer']; ?></strong>
                                    <br><small class="text-muted"><?php echo $item['id_cust']; ?></small>
                                </td>
                                <td><?php echo $item['nama_barang']; ?></td>
                                <td class="text-center"><?php echo $item['jumlah']; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($item['tgl_sewa'])); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($item['tgl_tenggat_pengembalian'])); ?></td>
                                <td>Rp <?php echo number_format($item['total_harga'], 0, ',', '.'); ?></td>
                                <td>
                                    <?php if ($item['status'] === 'Menunggu Persetujuan'): ?>
                                        <span class="badge bg-info">Menunggu Persetujuan</span>
                                    <?php elseif ($item['status'] === 'Disetujui'): ?>
                                        <span class="badge bg-success">Disetujui</span>
                                    <?php elseif ($item['status'] === 'Ditolak'): ?>
                                        <span class="badge bg-danger">Ditolak</span>
                                    <?php elseif ($item['status'] === 'Sedang Disewa'): ?>
                                        <span class="badge bg-warning text-dark">Sedang Disewa</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?php echo $item['status']; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalEditStatus" 
                                            onclick="setStatusModal('<?php echo $item['no_transaksi']; ?>', '<?php echo $item['status']; ?>')" title="Edit Status">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <a href="index.php?page=sewa&hapus=<?php echo $item['no_transaksi']; ?>" 
                                       class="btn btn-sm btn-danger" onclick="return confirm('Hapus data ini?')" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Edit Status -->
    <div class="modal fade" id="modalEditStatus" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Status Sewa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">No Transaksi</label>
                            <input type="text" class="form-control" id="no_transaksi_display" readonly>
                            <input type="hidden" name="no_transaksi" id="no_transaksi" value="">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status Baru</label>
                            <select class="form-control" name="status_baru" id="status_baru" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="Menunggu Persetujuan">Menunggu Persetujuan</option>
                                <option value="Disetujui">Disetujui</option>
                                <option value="Ditolak">Ditolak</option>
                                <option value="Sedang Disewa">Sedang Disewa</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="update_status" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function setStatusModal(noTransaksi, statusSaat) {
            document.getElementById('no_transaksi').value = noTransaksi;
            document.getElementById('no_transaksi_display').value = noTransaksi;
            document.getElementById('status_baru').value = statusSaat;
        }
    </script>
</div>

