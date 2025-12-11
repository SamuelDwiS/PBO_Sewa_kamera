<?php
// Halaman Data Pengembalian/Transaksi
include_once "koneksi.php";
$koneksi = Database::getInstance()->getConnection();
$sql = "SELECT ts.no_transaksi, ts.id_cust, tc.nama as nama_customer, tds.id_barang, tb.nama_barang, ts.tgl_sewa, ts.tgl_tenggat_pengembalian, tds.jumlah, ts.total_harga, ts.status
        FROM tb_sewa ts
        JOIN tb_customer tc ON ts.id_cust = tc.id_cust
        JOIN tb_detail_sewa tds ON ts.no_transaksi = tds.no_transaksi
        JOIN tb_barang tb ON tds.id_barang = tb.id_barang
        ORDER BY ts.no_transaksi DESC";
$result = $koneksi->query($sql);
?>
<div class="card mb-4">
    <div class="card-body">
        <h3 class="mb-3"><i class="bi bi-cash-coin"></i> Data Pengembalian</h3>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No Transaksi</th>
                        <th>Customer</th>
                        <th>Barang</th>
                        <th>Jumlah</th>
                        <th>Tgl Mulai</th>
                        <th>Tgl Kembali Estimasi</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['no_transaksi']) ?></td>
                        <td><b><?= htmlspecialchars($row['nama_customer']) ?></b></td>
                        <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                        <td><?= $row['jumlah'] ?></td>
                        <td><?= $row['tgl_sewa'] ?></td>
                        <td><?= $row['tgl_tenggat_pengembalian'] ?></td>
                        <td>Rp <?= number_format($row['total_harga'],0,',','.') ?></td>
                        <td>
                            <?php if($row['status']=='Disetujui'): ?>
                                <span class="badge bg-success">Disetujui</span>
                            <?php elseif($row['status']=='Selesai'): ?>
                                <span class="badge bg-primary">Selesai</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark"><?= htmlspecialchars($row['status']) ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
