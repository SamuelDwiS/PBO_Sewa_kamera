<?php
require_once 'registrasi.php';

$reg = new Registrasi();

if (isset($_POST['submit'])) {

    $data = [
        'nama' => $_POST['nama'],
        'email' => $_POST['email'],
        'username' => $_POST['username'],
        'password' => $_POST['password'],
        'alamat' => $_POST['alamat'],
        'no_telp' => $_POST['no_telp'],

    ];

    $insert = $reg->tambah($data);

    if ($insert) {
        echo "<script>alert('Registrasi Berhasil!'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('Registrasi Gagal!'); window.location='registrasi.php';</script>";
    }
}
