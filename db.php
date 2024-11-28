<?php
$host = "localhost"; // Perbaikan: Menghapus '@'
$user = "root";
$password = "";
$database = "db_budgetin";

// Membuat koneksi ke database
$conn = new mysqli($host, $user, $password, $database);

// Mengecek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
