<?php
include 'db_connection.php';

$message = '';

// Get categories for dropdown
$categories = $conn->query("SELECT * FROM Category ORDER BY Category");

if ($_POST) {
    $product_name = $_POST['product_name'];
    $cost = $_POST['cost'];
    $id_category = $_POST['id_category'];
    
    // Get next ID
    $result = $conn->query("SELECT MAX(id_product) as max_id FROM Product");
    $row = $result->fetch_assoc();
    $next_id = $row['max_id'] + 1;
    
    $stmt = $conn->prepare("INSERT INTO Product (id_product, Product, Cost, id_category) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isdi", $next_id, $product_name, $cost, $id_category);
    
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
            <a href="categories.php">Kategori</a>
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
                    <label for="id_category">Kategori:</label>
                    <select id="id_category" name="id_category" required>
                        <option value="">Pilih Kategori</option>
                        <?php while ($category = $categories->fetch_assoc()): ?>
                            <option value="<?php echo $category['id_category']; ?>">
                                <?php echo $category['Category']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button type="submit" class="btn">Tambah Produk</button>
                <a href="products.php" class="btn" style="background-color: #6c757d;">Kembali</a>
            </form>
        </div>
    </main>
</body>
</html>