<?php
include 'db_connection.php';

$message = '';

// Get customers and products for dropdowns
$customers = $conn->query("SELECT * FROM Customer ORDER BY customer_name");
$products = $conn->query("SELECT * FROM Product ORDER BY product_name");

if ($_POST) {
    $id_cust = $_POST['id_cust'];
    $date_inv = $_POST['date_inv'];
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
        $next_id = $row['max_id'] + 1;
        
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // First, check stock availability for all products
            $stock_errors = [];
            foreach ($valid_products as $item) {
                $stock_check = $conn->prepare("SELECT product_name, stok FROM Product WHERE id_product = ?");
                $stock_check->bind_param("i", $item['id']);
                $stock_check->execute();
                $product_info = $stock_check->get_result()->fetch_assoc();
                
                if (!$product_info) {
                    $stock_errors[] = "Produk dengan ID {$item['id']} tidak ditemukan";
                } elseif ($product_info['stok'] < $item['qty']) {
                    $stock_errors[] = "Stok {$product_info['product_name']} tidak mencukupi (tersedia: {$product_info['stok']}, diminta: {$item['qty']})";
                }
            }
            
            // If there are stock errors, throw exception
            if (!empty($stock_errors)) {
                throw new Exception(implode("; ", $stock_errors));
            }
            
            // Insert transaction header
            $stmt = $conn->prepare("INSERT INTO Transaction_Header (id_inv, date_inv, id_cust) VALUES (?, ?, ?)");
            $stmt->bind_param("isi", $next_id, $date_inv, $id_cust);
            $stmt->execute();
            
            // Insert transaction details and update stock
            foreach ($valid_products as $item) {
                // Insert transaction detail
                $stmt_detail = $conn->prepare("INSERT INTO Transaction_Detail (id_inv, id_product, qty) VALUES (?, ?, ?)");
                $stmt_detail->bind_param("iii", $next_id, $item['id'], $item['qty']);
                $stmt_detail->execute();
                
                // Update stock - reduce by quantity sold
                $stmt_stock = $conn->prepare("UPDATE Product SET stok = stok - ? WHERE id_product = ?");
                $stmt_stock->bind_param("ii", $item['qty'], $item['id']);
                $stmt_stock->execute();
                
                // Double check that update was successful and stock didn't go negative
                $verify_stock = $conn->prepare("SELECT stok FROM Product WHERE id_product = ?");
                $verify_stock->bind_param("i", $item['id']);
                $verify_stock->execute();
                $new_stock = $verify_stock->get_result()->fetch_assoc()['stok'];
                
                if ($new_stock < 0) {
                    throw new Exception("Error: Stok menjadi negatif setelah transaksi");
                }
            }
            
            $conn->commit();
            $message = "Transaksi berhasil ditambahkan! Stok produk telah diupdate.";
            
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
                                <?php echo $customer['customer_name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="date_inv">Tanggal:</label>
                    <input type="date" id="date_inv" name="date_inv" 
                           value="<?php echo isset($_POST['date_inv']) ? $_POST['date_inv'] : date('Y-m-d'); ?>" required>
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
                                <?php echo $product['product_name']; ?> 
                                (Rp <?php echo number_format($product['cost'], 0, ',', '.'); ?> - 
                                <span class="stock-info <?php echo $product['stok'] <= 5 ? 'low-stock' : ''; ?>">
                                    Stok: <?php echo $product['stok']; ?>
                                </span>)
                            </span>
                            <input type="number" name="quantities[]" id="qty_<?php echo $index; ?>" 
                                   min="1" max="<?php echo $product['stok']; ?>" 
                                   placeholder="Qty" value="<?php echo $qty_value; ?>"
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
        
        .low-stock {
            color: #EF4444;
            font-weight: bold;
        }
        
        .stock-info {
            font-weight: 500;
        }
    </style>
</body>
</html>