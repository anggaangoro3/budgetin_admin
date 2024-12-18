<?php
global $conn;
include 'db.php';

// Periksa apakah ada ID yang diterima
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Hapus data pengguna berdasarkan ID
    $sql = "DELETE FROM users WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo "Pengguna berhasil dihapus!";
        header("Location: admin.php");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    die("ID tidak ditemukan.");
}
?>
