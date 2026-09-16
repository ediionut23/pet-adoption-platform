<?php
class Animal {
    public static function getAnimalsByType($type) {
        $conn = getConnection();
        
        $sql = "BEGIN
                    :result := get_animals_by_type(:animal_type);
                END;";
                
        $stmt = oci_parse($conn, $sql);
        
        $result = oci_new_cursor($conn);
        
        oci_bind_by_name($stmt, ":result", $result, -1, SQLT_RSET);
        oci_bind_by_name($stmt, ":animal_type", $type);
        
        oci_execute($stmt);
        oci_execute($result);
        
        $animals = array();
        
        while ($row = oci_fetch_assoc($result)) {
            $animal = array();
            foreach ($row as $key => $value) {
                $animal[strtolower($key)] = $value;
            }
            $animals[] = $animal;
        }
        
        oci_free_statement($stmt);
        oci_free_statement($result);
        oci_close($conn);
        
        return $animals;
    }
    public static function filterAnimals($type = null, $gender = null, $age = null, $size = null) {
        $conn = getConnection();
        
        $sql = "BEGIN
                    :result := filter_animals(:type, :gender, :age, :size);
                END;";
                
        $stmt = oci_parse($conn, $sql);
        
        $result = oci_new_cursor($conn);
        
        oci_bind_by_name($stmt, ":result", $result, -1, SQLT_RSET);
        oci_bind_by_name($stmt, ":type", $type);
        oci_bind_by_name($stmt, ":gender", $gender);
        oci_bind_by_name($stmt, ":age", $age);
        oci_bind_by_name($stmt, ":size", $size);
        
        // Execute the statement
        oci_execute($stmt);
        oci_execute($result);
        
        $animals = array();
        
        while ($row = oci_fetch_assoc($result)) {
            $animal = array();
            foreach ($row as $key => $value) {
                $animal[strtolower($key)] = $value;
            }
            $animals[] = $animal;
        }
        
        oci_free_statement($stmt);
        oci_free_statement($result);
        oci_close($conn);
        
        return $animals;
    }
}
?> 