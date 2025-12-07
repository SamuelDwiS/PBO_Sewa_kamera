<?php
require_once __DIR__ . "/../koneksi.php";

class Barang
{
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function tampil()
    {
        $sql = "SELECT * FROM tb_barang";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM tb_barang WHERE id_barang = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function tambah($data)
    {
        $id = $this->generateId();
        $nama_barang = $data['nama_barang'];
        $deskripsi = $data['deskripsi'] ?? '';
        $harga_sewa_hari = $data['harga_sewa_hari'] ?? 0;
        $stok = $data['stok'] ?? 0;
        $merk = $data['merk'] ?? '';
        $kategori = $data['kategori'] ?? '';
        $gambar = $data['gambar'] ?? '';

        $stmt = $this->conn->prepare("INSERT INTO tb_barang (id_barang, nama_barang, deskripsi, harga_sewa_hari, stok, merk, kategori, gambar) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiiiss", $id, $nama_barang, $deskripsi, $harga_sewa_hari, $stok, $merk, $kategori, $gambar);
        return $stmt->execute();
    }

    public function edit($id, $data)
    {
        $nama_barang = $data['nama_barang'];
        $deskripsi = $data['deskripsi'] ?? '';
        $harga_sewa_hari = $data['harga_sewa_hari'] ?? 0;
        $stok = $data['stok'] ?? 0;
        $merk = $data['merk'] ?? '';
        $kategori = $data['kategori'] ?? '';

        $sql = "UPDATE tb_barang SET nama_barang=?, deskripsi=?, harga_sewa_hari=?, stok=?, merk=?, kategori=?";
        $types = "ssiiis";
        $params = [$nama_barang, $deskripsi, $harga_sewa_hari, $stok, $merk, $kategori];

        // Jika ada gambar baru
        if (isset($data['gambar']) && $data['gambar'] !== '') {
            $sql .= ", gambar=?";
            $types .= "s";
            $params[] = $data['gambar'];
        }

        $sql .= " WHERE id_barang=?";
        $types .= "s";
        $params[] = $id;

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        return $stmt->execute();
    }

    public function hapus($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM tb_barang WHERE id_barang=?");
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    public function generateId()
    {
        $sql = "SELECT MAX(CAST(SUBSTRING(id_barang, 5) AS UNSIGNED)) as max_id FROM tb_barang WHERE id_barang LIKE 'BRNG%'";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        $nextId = ($row['max_id'] ?? 0) + 1;
        return 'BRNG' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }

    public function uploadFile()
    {
        $targetDir = "uploads/";
        
        // Buat folder jika belum ada
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        if (!isset($_FILES['gambar']) || $_FILES['gambar']['size'] == 0) {
            return null;
        }

        $fileName = basename($_FILES["gambar"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        // Verifikasi Format File
        $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
        if (in_array(strtolower($fileType), $allowTypes)) {
            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $targetFilePath)) {
                return $fileName;
            }
        }
        return null;
    }

    public function updateStok($id, $jumlah_kurang)
    {
        $stmt = $this->conn->prepare("UPDATE tb_barang SET stok = stok - ? WHERE id_barang = ?");
        $stmt->bind_param("is", $jumlah_kurang, $id);
        return $stmt->execute();
    }

    public function restoreStok($id, $jumlah_tambah)
    {
        $stmt = $this->conn->prepare("UPDATE tb_barang SET stok = stok + ? WHERE id_barang = ?");
        $stmt->bind_param("is", $jumlah_tambah, $id);
        return $stmt->execute();
    }

    /**
     * Ambil semua data merk dari tb_merk
     */
    public function getAllMerk()
    {
        $sql = "SELECT id_merk, merk FROM tb_merk ORDER BY merk ASC";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Ambil semua data kategori dari tb_kategori
     */
    public function getAllKategori()
    {
        $sql = "SELECT id_kategori, kategori FROM tb_kategori ORDER BY kategori ASC";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Generate ID Barang berikutnya
     */
    public function getNextId()
    {
        return $this->generateId();
    }
}
