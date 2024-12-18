<?php

include 'db.php'; // Koneksi ke database
global $conn;
session_start(); // Memulai session

// Proses login jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    // Query untuk mencari email di tabel admin
    $sql = "SELECT * FROM admin WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        // Verifikasi password
        if (password_verify($password, $admin['password'])) {
            // Login berhasil, simpan data admin di session
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            header("Location: admin.php"); // Redirect ke halaman admin
            exit;
        } else {
            $error = "Password salah.";
        }
    } else {
        $error = "Email tidak ditemukan.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
<div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
    <h2 class="text-2xl font-bold text-center mb-6">Login Admin</h2>
    <?php if (isset($error)): ?>
        <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-4 text-sm">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    <form method="POST" action="login.php" class="space-y-4">
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
            Login
        </button>
    </form>
    <p class="text-center text-gray-600 text-sm mt-4">
        Belum punya akun? <a href="register.php" class="text-blue-500 hover:underline">Register</a>
    </p>
</div>
</body>
</html>
