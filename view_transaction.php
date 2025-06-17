<?php
include 'db_connection.php';

$id_inv = $_GET['id'];

// Get transaction header
$header_query = $conn->prepare("SELECT th.*, c.customer_name FROM Transaction_Header th JOIN Customer c ON th.id_cust = c.id_cust WHERE th.id_inv = ?");
$header_query->bind_param("i", $id_inv);
$header_query->execute();
$header = $header_query->get_result()->fetch_assoc();

// Get transaction details
$detail_query = $conn->prepare("
    SELECT td.*, p.product_name, p.cost, (td.qty * p.cost) as subtotal 
    FROM Transaction_Detail td 
    JOIN Product p ON td.id_product = p.id_product 
    WHERE td.id_inv = ?
");
$detail_query->bind_param("i", $id_inv);
$detail_query->execute();
$details = $detail_query->get_result();

$total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Transaksi - Renyhijab</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Detail Transaksi - Renyhijab</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="products.php">Produk</a>
            <a href="transactions.php">Transaksi</a>
            <a href="customers.php">Customer</a>
        </nav>
    </header>
    <main>
        <h2>Detail Transaksi</h2>
        
        <div class="form-container">
            <h3>Informasi Transaksi</h3>
            <p><strong>ID Invoice:</strong> <?php echo $header['id_inv']; ?></p>
            <p><strong>Tanggal:</strong> <?php echo $header['date_inv']; ?></p>
            <p><strong>Customer:</strong> <?php echo $header['customer_name']; ?></p>
        </div>
        
        <h3>Detail Produk</h3>
        <table>
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Harga Satuan</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $details->fetch_assoc()) {
                    $total += $row['subtotal'];
                    echo "<tr>
                            <td>{$row['product_name']}</td>
                            <td>Rp " . number_format($row['cost'], 0, ',', '.') . "</td>
                            <td>{$row['qty']}</td>
                            <td>Rp " . number_format($row['subtotal'], 0, ',', '.') . "</td>
                          </tr>";
                }
                ?>
            </tbody>
            <tfoot>
                <tr style="background-color: #35424a; color: white; font-weight: bold;">
                    <td colspan="3">Total</td>
                    <td>Rp <?php echo number_format($total, 0, ',', '.'); ?></td>
                </tr>
            </tfoot>
        </table>
        
        <a href="transactions.php" class="btn">Kembali ke Daftar Transaksi</a>
    </main>
</body>
</html>