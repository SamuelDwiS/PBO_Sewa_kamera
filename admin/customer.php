<?php
require_once "koneksi.php";

class Customer extends Database
{

    public function tampil()
    {
        return $this->conn->query("SELECT * FROM t_user ORDER BY id_pelanggan DESC");
    }
    public function tambah($data)
    {
        $stmt = $this->conn->prepare("INSERT INTO t_user (id_pelanggan, nama, alamat, email, no_telp, username, password) VALUES (?, ?, ?, ?,?)");
        $stmt->bind_param("isss", $data['id_user'], $data['nama'], $data['alamat'], $data['email'],  $data['no_telp'], $data['username'], $data['no_tlp']);
        return $stmt->execute();
    }

    public function hapus($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM t_user WHERE id_user=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM t_user WHERE id_user=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function edit($id, $data)
    {
        $stmt = $this->conn->prepare("UPDATE t_user SET nama=?, email= ?, alamat=?, email=?, no_tlp=?, username=?, password= ?, WHERE id_=?");
        $stmt->bind_param("sssi", $data['nama'], $data['alamat'], $data['no_tlp'], $id);
        return $stmt->execute();
    }
}

$pelanggan = new Customer();

$message = "";
$alertType = "";

if (isset($_GET['msg'])) {
    if ($_GET['msg'] === "added") {
        $message   = "Data berhasil disimpan!";
        $alertType = "success";
    } elseif ($_GET['msg'] === "updated") {
        $message   = "Data berhasil diupdate!";
        $alertType = "info";
    } elseif ($_GET['msg'] === "deleted") {
        $message   = "Data berhasil dihapus!";
        $alertType = "warning";
    }
}

if (isset($_POST['simpan'])) {

    $data = [
        'id_pelanggan'      => $_POST['id_pelanggan'],
        'nama'   => $_POST['nama'],
        'alamat'  => $_POST['alamat'],
        'no_tlp' => $_POST['no_tlp'],
    ];


    if ($pelanggan->tambah($data)) {
        header("Location: index.php?page=pelanggan&msg=added");
        exit;
    }
}

if (isset($_POST['update'])) {
   $id      = $_POST['id_pelanggan'];
        $data = [
        'nama'  => $_POST['nama'],
        'alamat'  => $_POST['alamat'],
        'no_telp' => $_POST['no_tlp'],
    ];
    

    if ($pelanggan->edit($id, $data)) {
        header("Location: index.php?page=pelanggan&msg=updated");
        exit;
    }
}

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    if ($pelanggan->hapus($id)) {
        header("Location: index.php?page=pelanggan&msg=deleted");
        exit;
    }
}

$editData = null;
if (isset($_GET['edit'])) {
    $editData = $pelanggan->getById($_GET['edit']);
}

?>

    <h4> Data Pelanggan</h4>
    <p>Halaman ini digunakan untuk menampilkan dan mengelola data pelanggan bengkel</p>
<div class="container mt-4">
    <?php if ($message != ""): ?>
        <div class="alert alert-<?php echo $alertType; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>



    <form method="POST" action="">
        <div class="mb-3">
            <label for="id_pelanggan" class="form-label">ID</label>
            <input type="text" class="form-control" id="id_pelanggan" name="id_pelanggan"
                value="<?php echo $editData['id_pelanggan'] ?? ''; ?>"
                <?php echo $editData ? "readonly" : ""; ?> required>
        </div>
        <div class="mb-3">
            <label for="nama" class="form-label">Nama Pelanggan</label>
            <input type="text" class="form-control" id="nama" name="nama"
                value="<?php echo $editData['nama'] ?? ''; ?>" required>
        </div>
        <div class="mb-3">
            <label for="alamat" class="form-label">Alamat</label>
            <textarea class="form-control" id="alamat" name="alamat" rows="3" required><?php echo $editData['alamat'] ?? ''; ?></textarea>
        </div>
        <div class="mb-3">
            <label for="no_tlp" class="form-label">Telepon</label>
            <input type="text" class="form-control" id="no_tlp" name="no_tlp"
                value="<?php echo $editData['no_tlp'] ?? ''; ?>" required>
        </div>
        <?php if ($editData): ?>
            <button type="submit" name="update" class="btn btn-primary">Update</button>
            <a href="index.php?page=pelanggan" class="btn btn-secondary">Batal</a>
        <?php else: ?>
            <button type="submit" name="simpan" class="btn btn-success">Simpan</button>
            <button type="reset" name="reset"class= "btn btn-danger">Reset</button>
        <?php endif; ?>
    </form>

        <hr>

    <h4>Daftar Pelanggan</h4>
    <table class="table table-bordered table-striped">
        <thead class="table-secondary">
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>Telepon</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $pelanggan->tampil();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['id_pelanggan']}</td>
                        <td>{$row['nama']}</td>
                        <td>{$row['alamat']}</td>
                        <td>{$row['no_tlp']}</td>
                        <td>
                            <a href='index.php?page=pelanggan&edit={$row['id_pelanggan']}' class='btn btn-warning btn-sm'>Edit</a>
                            <a href='index.php?page=pelanggan&hapus={$row['id_pelanggan']}' class='btn btn-danger btn-sm' onclick=\"return confirm('Yakin hapus data?');\">Hapus</a>
                        </td>
                      </tr>";
                }
            } else {
                echo "<tr><td colspan='5' class='text-center'>Belum ada data anggota</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>