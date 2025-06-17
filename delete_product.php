<?php
// delete_product.php (create separate file)
include 'db_connection.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Check if product has transactions
    $check = $conn->query("SELECT COUNT(*) as count FROM Transaction_Detail WHERE id_product = $id");
    $result = $check->fetch_assoc();
    
    if ($result['count'] > 0) {
        echo "<script>alert('Tidak dapat menghapus produk karena sudah ada dalam transaksi!'); window.location.href='products.php';</script>";
    } else {
        $conn->query("DELETE FROM Product WHERE id_product = $id");
        echo "<script>alert('Produk berhasil dihapus!'); window.location.href='products.php';</script>";
    }
}
?>