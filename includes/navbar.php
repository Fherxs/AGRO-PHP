<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Pastikan sudah login
if(!isset($_SESSION['role'])){
    header("Location: ../auth/login.php");
    exit();
}

$nama = $_SESSION['nama'];
$role = $_SESSION['role'];
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand" href="#">
       AGRO GROUP <?= strtoupper($role) ?>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <?php if($role == 'admin'): ?>
            <li class="nav-item"><a class="nav-link" href="../admin/dashboard.php">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="../admin/admin_data.php">Data Karyawan</a></li>
            <li class="nav-item"><a class="nav-link" href="../admin/admin_absensi.php">Absensi</a></li>
            <li class="nav-item"><a class="nav-link" href="../admin/admin_anggaran.php">Anggaran</a></li>
            <li class="nav-item"><a class="nav-link" href="../admin/admin_cuti.php">Cuti</a></li>
        <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="../karyawan/dashboard.php">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="../karyawan/absensi.php">Absensi</a></li>
            <li class="nav-item"><a class="nav-link" href="../karyawan/cuti_ajukan.php">Cuti</a></li>
            <li class="nav-item"><a class="nav-link" href="../karyawan/anggaran_ajukan.php">Anggaran</a></li>
        <?php endif; ?>
        
        <!-- Logout jadi tombol mencolok -->
        <li class="nav-item ms-2">
          <form method="POST" action="../auth/logout.php" class="d-flex">
            <button type="submit" class="btn btn-danger btn-sm">
              Logout (<?= htmlspecialchars($nama) ?>)
            </button>
          </form>
        </li>
      </ul>
    </div>
  </div>
</nav>
