<?php
include 'db_connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori - Renyhijab</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Kategori Renyhijab</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="products.php">Produk</a>
            <a href="transactions.php">Transaksi</a>
            <a href="customers.php">Customer</a>
            <a href="categories.php">Kategori</a>
        </nav>
    </header>
    <main>
        <h2>Daftar Kategori</h2>
        <table>
            <thead>
                <tr>
                    <th>ID Kategori</th>
                    <th>Nama Kategori</th>
                    <th>Jumlah Produk</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("
                    SELECT c.*, COUNT(p.id_product) as product_count 
                    FROM Category c 
                    LEFT JOIN Product p ON c.id_category = p.id_category 
                    GROUP BY c.id_category 
                    ORDER BY c.id_category
                ");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>CAT-{$row['id_category']}</td>
                            <td>{$row['Category']}</td>
                            <td>{$row['product_count']} produk</td>
                            <td class='action-links'>
                                <a href='edit_category.php?id={$row['id_category']}' class='edit-link'>Edit</a>
                                <a href='delete_category.php?id={$row['id_category']}' class='delete-link' onclick='return confirm(\"Yakin ingin menghapus kategori ini?\")'>Hapus</a>
                            </td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
        <a href="add_category.php" class="btn">Tambah Kategori</a>
    </main>
</body>
</html>