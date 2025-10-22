<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'karyawan') {
    header("Location: ../auth/login.php");
    exit();
}

$uid = $_SESSION['user_id'];
$stmt_user = $conn->prepare("SELECT nama, email FROM karyawan WHERE id=?");
$stmt_user->bind_param("i", $uid);
$stmt_user->execute();
$user = $stmt_user->get_result()->fetch_assoc();

$stmt_absen = $conn->prepare("SELECT tanggal, status FROM absensi WHERE karyawan_id=? ORDER BY tanggal DESC LIMIT 1");
$stmt_absen->bind_param("i", $uid);
$stmt_absen->execute();
$absen = $stmt_absen->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Karyawan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Navbar -->
<!-- Navbar -->
<?php include '../includes/navbar.php'; ?>


<div class="container mt-4">
  <div class="text-center mb-4">
    <h3 class="fw-bold text-primary">Selamat Datang, <?= htmlspecialchars($user['nama']); ?> 👋</h3>
    <p class="text-muted"><?= htmlspecialchars($user['email']); ?></p>
  </div>

  <div class="row g-4">
    <!-- Absensi Terakhir -->
    <div class="col-md-6 col-lg-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body text-center">
          <div class="text-primary fs-1 mb-2"><i class="bi bi-calendar-check"></i></div>
          <h5 class="fw-bold mb-2">Absensi Terakhir</h5>
          <?php if($absen): ?>
            <p class="text-secondary mb-1"><?= $absen['tanggal']; ?> — <span class="fw-semibold"><?= $absen['status']; ?></span></p>
          <?php else: ?>
            <p class="text-muted">Belum ada absensi</p>
          <?php endif; ?>
          <a href="absensi.php" class="btn btn-primary mt-2 w-100">Absen Sekarang</a>
        </div>
      </div>
    </div>

    <!-- Cuti -->
    <div class="col-md-6 col-lg-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body text-center">
          <div class="text-warning fs-1 mb-2"><i class="bi bi-briefcase"></i></div>
          <h5 class="fw-bold mb-2">Pengajuan Cuti</h5>
          <p class="text-secondary">Ajukan cuti dengan mudah dan pantau statusnya.</p>
          <a href="cuti_ajukan.php" class="btn btn-warning text-dark mt-2 w-100">Ajukan Cuti</a>
        </div>
      </div>
    </div>

    <!-- Anggaran -->
    <div class="col-md-6 col-lg-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body text-center">
          <div class="text-info fs-1 mb-2"><i class="bi bi-cash-stack"></i></div>
          <h5 class="fw-bold mb-2">Anggaran Divisi</h5>
          <p class="text-secondary">Lihat dan ajukan pengeluaran dari divisi Anda.</p>
          <a href="anggaran_ajukan.php" class="btn btn-info text-white mt-2 w-100">Ajukan Anggaran</a>
        </div>
      </div>
    </div>
  </div>
</div>

<footer class="text-center mt-5 py-3 text-muted small">
  &copy; <?= date('Y'); ?> AGRO GROUP System. All rights reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
