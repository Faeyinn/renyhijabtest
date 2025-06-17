<?php
include 'db_connection.php';

$message = '';

// Get customers and products for dropdowns
$customers = $conn->query("SELECT * FROM Customer ORDER BY Customer");
$products = $conn->query("SELECT * FROM Product ORDER BY Product");

if ($_POST) {
    $id_cust = $_POST['id_cust'];
    $date_inv = $_POST['date_inv'];
    $method_payment = $_POST['method_payment'];
    $payment_date = $_POST['payment_date'];
    $selected_products = $_POST['products'] ?? [];
    $quantities = $_POST['quantities'] ?? [];
    
    // Validate that we have products selected
    $valid_products = [];
    foreach ($selected_products as $index => $product_id) {
        if (!empty($quantities[$index]) && $quantities[$index] > 0) {
            $valid_products[] = [
                'id' => $product_id,
                'qty' => $quantities[$index]
            ];
        }
    }
    
    if (empty($valid_products)) {
        $message = "Error: Pilih minimal satu produk dengan quantity yang valid!";
    } else {
        // Get next invoice ID
        $result = $conn->query("SELECT MAX(id_inv) as max_id FROM Transaction_Header");
        $row = $result->fetch_assoc();
        $next_inv_id = $row['max_id'] + 1;
        
        // Get next payment ID
        $result = $conn->query("SELECT MAX(id_payment) as max_id FROM Payment");
        $row = $result->fetch_assoc();
        $next_payment_id = $row['max_id'] + 1;
        
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Insert payment record
            $stmt_payment = $conn->prepare("INSERT INTO Payment (id_payment, method_payment, payment_date) VALUES (?, ?, ?)");
            $stmt_payment->bind_param("iss", $next_payment_id, $method_payment, $payment_date);
            $stmt_payment->execute();
            
            // Insert transaction header
            $stmt = $conn->prepare("INSERT INTO Transaction_Header (id_inv, date_inv, id_cust, id_payment) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isii", $next_inv_id, $date_inv, $id_cust, $next_payment_id);
            $stmt->execute();
            
            // Insert transaction details
            foreach ($valid_products as $item) {
                $stmt_detail = $conn->prepare("INSERT INTO Transaction_Detail (id_inv, id_product, Qty) VALUES (?, ?, ?)");
                $stmt_detail->bind_param("iii", $next_inv_id, $item['id'], $item['qty']);
                $stmt_detail->execute();
            }
            
            $conn->commit();
            $message = "Transaksi berhasil ditambahkan!";
            
        } catch (Exception $e) {
            $conn->rollback();
            $message = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Transaksi - Renyhijab</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Tambah Transaksi - Renyhijab</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="products.php">Produk</a>
            <a href="transactions.php">Transaksi</a>
            <a href="customers.php">Customer</a>
            <a href="categories.php">Kategori</a>
        </nav>
    </header>
    <main>
        <h2>Tambah Transaksi Baru</h2>
        
        <?php if ($message): ?>
            <div class="alert <?php echo strpos($message, 'Error:') === 0 ? 'alert-error' : ''; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <div class="form-container">
            <form method="POST">
                <div class="form-group">
                    <label for="id_cust">Customer:</label>
                    <select id="id_cust" name="id_cust" required>
                        <option value="">Pilih Customer</option>
                        <?php while ($customer = $customers->fetch_assoc()): ?>
                            <option value="<?php echo $customer['id_cust']; ?>" 
                                    <?php echo (isset($_POST['id_cust']) && $_POST['id_cust'] == $customer['id_cust']) ? 'selected' : ''; ?>>
                                <?php echo $customer['Customer']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="date_inv">Tanggal Invoice:</label>
                    <input type="date" id="date_inv" name="date_inv" 
                           value="<?php echo isset($_POST['date_inv']) ? $_POST['date_inv'] : date('Y-m-d'); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="method_payment">Metode Pembayaran:</label>
                    <select id="method_payment" name="method_payment" required>
                        <option value="">Pilih Metode</option>
                        <option value="Cash" <?php echo (isset($_POST['method_payment']) && $_POST['method_payment'] == 'Cash') ? 'selected' : ''; ?>>Cash</option>
                        <option value="Transfer" <?php echo (isset($_POST['method_payment']) && $_POST['method_payment'] == 'Transfer') ? 'selected' : ''; ?>>Transfer</option>
                        <option value="Credit Card" <?php echo (isset($_POST['method_payment']) && $_POST['method_payment'] == 'Credit Card') ? 'selected' : ''; ?>>Credit Card</option>
                        <option value="E-Wallet" <?php echo (isset($_POST['method_payment']) && $_POST['method_payment'] == 'E-Wallet') ? 'selected' : ''; ?>>E-Wallet</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="payment_date">Tanggal Pembayaran:</label>
                    <input type="date" id="payment_date" name="payment_date" 
                           value="<?php echo isset($_POST['payment_date']) ? $_POST['payment_date'] : date('Y-m-d'); ?>" required>
                </div>
                
                <h3>Produk</h3>
                <div id="product-list">
                    <?php 
                    $products->data_seek(0); // Reset pointer
                    $index = 0;
                    while ($product = $products->fetch_assoc()): 
                        $is_checked = isset($_POST['products']) && in_array($product['id_product'], $_POST['products']);
                        $qty_value = $is_checked && isset($_POST['quantities'][$index]) ? $_POST['quantities'][$index] : '';
                    ?>
                        <div class="product-item" style="display: flex; align-items: center; margin-bottom: 10px; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                            <input type="checkbox" name="products[]" value="<?php echo $product['id_product']; ?>" 
                                   onchange="toggleQuantity(this, <?php echo $index; ?>)" 
                                   <?php echo $is_checked ? 'checked' : ''; ?>
                                   style="margin-right: 10px;">
                            <span style="flex: 1;">
                                <?php echo $product['Product']; ?> 
                                (Rp <?php echo number_format($product['Cost'], 0, ',', '.'); ?>)
                            </span>
                            <input type="number" name="quantities[]" id="qty_<?php echo $index; ?>" 
                                   min="1" placeholder="Qty" value="<?php echo $qty_value; ?>"
                                   <?php echo !$is_checked ? 'disabled' : ''; ?>
                                   style="width: 80px; margin-left: 10px;">
                        </div>
                    <?php 
                    $index++;
                    endwhile; 
                    ?>
                </div>
                
                <button type="submit" class="btn">Tambah Transaksi</button>
                <a href="transactions.php" class="btn" style="background-color: #6c757d;">Kembali</a>
            </form>
        </div>
    </main>
    
    <script>
        function toggleQuantity(checkbox, index) {
            const qtyInput = document.getElementById('qty_' + index);
            if (checkbox.checked) {
                qtyInput.disabled = false;
                qtyInput.required = true;
            } else {
                qtyInput.disabled = true;
                qtyInput.required = false;
                qtyInput.value = '';
            }
        }
        
        // Initialize quantity inputs on page load
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('input[name="products[]"]');
            checkboxes.forEach((checkbox, index) => {
                toggleQuantity(checkbox, index);
            });
        });
    </script>
    
    <style>
        .alert-error {
            background: linear-gradient(135deg, #EF4444, #DC2626) !important;
            color: white;
        }
    </style>
</body>
</html>