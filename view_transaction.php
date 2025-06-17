<?php
include 'db_connection.php';

if (!isset($_GET['id'])) {
    echo "<script>alert('ID transaksi tidak valid!'); window.location.href='transactions.php';</script>";
    exit;
}

$id_inv = $_GET['id'];

// Get transaction header with payment info
$header_query = $conn->prepare("
    SELECT th.*, c.Customer, p.method_payment, p.payment_date 
    FROM Transaction_Header th 
    JOIN Customer c ON th.id_cust = c.id_cust 
    LEFT JOIN Payment p ON th.id_payment = p.id_payment 
    WHERE th.id_inv = ?
");
$header_query->bind_param("i", $id_inv);
$header_query->execute();
$header = $header_query->get_result()->fetch_assoc();
$header_query->close();

if (!$header) {
    echo "<script>alert('Transaksi tidak ditemukan!'); window.location.href='transactions.php';</script>";
    exit;
}

// Get transaction details
$detail_query = $conn->prepare("
    SELECT td.*, p.Product, p.Cost, (td.Qty * p.Cost) as subtotal 
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
            <a href="categories.php">Kategori</a>
        </nav>
    </header>
    <main>
        <h2>Detail Transaksi #<?php echo $header['id_inv']; ?></h2>
        
        <div class="form-container">
            <h3>Informasi Transaksi</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <p><strong>ID Invoice:</strong> <?php echo $header['id_inv']; ?></p>
                    <p><strong>Tanggal Invoice:</strong> <?php echo date('d/m/Y', strtotime($header['date_inv'])); ?></p>
                    <p><strong>Customer:</strong> <?php echo $header['Customer']; ?></p>
                </div>
                <div>
                    <p><strong>Metode Pembayaran:</strong> <?php echo $header['method_payment'] ?? 'Tidak ada data'; ?></p>
                    <p><strong>Tanggal Pembayaran:</strong> <?php echo $header['payment_date'] ? date('d/m/Y', strtotime($header['payment_date'])) : 'Tidak ada data'; ?></p>
                </div>
            </div>
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
                        <td>{$row['Product']}</td>
                        <td>Rp " . number_format($row['Cost'], 0, ',', '.') . "</td>
                        <td>{$row['Qty']}</td>
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
        
        <div style="margin-top: 20px;">
            <a href="transactions.php" class="btn">Kembali ke Daftar Transaksi</a>
            <button onclick="window.print()" class="btn" style="background-color: #007bff;">Cetak</button>
        </div>
    </main>
    
    <style>
        @media print {
            header nav, .btn { display: none; }
            body { font-size: 12px; }
        }
    </style>
</body>
</html>