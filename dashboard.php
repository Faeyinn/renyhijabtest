<?php
include 'db_connection.php';

// Get statistics
$total_customers = $conn->query("SELECT COUNT(*) as count FROM Customer")->fetch_assoc()['count'];
$total_products = $conn->query("SELECT COUNT(*) as count FROM Product")->fetch_assoc()['count'];
$total_transactions = $conn->query("SELECT COUNT(*) as count FROM Transaction_Header")->fetch_assoc()['count'];
$total_revenue = $conn->query("SELECT SUM(p.cost * td.qty) as total FROM Transaction_Detail td JOIN Product p ON td.id_product = p.id_product")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Renyhijab</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Dashboard Renyhijab</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="products.php">Produk</a>
            <a href="transactions.php">Transaksi</a>
            <a href="customers.php">Customer</a>
        </nav>
    </header>
    <main>
        <h2>Selamat Datang di Dashboard</h2>
        
        <div class="stats-container">
            <div class="stat-card">
                <h3>Total Customer</h3>
                <p class="stat-number"><?php echo $total_customers; ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Produk</h3>
                <p class="stat-number"><?php echo $total_products; ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Transaksi</h3>
                <p class="stat-number"><?php echo $total_transactions; ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Pendapatan</h3>
                <p class="stat-number">Rp <?php echo number_format($total_revenue, 0, ',', '.'); ?></p>
            </div>
        </div>

        <h3>Transaksi Terbaru</h3>
        <table>
            <thead>
                <tr>
                    <th>ID Invoice</th>
                    <th>Tanggal</th>
                    <th>Customer</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $recent_transactions = $conn->query("
                    SELECT th.id_inv, th.date_inv, c.customer_name, 
                           SUM(p.cost * td.qty) as total
                    FROM Transaction_Header th
                    JOIN Customer c ON th.id_cust = c.id_cust
                    JOIN Transaction_Detail td ON th.id_inv = td.id_inv
                    JOIN Product p ON td.id_product = p.id_product
                    GROUP BY th.id_inv, th.date_inv, c.customer_name
                    ORDER BY th.date_inv DESC
                    LIMIT 5
                ");
                
                while ($row = $recent_transactions->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['id_inv']}</td>
                            <td>{$row['date_inv']}</td>
                            <td>{$row['customer_name']}</td>
                            <td>Rp " . number_format($row['total'], 0, ',', '.') . "</td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </main>
</body>
</html>