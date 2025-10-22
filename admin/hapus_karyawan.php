<?php
session_start();
require '../config/db.php';
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}

$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("DELETE FROM karyawan WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();

header("Location: admin_data.php");
exit();
