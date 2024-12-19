<?php
include 'db.php'; // Koneksi ke database
global $conn;
// Proses registrasi jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Query untuk memasukkan data admin baru
    $sql = "INSERT INTO admin (email, password) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $password);

    if ($stmt->execute()) {
        $success = "Akun admin berhasil didaftarkan!";
    } else {
        $error = "Terjadi kesalahan: " . $stmt->error;
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
    <title>Register Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
<!-- Navigasi -->
<nav class="bg-white shadow-md">
    <div class="container mx-auto px-2 py-3 flex justify-between items-center">
        <a href="../index.php" class="flex items-center space-x-3">
            <img src="img/logo-removebg-preview.png" alt="BudgetIn" class="embed-responsive-3by20"> <!-- Logo diperbesar -->
        </a>
        <ul class="flex space-x-8 text-lg font-semibold"> <!-- Ukuran font lebih besar -->
            <li>
                <a href="../index.php" class="text-gray-700 hover:text-blue-500 transition">Home</a>
            </li>
            <li>
                <a href="register.php" class="bg-blue-500 text-white py-3 px-6 rounded-lg hover:bg-blue-600 transition">
                    Register
                </a>
            </li>
        </ul>
    </div>
</nav>
<div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
    <h2 class="text-2xl font-bold text-center mb-6">Register Admin</h2>
    <?php if (isset($success)): ?>
        <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-4 text-sm">
            <?php echo $success; ?>
        </div>
    <?php elseif (isset($error)): ?>
        <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-4 text-sm">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    <form method="POST" action="register.php" class="space-y-4">
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
            Register
        </button>
    </form>
    <p class="text-center text-gray-600 text-sm mt-4">
        Sudah punya akun? <a href="login.php" class="text-blue-500 hover:underline">Login</a>
    </p>
</div>
</body>
</html>
