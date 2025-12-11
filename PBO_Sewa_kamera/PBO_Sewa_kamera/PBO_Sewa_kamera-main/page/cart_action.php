<?php
require_once '../admin/koneksi.php';
header('Content-Type: application/json');

if (!isset($_SESSION)) session_start();

$action = $_POST['action'] ?? '';
$id_barang = $_POST['id_barang'] ?? null;
$jumlah = isset($_POST['jumlah']) ? intval($_POST['jumlah']) : 1;

// Pastikan session cart ada
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

switch ($action) {

    // Tambah barang ke cart sewa
    case 'add':
        if (!$id_barang) {
            echo json_encode(['success'=>false,'msg'=>'id_barang kosong']);
            exit;
        }

        $barangModel = model('barang');
        $b = $barangModel->getById($id_barang);
        if (!$b) {
            echo json_encode(['success'=>false,'msg'=>'Barang tidak ditemukan']);
            exit;
        }

        // Jika sudah ada, tolak penambahan (hanya boleh satu kali sewa per barang)
        if (isset($_SESSION['cart'][$id_barang])) {
            echo json_encode(['success'=>false,'msg'=>'Barang sudah ada di keranjang. Tidak boleh sewa dua kali untuk barang yang sama.']);
            exit;
        } else {
            // Simpan juga tanggal mulai dan kembali jika dikirim
            $tgl_mulai = $_POST['tgl_mulai_sewa'] ?? null;
            $tgl_kembali = $_POST['tgl_kembali_est'] ?? null;
            $_SESSION['cart'][$id_barang] = [
                'id_barang' => $id_barang,
                'id_kategori' => $b['kategori'],
                'nama' => $b['nama_barang'],
                'harga_sewa_hari' => intval($b['harga_sewa_hari']),
                'jumlah' => $jumlah,
                'stok' => intval($b['stok']),
                'tgl_mulai_sewa' => $tgl_mulai,
                'tgl_kembali_est' => $tgl_kembali
            ];
        }

        echo json_encode(['success'=>true, 'cart_count'=>count($_SESSION['cart'])]);
        break;

    // Hapus barang dari cart
    case 'remove':
        if ($id_barang && isset($_SESSION['cart'][$id_barang])) {
            unset($_SESSION['cart'][$id_barang]);
        }
        echo json_encode(['success'=>true, 'cart_count'=>count($_SESSION['cart'])]);
        break;

    // Kosongkan cart
    case 'clear':
        $_SESSION['cart'] = [];
        echo json_encode(['success'=>true, 'cart_count'=>0]);
        break;

    // Update jumlah barang di cart
    case 'update':
        if (!$id_barang) { 
            echo json_encode(['success'=>false]); 
            exit; 
        }
        $newQty = max(1, $jumlah);
        if (isset($_SESSION['cart'][$id_barang])) {
            $_SESSION['cart'][$id_barang]['jumlah'] = $newQty;
        }
        echo json_encode(['success'=>true]);
        break;

    // Ambil info cart untuk badge atau daftar cart
    case 'info':
        $items = array_values($_SESSION['cart']);
        echo json_encode(['success'=>true, 'items'=>$items]);
        break;

    default:
        echo json_encode(['success'=>false,'msg'=>'Action tidak valid']);
}
