<?php
// Halaman Riwayat Sewa untuk customer
if (!isset($_SESSION)) session_start();
require_once '../admin/koneksi.php';

$id_cust = $_SESSION['id_cust'] ?? null;
if (!$id_cust) {
    echo '<div class="alert alert-warning">Anda belum login.</div>';
    exit;
}

$conn = Database::getInstance()->getConnection();
$sql = "SELECT ts.no_transaksi, ts.tgl_sewa, ts.tgl_tenggat_pengembalian, ts.total_harga,
    GROUP_CONCAT(tb.nama_barang SEPARATOR ', ') as barang, GROUP_CONCAT(tds.jumlah SEPARATOR ', ') as jumlah
    FROM tb_sewa ts
    JOIN tb_detail_sewa tds ON ts.no_transaksi = tds.no_transaksi
    JOIN tb_barang tb ON tds.id_barang = tb.id_barang
    WHERE ts.id_cust = ? AND ts.status = 'Disetujui'
    GROUP BY ts.no_transaksi
    ORDER BY ts.tgl_sewa DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $id_cust);
$stmt->execute();
$result = $stmt->get_result();
?>
<div class="card mb-4">
    <div class="card-body">
        <h3 class="mb-3"><i class="bi bi-clock-history"></i> Riwayat Sewa Anda</h3>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No Transaksi</th>
                        <th>Barang</th>
                        <th>Jumlah</th>
                        <th>Tgl Sewa</th>
                        <th>Tgl Kembali</th>
                        <th>Total</th>

                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['no_transaksi']) ?></td>
                        <td><?= htmlspecialchars($row['barang']) ?></td>
                        <td><?= htmlspecialchars($row['jumlah']) ?></td>
                        <td><?= $row['tgl_sewa'] ?></td>
                        <td><?= $row['tgl_tenggat_pengembalian'] ?></td>
                        <td>Rp <?= number_format($row['total_harga'],0,',','.') ?></td>

                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
