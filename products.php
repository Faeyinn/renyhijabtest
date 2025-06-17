<?php
include 'db_connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk - Renyhijab</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Produk Renyhijab</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="products.php">Produk</a>
            <a href="transactions.php">Transaksi</a>
            <a href="customers.php">Customer</a>
            <a href="categories.php">Kategori</a>
        </nav>
    </header>
    <main>
        <h2>Daftar Produk</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Produk</th>
                    <th>Harga</th>
                    <th>Kategori</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("
                    SELECT p.*, c.Category 
                    FROM Product p 
                    JOIN Category c ON p.id_category = c.id_category 
                    ORDER BY p.id_product
                ");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>PRD-{$row['id_product']}</td>
                            <td>{$row['Product']}</td>
                            <td>Rp " . number_format($row['Cost'], 0, ',', '.') . "</td>
                            <td>{$row['Category']}</td>
                            <td class='action-links'>
                                <a href='edit_product.php?id={$row['id_product']}' class='edit-link'>Edit</a>
                                <a href='delete_product.php?id={$row['id_product']}' class='delete-link' onclick='return confirm(\"Yakin ingin menghapus produk ini?\")'>Hapus</a>
                            </td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
        <a href="add_product.php" class="btn">Tambah Produk</a>
    </main>
</body>
</html>