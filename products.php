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
    <style>
        .stock-status {
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .stock-high {
            background-color: #D1FAE5;
            color: #065F46;
        }
        
        .stock-medium {
            background-color: #FEF3C7;
            color: #92400E;
        }
        
        .stock-low {
            background-color: #FEE2E2;
            color: #991B1B;
        }
        
        .stock-empty {
            background-color: #F3F4F6;
            color: #374151;
            text-decoration: line-through;
        }
    </style>
</head>
<body>
    <header>
        <h1>Produk Renyhijab</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="products.php">Produk</a>
            <a href="transactions.php">Transaksi</a>
            <a href="customers.php">Customer</a>
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
                    <th>Stok</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT * FROM Product ORDER BY id_product");
                while ($row = $result->fetch_assoc()) {
                    // Determine stock status
                    $stock = $row['stok'];
                    $status_class = '';
                    $status_text = '';
                    
                    if ($stock == 0) {
                        $status_class = 'stock-empty';
                        $status_text = 'Habis';
                    } elseif ($stock <= 5) {
                        $status_class = 'stock-low';
                        $status_text = 'Stok Rendah';
                    } elseif ($stock <= 20) {
                        $status_class = 'stock-medium';
                        $status_text = 'Stok Sedang';
                    } else {
                        $status_class = 'stock-high';
                        $status_text = 'Stok Baik';
                    }
                    
                    echo "<tr>
                            <td>PRD-{$row['id_product']}</td>
                            <td>{$row['product_name']}</td>
                            <td>Rp " . number_format($row['cost'], 0, ',', '.') . "</td>
                            <td>{$row['stok']}</td>
                            <td><span class='stock-status {$status_class}'>{$status_text}</span></td>
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