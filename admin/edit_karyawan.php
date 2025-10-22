<?php
session_start();
require '../config/db.php';
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}

$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM karyawan WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if(!$user){
    header("Location: admin_data.php");
    exit();
}

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $password = $_POST['password'];

    if($nama == '' || $email == ''){
        $error = "Nama dan email harus diisi!";
    } else {
        if($password != ''){
            $password_hash = password_hash($password,PASSWORD_DEFAULT);
            $stmt_update = $conn->prepare("UPDATE karyawan SET nama=?, email=?, role=?, password=? WHERE id=?");
            $stmt_update->bind_param("ssssi",$nama,$email,$role,$password_hash,$id);
        } else {
            $stmt_update = $conn->prepare("UPDATE karyawan SET nama=?, email=?, role=? WHERE id=?");
            $stmt_update->bind_param("sssi",$nama,$email,$role,$id);
        }
        if($stmt_update->execute()){
            $success = "Data berhasil diperbarui!";
        } else {
            $error = "Gagal memperbarui data!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Karyawan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include '../includes/navbar.php'; ?>

<div class="container mt-4">
    <h3>Edit Karyawan</h3>
    <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
    <?php if($success) echo "<div class='alert alert-success'>$success</div>"; ?>
    <form method="POST">
        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($user['nama']); ?>" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="mb-3">
            <label>Password (kosongkan jika tidak ingin diubah)</label>
            <input type="password" name="password" class="form-control">
        </div>
        <div class="mb-3">
            <label>Role</label>
            <select name="role" class="form-select">
                <option value="karyawan" <?= $user['role']=='karyawan'?'selected':''; ?>>Karyawan</option>
                <option value="admin" <?= $user['role']=='admin'?'selected':''; ?>>Admin</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="admin_data.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
