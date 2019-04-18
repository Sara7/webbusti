<?php

namespace DataAccess\Config;

use PDO;

class Database {

    private $pdo;
    private $hostname;
    private $username;
    private $password;
    private $dbname;
    private $options;
    private $dsn;
    private $_driver = "mysql";

    // constructor (initialize connection)
    public function __construct($hostname, $username, $password, $dbname) {
        $this->charset = 'utf8mb4';
        $this->hostname = $hostname;
        $this->username = $username;
        $this->password = $password;
        $this->dbname = $dbname;

        // build dns based on driver type (default is mysql)
        $dns = null;
        switch ($this->_driver) {
            case "mysql":
                $this->dsn = "mysql:host=$hostname;dbname=$dbname;charset=$this->charset";
                break;
            default:
                $this->dsn = "mysql:host=$hostname;dbname=$dbname;charset=$this->charset";
        }

        // set pdo options
        $this->options = [
            // PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            // PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // PDO::ATTR_EMULATE_PREPARES => false,
        ];

        $this->connect();
    }

    public function getPdo() {
        return $this->pdo;
    }

    // try to connect to database
    public function connect() {
        try {
            $this->pdo = new PDO($this->dsn, $this->username, $this->password, $this->options);
            return $this->pdo;
        } catch (PDOException $e) {
            echo "PDO connection error";
        }
    }

    // getters
    public function getUsername() {
        return $this->username;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getDbname() {
        return $this->dbname;
    }

    public function getDsn() {
        return $this->dsn;
    }

    public function getOptions() {
        return $this->options;
    }

}
