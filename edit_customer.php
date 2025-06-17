<?php
include 'db_connection.php';

$message = '';
$customer = null;

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM Customer WHERE id_cust = $id");
    $customer = $result->fetch_assoc();
}

if ($_POST && $customer) {
    $customer_name = $_POST['customer_name'];
    
    $stmt = $conn->prepare("UPDATE Customer SET customer_name = ? WHERE id_cust = ?");
    $stmt->bind_param("si", $customer_name, $customer['id_cust']);
    
    if ($stmt->execute()) {
        $message = "Customer berhasil diupdate!";
        $customer['customer_name'] = $customer_name; // Update display
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
    <title>Edit Customer - Renyhijab</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Edit Customer - Renyhijab</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="products.php">Produk</a>
            <a href="transactions.php">Transaksi</a>
            <a href="customers.php">Customer</a>
        </nav>
    </header>
    <main>
        <h2>Edit Customer</h2>
        
        <?php if ($message): ?>
            <div class="alert"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if ($customer): ?>
            <div class="form-container">
                <form method="POST">
                    <div class="form-group">
                        <label>ID Customer:</label>
                        <input type="text" value="<?php echo $customer['id_cust']; ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="customer_name">Nama Customer:</label>
                        <input type="text" id="customer_name" name="customer_name" 
                               value="<?php echo htmlspecialchars($customer['customer_name']); ?>" required>
                    </div>
                    <button type="submit" class="btn">Update Customer</button>
                    <a href="customers.php" class="btn" style="background-color: #6c757d;">Kembali</a>
                </form>
            </div>
        <?php else: ?>
            <p>Customer tidak ditemukan.</p>
            <a href="customers.php" class="btn">Kembali ke Daftar Customer</a>
        <?php endif; ?>
    </main>
</body>
</html>