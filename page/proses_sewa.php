<?php
/**
 * PROSES SEWA - Memproses form sewa dari customer
 * Menggunakan pattern Header-Detail (tb_sewa & tb_detail_sewa)
 */
require '../admin/koneksi.php';

// Check login
if (!isset($_SESSION['id_cust'])) {
    echo "<script>alert('Silakan login terlebih dahulu'); window.location='login.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi input
    if (empty($_POST['id_barang']) || empty($_POST['jumlah']) || empty($_POST['tgl_sewa']) || empty($_POST['durasi_sewa'])) {
        echo "<script>alert('Mohon isi semua field yang diperlukan'); window.history.back();</script>";
        exit();
    }

    // Persiapan data
    $id_cust = $_SESSION['id_cust'];
    $id_barang = $_POST['id_barang'];
    $tgl_sewa = $_POST['tgl_sewa'];
    $durasi_sewa = intval($_POST['durasi_sewa']);
    $jumlah = intval($_POST['jumlah']);
    $total_sewa = intval($_POST['total_sewa']);
    $catatan = $_POST['catatan'] ?? '';

    // Gunakan factory untuk cek stok kamera
    $barang = model('barang');
    $kamera = $barang->getById($id_barang);

    if (!$kamera) {
        echo "<script>alert('Kamera tidak ditemukan'); window.location='index.php?page=daftar_kamera';</script>";
        exit();
    }

    if (intval($kamera['stok']) < $jumlah) {
        echo "<script>alert('Stok kamera tidak mencukupi. Stok tersedia: " . $kamera['stok'] . "'); window.history.back();</script>";
        exit();
    }

    // Generate ID Sewa (no_transaksi)
    $conn = db();
    $sql = "SELECT no_transaksi FROM tb_sewa ORDER BY no_transaksi DESC LIMIT 1";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastId = $row['no_transaksi'];
        $number = (int)substr($lastId, -3) + 1;
    } else {
        $number = 1;
    }
    
    $no_transaksi = "SWA" . str_pad($number, 3, "0", STR_PAD_LEFT);

    // Calculate tanggal tenggat pengembalian
    $tglSewa = new DateTime($tgl_sewa);
    $tglSewa->add(new DateInterval('P' . $durasi_sewa . 'D'));
    $tgl_tenggat = $tglSewa->format('Y-m-d');

    // Mulai transaksi database (untuk consistency)
    $conn->begin_transaction();

    try {
        // 1. INSERT ke tb_sewa (HEADER)
        $sqlSewa = "INSERT INTO tb_sewa (no_transaksi, id_cust, tgl_sewa, tgl_tenggat_pengembalian, status, jaminan, total_harga)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmtSewa = $conn->prepare($sqlSewa);
        if (!$stmtSewa) throw new Exception("Prepare failed: " . $conn->error);

        $status = 'Aktif';
        $jaminan = $catatan; // Atau bisa dari field terpisah
        $total_harga = $total_sewa;

        $stmtSewa->bind_param(
            "ssssssi",
            $no_transaksi,
            $id_cust,
            $tgl_sewa,
            $tgl_tenggat,
            $status,
            $jaminan,
            $total_harga
        );

        if (!$stmtSewa->execute()) {
            throw new Exception("Execute failed: " . $stmtSewa->error);
        }

        // 2. INSERT ke tb_detail_sewa (DETAIL)
        $sqlDetail = "INSERT INTO tb_detail_sewa (no_transaksi, id_barang, id_kategori, jumlah, subtotal)
                      VALUES (?, ?, ?, ?, ?)";
        
        $stmtDetail = $conn->prepare($sqlDetail);
        if (!$stmtDetail) throw new Exception("Prepare detail failed: " . $conn->error);

        $id_kategori = $kamera['kategori']; // Atau ambil dari tb_kategori jika perlu
        $subtotal = $total_sewa; // Dalam contoh ini hanya 1 item, jadi subtotal = total

        $stmtDetail->bind_param(
            "sssii",
            $no_transaksi,
            $id_barang,
            $id_kategori,
            $jumlah,
            $subtotal
        );

        if (!$stmtDetail->execute()) {
            throw new Exception("Execute detail failed: " . $stmtDetail->error);
        }

        // 3. UPDATE stok kamera
        $newStok = intval($kamera['stok']) - $jumlah;
        $sqlUpdateStok = "UPDATE tb_barang SET stok = ? WHERE id_barang = ?";
        
        $stmtStok = $conn->prepare($sqlUpdateStok);
        if (!$stmtStok) throw new Exception("Prepare stok failed: " . $conn->error);

        $stmtStok->bind_param("is", $newStok, $id_barang);

        if (!$stmtStok->execute()) {
            throw new Exception("Execute stok failed: " . $stmtStok->error);
        }

        // Commit jika semua berhasil
        $conn->commit();

        echo "<script>alert('Sewa berhasil dibuat! ID Transaksi: {$no_transaksi}\\n\\nSilakan lakukan pengembalian sesuai jadwal'); window.location='index.php?page=sewa';</script>";
        
    } catch (Exception $e) {
        // Rollback jika ada error
        $conn->rollback();
        echo "<script>alert('Sewa gagal dibuat: " . addslashes($e->getMessage()) . "\\n\\nSilakan coba lagi'); window.history.back();</script>";
    }
    
} else {
    echo "<script>window.location='index.php?page=daftar_kamera';</script>";
}
?>

