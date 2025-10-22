<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "hris"; // Pastikan database sudah dibuat di MySQL

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
