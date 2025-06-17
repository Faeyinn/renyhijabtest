<?php
include 'db_connection.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Check if product has transactions using prepared statement
    $check_stmt = $conn->prepare("SELECT COUNT(*) as count FROM Transaction_Detail WHERE id_product = ?");
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $result = $check_stmt->get_result()->fetch_assoc();
    $check_stmt->close();
    
    if ($result['count'] > 0) {
        echo "<script>alert('Tidak dapat menghapus produk karena sudah ada dalam transaksi!'); window.location.href='products.php';</script>";
    } else {
        $delete_stmt = $conn->prepare("DELETE FROM Product WHERE id_product = ?");
        $delete_stmt->bind_param("i", $id);
        
        if ($delete_stmt->execute()) {
            echo "<script>alert('Produk berhasil dihapus!'); window.location.href='products.php';</script>";
        } else {
            echo "<script>alert('Error: Gagal menghapus produk!'); window.location.href='products.php';</script>";
        }
        $delete_stmt->close();
    }
} else {
    echo "<script>alert('ID produk tidak valid!'); window.location.href='products.php';</script>";
}
?>