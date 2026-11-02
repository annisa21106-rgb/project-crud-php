<?php
$host = "localhost";
$user = "root";
$pass = ""; 
$db   = "nama_database_dari_mahasiswa_A"; // GANTI DENGAN NAMA DB YANG BENAR

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$connection) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>

