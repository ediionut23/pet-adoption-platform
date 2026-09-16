<?php
require_once '../config/database.php';

class AdoptionRequest {
    private $conn;

    public function __construct() {
        $this->conn = getConnection();
    }

    public function getAdoptionRequests($userId) {
        try {
            $cursor = oci_new_cursor($this->conn);
            
            $stmt = oci_parse($this->conn, "BEGIN :result := get_adoption_requests(:user_id); END;");
            
            oci_bind_by_name($stmt, ":result", $cursor, -1, SQLT_RSET);
            oci_bind_by_name($stmt, ":user_id", $userId);
            
            oci_execute($stmt);
            oci_execute($cursor);

            $requests = [];
            while ($row = oci_fetch_assoc($cursor)) {
                $requests[] = $row;
            }

            oci_free_statement($cursor);
            return $requests;

        } catch (Exception $e) {
            throw new Exception("Error fetching adoption requests: " . $e->getMessage());
        }
    }

    public function __destruct() {
        if ($this->conn) {
            oci_close($this->conn);
        }
    }
} 