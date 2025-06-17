<?php
include 'db_connection.php';

$message = '';

if ($_POST) {
    $customer_name = $_POST['customer_name'];
    
    // Get next ID
    $result = $conn->query("SELECT MAX(id_cust) as max_id FROM Customer");
    $row = $result->fetch_assoc();
    $next_id = $row['max_id'] + 1;
    
    $stmt = $conn->prepare("INSERT INTO Customer (id_cust, customer_name) VALUES (?, ?)");
    $stmt->bind_param("is", $next_id, $customer_name);
    
    if ($stmt->execute()) {
        $message = "Customer berhasil ditambahkan!";
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
    <title>Tambah Customer - Renyhijab</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Tambah Customer - Renyhijab</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="products.php">Produk</a>
            <a href="transactions.php">Transaksi</a>
            <a href="customers.php">Customer</a>
        </nav>
    </header>
    <main>
        <h2>Tambah Customer Baru</h2>
        
        <?php if ($message): ?>
            <div class="alert"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <div class="form-container">
            <form method="POST">
                <div class="form-group">
                    <label for="customer_name">Nama Customer:</label>
                    <input type="text" id="customer_name" name="customer_name" required>
                </div>
                <button type="submit" class="btn">Tambah Customer</button>
                <a href="customers.php" class="btn" style="background-color: #6c757d;">Kembali</a>
            </form>
        </div>
    </main>
</body>
</html>