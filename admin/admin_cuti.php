<?php
session_start();
require '../config/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}

$jatah_tahunan = 12;
$cuti_bersama = 8;
$sisa_cuti_awal = $jatah_tahunan - $cuti_bersama;

// Ambil semua pengajuan cuti
$stmt = $conn->prepare("
    SELECT c.id, c.karyawan_id, k.nama, c.tanggal_mulai, c.tanggal_selesai, c.alasan, c.status, c.hari_cuti
    FROM cuti c
    JOIN karyawan k ON c.karyawan_id = k.id
    ORDER BY c.tanggal_mulai DESC
");
$stmt->execute();
$res = $stmt->get_result();

// Fungsi hitung total cuti yang sudah disetujui per karyawan
function total_cuti_disetujui($conn, $karyawan_id){
    $stmt = $conn->prepare("SELECT SUM(hari_cuti) as total FROM cuti WHERE karyawan_id=? AND status='Disetujui'");
    $stmt->bind_param("i", $karyawan_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['total'] ?? 0;
}

// Proses approve/reject
if(isset($_GET['action']) && isset($_GET['id'])){
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if($action == 'approve'){
        $status = 'Disetujui';
    } elseif($action == 'reject'){
        $status = 'Ditolak';
    } else {
        $status = '';
    }

    if($status){
        $upd = $conn->prepare("UPDATE cuti SET status=? WHERE id=?");
        $upd->bind_param("si", $status, $id);
        $upd->execute();
        header("Location: admin_cuti.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - Pengajuan Cuti</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include '../includes/navbar.php'; ?>

<div class="container mt-4">
  <h3 class="fw-bold mb-3">Daftar Pengajuan Cuti Karyawan</h3>

  <div class="card shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped mb-0">
          <thead class="table-light">
            <tr>
              <th>Karyawan</th>
              <th>Mulai</th>
              <th>Selesai</th>
              <th>Alasan</th>
              <th>Hari</th>
              <th>Sisa Cuti</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php while($row = $res->fetch_assoc()): 
                $total_disetujui = total_cuti_disetujui($conn, $row['karyawan_id']);
                $sisa_cuti = $sisa_cuti_awal - $total_disetujui;
            ?>
            <tr>
              <td><?= htmlspecialchars($row['nama']); ?></td>
              <td><?= htmlspecialchars($row['tanggal_mulai']); ?></td>
              <td><?= htmlspecialchars($row['tanggal_selesai']); ?></td>
              <td><?= htmlspecialchars($row['alasan']); ?></td>
              <td><?= $row['hari_cuti']; ?> hari</td>
              <td><?= $sisa_cuti; ?> hari</td>
              <td>
                <?php
                $status = $row['status'];
                if($status == 'Disetujui') {
                    echo "<span class='badge bg-success'>$status</span>";
                } elseif($status == 'Ditolak') {
                    echo "<span class='badge bg-danger'>$status</span>";
                } else {
                    if($row['hari_cuti'] > $sisa_cuti){
                        echo "<span class='badge bg-warning text-dark'>Pending (lebih → potong gaji)</span>";
                    } else {
                        echo "<span class='badge bg-warning text-dark'>$status</span>";
                    }
                }
                ?>
              </td>
              <td>
                <?php if($row['status']=='Pending'): ?>
                    <a href="?action=approve&id=<?= $row['id']; ?>" class="btn btn-success btn-sm"><i class="bi bi-check-lg"></i></a>
                    <a href="?action=reject&id=<?= $row['id']; ?>" class="btn btn-danger btn-sm"><i class="bi bi-x-lg"></i></a>
                <?php else: ?>
                    -
                <?php endif; ?>
              </td>
            </tr>
            <?php endwhile; ?>
            <?php if($res->num_rows == 0): ?>
                <tr><td colspan="8" class="text-center text-muted">Belum ada pengajuan cuti.</td></tr>
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
