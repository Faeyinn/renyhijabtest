<?php
include 'db_connection.php';

$message = '';
$product = null;

// Get categories for dropdown
$categories = $conn->query("SELECT * FROM Category ORDER BY Category");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Sanitize input
    $stmt = $conn->prepare("SELECT * FROM Product WHERE id_product = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
}

if ($_POST && $product) {
    $product_name = $_POST['product_name'];
    $cost = $_POST['cost'];
    $id_category = $_POST['id_category'];
    
    $stmt = $conn->prepare("UPDATE Product SET Product = ?, Cost = ?, id_category = ? WHERE id_product = ?");
    $stmt->bind_param("sdii", $product_name, $cost, $id_category, $product['id_product']);
    
    if ($stmt->execute()) {
        $message = "Produk berhasil diupdate!";
        // Update display values
        $product['Product'] = $product_name;
        $product['Cost'] = $cost;
        $product['id_category'] = $id_category;
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
            <a href="categories.php">Kategori</a>
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
                        <input type="text" value="PRD-<?php echo $product['id_product']; ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="product_name">Nama Produk:</label>
                        <input type="text" id="product_name" name="product_name" 
                               value="<?php echo htmlspecialchars($product['Product']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="cost">Harga:</label>
                        <input type="number" id="cost" name="cost" min="0" step="0.01" 
                               value="<?php echo $product['Cost']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="id_category">Kategori:</label>
                        <select id="id_category" name="id_category" required>
                            <option value="">Pilih Kategori</option>
                            <?php 
                            if ($categories && $categories->num_rows > 0) {
                                while ($category = $categories->fetch_assoc()): 
                            ?>
                                <option value="<?php echo $category['id_category']; ?>" 
                                        <?php echo ($product['id_category'] == $category['id_category']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['Category']); ?>
                                </option>
                            <?php 
                                endwhile; 
                            } else {
                                echo "<option value=''>Tidak ada kategori tersedia</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="btn">Update Produk</button>
                    <a href="products.php" class="btn" style="background-color: #6c757d;">Kembali</a>
                </form>
            </div>
        <?php else: ?>
            <div class="alert" style="background-color: #e74c3c; color: white;">
                Produk tidak ditemukan atau ID tidak valid.
            </div>
            <a href="products.php" class="btn">Kembali ke Daftar Produk</a>
        <?php endif; ?>
    </main>
</body>
</html>