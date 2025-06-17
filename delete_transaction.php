<?php
include 'db_connection.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Get the payment ID from transaction header to delete payment record
        $payment_query = $conn->prepare("SELECT id_payment FROM Transaction_Header WHERE id_inv = ?");
        $payment_query->bind_param("i", $id);
        $payment_query->execute();
        $payment_result = $payment_query->get_result();
        $payment_data = $payment_result->fetch_assoc();
        
        // Delete transaction details first (foreign key constraint)
        $delete_details = $conn->prepare("DELETE FROM Transaction_Detail WHERE id_inv = ?");
        $delete_details->bind_param("i", $id);
        $delete_details->execute();
        
        // Delete transaction header
        $delete_header = $conn->prepare("DELETE FROM Transaction_Header WHERE id_inv = ?");
        $delete_header->bind_param("i", $id);
        $delete_header->execute();
        
        // Delete payment record if exists
        if ($payment_data && $payment_data['id_payment']) {
            $delete_payment = $conn->prepare("DELETE FROM Payment WHERE id_payment = ?");
            $delete_payment->bind_param("i", $payment_data['id_payment']);
            $delete_payment->execute();
        }
        
        $conn->commit();
        echo "<script>alert('Transaksi berhasil dihapus!'); window.location.href='transactions.php';</script>";
        
    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('Error: Gagal menghapus transaksi - " . addslashes($e->getMessage()) . "'); window.location.href='transactions.php';</script>";
    }
} else {
    echo "<script>alert('ID transaksi tidak valid!'); window.location.href='transactions.php';</script>";
}
?>