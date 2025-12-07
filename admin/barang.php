<?php
require 'koneksi.php';
require 'model/barang.php';

$barang = new Barang();
$daftarBarang = $barang->tampil();
$daftarMerk = $barang->getAllMerk();
$daftarKategori = $barang->getAllKategori();

$message = "";
$alertType = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_barang = $_POST['id_barang'] ?? '';
    $nama_barang = $_POST['nama_barang'] ?? '';
    $deskripsi = $_POST['deskripsi'] ?? '';
    $harga_sewa_hari = $_POST['harga_sewa_hari'] ?? 0;
    $stok = $_POST['stok'] ?? 0;
    $merk = $_POST['merk'] ?? '';
    $kategori = $_POST['kategori'] ?? '';
    $gambar = '';

    if (isset($_FILES['gambar']) && $_FILES['gambar']['size'] > 0) {
        $gambar = $barang->uploadFile();
        if (!$gambar) {
            $message = "Format file tidak didukung!";
            $alertType = "danger";
        }
    }

    if (isset($_POST['simpan']) || isset($_POST['update'])) {
        $data = [
            'nama_barang' => $nama_barang,
            'deskripsi' => $deskripsi,
            'harga_sewa_hari' => $harga_sewa_hari,
            'stok' => $stok,
            'merk' => $merk,
            'kategori' => $kategori
        ];

        if (!empty($gambar)) {
            $data['gambar'] = $gambar;
        }

        if (isset($_POST['simpan'])) {
            if ($barang->tambah($data)) {
                header("Location: index.php?page=barang&msg=added");
                exit;
            }
        } elseif (isset($_POST['update'])) {
            if ($barang->edit($id_barang, $data)) {
                header("Location: index.php?page=barang&msg=updated");
                exit;
            }
        }
    }
}

// Handle delete
if (isset($_GET['hapus'])) {
    if ($barang->hapus($_GET['hapus'])) {
        header("Location: index.php?page=barang&msg=deleted");
        exit;
    }
}

// Get alert message
if (isset($_GET['msg'])) {
    $messages = [
        'added' => ['Data barang berhasil disimpan!', 'success'],
        'updated' => ['Data barang berhasil diupdate!', 'info'],
        'deleted' => ['Data barang berhasil dihapus!', 'warning']
    ];
    if (isset($messages[$_GET['msg']])) {
        list($message, $alertType) = $messages[$_GET['msg']];
    }
}

$editData = null;
if (isset($_GET['edit'])) {
    $editData = $barang->getById($_GET['edit']);
}

$nextId = $barang->getNextId();
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
        <h2 class="fw-bold"><i class=""></i> Manajemen Barang</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalForm">
            <i class="bi bi-plus-circle"></i> Tambah Barang
        </button>
    </div>

    <!-- Tabel Barang -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Merk</th>
                        <th>Kategori</th>
                        <th>Harga/Hari</th>
                        <th>Stok</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($daftarBarang)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada data barang</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($daftarBarang as $item): 
                            $merk_name = '';
                            $kategori_name = '';
                            foreach ($daftarMerk as $m) {
                                if ($m['id_merk'] == $item['merk']) {
                                    $merk_name = $m['merk'];
                                    break;
                                }
                            }
                            foreach ($daftarKategori as $k) {
                                if ($k['id_kategori'] == $item['kategori']) {
                                    $kategori_name = $k['kategori'];
                                    break;
                                }
                            }
                        ?>
                            <tr>
                                <td><span><?php echo $item['id_barang']; ?></span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <?php if (!empty($item['gambar']) && file_exists('uploads/' . $item['gambar'])): ?>
                                            <img src="uploads/<?php echo $item['gambar']; ?>" alt="<?php echo $item['nama_barang']; ?>" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                        <?php else: ?>
                                            <div style="width: 40px; height: 40px; background-color: #e9ecef; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <strong><?php echo $item['nama_barang']; ?></strong>
                                            <br><small class="text-muted"><?php echo substr($item['deskripsi'], 0, 35); ?>...</small>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo $merk_name; ?></td>
                                <td><?php echo $kategori_name; ?></td>
                                <td>Rp <?php echo number_format($item['harga_sewa_hari'], 0, ',', '.'); ?></td>
                                <td><?php echo $item['stok']; ?></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-warning" title="Edit" data-bs-toggle="modal" data-bs-target="#modalForm" onclick="editBarang('<?php echo $item['id_barang']; ?>')">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <a href="index.php?page=barang&hapus=<?php echo $item['id_barang']; ?>" 
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
</div>

<!-- Modal Form -->
<div class="modal fade" id="modalForm" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">ID Barang</label>
                        <input type="text" class="form-control" id="id_barang" name="id_barang" 
                               value="<?php echo $editData['id_barang'] ?? $nextId; ?>" readonly>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Barang</label>
                            <input type="text" class="form-control" name="nama_barang" 
                                   value="<?php echo $editData['nama_barang'] ?? ''; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga/Hari</label>
                            <input type="number" class="form-control" name="harga_sewa_hari" 
                                   value="<?php echo $editData['harga_sewa_hari'] ?? 0; ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Merk</label>
                            <select class="form-control" name="merk" required>
                                <option value="">-- Pilih --</option>
                                <?php foreach ($daftarMerk as $m): ?>
                                    <option value="<?php echo $m['id_merk']; ?>" 
                                            <?php echo ($editData['merk'] ?? '') === $m['id_merk'] ? 'selected' : ''; ?>>
                                        <?php echo $m['merk']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kategori</label>
                            <select class="form-control" name="kategori" required>
                                <option value="">-- Pilih --</option>
                                <?php foreach ($daftarKategori as $k): ?>
                                    <option value="<?php echo $k['id_kategori']; ?>" 
                                            <?php echo ($editData['kategori'] ?? '') === $k['id_kategori'] ? 'selected' : ''; ?>>
                                        <?php echo $k['kategori']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stok</label>
                            <input type="number" class="form-control" name="stok" 
                                   value="<?php echo $editData['stok'] ?? 0; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Gambar</label>
                            <input type="file" class="form-control" name="gambar" accept="image/*">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" rows="2" 
                                  required><?php echo $editData['deskripsi'] ?? ''; ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <?php if ($editData): ?>
                        <button type="submit" name="update" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Update
                        </button>
                    <?php else: ?>
                        <button type="submit" name="simpan" class="btn btn-success">
                            <i class="bi bi-plus-circle"></i> Simpan
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Reset form dan modal title saat tambah
document.getElementById('modalForm').addEventListener('hidden.bs.modal', function() {
    document.querySelector('form').reset();
    document.getElementById('modalTitle').textContent = 'Tambah Barang';
});

// Jika ada edit, buka modal otomatis
<?php if ($editData): ?>
    document.addEventListener('DOMContentLoaded', function() {
        new bootstrap.Modal(document.getElementById('modalForm')).show();
        document.getElementById('modalTitle').textContent = 'Edit Barang';
    });
<?php endif; ?>

// Fungsi untuk edit barang
function editBarang(id) {
    window.location.href = 'index.php?page=barang&edit=' + id;
}
</script>

