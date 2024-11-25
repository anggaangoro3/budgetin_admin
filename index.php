<?php
include 'db.php';

// Ambil data pengguna
$sql = "SELECT * FROM users";
$result = $conn->query($sql);
?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard Admin</title>
        <link rel="stylesheet" href="styles.css">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </head>
    <body>
    <div class="container">
        <h1>Dashboard Admin - Budget Tracker</h1>

        <div class="flex-container">
            <div class="table-container">
                <h2>Data Pengguna</h2>
                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Nomor Handphone</th>
                        <th>Saldo</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['phone']; ?></td>
                                <td><?php echo $row['balance']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">Tidak ada data pengguna.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="chart-container">
                <h2>Grafik Pergerakan Saldo Pengguna</h2>
                <canvas id="balanceChart" width="400" height="200"></canvas>
                <script>
                    const ctx = document.getElementById('balanceChart').getContext('2d');
                    const balanceChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'],
                            datasets: [{
                                label: 'Saldo Pengguna',
                                data: [12, 19, 3, 5], // Ganti dengan data saldo per minggu
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
    </body>
    </html>

<?php
$conn->close();
?>