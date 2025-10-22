<?php
session_start();
require '../config/db.php';
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}

$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("
    SELECT p.*, k.nama, k.email 
    FROM payroll p 
    JOIN karyawan k ON p.karyawan_id=k.id 
    WHERE p.id=?
");
$stmt->bind_param("i",$id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if(!$data){
    echo "Data tidak ditemukan!";
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Slip Payroll</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <div class="card p-4 shadow-sm">
        <h3 class="text-center mb-4">Slip Gaji</h3>
        <p><strong>Nama:</strong> <?= htmlspecialchars($data['nama']); ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($data['email']); ?></p>
        <p><strong>Bulan:</strong> <?= $data['bulan']; ?></p>
        <table class="table table-bordered mt-3">
            <tr>
                <th>Gaji Pokok</th>
                <td>Rp <?= number_format($data['gaji_pokok'],0,',','.'); ?></td>
            </tr>
            <tr>
                <th>Tunjangan</th>
                <td>Rp <?= number_format($data['tunjangan'],0,',','.'); ?></td>
            </tr>
            <tr>
                <th>Potongan</th>
                <td>Rp <?= number_format($data['potongan'],0,',','.'); ?></td>
            </tr>
            <tr>
                <th>Total</th>
                <td>Rp <?= number_format($data['total'],0,',','.'); ?></td>
            </tr>
        </table>
        <a href="admin_payroll.php" class="btn btn-secondary mt-3">Kembali</a>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
