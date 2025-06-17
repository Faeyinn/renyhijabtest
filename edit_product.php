<?php
include 'db_connection.php';

$message = '';
$product = null;

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM Product WHERE id_product = $id");
    $product = $result->fetch_assoc();
}

if ($_POST && $product) {
    $product_name = $_POST['product_name'];
    $cost = $_POST['cost'];
    $stok = $_POST['stok'];
    
    $stmt = $conn->prepare("UPDATE Product SET product_name = ?, cost = ?, stok = ? WHERE id_product = ?");
    $stmt->bind_param("sdii", $product_name, $cost, $stok, $product['id_product']);
    
    if ($stmt->execute()) {
        $message = "Produk berhasil diupdate!";
        // Update display values
        $product['product_name'] = $product_name;
        $product['cost'] = $cost;
        $product['stok'] = $stok;
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
    <title>Edit Produk - Renyhijab</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Edit Produk - Renyhijab</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="products.php">Produk</a>
            <a href="transactions.php">Transaksi</a>
            <a href="customers.php">Customer</a>
        </nav>
    </header>
    <main>
        <h2>Edit Produk</h2>
        
        <?php if ($message): ?>
            <div class="alert"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if ($product): ?>
            <div class="form-container">
                <form method="POST">
                    <div class="form-group">
                        <label>ID Produk:</label>
                        <input type="text" value="<?php echo $product['id_product']; ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="product_name">Nama Produk:</label>
                        <input type="text" id="product_name" name="product_name" 
                               value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="cost">Harga:</label>
                        <input type="number" id="cost" name="cost" min="0" step="0.01" 
                               value="<?php echo $product['cost']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="stok">Stok:</label>
                        <input type="number" id="stok" name="stok" min="0" 
                               value="<?php echo $product['stok']; ?>" required>
                    </div>
                    <button type="submit" class="btn">Update Produk</button>
                    <a href="products.php" class="btn" style="background-color: #6c757d;">Kembali</a>
                </form>
            </div>
        <?php else: ?>
            <p>Produk tidak ditemukan.</p>
            <a href="products.php" class="btn">Kembali ke Daftar Produk</a>
        <?php endif; ?>
    </main>
</body>
</html>