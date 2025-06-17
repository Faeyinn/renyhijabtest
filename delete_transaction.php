<?php
include 'db_connection.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // First, get all transaction details to restore stock
        $details_query = $conn->prepare("SELECT id_product, qty FROM Transaction_Detail WHERE id_inv = ?");
        $details_query->bind_param("i", $id);
        $details_query->execute();
        $details_result = $details_query->get_result();
        
        // Restore stock for each product
        while ($detail = $details_result->fetch_assoc()) {
            $restore_stock = $conn->prepare("UPDATE Product SET stok = stok + ? WHERE id_product = ?");
            $restore_stock->bind_param("ii", $detail['qty'], $detail['id_product']);
            $restore_stock->execute();
        }
        
        // Delete transaction details first (foreign key constraint)
        $delete_details = $conn->prepare("DELETE FROM Transaction_Detail WHERE id_inv = ?");
        $delete_details->bind_param("i", $id);
        $delete_details->execute();
        
        // Delete transaction header
        $delete_header = $conn->prepare("DELETE FROM Transaction_Header WHERE id_inv = ?");
        $delete_header->bind_param("i", $id);
        $delete_header->execute();
        
        $conn->commit();
        echo "<script>alert('Transaksi berhasil dihapus dan stok produk telah dikembalikan!'); window.location.href='transactions.php';</script>";
        
    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('Error: Gagal menghapus transaksi - " . addslashes($e->getMessage()) . "'); window.location.href='transactions.php';</script>";
    }
} else {
    echo "<script>alert('ID transaksi tidak valid!'); window.location.href='transactions.php';</script>";
}
?>