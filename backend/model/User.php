<?php
    require_once __DIR__ . '/../config/database.php';

    class User{
        private $conn;

        public function __construct(){
            $this->conn = getConnection();
        }

        public function createUser($name, $surname, $email, $password){
            $sql = "INSERT INTO users (name, surname, email,password) VALUES (:name, :surname, :email, :password)";
            $stmt = oci_parse($this->conn, $sql);
            oci_bind_by_name($stmt, ':name', $name);
            oci_bind_by_name($stmt, ':surname', $surname);
            oci_bind_by_name($stmt, ':email', $email);
            oci_bind_by_name($stmt, ':password', password_hash($password, PASSWORD_DEFAULT));

            $result = oci_execute($stmt);
            oci_free_statement($stmt);
            return $result;
        }

        public function authenticate($name, $surname, $password){
            $sql = "SELECT password FROM users WHERE name = :name AND surname = :surname";
            $stmt = oci_parse($this->conn, $sql);
            oci_bind_by_name($stmt, ':name', $name);
            oci_bind_by_name($stmt, ':surname', $surname);
            oci_execute($stmt);

            $row = oci_fetch_assoc($stmt);
            oci_free_statement($stmt);

            if($row && password_verify($password, $row['PASSWORD'])){
                return true;
            }
            return false;
        }
    }