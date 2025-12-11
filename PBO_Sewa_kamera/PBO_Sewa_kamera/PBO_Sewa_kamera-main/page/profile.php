<?php
// Halaman Profil Customer
if (!isset($_SESSION)) session_start();
require_once '../admin/koneksi.php';

// Proteksi akses profil
$id_cust = $_SESSION['id_cust'] ?? null;
if (!$id_cust) {
    echo '<div class="alert alert-warning">Anda belum login.</div>';
    exit;
}

// Jika ada parameter id_cust di URL, pastikan hanya user sendiri yang bisa akses
if (isset($_GET['id_cust']) && $_GET['id_cust'] !== $id_cust) {
    echo '<div class="alert alert-danger">Akses ditolak. Anda tidak berhak melihat profil ini.</div>';
    exit;
}

$conn = db();
// Handle update profil

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $newName = 'profile_' . $id_cust . '_' . time() . '.' . $ext;
        $target = '../admin/uploads/' . $newName;
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target)) {
            $foto = $newName;
            // Hapus foto lama jika ada
            $old = $conn->query("SELECT foto FROM tb_customer WHERE id_cust='{$id_cust}'")->fetch_assoc();
            if ($old && !empty($old['foto']) && file_exists('../admin/uploads/' . $old['foto'])) {
                @unlink('../admin/uploads/' . $old['foto']);
            }
            $conn->query("UPDATE tb_customer SET foto='{$foto}' WHERE id_cust='{$id_cust}'");
        }
        echo '<script>window.location="profile.php";</script>';
        exit;
    }

$res = $conn->query("SELECT * FROM tb_customer WHERE id_cust='{$id_cust}' LIMIT 1");
$cust = $res ? $res->fetch_assoc() : null;

if (!$cust) {
    echo '<div class="alert alert-danger">Data profil tidak ditemukan.</div>';
    exit;
}
?>
<style>
        .custom-file-upload {
            display: inline-block;
            padding: 10px 22px;
            cursor: pointer;
            background: linear-gradient(90deg, #2575fc 0%, #6a11cb 100%);
            color: #fff;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 18px;
            border: none;
            transition: background 0.2s;
        }
        .custom-file-upload:hover {
            background: linear-gradient(90deg, #6a11cb 0%, #2575fc 100%);
        }
        .custom-file-upload input[type="file"] {
            display: none;
        }
    .wa-profile-card {
        max-width: 370px;
        margin: 40px auto 0 auto;
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 4px 18px rgba(37,117,252,0.08);
        padding: 32px 24px 24px 24px;
        text-align: center;
    }
    .wa-profile-avatar {
        width: 96px;
        height: 96px;
        border-radius: 50%;
        background: #e0eafc;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        color: #2575fc;
        margin: 0 auto 18px auto;
        overflow: hidden;
    }
    .wa-profile-name {
        font-size: 1.6rem;
        font-weight: 700;
        color: #222;
        margin-bottom: 6px;
    }
    .wa-profile-info {
        color: #444;
        font-size: 1.05rem;
        margin-bottom: 2px;
    }
    .wa-profile-label {
        color: #888;
        font-size: 0.98rem;
        margin-right: 6px;
    }
    .wa-profile-divider {
        border: none;
        border-top: 1px solid #e0eafc;
        margin: 18px 0 18px 0;
    }
</style>
<div class="wa-profile-card">
    <form method="post" enctype="multipart/form-data">
        <div class="wa-profile-avatar" style="margin-bottom:12px;">
            <?php if (!empty($cust['foto']) && file_exists('../admin/uploads/' . $cust['foto'])): ?>
                <img src="../admin/uploads/<?= htmlspecialchars($cust['foto']) ?>" alt="Foto Profil" style="width:96px;height:96px;object-fit:cover;border-radius:50%;">
            <?php else: ?>
                <span><?= strtoupper(substr($cust['nama'],0,1)) ?></span>
            <?php endif; ?>
        </div>
        <label class="custom-file-upload w-100 text-center">
            <input type="file" name="foto" accept="image/*" onchange="this.form.submit()">
            Ubah Foto Profil
        </label>
    </form>
    <div class="wa-profile-name">
        <?= htmlspecialchars($cust['nama']) ?>
    </div>
    <div class="wa-profile-info">
        <span class="wa-profile-label">Email:</span><?= htmlspecialchars($cust['email']) ?>
    </div>
    <div class="wa-profile-info">
        <span class="wa-profile-label">Telepon:</span><?= isset($cust['no_telp']) ? htmlspecialchars($cust['no_telp']) : '-' ?>
    </div>
    <div class="wa-profile-info">
        <span class="wa-profile-label">Alamat:</span><?= htmlspecialchars($cust['alamat']) ?>
    </div>
    <hr class="wa-profile-divider" />
    <div class="wa-profile-info mb-3">
        <span class="wa-profile-label">Tanggal Registrasi:</span><?= isset($cust['tanggal_registrasi']) ? htmlspecialchars($cust['tanggal_registrasi']) : '-' ?>
    </div>
</div>
