<?php
//koneksi
$host     = 'localhost';
$user     = 'root';
$password = '';
$database = 'sistem_manajemen_tugas_harian';

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');
