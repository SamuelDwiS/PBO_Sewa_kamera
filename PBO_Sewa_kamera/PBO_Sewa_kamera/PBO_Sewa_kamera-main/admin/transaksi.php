<?php
// Halaman Transaksi Sewa (Ringkasan)
include_once "koneksi.php";
$koneksi = Database::getInstance()->getConnection();
$sql = "SELECT ts.no_transaksi, tc.nama as nama_customer, ts.tgl_sewa, ts.tgl_tenggat_pengembalian, ts.total_harga, ts.status
        FROM tb_sewa ts
        JOIN tb_customer tc ON ts.id_cust = tc.id_cust
        ORDER BY ts.no_transaksi DESC";
$result = $koneksi->query($sql);
?>
<div class="card mb-4">
    <div class="card-body">
        <h3 class="mb-3"><i class="bi bi-cash-coin"></i> Daftar Transaksi Sewa</h3>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No Transaksi</th>
                        <th>Customer</th>
                        <th>Tgl Sewa</th>
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
                        <td><?= htmlspecialchars($row['nama_customer']) ?></td>
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
