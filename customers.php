<?php
include 'db_connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer - Renyhijab</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Customer Renyhijab</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="products.php">Produk</a>
            <a href="transactions.php">Transaksi</a>
            <a href="customers.php">Customer</a>
            <a href="categories.php">Kategori</a>
        </nav>
    </header>
    <main>
        <h2>Daftar Customer</h2>
        <table>
            <thead>
                <tr>
                    <th>ID Customer</th>
                    <th>Nama Customer</th>
                    <th>Total Transaksi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("
                    SELECT c.*, COUNT(th.id_inv) as transaction_count 
                    FROM Customer c 
                    LEFT JOIN Transaction_Header th ON c.id_cust = th.id_cust 
                    GROUP BY c.id_cust 
                    ORDER BY c.id_cust
                ");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>CUST-{$row['id_cust']}</td>
                            <td>{$row['Customer']}</td>
                            <td>{$row['transaction_count']} transaksi</td>
                            <td class='action-links'>
                                <a href='edit_customer.php?id={$row['id_cust']}' class='edit-link'>Edit</a>
                                <a href='delete_customer.php?id={$row['id_cust']}' class='delete-link' onclick='return confirm(\"Yakin ingin menghapus customer ini?\")'>Hapus</a>
                            </td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
        <a href="add_customer.php" class="btn">Tambah Customer</a>
    </main>
</body>
</html>