<?php
session_start();
require '../config/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}

$id = $_GET['id'] ?? 0;
$action = $_GET['action'] ?? '';

if($id && in_array($action, ['approve','reject'])){
    $status = $action === 'approve' ? 'Disetujui' : 'Ditolak';
    $stmt = $conn->prepare("UPDATE cuti SET status=? WHERE id=?");
    $stmt->bind_param("si",$status,$id);
    $stmt->execute();
}

// Kembali ke halaman admin cuti
header("Location: admin_cuti.php");
exit();
