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

// Jatah cuti
$jatah_tahunan = 12; // Total cuti tahunan
$cuti_bersama = 8;   // Cuti bersama pemerintah
$sisa_cuti_awal = $jatah_tahunan - $cuti_bersama; // sisa hak cuti awal

$error = '';
$success = '';

// Fungsi hitung jumlah hari cuti
function hitung_hari_cuti($mulai, $selesai){
    $start = new DateTime($mulai);
    $end = new DateTime($selesai);
    $diff = $start->diff($end);
    return $diff->days + 1; // termasuk hari mulai
}

// Ambil total cuti yang sudah disetujui karyawan ini
$stmt_total = $conn->prepare("SELECT SUM(hari_cuti) as total FROM cuti WHERE karyawan_id=? AND status='Disetujui'");
$stmt_total->bind_param("i", $uid);
$stmt_total->execute();
$total_cuti_disetujui = $stmt_total->get_result()->fetch_assoc()['total'] ?? 0;

// Sisa cuti aktual
$sisa_cuti_aktual = $sisa_cuti_awal - $total_cuti_disetujui;

// Proses submit cuti
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $mulai = $_POST['tanggal_mulai'];
    $selesai = $_POST['tanggal_selesai'];
    $alasan = $_POST['alasan'];

    $jumlah_hari = hitung_hari_cuti($mulai, $selesai);

    if($jumlah_hari > $sisa_cuti_aktual){
        $warning = "Cuti melebihi sisa cuti ($sisa_cuti_aktual hari). Lebihnya akan dipotong gaji.";
        $status = 'Pending';
    } else {
        $status = 'Pending';
    }

    $stmt_insert = $conn->prepare("INSERT INTO cuti (karyawan_id, tanggal_mulai, tanggal_selesai, alasan, status, hari_cuti) VALUES (?,?,?,?,?,?)");
    $stmt_insert->bind_param("issssi", $uid, $mulai, $selesai, $alasan, $status, $jumlah_hari);

    if($stmt_insert->execute()){
        $success = "Pengajuan cuti berhasil! Jumlah hari: $jumlah_hari";
        if(isset($warning)) $success .= " <br><span class='text-warning'>$warning</span>";
    } else {
        $error = "Gagal mengajukan cuti!";
    }
}

// Ambil riwayat cuti
$stmt_hist = $conn->prepare("SELECT tanggal_mulai, tanggal_selesai, alasan, status, hari_cuti FROM cuti WHERE karyawan_id=? ORDER BY tanggal_mulai DESC");
$stmt_hist->bind_param("i", $uid);
$stmt_hist->execute();
$res = $stmt_hist->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pengajuan Cuti</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include '../includes/navbar.php'; ?>

<div class="container mt-4">
  <h3 class="fw-bold mb-3">Ajukan Cuti</h3>
  <p>Halo, <strong><?= htmlspecialchars($user['nama']); ?></strong></p>
  <p>Sisa cuti tahunan saat ini: <strong><?= $sisa_cuti_aktual; ?> hari</strong></p>

  <?php if($success) echo "<div class='alert alert-success'>$success</div>"; ?>
  <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>

  <!-- Form Pengajuan Cuti -->
  <div class="card mb-4 shadow-sm">
    <div class="card-body">
      <form method="POST" class="row g-3">
        <div class="col-md-4">
          <label>Tanggal Mulai</label>
          <input type="date" name="tanggal_mulai" class="form-control" required>
        </div>
        <div class="col-md-4">
          <label>Tanggal Selesai</label>
          <input type="date" name="tanggal_selesai" class="form-control" required>
        </div>
        <div class="col-md-4">
          <label>Alasan</label>
          <input type="text" name="alasan" class="form-control" placeholder="Alasan cuti" required>
        </div>
        <div class="col-12 d-flex justify-content-end">
          <button type="submit" class="btn btn-primary"><i class="bi bi-send-fill"></i> Ajukan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Riwayat Cuti -->
  <div class="card shadow-sm">
    <div class="card-header bg-secondary text-white fw-bold">Riwayat Cuti</div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped mb-0">
          <thead class="table-light">
            <tr>
              <th>Mulai</th>
              <th>Selesai</th>
              <th>Alasan</th>
              <th>Hari</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if($res->num_rows > 0): ?>
              <?php while($row = $res->fetch_assoc()): ?>
                <tr>
                  <td><?= htmlspecialchars($row['tanggal_mulai']); ?></td>
                  <td><?= htmlspecialchars($row['tanggal_selesai']); ?></td>
                  <td><?= htmlspecialchars($row['alasan']); ?></td>
                  <td><?= $row['hari_cuti']; ?> hari</td>
                  <td>
                    <?php
                    $status = $row['status'];
                    if($status == 'Disetujui') {
                        echo "<span class='badge bg-success'>$status</span>";
                    } elseif($status == 'Ditolak') {
                        echo "<span class='badge bg-danger'>$status</span>";
                    } else {
                        if($row['hari_cuti'] > $sisa_cuti_aktual){
                            echo "<span class='badge bg-warning text-dark'>Pending (lebih → potong gaji)</span>";
                        } else {
                            echo "<span class='badge bg-warning text-dark'>$status</span>";
                        }
                    }
                    ?>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="5" class="text-center text-muted">Belum ada pengajuan cuti.</td></tr>
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
