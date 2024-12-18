<?php
// Sertakan file koneksi database
include 'db.php';
global $conn;

// Inisialisasi variabel untuk tabel dan grafik
$users = [];
$labels = [];
$balances = []; // Tambahkan variabel ini

// Ambil data pengguna
$sql = "SELECT user_id, name, email FROM users";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row; // Simpan data untuk tabel
        $labels[] = $row['name']; // Label grafik
    }
} else {
    die("Error query: " . $conn->error);
}

// Ambil data saldo pengguna dari tabel budgets
$sql_balances = "SELECT users.name, IFNULL(SUM(budgets.amount), 0) AS total_balance 
                 FROM users 
                 LEFT JOIN budgets ON users.user_id = budgets.user_id 
                 GROUP BY users.user_id";

$result_balances = $conn->query($sql_balances);

if ($result_balances) {
    while ($row = $result_balances->fetch_assoc()) {
        $balances[] = $row['total_balance']; // Simpan saldo pengguna
    }
} else { // Jangan ada HTML/PHP lain di antara ini
    die("Error query (balances): " . $conn->error);
}
?>



<?php
/* Delete data berdasarkan ID */
if (isset($_GET['delete'])) {
    $user_id = (int) $_GET['delete']; // Cast ke integer untuk keamanan
    $deleteQuery = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $deleteQuery->bind_param('i', $user_id);

    if ($deleteQuery->execute()) {
        header("Location: admin.php?msg=Data deleted successfully");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
$sql_balances = "
    SELECT 
        users.user_id, 
        users.name, 
        IFNULL(SUM(budgets.amount), 0) AS total_budget, 
        IFNULL(SUM(expenses.amount), 0) AS total_expenses, 
        (IFNULL(SUM(budgets.amount), 0) - IFNULL(SUM(expenses.amount), 0)) AS total_balance
    FROM 
        users
    LEFT JOIN budgets ON users.user_id = budgets.user_id
    LEFT JOIN expenses ON users.user_id = expenses.user_id
    GROUP BY users.user_id
";
$sql_growth = "
    SELECT 
        users.name, 
        budgets.created_at, 
        SUM(budgets.amount) - IFNULL(SUM(expenses.amount), 0) AS saldo_terkini
    FROM 
        users
    LEFT JOIN budgets ON users.user_id = budgets.user_id
    LEFT JOIN expenses ON users.user_id = expenses.user_id
    GROUP BY users.name, budgets.created_at
    ORDER BY budgets.created_at ASC
";
$result_growth = $conn->query($sql_growth);

$growth_data = [];
if ($result_growth) {
    while ($row = $result_growth->fetch_assoc()) {
        $growth_data[] = $row;
    }
}

$total_budget = 0;
$total_expense = 0;

if (isset($user_id) && !empty($user_id)) {
    // Ambil total budget pengguna
    $sql_get_total_budget = "SELECT SUM(amount) AS total_budget FROM budgets WHERE user_id = ?";
    $stmt_get_total_budget = $conn->prepare($sql_get_total_budget);
    $stmt_get_total_budget->bind_param("i", $user_id);
    $stmt_get_total_budget->execute();
    $result_get_total_budget = $stmt_get_total_budget->get_result();
    $total_budget = $result_get_total_budget->fetch_assoc()['total_budget'] ?? 0;
    $stmt_get_total_budget->close();

    // Ambil total pengeluaran pengguna
    $sql_get_total_expense = "SELECT SUM(amount) AS total_expense FROM expenses WHERE user_id = ?";
    $stmt_get_total_expense = $conn->prepare($sql_get_total_expense);
    $stmt_get_total_expense->bind_param("i", $user_id);
    $stmt_get_total_expense->execute();
    $result_get_total_expense = $stmt_get_total_expense->get_result();
    $total_expense = $result_get_total_expense->fetch_assoc()['total_expense'] ?? 0;
    $stmt_get_total_expense->close();
}

?>

<?php
// Ambil Total Pemasukan (Budgets) dan Total Pengeluaran (Expenses) untuk semua pengguna
$sql_total_balances = "SELECT IFNULL(SUM(amount), 0) AS total_balance FROM budgets";
$sql_total_expenses = "SELECT IFNULL(SUM(amount), 0) AS total_expense FROM expenses";

$result_total_balances = $conn->query($sql_total_balances);
$result_total_expenses = $conn->query($sql_total_expenses);

$total_balance = 0;
$total_expense = 0;

if ($result_total_balances) {
    $total_balance = $result_total_balances->fetch_assoc()['total_balance'] ?? 0;
}

if ($result_total_expenses) {
    $total_expense = $result_total_expenses->fetch_assoc()['total_expense'] ?? 0;
}

// Hitung sisa saldo setelah pengeluaran
$remaining_balance = $total_balance - $total_expense;
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTFs-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="img/favicon.png">
    <title>Adm-Budgetin</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to CSS file -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="path/to/chart.min.js"></script>
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
            <a href="#">
                <i class="fas fa-user"></i>
                <span class="nav-item">Tabel User</span>
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
<!-- Main Content -->
<main>
    <div class="wrapper">
        <h1>Dashboard Utama</h1>
        <!-- Conntainer Bagian Tabel -->
        <div class="container_index">
            <div class="table-container">
                <h2>Data Pengguna</h2>
                <table>
                    <thead>
                    <tr>
                        <th>ID Pengguna</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Saldo</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (count($users) > 0): ?>
                        <?php foreach ($users as $index => $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                                <td><?php echo htmlspecialchars($user['name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>Rp <?php echo number_format($balances[$index], 2, ',', '.'); ?></td> <!-- indeks -->
                                <td>
                                    <a href="admin.php?delete=<?php echo $user['user_id']; ?>" onclick="return confirm('Apakah Anda yakin?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">Tidak ada data pengguna.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <button onclick="window.location.href='add_user.php'" class="buttonStyle">Tambah Pengguna</button>
            <button onclick="window.location.href='edit_user.php'" class="buttonStyle">Edit Pengguna</button>
        </div>
        <div class="chart-container">
            <!-- Grafik Garis -->
            <div class="chart-container-line">
                <h3 class="teks-grafik">Grafik Pertumbuhan Umum</h3>
                <canvas id="line-chart"></canvas>
            </div>
            <div class="chart-container-bar">
                <h3 class="teks-grafik">Grafik Pemasukan Pengeluaran</h3>
                <canvas id="pie-chart"></canvas>
        </div>
    </div>
</main>
<!-- Footer Section -->
<footer>
    <div class="bawah">
        <p>&copy; 2024 Budgetin. Hak cipta dilindungi.</p>
        <a href="#">Azmi Reza Anggoro</a>
    </div>
</footer>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    const growthLabels = <?php echo json_encode(array_column($growth_data, 'created_at')); ?>;
    const growthBalances = <?php echo json_encode(array_column($growth_data, 'saldo_terkini')); ?>;

    const growthCtx = document.getElementById('line-chart').getContext('2d');
    new Chart(growthCtx, {
        type: 'line',
        data: {
            labels: growthLabels,
            datasets: [{
                label: 'Pertumbuhan Saldo',
                data: growthBalances,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2,
                fill: true
            }]
        },
        options: {
            scales: {
                x: { title: { display: true, text: 'Tanggal' } },
                y: { title: { display: true, text: 'Saldo' }, beginAtZero: true }
            }
        }
    });
    window.addEventListener('resize', () => {
        if (growthCtx) {
            growthChart.resize(); // Redraw grafik
        }
    });

</script>
<!-- Tambahkan javascript pie chart di sini -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Data dari PHP
        const totalIncome = <?php echo json_encode($total_balance); ?>; // Total pemasukan
        const totalExpenses = <?php echo json_encode($total_expense); ?>; // Total pengeluaran
        const remainingBalance = <?php echo json_encode($remaining_balance); ?>; // Sisa saldo

        // Debugging data di console
        console.log("Total Income:", totalIncome);
        console.log("Total Expenses:", totalExpenses);
        console.log("Remaining Balance:", remainingBalance);

        // Buat grafik pie
        const ctx = document.getElementById('pie-chart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Saldo Sisa', 'Pengeluaran Total'], // Label untuk setiap bagian
                datasets: [{
                    data: [remainingBalance, totalExpenses], // Data untuk pie chart
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.6)', // Warna Saldo Sisa
                        'rgba(227,0,47,0.99)'  // Warna Pengeluaran Total
                    ],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    title: {
                        display: true,
                        text: 'Grafik Perbandingan Saldo Sisa dan Pengeluaran'
                    }
                }
            }
        });
    });
</script>
</html>
<?php
$conn->close();
?>