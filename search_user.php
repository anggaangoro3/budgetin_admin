<?php
global $conn;
include 'db.php'; // Sertakan koneksi database

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['phoneSearch'])) {
    $phone = $_POST['phoneSearch'];
    $query = "SELECT * FROM users WHERE phone = '$phone'";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo json_encode(['success' => true, 'user' => $user]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit;
}
?>
