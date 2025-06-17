<?php
include 'db_connection.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Check if category has products
    $check = $conn->query("SELECT COUNT(*) as count FROM Product WHERE id_category = $id");
    $result = $check->fetch_assoc();
    
    if ($result['count'] > 0) {
        echo "<script>alert('Tidak dapat menghapus kategori karena masih memiliki produk!'); window.location.href='categories.php';</script>";
    } else {
        $conn->query("DELETE FROM Category WHERE id_category = $id");
        echo "<script>alert('Kategori berhasil dihapus!'); window.location.href='categories.php';</script>";
    }
}
?>