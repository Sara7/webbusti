<?php

class SQLException extends Exception {
    var $sql;
    var $params;
    var $errorInfo;
    
    public function __construct($message, $sql, $params, $errorInfo) {
        parent::__construct($message, 12345, null);
        $this->sql = $sql;
        $this->params = $params;
        $this->errorInfo = $errorInfo;
    }
}