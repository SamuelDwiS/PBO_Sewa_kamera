<?php
require_once "koneksi.php";

class Customer
{
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function tampil()
    {
        $sql = "SELECT * FROM tb_customer ORDER BY id_cust DESC";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function tambah($data)
    {
        $id = $this->generateId();
        $nama = $data['nama'];
        $email = $data['email'];
        $username = $data['username'];
        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        $alamat = $data['alamat'] ?? '';
        $no_telp = $data['no_telp'] ?? '';

        $stmt = $this->conn->prepare("INSERT INTO tb_customer (id_cust, nama, email, username, password, alamat, no_telp) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $id, $nama, $email, $username, $password, $alamat, $no_telp);
        return $stmt->execute();
    }

    public function hapus($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM tb_customer WHERE id_cust=?");
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM tb_customer WHERE id_cust=?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function edit($id, $data)
    {
        $nama = $data['nama'];
        $email = $data['email'];
        $alamat = $data['alamat'] ?? '';
        $no_telp = $data['no_telp'] ?? '';

        $stmt = $this->conn->prepare("UPDATE tb_customer SET nama=?, email=?, alamat=?, no_telp=? WHERE id_cust=?");
        $stmt->bind_param("sssss", $nama, $email, $alamat, $no_telp, $id);
        return $stmt->execute();
    }

    public function generateId()
    {
        $sql = "SELECT MAX(CAST(SUBSTRING(id_cust, 5) AS UNSIGNED)) as max_id FROM tb_customer WHERE id_cust LIKE 'CUST%'";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        $nextId = ($row['max_id'] ?? 0) + 1;
        return 'CUST' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }

    public function checkUsername($username, $excludeId = null)
    {
        if ($excludeId) {
            $sql = "SELECT COUNT(*) as count FROM tb_customer WHERE username = ? AND id_cust != ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ss", $username, $excludeId);
        } else {
            $sql = "SELECT COUNT(*) as count FROM tb_customer WHERE username = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $username);
        }
        
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'] > 0;
    }

    public function checkEmail($email, $excludeId = null)
    {
        if ($excludeId) {
            $sql = "SELECT COUNT(*) as count FROM tb_customer WHERE email = ? AND id_cust != ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ss", $email, $excludeId);
        } else {
            $sql = "SELECT COUNT(*) as count FROM tb_customer WHERE email = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $email);
        }
        
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'] > 0;
    }

    public function findByUsernameOrEmail($username)
    {
        $sql = "SELECT * FROM tb_customer WHERE username = ? OR email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
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