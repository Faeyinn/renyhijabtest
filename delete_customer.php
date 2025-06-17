<?php
// delete_customer.php
include 'db_connection.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Check if customer has transactions
    $check = $conn->query("SELECT COUNT(*) as count FROM Transaction_Header WHERE id_cust = $id");
    $result = $check->fetch_assoc();
    
    if ($result['count'] > 0) {
        echo "<script>alert('Tidak dapat menghapus customer karena masih memiliki transaksi!'); window.location.href='customers.php';</script>";
    } else {
        $conn->query("DELETE FROM Customer WHERE id_cust = $id");
        echo "<script>alert('Customer berhasil dihapus!'); window.location.href='customers.php';</script>";
    }
}
?>