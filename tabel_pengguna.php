<?php
include 'db.php'; // Koneksi ke database
global $conn;

// Inisialisasi variabel
$users = [];
$budgets = [];
$expenses = [];
$search_email = '';
$error = '';

// Ambil semua data pengguna saat pertama kali load halaman
$sql_users = "
    SELECT 
        users.user_id, 
        users.name, 
        users.email, 
        IFNULL(SUM(budgets.amount), 0) AS total_income,
        IFNULL(SUM(expenses.amount), 0) AS total_expenses
    FROM users
    LEFT JOIN budgets ON users.user_id = budgets.user_id
    LEFT JOIN expenses ON users.user_id = expenses.user_id
    GROUP BY users.user_id";
$result_users = $conn->query($sql_users);

if ($result_users) {
    $users = $result_users->fetch_all(MYSQLI_ASSOC);
} else {
    $error = "Terjadi kesalahan dalam mengambil data.";
}

// Jika email dicari
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['email'])) {
    $search_email = htmlspecialchars($_POST['email']); // Ambil email dari input

    // Query untuk mencari data pengguna berdasarkan email
    $sql_search = "
        SELECT 
            users.user_id, 
            users.name, 
            users.email, 
            IFNULL(SUM(budgets.amount), 0) AS total_income,
            IFNULL(SUM(expenses.amount), 0) AS total_expenses
        FROM users
        LEFT JOIN budgets ON users.user_id = budgets.user_id
        LEFT JOIN expenses ON users.user_id = expenses.user_id
        WHERE users.email = ?
        GROUP BY users.user_id";
    $stmt_search = $conn->prepare($sql_search);
    $stmt_search->bind_param('s', $search_email);
    $stmt_search->execute();
    $result_search = $stmt_search->get_result();

    if ($result_search->num_rows > 0) {
        $users = $result_search->fetch_all(MYSQLI_ASSOC);
        $user_id = $users[0]['user_id']; // Ambil ID pengguna untuk rincian data

        // Ambil data dari tabel budgets
        $sql_budgets = "SELECT name, amount, created_at FROM budgets WHERE user_id = ?";
        $stmt_budgets = $conn->prepare($sql_budgets);
        $stmt_budgets->bind_param('i', $user_id);
        $stmt_budgets->execute();
        $budgets_result = $stmt_budgets->get_result();
        $budgets = $budgets_result->fetch_all(MYSQLI_ASSOC);

        // Ambil data dari tabel expenses
        $sql_expenses = "SELECT description, amount, created_at FROM expenses WHERE user_id = ?";
        $stmt_expenses = $conn->prepare($sql_expenses);
        $stmt_expenses->bind_param('i', $user_id);
        $stmt_expenses->execute();
        $expenses_result = $stmt_expenses->get_result();
        $expenses = $expenses_result->fetch_all(MYSQLI_ASSOC);
    } else {
        $error = "Pengguna dengan email '$search_email' tidak ditemukan.";
    }
    $stmt_search->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabel Pengguna</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<aside class="naviganteng">
    <ul>
        <li>
            <a href="#">
                <div class="logonav">
                    <img src="img/favicon.png" class="logonav">
                </div>
            </a>
        </li>
        <li>
            <a href="admin.php"> <!-- Link ke file untuk menampilkan tabel keseluruhan pengguna, kemudian terdapat searching berdasar email yang akan melakukan filter hanya ke data pengguna yang email nya di input -->
                <i class="fas fa-home"></i>
                <span class="nav-item">Dashboard</span>
            </a>
        </li>
        <li>
            <a href="edit_user.php">
                <i class="fas fa-edit"></i>
                <span class="nav-item">Edit</span>
            </a>
        </li>
        <li>
            <a href="logout.php" class="logout">
                <i class="fas fa-sign-out-alt"></i>
                <span class="nav-item">Logout</span>
            </a>
        </li>
    </ul>
</aside>
<body>
<div class="container-data-tabel">
    <h1>Tabel Data Pengguna</h1>

    <!-- Form Pencarian -->
    <form method="POST" action="">
        <input type="email" id="email" name="email" placeholder="Masukkan email pengguna" value="<?= htmlspecialchars($search_email); ?>" required>
        <button type="submit">Cari</button>
    </form>

    <!-- Tampilkan pesan error jika ada -->
    <?php if (!empty($error)): ?>
        <div class="error-message"><?= $error; ?></div>
    <?php endif; ?>

    <!-- Tabel Data Pengguna -->
    <h2>Daftar Seluruh Pengguna</h2>
    <table>
        <thead>
        <tr>
            <th>ID Pengguna</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Total Pemasukan</th>
            <th>Total Pengeluaran</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($users)): ?>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['user_id']); ?></td>
                    <td><?= htmlspecialchars($user['name']); ?></td>
                    <td><?= htmlspecialchars($user['email']); ?></td>
                    <td>Rp <?= number_format($user['total_income'], 2, ',', '.'); ?></td>
                    <td>Rp <?= number_format($user['total_expenses'], 2, ',', '.'); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">Tidak ada data pengguna.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <!-- Rincian Pemasukan -->
    <?php if (!empty($budgets)): ?>
        <h2>Rincian Pemasukan</h2>
        <table>
            <thead>
            <tr>
                <th>Nama</th>
                <th>Jumlah</th>
                <th>Tanggal</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($budgets as $budget): ?>
                <tr>
                    <td><?= htmlspecialchars($budget['name']); ?></td>
                    <td>Rp <?= number_format($budget['amount'], 2, ',', '.'); ?></td>
                    <td><?= date('d M Y', strtotime($budget['created_at'])); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <!-- Rincian Pengeluaran -->
    <?php if (!empty($expenses)): ?>
        <h2>Rincian Pengeluaran</h2>
        <table>
            <thead>
            <tr>
                <th>Deskripsi</th>
                <th>Jumlah</th>
                <th>Tanggal</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($expenses as $expense): ?>
                <tr>
                    <td><?= htmlspecialchars($expense['description']); ?></td>
                    <td>Rp <?= number_format($expense['amount'], 2, ',', '.'); ?></td>
                    <td><?= date('d M Y', strtotime($expense['created_at'])); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>

