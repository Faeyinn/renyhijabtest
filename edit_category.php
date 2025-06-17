<?php
include 'db_connection.php';

$message = '';
$category = null;

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM Category WHERE id_category = $id");
    $category = $result->fetch_assoc();
}

if ($_POST && $category) {
    $category_name = $_POST['category_name'];
    
    $stmt = $conn->prepare("UPDATE Category SET Category = ? WHERE id_category = ?");
    $stmt->bind_param("si", $category_name, $category['id_category']);
    
    if ($stmt->execute()) {
        $message = "Kategori berhasil diupdate!";
        $category['Category'] = $category_name;
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
    <title>Edit Kategori - Renyhijab</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Edit Kategori - Renyhijab</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="products.php">Produk</a>
            <a href="transactions.php">Transaksi</a>
            <a href="customers.php">Customer</a>
            <a href="categories.php">Kategori</a>
        </nav>
    </header>
    <main>
        <h2>Edit Kategori</h2>
        
        <?php if ($message): ?>
            <div class="alert"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if ($category): ?>
            <div class="form-container">
                <form method="POST">
                    <div class="form-group">
                        <label>ID Kategori:</label>
                        <input type="text" value="<?php echo $category['id_category']; ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="category_name">Nama Kategori:</label>
                        <input type="text" id="category_name" name="category_name" 
                               value="<?php echo htmlspecialchars($category['Category']); ?>" required>
                    </div>
                    <button type="submit" class="btn">Update Kategori</button>
                    <a href="categories.php" class="btn" style="background-color: #6c757d;">Kembali</a>
                </form>
            </div>
        <?php else: ?>
            <p>Kategori tidak ditemukan.</p>
            <a href="categories.php" class="btn">Kembali ke Daftar Kategori</a>
        <?php endif; ?>
    </main>
</body>
</html>