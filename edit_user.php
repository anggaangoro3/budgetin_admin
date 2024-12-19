<?php
require_once 'db.php';
global $conn;

// Pencarian berdasarkan email
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['emailSearch'])) {
    $email = $conn->real_escape_string($_POST['emailSearch']);
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo "<script>
            var userData = " . json_encode($user) . ";
            showEditPopup(userData);
        </script>";
    } else {
        echo "<script>alert('Pengguna tidak ditemukan!');</script>";
    }
}

// Update data pengguna
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $user_id = $_POST['user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $originalEmail = $_POST['emailHidden'];

    // $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Enkripsi password

    $updateQuery = "UPDATE users SET name='$name', email='$email', password='$password' WHERE email='$originalEmail'";
    if ($conn->query($updateQuery) === TRUE) {
        echo "<script>
            alert('Data berhasil diubah!');
            window.location.href = 'edit_user.php';
        </script>";
    } else {
        echo "<script>alert('Terjadi kesalahan: " . $conn->error . "');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="img/favicon.png">
    <title>Edit User - Budgetin</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <aside class="sidebar" id="sidebar">
        <!-- nav lama -->
        <nav class="naviganteng">
            <ul>
                <li>
                    <a href="#">
                        <div class="logonav">
                            <img src="img/favicon.png" class="logonav">
                        </div>
                    </a>
                </li>
                <li>
                    <a href="../admin/admin.php">
                        <i class="fas fa-home"></i>
                        <span class="nav-item">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="tabel_pengguna.php">
                        <i class="fas fa-user"></i>
                        <span class="nav-item">Tabel User</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>
</div>
<div class="container_index">
    <h1>Dashboard Admin - Budgetin</h1>
    <div class="flex-container">

        <!-- Bagian Pencarian -->
        <div class="search-container">
            <h2 class="teks-search-container">Edit Pengguna</h2>
            <label for="emailSearch">Masukkan Email Pengguna:</label>
            <input type="text" id="emailSearch" name="emailSearch" required>
            <button type="button" class="buttonStyle" onclick="searchUser()">Cari</button>
        </div>
    </div>
    <!-- Bagian Popup Edit -->
    <div id="editPopup" class="popup">
        <div class="popup-content">
            <button class="close" onclick="closeEditPopup()">&times;</button>
            <!-- Logo -->
            <div class="popup-logo">
                <img src="img/logo-removebg-preview.png" alt="Logo" class="popup-logo-img">
            </div>
            <h2>Edit Pengguna</h2>
            <form id="editForm" method="POST" class="popup-form">
                <input type="hidden" name="emailHidden" id="editFormEmailHidden">

                <div class="form-group">
                    <label>ID Pengguna:</label>
                    <p id="editUserId" style="font-weight: bold;"></p> <!-- ID hanya ditampilkan -->
                    <input type="hidden" id="hiddenUserId" name="user_id"> <!-- Tetap dikirim ke server -->
                </div>

                <div class="form-group">
                    <label for="editFormName">Name:</label>
                    <input type="text" id="editFormName" name="name" required>
                </div>

                <div class="form-group">
                    <label for="editFormEmail">Email:</label>
                    <input type="email" id="editFormEmail" name="email" required>
                </div>

                <div class="form-group">
                    <label for="editFormPassword">Masukkan Ulang Password:</label>
                    <input type="password" id="editFormPassword" name="password" required>
                </div>

                <!-- Button -->
                <div class="form-actions">
                    <button type="submit" name="update" class="styled-button">Ganti</button>
                </div>
            </form>
        </div>
    </div>
</div>
    <script>
        // Fungsi untuk menampilkan popup
        function showEditPopup(data) {
            const popup = document.getElementById('editPopup');
            document.getElementById('editFormName').value = data.name;
            document.getElementById('editFormEmail').value = data.email;
            document.getElementById('editFormEmailHidden').value = data.email; // Email asli
            popup.style.display = 'flex'; // Flex untuk posisi tengah
        }

        // Fungsi untuk menutup popup
        function closeEditPopup() {
            const popup = document.getElementById('editPopup');
            popup.style.display = 'none';
        }
        //menamilkan ID Pengguna
        function showEditPopup(data) {
            document.getElementById('editUserId').innerText = data.user_id; // Menampilkan ID pengguna di <p>
            document.getElementById('hiddenUserId').value = data.user_id; // Tetap simpan ID pengguna sebagai input tersembunyi
            document.getElementById('editFormName').value = data.name;
            document.getElementById('editFormEmail').value = data.email;
            document.getElementById('editFormEmailHidden').value = data.email; // Email asli
            document.getElementById('editPopup').style.display = 'flex';
        }


        // Fungsi untuk mencari pengguna berdasarkan email
        function searchUser() {
            const email = document.getElementById('emailSearch').value;

            // Kirim data ke server menggunakan fetch (AJAX)
            fetch('search_user.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'emailSearch=' + encodeURIComponent(email),
            })
                .then(response => response.json()) // Parsing JSON
                .then(data => {
                    if (data.success) {
                        showEditPopup(data.user); // Tampilkan popup dengan data pengguna
                    } else {
                        alert(data.message); // Tampilkan pesan error
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
    </script>
</body>
</html>
