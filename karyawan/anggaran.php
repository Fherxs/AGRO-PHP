<?php
// Mulai session hanya jika belum ada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require '../config/db.php';

// Pastikan karyawan login
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'karyawan'){
    header("Location: ../auth/login.php");
    exit();
}

$uid = $_SESSION['user_id'];

// Ambil data user
$stmt_user = $conn->prepare("SELECT nama, email FROM karyawan WHERE id=?");
$stmt_user->bind_param("i", $uid);
$stmt_user->execute();
$user = $stmt_user->get_result()->fetch_assoc();

// Proses submit anggaran
$error = '';
$success = '';
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $keterangan = trim($_POST['keterangan']);
    $nominal = floatval($_POST['nominal']);
    $tanggal = date('Y-m-d');

    if($keterangan == '' || $nominal <= 0){
        $error = "Keterangan dan nominal harus diisi dengan benar!";
    } else {
        $stmt_insert = $conn->prepare("INSERT INTO anggaran (karyawan_id, tgl_pengajuan, keterangan, nominal, status) VALUES (?,?,?,?, 'Pending')");
        $stmt_insert->bind_param("issd", $uid, $tanggal, $keterangan, $nominal);
        if($stmt_insert->execute()){
            $success = "Pengajuan anggaran berhasil!";
        } else {
            $error = "Gagal mengajukan anggaran!";
        }
    }
}

// Ambil riwayat anggaran
$stmt_hist = $conn->prepare("SELECT tgl_pengajuan AS tanggal, keterangan, nominal, status FROM anggaran WHERE karyawan_id=? ORDER BY tgl_pengajuan DESC");
$stmt_hist->bind_param("i",$uid);
$stmt_hist->execute();
$res = $stmt_hist->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Anggaran Karyawan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include '../includes/navbar.php'; // navbar TANPA session_start() ?>

<div class="container mt-4">
    <h3>Halo, <?= htmlspecialchars($user['nama']); ?> 👋</h3>
    <p class="text-muted mb-4">Email: <?= htmlspecialchars($user['email']); ?></p>

    <?php if($error): ?>
        <div class="alert alert-danger"><?= $error; ?></div>
    <?php endif; ?>
    <?php if($success): ?>
        <div class="alert alert-success"><?= $success; ?></div>
    <?php endif; ?>

    <!-- Form Ajukan Anggaran -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white fw-bold">Ajukan Pengeluaran Anggaran</div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label>Keterangan</label>
                    <input type="text" name="keterangan" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Nominal (Rp)</label>
                    <input type="number" name="nominal" class="form-control" min="0" step="0.01" required>
                </div>
                <button type="submit" class="btn btn-primary">Ajukan</button>
            </form>
        </div>
    </div>

    <!-- Riwayat Anggaran -->
    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white fw-bold">Riwayat Pengajuan Anggaran</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th>Nominal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($res->num_rows > 0): ?>
                            <?php while($row = $res->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['tanggal']); ?></td>
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
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center text-muted">Belum ada data pengajuan anggaran.</td></tr>
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
