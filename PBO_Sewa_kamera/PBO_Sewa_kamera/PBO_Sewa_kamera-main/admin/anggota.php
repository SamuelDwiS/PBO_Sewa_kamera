<?php
// Halaman Data Customer (anggota)
include_once "koneksi.php";
$koneksi = Database::getInstance()->getConnection();
$query = mysqli_query($koneksi, "SELECT * FROM tb_customer");
?>
<div class="card mb-4">
    <div class="card-body">
        <h3 class="mb-3"><i class="bi bi-people"></i> Data Customer</h3>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>ID Customer</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Alamat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; while($row = mysqli_fetch_assoc($query)): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $row['id_cust'] ?></td>
                        <td><?= $row['nama'] ?></td>
                        <td><?= $row['email'] ?></td>
                        <td><?= $row['no_telp'] ?></td>
                        <td><?= $row['alamat'] ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
