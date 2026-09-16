<?php
    function getConnection(){
        $conn = oci_connect('system', 'STUDENT', '(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=localhost)(PORT=1522))(CONNECT_DATA=(SERVICE_NAME=XEPDB1)))');
        if(!$conn){
            $e = oci_error();
            die("Conexiune esuata: ". $e['message']);
        }
        return $conn;
    }