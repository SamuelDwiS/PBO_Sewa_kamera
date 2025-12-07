<?php
require '../admin/koneksi.php';

class Registrasi extends Database
{
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }
    public function tampil()
    {
        return $this->conn->query("SELECT * FROM tb_customer ORDER BY id_cust DESC");
    }

    private function generateIdCust()
    {
        $result = $this->conn->query("SELECT id_cust FROM tb_customer ORDER BY id_cust DESC LIMIT 1");
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $lastId = $row['id_cust'];
            // Extract number from id (e.g., "CUST001" -> 1)
            $number = (int)substr($lastId, 4);
            $newNumber = $number + 1;
            return "CUST" . str_pad($newNumber, 3, "0", STR_PAD_LEFT);
        } else {
            return "CUST001";
        }
    }

    public function tambah($data)
    {
        $id_cust = $this->generateIdCust();
        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        $sql = "INSERT INTO tb_customer (id_cust, nama, email, username, password, alamat, no_telp)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param(
                "sssssss",
                $id_cust,
                $data['nama'],
                $data['email'],
                $data['username'],
                $password,
                $data['alamat'],
                $data['no_telp']
            );

            return $stmt->execute();
        } else {
            return false;
        }
    }
    public function edit($id, $data) {}
    public function hapus($id) {}
    public function getById($id) {}
}

if (isset($_POST['submit'])) {
    $data = [
        'nama' => $_POST['nama'],
        'email' => $_POST['email'],
        'username' => $_POST['username'],
        'password' => $_POST['password'],
        'alamat' => $_POST['alamat'],
        'no_telp' => $_POST['no_telp'],

    ];
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Registrasi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
        }

        .register-box {
            width: 450px;
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.15);
            animation: fadeIn 0.8s ease;
        }

        h2 {
            font-weight: 600;
            margin-bottom: 25px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>

    <div class="register-box">
        <h2 class="text-center">Registrasi</h2>

        <form action="proses_registrasi.php" method="POST">

            <div class="mb-3">
                <label class="form-label">Nama</label>
                <input type="text" class="form-control" name="nama" placeholder="Masukkan Nama" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Alamat</label>
                <input type="text" class="form-control" name="alamat" placeholder="Masukkan Alamat" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" placeholder="name@example.com" required>
            </div>

            <div class="mb-3">
                <label class="form-label">No Telepon</label>
                <input type="text" class="form-control" name="no_telp" placeholder="Masukkan No Telepon" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" name="username" placeholder="Masukkan Username" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div>

            <button type="submit" name="submit" class="btn btn-primary w-100">Register</button>

        </form>

    </div>

</body>

</html>