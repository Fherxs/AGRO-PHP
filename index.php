<?php
session_start();
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: karyawan/dashboard.php");
    }
    exit();
} else {
    header("Location: auth/login.php");
    exit();
}

//untuk manggil halaman 
// http://localhost/HRIS/auth/login.php