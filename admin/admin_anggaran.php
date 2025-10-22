<?php
session_start();
require '../config/db.php';
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}

// Ambil semua pengajuan anggaran
$res = $conn->query("
    SELECT a.id, k.nama, a.tgl_pengajuan, a.keterangan, a.nominal, a.status
    FROM anggaran a
    JOIN karyawan k ON a.karyawan_id=k.id
    ORDER BY a.tgl_pengajuan DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pengajuan Anggaran</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include '../includes/navbar.php'; ?>

<div class="container mt-4">
    <h3>Pengajuan Anggaran Karyawan</h3>
        <!-- Tombol Cetak PDF Otomatis -->
    <a href="cetak_anggaran.php" target="_blank" class="btn btn-primary mb-3">
        <i class="bi bi-printer"></i> Cetak PDF Otomatis
    </a>
    <table class="table table-bordered table-striped">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Nama Karyawan</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Nominal</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if($res->num_rows > 0): ?>
                <?php while($row=$res->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= htmlspecialchars($row['nama']); ?></td>
                        <td><?= $row['tgl_pengajuan']; ?></td>
                        <td><?= htmlspecialchars($row['keterangan']); ?></td>
                        <td>Rp <?= number_format($row['nominal'],0,',','.'); ?></td>
                        <td>
                            <?php 
                                switch($row['status']){
                                    case 'Disetujui': echo "<span class='badge bg-success'>{$row['status']}</span>"; break;
                                    case 'Ditolak': echo "<span class='badge bg-danger'>{$row['status']}</span>"; break;
                                    default: echo "<span class='badge bg-warning text-dark'>{$row['status']}</span>";
                                }
                            ?>
                        </td>
                        <td>
                            <?php if($row['status']=='Pending'): ?>
                                <a href="proses_anggaran.php?id=<?= $row['id']; ?>&action=approve" class="btn btn-sm btn-success">Setuju</a>
                                <a href="proses_anggaran.php?id=<?= $row['id']; ?>&action=reject" class="btn btn-sm btn-danger">Tolak</a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" class="text-center text-muted">Belum ada data pengajuan anggaran.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
