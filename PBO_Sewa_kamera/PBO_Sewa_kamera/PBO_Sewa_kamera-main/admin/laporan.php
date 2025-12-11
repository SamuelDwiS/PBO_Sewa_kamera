<?php
// Laporan Data Sewa - Bisa diprint PDF
require_once "koneksi.php";
$koneksi = Database::getInstance()->getConnection();

$sql = "SELECT ts.no_transaksi, tc.nama as nama_customer, tb.nama_barang, tds.jumlah, ts.tgl_sewa, ts.tgl_tenggat_pengembalian, ts.total_harga, ts.status
        FROM tb_sewa ts
        JOIN tb_customer tc ON ts.id_cust = tc.id_cust
        JOIN tb_detail_sewa tds ON ts.no_transaksi = tds.no_transaksi
        JOIN tb_barang tb ON tds.id_barang = tb.id_barang
        ORDER BY ts.no_transaksi DESC";
$result = $koneksi->query($sql);

?>
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0"><i class="bi bi-file-earmark-text"></i> Laporan Data Sewa</h3>
            <button onclick="window.print()" class="btn btn-danger"><i class="bi bi-printer"></i> Print PDF</button>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="laporanTable">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>No Transaksi</th>
                        <th>Customer</th>
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
                        <td><?= htmlspecialchars($row['nama_customer']) ?></td>
                        <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                        <td><?= $row['jumlah'] ?></td>
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
<style>
@media print {
    body * { visibility: hidden; }
    .card, .card * { visibility: visible; }
    .card { position: absolute; left: 0; top: 0; width: 100vw; }
    .btn, .sidebar, nav, .content > :not(.card) { display: none !important; }
}
</style>
