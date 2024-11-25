<?php
$host = "localhost"; // Perbaikan: Menghapus '@'
$user = "root";
$password = "";
$database = "db_budgetin";

// Membuat koneksi ke database
$conn = mysqli_connect($host, $user, $password, $database);

// Mengecek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error()); // Lebih baik gunakan die untuk menangani error
} else {
    echo "Koneksi Berhasil";
}
?>
