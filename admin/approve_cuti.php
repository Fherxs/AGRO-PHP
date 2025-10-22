<?php
require '../config/db.php';
$id = $_GET['id'];
$status = $_GET['status'];
$conn->query("UPDATE cuti SET status='$status' WHERE id=$id");
header("Location: admin_cuti.php");
exit();
