<?php
session_start();
require '../config/db.php';
require '../assets/tcpdf/tcpdf.php';

// Pastikan hanya admin yang bisa akses
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

// Inisialisasi TCPDF
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Laporan Anggaran Karyawan');
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(TRUE, 10);
$pdf->AddPage();

// Buat tabel HTML
$html = '<h3>Laporan Anggaran Karyawan</h3>
<table border="1" cellpadding="5">
<tr style="background-color:#f2f2f2;">
<th>ID</th>
<th>Nama Karyawan</th>
<th>Tanggal</th>
<th>Keterangan</th>
<th>Nominal</th>
<th>Status</th>
</tr>';

while($row = $res->fetch_assoc()){
    $html .= '<tr>
        <td>'.$row['id'].'</td>
        <td>'.htmlspecialchars($row['nama']).'</td>
        <td>'.$row['tgl_pengajuan'].'</td>
        <td>'.htmlspecialchars($row['keterangan']).'</td>
        <td>Rp '.number_format($row['nominal'],0,',','.').'</td>
        <td>'.$row['status'].'</td>
    </tr>';
}

$html .= '</table>';

// Tulis HTML ke PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Output PDF langsung diunduh
$pdf->Output('Laporan_Anggaran.pdf', 'D'); // 'D' = download otomatis
exit();
?>
