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
        
        <?php
        // Check if there are any categories first
        $category_check = $conn->query("SELECT COUNT(*) as count FROM Category");
        $category_count = $category_check->fetch_assoc()['count'];
        
        if ($category_count == 0) {
            echo "<div class='alert' style='background-color: #f39c12; color: white; padding: 10px; margin: 10px 0; border-radius: 4px;'>
                    <strong>Peringatan:</strong> Belum ada kategori yang tersedia. 
                    <a href='add_category.php' style='color: white; text-decoration: underline;'>Tambah kategori terlebih dahulu</a>
                  </div>";
        }
        ?>
        
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
                // Modified query with LEFT JOIN to handle missing categories
                $result = $conn->query("
                    SELECT p.*, COALESCE(c.Category, 'Kategori Tidak Ditemukan') as Category 
                    FROM Product p 
                    LEFT JOIN Category c ON p.id_category = c.id_category 
                    ORDER BY p.id_product
                ");
                
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Add warning style if category is missing
                        $category_style = ($row['Category'] == 'Kategori Tidak Ditemukan') ? 'color: red; font-style: italic;' : '';
                        
                        echo "<tr>
                                <td>PRD-{$row['id_product']}</td>
                                <td>{$row['Product']}</td>
                                <td>Rp " . number_format($row['Cost'], 0, ',', '.') . "</td>
                                <td style='{$category_style}'>{$row['Category']}</td>
                                <td class='action-links'>
                                    <a href='edit_product.php?id={$row['id_product']}' class='edit-link'>Edit</a>
                                    <a href='delete_product.php?id={$row['id_product']}' class='delete-link' onclick='return confirm(\"Yakin ingin menghapus produk ini?\")'>Hapus</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' style='text-align: center; padding: 20px; color: #666;'>Belum ada produk yang tersedia</td></tr>";
                }
                ?>
            </tbody>
        </table>
        
        <?php if ($category_count > 0): ?>
            <a href="add_product.php" class="btn">Tambah Produk</a>
        <?php else: ?>
            <p style="color: #666; font-style: italic;">Tambah kategori terlebih dahulu sebelum menambah produk.</p>
        <?php endif; ?>
    </main>
</body>
</html>