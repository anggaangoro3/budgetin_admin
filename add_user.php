<?php
global $conn;
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $balance = $_POST['balance'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Enkripsi password
    $email = $_POST['email']; // Ambil email dari input

    $sql = "INSERT INTO users (name, phone, balance, password, email) VALUES ('$name', '$phone', '$balance', '$password', '$email')";

    if ($conn->query($sql) === TRUE) {
        echo "Pengguna berhasil ditambahkan!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Add User</title>
        <link rel="stylesheet" href="styles.css">
        <title>Back Button Example</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
    <div class="back-button-container">
        <button onclick="window.location.href='index.php'" class="buttonStyle">Kembali</button>
    </div>
    <div class="container">
        <h1>Add User</h1>
        <form class="user-form" method="POST">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="balance">Balance:</label>
                <input type="number" id="balance" name="balance" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="button-container">
                <button type="submit" class="styled-button">Add User</button>
            </div>
        </form>
    </div>
    </body>
    </html>

<?php
$conn->close();
?>