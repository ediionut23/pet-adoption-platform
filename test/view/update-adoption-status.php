<?php
header('Content-Type: application/json');
require_once '../config/database.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    if (!isset($_POST['form_id']) || !isset($_POST['status'])) {
        throw new Exception('Missing required parameters');
    }

    $form_id = (int)$_POST['form_id'];
    $status = $_POST['status'];

    if (!in_array($status, ['approved', 'rejected'])) {
        throw new Exception('Invalid status');
    }

    $conn = getConnection();
    
    $query = "UPDATE adoption_form SET status = :status WHERE id = :form_id";
    $stmt = oci_parse($conn, $query);
    
    oci_bind_by_name($stmt, ":status", $status);
    oci_bind_by_name($stmt, ":form_id", $form_id);
    
    if (!oci_execute($stmt)) {
        $e = oci_error($stmt);
        throw new Exception($e['message']);
    }

    if ($status === 'approved') {
        $query = "UPDATE pets p 
                 SET p.available_for_adoption = 0 
                 WHERE p.id = (
                     SELECT pet_id 
                     FROM adoption_form 
                     WHERE id = :form_id
                 )";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ":form_id", $form_id);
        
        if (!oci_execute($stmt)) {
            $e = oci_error($stmt);
            throw new Exception($e['message']);
        }
    }

    oci_commit($conn);
    
    echo json_encode([
        'success' => true,
        'message' => 'Status updated successfully'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) {
        oci_free_statement($stmt);
    }
    if (isset($conn)) {
        oci_close($conn);
    }
} 