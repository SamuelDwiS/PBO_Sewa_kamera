<?php
require_once '../admin/koneksi.php';

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
        $sql = "INSERT INTO tb_customer (id_cust, nama, email, username, password, alamat, no_telp, tanggal_registrasi)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
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
                        .close-btn {
                            position: absolute;
                            top: 16px;
                            right: 18px;
                            font-size: 1.5rem;
                            color: #888;
                            background: none;
                            border: none;
                            cursor: pointer;
                            z-index: 10;
                            transition: color 0.2s;
                        }
                        .close-btn:hover {
                            color: #d32f2f;
                        }
                .form-group {
                    margin-bottom: 18px;
                }
                .form-label {
                    margin-bottom: 6px;
                    font-weight: 500;
                    color: #2575fc;
                }
                .form-control {
                    width: 100%;
                    border-radius: 10px;
                    border: 1px solid #dbeafe;
                    padding: 13px 16px;
                    font-size: 16px;
                    background: #f6f8fa;
                    transition: border-color 0.2s, box-shadow 0.2s;
                }
                .form-control:focus {
                    border-color: #2575fc;
                    box-shadow: 0 0 0 2px rgba(37,117,252,0.10);
                    background: #fff;
                }
        body {
            background: linear-gradient(120deg, #e0eafc 0%, #cfdef3 100%);
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
        }

        .register-box {
            width: 100%;
            max-width: 370px;
            background: #fff;
            border-radius: 12px;
            padding: 28px 22px 22px 22px;
            box-shadow: 0 4px 18px rgba(37,117,252,0.08);
            animation: fadeIn 0.7s ease;
        }

        .form-group {
            margin-bottom: 18px;
        }
        .form-label {
            display: block;
            margin-bottom: 7px;
            font-weight: 500;
            color: #2575fc;
        }
        .form-control {
            width: 100%;
            border-radius: 8px;
            border: 1px solid #dbeafe;
            padding: 12px 14px;
            font-size: 16px;
            background: #f6f8fa;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-control:focus {
            border-color: #2575fc;
            box-shadow: 0 0 0 2px rgba(37,117,252,0.10);
            background: #fff;
        }
        .btn-primary {
            background: linear-gradient(90deg, #2575fc 0%, #6a11cb 100%);
            border: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 17px;
            padding: 13px 0;
            margin-top: 10px;
            box-shadow: 0 2px 8px rgba(37,117,252,0.08);
            transition: background 0.2s, box-shadow 0.2s;
        }
        .btn-primary:hover {
            background: linear-gradient(90deg, #6a11cb 0%, #2575fc 100%);
            box-shadow: 0 4px 16px rgba(37,117,252,0.12);
        }
        @media (max-width: 500px) {
            .register-box {
                max-width: 98vw;
                padding: 12px 2vw;
            }
        }

        h2 {
            font-weight: 700;
            margin-bottom: 32px;
            color: #2575fc;
            letter-spacing: 1px;
        }

        .form-label {
            font-weight: 500;
            color: #2575fc;
            margin-bottom: 6px;
        }

        .form-control {
            border-radius: 10px;
            border: 1px solid #dbeafe;
            padding: 13px 16px;
            font-size: 16px;
            background: #f6f8fa;
            margin-bottom: 18px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-control:focus {
            border-color: #2575fc;
            box-shadow: 0 0 0 2px rgba(37,117,252,0.10);
            background: #fff;
        }

        .btn-primary {
            background: linear-gradient(90deg, #2575fc 0%, #6a11cb 100%);
            border: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 17px;
            padding: 13px 0;
            margin-top: 10px;
            box-shadow: 0 2px 8px rgba(37,117,252,0.08);
            transition: background 0.2s, box-shadow 0.2s;
        }
        .btn-primary:hover {
            background: linear-gradient(90deg, #6a11cb 0%, #2575fc 100%);
            box-shadow: 0 4px 16px rgba(37,117,252,0.12);
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
    <div class="register-box position-relative">
        <a href="index.php" class="close-btn" title="Batal Registrasi">&times;</a>
        <h2 class="text-center">Registrasi</h2>

        <form action="proses_registrasi.php" method="POST" class="form-horizontal">
            <div class="form-group">
                <label class="form-label">Nama</label>
                <input type="text" class="form-control" name="nama" placeholder="Masukkan Nama" required>
            </div>
            <div class="form-group">
                <label class="form-label">Alamat</label>
                <input type="text" class="form-control" name="alamat" placeholder="Masukkan Alamat" required>
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" placeholder="name@example.com" required>
            </div>
            <div class="form-group">
                <label class="form-label">No Telepon</label>
                <input type="text" class="form-control" name="no_telp" placeholder="Masukkan No Telepon" required>
            </div>
            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" name="username" placeholder="Masukkan Username" required>
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div>
            <button type="submit" name="submit" class="btn btn-primary w-100">Register</button>
        </form>

    </div>

</body>

</html>