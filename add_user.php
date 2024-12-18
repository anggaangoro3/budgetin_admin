<?php
include 'db.php'; // Koneksi ke database
global $conn;

// Proses register jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Enkripsi password


    // Query untuk memasukkan data ke tabel user
    $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $name, $email, $password);

    if ($stmt->execute()) {
        header("Location: add_user.php?register=success"); // Redirect ke budgetin.php jika sukses
        exit;
    } else {
        $error = "Gagal mendaftarkan pengguna: " . $conn->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EditData</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/x-icon" href="img/favicon.png">
    <link rel="stylesheet" href="../admin/styles.css">
    <style>
        body {
            background: linear-gradient(120deg, #00B4DB, #0083B0);
        }
    </style>
</head>
<body class="flex items-center justify-center h-screen">
<div class="back-button-container">
    <button onclick="window.location.href='admin.php'" class="buttonStyle">Kembali</button>
</div>
<div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
    <div class="text-center mb-6">
        <img src="img/logo-removebg-preview.png" alt="BudgetIn" class="w-32 mx-auto">
    </div>
    <h2 class="text-2xl font-bold text-center mb-4">Add User by Admin</h2>
    <?php if (isset($error)): ?>
        <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-4 text-sm">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    <form method="POST" action="add_user.php" class="space-y-4">
        <div>
            <label for="name" class="block text-gray-700 font-medium">Nama Lengkap</label>
            <input type="text" id="name" name="name" required
                   class="w-full border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring focus:ring-blue-300">
        </div>
        <div>
            <label for="email" class="block text-gray-700 font-medium">Email</label>
            <input type="email" id="email" name="email" required
                   class="w-full border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring focus:ring-blue-300">
        </div>
        <div>
            <label for="password" class="block text-gray-700 font-medium">Password</label>
            <input type="password" id="password" name="password" required
                   class="w-full border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring focus:ring-blue-300">
        </div>
        <button type="submit"
                class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition">
            Add User
        </button>
    </form>
</div>
</body>
</html>
