<?php
// Sertakan file koneksi database
include 'db.php'; // Pastikan hanya sekali
global $conn;

// Inisialisasi variabel untuk tabel dan grafik
$users = [];
$labels = [];
$balances = [];

// Ambil data pengguna
$sql = "SELECT user_id, name, phone, email, balance FROM users"; // Tambahkan user_id
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row; // Simpan data untuk tabel
        $labels[] = $row['name']; // Label grafik
        $balances[] = $row['balance']; // Data saldo grafik
    }
} else {
    die("Error query: " . $conn->error);
}
?>


<?php
global $conn;
include 'db.php';

$response = ['status' => 'error', 'message' => ''];

// Jika nomor telepon diterima untuk pencarian
if (isset($_POST['search_phone'])) {
    $phone = $_POST['search_phone'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE phone = ?");
    $stmt->bind_param('s', $phone); // 's' untuk string
    $stmt->execute();
    $result = $stmt->get_result();

    $response = ['status' => 'error', 'message' => ''];
    if ($result->num_rows > 0) {
        $response['status'] = 'success';
        $response['data'] = $result->fetch_assoc();
    } else {
        $response['message'] = 'Pengguna tidak ditemukan!';
    }

    echo json_encode($response);
    exit;
}

// Jika data untuk pembaruan diterima
if (isset($_POST['edit_user'])) {
    $user_id = $_POST['user_id'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $balance = $_POST['balance'];

    // Update data pengguna
    $sql = "UPDATE users SET name='$name', phone='$phone', email='$email', balance='$balance' WHERE user_id=$user_id";

    if ($conn->query($sql) === TRUE) {
        $response['status'] = 'success';
        $response['message'] = 'Data berhasil diperbarui!';
    } else {
        $response['message'] = 'Error: ' . $conn->error;
    }
    echo json_encode($response);
    exit;
}
?>
<?php
/* Delete data berdasarkan ID */
if (isset($_GET['delete'])) {
    $user_id = (int) $_GET['delete']; // Cast ke integer untuk keamanan
    $deleteQuery = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $deleteQuery->bind_param('i', $user_id);

    if ($deleteQuery->execute()) {
        header("Location: index.php?msg=Data deleted successfully");
        exit;
    } else {
        echo "Error: " . $conn->error;
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Sertakan Chart.js -->
</head>
<body>
<div class="container_index">
    <h1>Dashboard Admin - Budgetin</h1>
    <div class="flex-container">
        <!-- Bagian Tabel -->
        <div class="table-container">
            <h2>Data Pengguna</h2>
            <button onclick="window.location.href='add_user.php'" class="buttonStyle">Tambah Pengguna</button>
            <button onclick="window.location.href='edit_user.php'" class="buttonStyle">Edit Pengguna</button>
            <table>
                <thead>
                <tr>
                    <th>Nama</th>
                    <th>Nomor Handphone</th>
                    <th>Email</th>
                    <th>Saldo</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php if (count($users) > 0): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['phone']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['balance']); ?></td>
                            <td>
                                <a href="edit_user.php?id=<?php echo $user['user_id']; ?>">Edit</a> |
                                <a href="index.php?delete=<?php echo $user['user_id']; ?>" onclick="return confirm('Apakah Anda yakin?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Tidak ada data pengguna.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="flex-container">
        <!-- Bagian Grafik -->
        <div class="chart-container">
            <h2 class="grafik_saldo">Grafik Saldo Pengguna</h2>
            <canvas id="balanceChart"></canvas>
            <div class="batang">
                <script>
                    const ctx = document.getElementById('balanceChart').getContext('2d');
                    const balanceChart = new Chart(ctx, {
                        type: 'bar', // Tipe grafik
                        data: {
                            labels: <?php echo json_encode($labels); ?>, // Label (nama pengguna)
                            datasets: [{
                                label: 'Saldo Pengguna',
                                data: <?php echo json_encode($balances); ?>, // Data saldo
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                </script>
            </div>

            </div>


    </div>
</div>
</body>
</html>

<?php
$conn->close(); // Tutup koneksi database
?>
