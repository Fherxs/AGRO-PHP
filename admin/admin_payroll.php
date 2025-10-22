<?php
session_start();
require '../config/db.php';
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}

// Ambil data payroll
$res = $conn->query("
    SELECT p.id, k.nama, p.gaji_pokok, p.tunjangan, p.potongan, p.total, p.bulan 
    FROM payroll p 
    JOIN karyawan k ON p.karyawan_id=k.id
    ORDER BY p.bulan DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payroll Karyawan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include '../includes/navbar.php'; ?>

<div class="container mt-4">
    <h3>Payroll Karyawan</h3>
    <table class="table table-bordered table-striped">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Nama Karyawan</th>
                <th>Bulan</th>
                <th>Gaji Pokok</th>
                <th>Tunjangan</th>
                <th>Potongan</th>
                <th>Total</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if($res->num_rows > 0): ?>
                <?php while($row=$res->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= htmlspecialchars($row['nama']); ?></td>
                        <td><?= $row['bulan']; ?></td>
                        <td>Rp <?= number_format($row['gaji_pokok'],0,',','.'); ?></td>
                        <td>Rp <?= number_format($row['tunjangan'],0,',','.'); ?></td>
                        <td>Rp <?= number_format($row['potongan'],0,',','.'); ?></td>
                        <td>Rp <?= number_format($row['total'],0,',','.'); ?></td>
                        <td>
                            <a href="payroll_slip.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-info">Slip</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="8" class="text-center text-muted">Belum ada data payroll.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
