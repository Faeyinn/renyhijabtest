<?php
include 'db_connection.php';

$message = '';

if ($_POST) {
    $category_name = $_POST['category_name'];
    
    // Get next ID
    $result = $conn->query("SELECT MAX(id_category) as max_id FROM Category");
    $row = $result->fetch_assoc();
    $next_id = $row['max_id'] + 1;
    
    $stmt = $conn->prepare("INSERT INTO Category (id_category, Category) VALUES (?, ?)");
    $stmt->bind_param("is", $next_id, $category_name);
    
    if ($stmt->execute()) {
        $message = "Kategori berhasil ditambahkan!";
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
    <title>Tambah Kategori - Renyhijab</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Tambah Kategori - Renyhijab</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="products.php">Produk</a>
            <a href="transactions.php">Transaksi</a>
            <a href="customers.php">Customer</a>
            <a href="categories.php">Kategori</a>
        </nav>
    </header>
    <main>
        <h2>Tambah Kategori Baru</h2>
        
        <?php if ($message): ?>
            <div class="alert"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <div class="form-container">
            <form method="POST">
                <div class="form-group">
                    <label for="category_name">Nama Kategori:</label>
                    <input type="text" id="category_name" name="category_name" required>
                </div>
                <button type="submit" class="btn">Tambah Kategori</button>
                <a href="categories.php" class="btn" style="background-color: #6c757d;">Kembali</a>
            </form>
        </div>
    </main>
</body>
</html>