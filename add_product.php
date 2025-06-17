<?php
include 'db_connection.php';

$message = '';

if ($_POST) {
    $product_name = $_POST['product_name'];
    $cost = $_POST['cost'];
    $stok = $_POST['stok'];
    
    // Get next ID
    $result = $conn->query("SELECT MAX(id_product) as max_id FROM Product");
    $row = $result->fetch_assoc();
    $next_id = $row['max_id'] + 1;
    
    $stmt = $conn->prepare("INSERT INTO Product (id_product, product_name, cost, stok) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isdi", $next_id, $product_name, $cost, $stok);
    
    if ($stmt->execute()) {
        $message = "Produk berhasil ditambahkan!";
    } else {
        $message = "Error: " . $conn->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk - Renyhijab</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Tambah Produk - Renyhijab</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="products.php">Produk</a>
            <a href="transactions.php">Transaksi</a>
            <a href="customers.php">Customer</a>
        </nav>
    </header>
    <main>
        <h2>Tambah Produk Baru</h2>
        
        <?php if ($message): ?>
            <div class="alert"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <div class="form-container">
            <form method="POST">
                <div class="form-group">
                    <label for="product_name">Nama Produk:</label>
                    <input type="text" id="product_name" name="product_name" required>
                </div>
                <div class="form-group">
                    <label for="cost">Harga:</label>
                    <input type="number" id="cost" name="cost" min="0" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="stok">Stok:</label>
                    <input type="number" id="stok" name="stok" min="0" required>
                </div>
                <button type="submit" class="btn">Tambah Produk</button>
                <a href="products.php" class="btn" style="background-color: #6c757d;">Kembali</a>
            </form>
        </div>
    </main>
</body>
</html>