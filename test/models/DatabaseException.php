<?php

class DatabaseException extends Exception {
    private $sqlCode;
    
    public function __construct($message, $code = 0, $sqlCode = null) {
        parent::__construct($message, $code);
        $this->sqlCode = $sqlCode;
    }
    
    public function getSqlCode() {
        return $this->sqlCode;
    }
} 