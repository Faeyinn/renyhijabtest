<?php
include 'db_connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi - Renyhijab</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Transaksi Renyhijab</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="products.php">Produk</a>
            <a href="transactions.php">Transaksi</a>
            <a href="customers.php">Customer</a>
        </nav>
    </header>
    <main>
        <h2>Daftar Transaksi</h2>
        <table>
            <thead>
                <tr>
                    <th>ID Invoice</th>
                    <th>Tanggal</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("
                    SELECT th.id_inv, th.date_inv, c.customer_name, 
                           SUM(p.cost * td.qty) as total
                    FROM Transaction_Header th
                    JOIN Customer c ON th.id_cust = c.id_cust
                    JOIN Transaction_Detail td ON th.id_inv = td.id_inv
                    JOIN Product p ON td.id_product = p.id_product
                    GROUP BY th.id_inv, th.date_inv, c.customer_name
                    ORDER BY th.date_inv DESC, th.id_inv DESC
                ");
                
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['id_inv']}</td>
                            <td>{$row['date_inv']}</td>
                            <td>{$row['customer_name']}</td>
                            <td>Rp " . number_format($row['total'], 0, ',', '.') . "</td>
                            <td class='action-links'>
                                <a href='view_transaction.php?id={$row['id_inv']}' class='view-link'>Lihat</a>
                                <a href='delete_transaction.php?id={$row['id_inv']}' class='delete-link' onclick='return confirm(\"Yakin ingin menghapus transaksi ini?\")'>Hapus</a>
                            </td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
        <a href="add_transaction.php" class="btn">Tambah Transaksi</a>
    </main>
</body>
</html>