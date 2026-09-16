<?php
function getConnection() {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    try {
        $tns_admin = getenv('TNS_ADMIN') ?: __DIR__ . '/../wallet';
        putenv("TNS_ADMIN=" . $tns_admin);

        $username = getenv('DB_USERNAME') ?: 'ADMIN';
        $password = getenv('DB_PASSWORD') ?: 'YOUR_DB_PASSWORD';
        $connection_string = getenv('DB_TNS') ?: 'pow_high';

        $conn = @oci_pconnect($username, $password, $connection_string);

        if (!$conn) {
            $e = oci_error();
            error_log("Database Connection Error: " . json_encode($e));
            throw new Exception("Could not connect to database. Please check configuration.");
        }

        return $conn;

    } catch (Exception $e) {
        error_log("Database Connection Error: " . $e->getMessage());
        throw new Exception("Could not connect to database. Please check configuration.");
    }
}
?>
