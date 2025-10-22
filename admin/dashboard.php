<?php
session_start();
require '../config/db.php';

// Pastikan admin login
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}

// Hitung total karyawan
$total_karyawan = $conn->query("SELECT COUNT(*) AS total FROM karyawan")->fetch_assoc()['total'];

// Hitung absensi hari ini
$today = date('Y-m-d');
$total_absen = $conn->query("SELECT COUNT(*) AS total FROM absensi WHERE tanggal='$today'")->fetch_assoc()['total'];

// Hitung cuti pending
$total_cuti_pending = $conn->query("SELECT COUNT(*) AS total FROM cuti WHERE status='Pending'")->fetch_assoc()['total'];

// Hitung anggaran pending
$total_anggaran_pending = $conn->query("SELECT COUNT(*) AS total FROM anggaran WHERE status='Pending'")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include '../includes/navbar.php'; ?>

<div class="container mt-4">
    <h3 class="mb-4">Dashboard Admin</h3>
    <div class="row g-4">
        <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <h5>Total Karyawan</h5>
                    <h2><?= $total_karyawan; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <h5>Absensi Hari Ini</h5>
                    <h2><?= $total_absen; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <h5>Cuti Pending</h5>
                    <h2><?= $total_cuti_pending; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <h5>Anggaran Pending</h5>
                    <h2><?= $total_anggaran_pending; ?></h2>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
