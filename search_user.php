<?php
include 'db.php'; // Sertakan koneksi database
global $conn;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['emailSearch'])) {
    $email = $conn->real_escape_string($_POST['emailSearch']); // Hindari SQL Injection
    $query = "SELECT user_id, name, email, password FROM users WHERE email = '$email'";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo json_encode(['success' => true, 'user' => $user]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Pengguna tidak ditemukan']);
    }
    exit;
} else {
    echo json_encode(['success' => false, 'message' => 'Permintaan tidak valid']);
    exit;
}
?>