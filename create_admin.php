<?php
require 'config/db.php'; // pastikan path benar

$email = 'admin@gmail.com';
$nama = 'Admin HRIS';
$password_plain = '12345';
$role = 'admin';

// buat hash password
$hash = password_hash($password_plain, PASSWORD_DEFAULT);

// cek apakah email sudah ada
$stmt = $conn->prepare("SELECT id FROM karyawan WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

if ($res && $res->num_rows > 0) {
    // update password & role jika email sudah ada
    $row = $res->fetch_assoc();
    $id = $row['id'];
    $upd = $conn->prepare("UPDATE karyawan SET nama=?, password=?, role=? WHERE id=?");
    $upd->bind_param("sssi", $nama, $hash, $role, $id);
    $upd->execute();
    echo "Akun admin diupdate: $email";
    $upd->close();
} else {
    // insert user baru
    $ins = $conn->prepare("INSERT INTO karyawan (nama,email,password,role) VALUES (?,?,?,?)");
    $ins->bind_param("ssss", $nama, $email, $hash, $role);
    $ins->execute();
    echo "Akun admin berhasil dibuat: $email";
    $ins->close();
}

$stmt->close();
$conn->close();
echo "<br>Password: $password_plain<br>Hapus file create_admin.php setelah selesai.";
?>
