<?php
require '../admin/koneksi.php';

class Login extends Database
{
    public function auth($username, $password)
    {
        $sql = "SELECT * FROM tb_customer WHERE username = ?";
        $stmt = $this->conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                // Verify password
                if (password_verify($password, $row['password'])) {
                    return $row;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    public function tampil() {}
    public function tambah($data) {}
    public function edit($id, $data) {}
    public function hapus($id) {}
    public function getById($id) {}
}

session_start();

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $login = new Login();
    $user = $login->auth($username, $password);

    if ($user) {
        // Set session
        $_SESSION['id_cust'] = $user['id_cust'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['email'] = $user['email'];

        echo "<script>alert('Login Berhasil!'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Username atau Password Salah!'); window.location='login.php';</script>";
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Registrasi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap Online CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
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
          box-shadow: 0 5px 30px rgba(0,0,0,0.15);
          animation: fadeIn 0.8s ease;
      }
      h2 {
          font-weight: 600;
          margin-bottom: 25px;
      }
      @keyframes fadeIn {
          from { opacity: 0; transform: translateY(10px);}
          to { opacity: 1; transform: translateY(0);}
      }
</style>
</head>

<body>

    <div class="register-box">
        <h2 class="text-center">Login</h2>

        <form action="" method="POST">

            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" name="username" placeholder="Masukkan Username" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div>

            <button type="submit" name="submit" class="btn btn-primary w-100">Login</button>

        </form>

    </div>

</body>

</html>