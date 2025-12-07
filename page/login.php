<?php
require '../admin/koneksi.php';

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Gunakan factory untuk membuat model customer
    $customer = model('customer');
    $user = $customer->findByUsernameOrEmail($username);

    if ($user && password_verify($password, $user['password'])) {
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
    <title>Login</title>
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