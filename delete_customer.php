<?php
include 'db_connection.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Check if customer has transactions using prepared statement
    $check_stmt = $conn->prepare("SELECT COUNT(*) as count FROM Transaction_Header WHERE id_cust = ?");
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $result = $check_stmt->get_result()->fetch_assoc();
    $check_stmt->close();
    
    if ($result['count'] > 0) {
        echo "<script>alert('Tidak dapat menghapus customer karena masih memiliki transaksi!'); window.location.href='customers.php';</script>";
    } else {
        $delete_stmt = $conn->prepare("DELETE FROM Customer WHERE id_cust = ?");
        $delete_stmt->bind_param("i", $id);
        
        if ($delete_stmt->execute()) {
            echo "<script>alert('Customer berhasil dihapus!'); window.location.href='customers.php';</script>";
        } else {
            echo "<script>alert('Error: Gagal menghapus customer!'); window.location.href='customers.php';</script>";
        }
        $delete_stmt->close();
    }
} else {
    echo "<script>alert('ID customer tidak valid!'); window.location.href='customers.php';</script>";
}
?>