<?php
require '../config/db.php';
$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'karyawan'; // default karyawan

    $sql_check = "SELECT * FROM karyawan WHERE email='$email'";
    $res = $conn->query($sql_check);
    if($res->num_rows > 0){
        $error = "Email sudah terdaftar!";
    } else {
        $sql = "INSERT INTO karyawan (nama, email, password, role) VALUES ('$nama','$email','$password','$role')";
        if($conn->query($sql)){
            header("Location: login.php");
        } else {
            $error = "Gagal mendaftar!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register AGRO GROUP</title>
<!-- Bootstrap CSS CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Custom CSS -->
<link href="../assets/css/style.css" rel="stylesheet">
<link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-4">
      <div class="card shadow">
        <div class="card-body">
          <h3 class="text-center mb-3">Daftar AGRO GROUP</h3>
          <?php if($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
          <?php endif; ?>
          <form method="POST">
            <div class="mb-3">
              <label>Nama</label>
              <input type="text" name="nama" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Email</label>
              <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Daftar</button>
          </form>
          <p class="text-center mt-3"><a href="login.php">Sudah punya akun? Login</a></p>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Bootstrap JS CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS -->
<script src="../assets/js/script.js"></script>
<script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
s