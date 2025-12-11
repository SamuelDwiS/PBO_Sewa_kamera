
<?php
require_once '../admin/koneksi.php';
if (!isset($_SESSION)) session_start();

if (!isset($_SESSION['id_cust'])) {
    echo "<script>alert('Silakan login terlebih dahulu'); window.location='login.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?page=daftar_kamera');
    exit();
}

// Sinkronisasi tanggal sewa dan kembali ke semua item cart sebelum proses checkout
if (isset($_POST['tgl_sewa'], $_POST['tgl_kembali_est'])) {
    $tgl_sewa = $_POST['tgl_sewa'];
    $tgl_kembali_est = $_POST['tgl_kembali_est'];
    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $id => $item) {
            // Pastikan tanggal diisi ke setiap item
            $_SESSION['cart'][$id]['tgl_mulai_sewa'] = $tgl_sewa;
            $_SESSION['cart'][$id]['tgl_kembali_est'] = $tgl_kembali_est;
        }
    }
    // Refresh $cart agar data terbaru
    $cart = $_SESSION['cart'];
}

$id_cust = $_SESSION['id_cust'];
$cart = $_SESSION['cart'] ?? [];
$catatan = $_POST['catatan'] ?? '';
$total_harga = intval($_POST['total_harga'] ?? 0);

// Ambil tanggal sewa dan kembali dari item pertama di cart (asumsi semua sama)
if (!empty($cart)) {
    $first = reset($cart);
    $tgl_sewa = $first['tgl_mulai_sewa'] ?? ($_POST['tgl_sewa'] ?? '');
    $tgl_kembali_est = $first['tgl_kembali_est'] ?? ($_POST['tgl_kembali_est'] ?? '');
} else {
    $tgl_sewa = '';
    $tgl_kembali_est = '';
}

if (empty($cart) || empty($tgl_sewa) || empty($tgl_kembali_est)) {
    echo "<script>alert('Data sewa tidak lengkap'); window.history.back();</script>";
    exit();
}


$conn = db();
$conn->begin_transaction();

try {
    // Generate no_transaksi
    // Reset ke SEW0001 jika tidak ada data sama sekali
    $row = $conn->query("SELECT COUNT(*) as cnt FROM tb_sewa")->fetch_assoc();
    if (($row['cnt'] ?? 0) == 0) {
        $no_transaksi = 'SEW0001';
    } else {
        $row2 = $conn->query("SELECT MAX(CAST(SUBSTRING(no_transaksi, 4) AS UNSIGNED)) as max_id FROM tb_sewa WHERE no_transaksi LIKE 'SEW%'")->fetch_assoc();
        $nextId = ($row2['max_id'] ?? 0) + 1;
        $no_transaksi = 'SEW' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }

    // Insert header
    $status = 'Menunggu Persetujuan';
    $stmt = $conn->prepare("INSERT INTO tb_sewa (no_transaksi, id_cust, tgl_sewa, tgl_tenggat_pengembalian, status, jaminan, total_harga) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssi", $no_transaksi, $id_cust, $tgl_sewa, $tgl_kembali_est, $status, $catatan, $total_harga);
    if (!$stmt->execute()) throw new Exception($stmt->error);

    // Insert detail untuk setiap barang di keranjang
    $stmtDet = $conn->prepare("INSERT INTO tb_detail_sewa (no_transaksi, id_barang, id_kategori, jumlah, subtotal) VALUES (?, ?, ?, ?, ?)");
    foreach ($cart as $c) {
        // Ambil info barang
        $res = $conn->query("SELECT * FROM tb_barang WHERE id_barang='{$c['id_barang']}' LIMIT 1");
        $b = $res->fetch_assoc();
        if (!$b) throw new Exception("Barang tidak ditemukan: {$c['nama']}");
        if (intval($b['stok']) < $c['jumlah']) throw new Exception("Stok untuk {$b['nama_barang']} tidak mencukupi (tersedia: {$b['stok']})");

        $subtotal = $c['jumlah'] * intval($c['harga_sewa_hari']) * ( (strtotime($tgl_kembali_est) - strtotime($tgl_sewa)) / (60*60*24) + 1 );
        $stmtDet->bind_param("ssiii", $no_transaksi, $c['id_barang'], $c['id_kategori'], $c['jumlah'], $subtotal);
        if (!$stmtDet->execute()) throw new Exception($stmtDet->error);

        // Update stok
        $newStok = intval($b['stok']) - $c['jumlah'];
        $conn->query("UPDATE tb_barang SET stok={$newStok} WHERE id_barang='{$c['id_barang']}'");
    }

    $conn->commit();
    // Kosongkan keranjang setelah checkout
    $_SESSION['cart'] = [];

    echo "<script>alert('Sewa berhasil! No Transaksi: {$no_transaksi}'); window.location='index.php?page=sewa';</script>";
    exit();

} catch (Exception $e) {
    $conn->rollback();
    $msg = addslashes($e->getMessage());
    echo "<script>alert('Gagal membuat sewa: {$msg}'); window.history.back();</script>";
    exit();
}
