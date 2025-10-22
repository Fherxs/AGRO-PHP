<?php
session_start();
require '../config/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'karyawan'){
    header("Location: ../auth/login.php");
    exit();
}

$uid = $_SESSION['user_id'];

// Ambil data user
$stmt_user = $conn->prepare("SELECT nama FROM karyawan WHERE id=?");
$stmt_user->bind_param("i", $uid);
$stmt_user->execute();
$user = $stmt_user->get_result()->fetch_assoc();

// Proses submit absensi
if(isset($_POST['submit'])){
    $tanggal = date('Y-m-d');
    $status = $_POST['status'];

    $stmt_cek = $conn->prepare("SELECT * FROM absensi WHERE karyawan_id=? AND tanggal=?");
    $stmt_cek->bind_param("is", $uid, $tanggal);
    $stmt_cek->execute();
    $cek = $stmt_cek->get_result();

    if($cek->num_rows == 0){
        $stmt_insert = $conn->prepare("INSERT INTO absensi (karyawan_id, tanggal, status) VALUES (?,?,?)");
        $stmt_insert->bind_param("iss", $uid, $tanggal, $status);
        $stmt_insert->execute();
        $success = "Absensi berhasil!";
    } else {
        $error = "Anda sudah absen hari ini!";
    }
}

// Ambil riwayat absensi
$stmt_hist = $conn->prepare("SELECT tanggal, status FROM absensi WHERE karyawan_id=? ORDER BY tanggal DESC");
$stmt_hist->bind_param("i", $uid);
$stmt_hist->execute();
$res = $stmt_hist->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Absensi Karyawan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="../assets/css/style.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Navbar -->
<?php include '../includes/navbar.php'; ?>


<div class="container mt-4">
  <h3 class="fw-bold mb-3">Absensi Hari Ini</h3>
  <p>Selamat datang, <strong><?= htmlspecialchars($user['nama']); ?></strong></p>

  <?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
  <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

  <div class="card mb-4 shadow-sm">
    <div class="card-body">
      <form method="POST" class="row g-3 align-items-center">
        <div class="col-md-6">
          <label>Status Kehadiran</label>
          <select name="status" class="form-select" required>
            <option value="Hadir">Hadir</option>
            <option value="Izin">Izin</option>
            <option value="Alpha">Alpha</option>
          </select>
        </div>
        <div class="col-md-6 d-flex align-items-end">
          <button type="submit" name="submit" class="btn btn-primary w-100"><i class="bi bi-check-circle"></i> Absen Sekarang</button>
        </div>
      </form>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-header bg-secondary text-white fw-bold">Riwayat Absensi</div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped mb-0">
          <thead class="table-light">
            <tr>
              <th>Tanggal</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if($res->num_rows > 0): ?>
              <?php while($row = $res->fetch_assoc()): ?>
                <tr>
                  <td><?= htmlspecialchars($row['tanggal']); ?></td>
                  <td>
                    <?php if($row['status']=='Hadir'): ?>
                      <span class="badge bg-success"><?= $row['status']; ?></span>
                    <?php elseif($row['status']=='Izin'): ?>
                      <span class="badge bg-warning text-dark"><?= $row['status']; ?></span>
                    <?php else: ?>
                      <span class="badge bg-danger"><?= $row['status']; ?></span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="2" class="text-center text-muted">Belum ada absensi.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
