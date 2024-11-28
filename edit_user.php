<?php
global $conn;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['phoneSearch'])) {
    $phone = $_POST['phoneSearch'];
    $query = "SELECT * FROM users WHERE phone = '$phone'";
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
?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $balance = $_POST['balance'];
    $originalPhone = $_POST['phoneHidden'];

    $updateQuery = "UPDATE users SET name='$name', phone='$phone', email='$email', balance='$balance' WHERE phone='$originalPhone'";
    if ($conn->query($updateQuery) === TRUE) {
        echo "<script>
            alert('Data berhasil diubah!');
            window.location.href = 'index.php';
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
    <title>Adm-Budgetin</title>
    <link rel="stylesheet" href="styles.css"> <!-- Sertakan file CSS -->
    <script>
        // Fungsi untuk menampilkan popup
        function showEditPopup(data) {
            const popup = document.getElementById('editPopup');
            document.getElementById('editFormName').value = data.name;
            document.getElementById('editFormPhone').value = data.phone;
            document.getElementById('editFormEmail').value = data.email;
            document.getElementById('editFormBalance').value = data.balance;
            document.getElementById('editFormPhoneHidden').value = data.phone;
            popup.style.display = 'block';
        }

        // Fungsi untuk menutup popup
        function closeEditPopup() {
            document.getElementById('editPopup').style.display = 'none';
        }
    </script>
</head>
<body>
<div class="container_index">
    <h1>Dashboard Admin - Budgetin</h1>
    <div class="flex-container">
        <!-- Bagian Pencarian -->
        <div class="search-container">
            <h2>Edit Pengguna</h2>
            <label for="phoneSearch">Masukkan Nomor Handphone:</label>
            <input type="text" id="phoneSearch" name="phoneSearch" required>
            <button type="button" class="buttonStyle" onclick="searchUser()">Cari</button>
        </div>
    </div>

    <script>
        // Fungsi untuk mencari pengguna berdasarkan nomor telepon
        function searchUser() {
            const phone = document.getElementById('phoneSearch').value;

            // Kirim data ke server menggunakan fetch (AJAX)
            fetch('search_user.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'phoneSearch=' + encodeURIComponent(phone),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showEditPopup(data.user); // Tampilkan popup dengan data pengguna
                    } else {
                        alert('Pengguna tidak ditemukan!');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
    </script>


    <!-- Bagian Popup Edit -->
    <div id="editPopup" class="popup" style="display: none;">
        <div class="popup-content">
            <span class="close" onclick="closeEditPopup()">&times;</span>
            <h2>Edit Pengguna</h2>
            <form id="editForm" method="POST" action="index.php">
                <input type="hidden" name="phoneHidden" id="editFormPhoneHidden">
                <label for="editFormName">Name:</label>
                <input type="text" id="editFormName" name="name" required>
                <label for="editFormPhone">Phone:</label>
                <input type="text" id="editFormPhone" name="phone" required>
                <label for="editFormEmail">Email:</label>
                <input type="email" id="editFormEmail" name="email" required>
                <label for="editFormBalance">Balance:</label>
                <input type="number" id="editFormBalance" name="balance" required>
                <button type="submit" name="update" class="styled-button">Ganti</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
