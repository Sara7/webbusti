<?php

class DB {
    // Public properties
    /**
     * @var PDO
     */
    public $pdo;                            // Reference to PDO connection

    // Public static properties
    public static $query_count = 0;         // Global counter for queries
    public static $current = null;          // Reference to active connection
    
    // Private properties
    private $cache = [];
    private $db = "";
    private $in_transaction = false;
    private $callbacks = [];
    private $rollback_callbacks = [];

    // Private static properties
    private static $connections = [];       // List of active connections
    
    // Constructor
    /**
     * Instantiate a new connection towards the given database 
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $db
     * @param integer $port
     * @param string $type
     * @param array $init_scripts
     * @param string $connection_name
     * @throws Exception
     */
    public function __construct($host, $user, $password, $db, $port = null, $type = "mysql", $init_scripts = [], $connection_name = "default") {
        // Check connection name existance
        if (isset(self::$connections[$connection_name])) {
            throw new Exception("Unable to create a new database connection called $connection_name: already exists.");
        }

        // Store DB connection
        self::$connections[$connection_name] = $this;

        // Activate first connection
        if (count(self::$connections) === 1) {
            self::$current = $this;
        }

        // Try connection
        switch ($type) {
            case "mysql":
                $dsn = "$type:dbname=$db;host=$host" . (empty($port) ? "" : ";port=$port");
                $options[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES \'UTF8\'';
                break;
            default:
                throw new Exception("Unable to handle '$type' database connection");
        }

        $this->pdo = new PDO($dsn, $user, $password, $options);
        $this->db = $db;

        if (is_array($init_scripts)) {
            foreach ($init_scripts as $init_script) {
                if ($init_script != "") {
                    $this->query($init_script);
                }
            }
        }
    }

    // Magic methods
    public function __call($name, $arguments) {
        trigger_error("Undefined function '$name' in " . $this::getClass());
    }
    
    public function __get($name) {
        trigger_error("Undefined property '$name' in " . $this::getClass());
    }

    // Public methods
    /*
     * Commits executed queries
     */
    public function commit() {
        $ziz = self::getInstance();
        
        $ziz->in_transaction = false;
        
        // Perform commit
        $ziz->pdo->commit();

        foreach ($ziz->callbacks as $cb) {
            call_user_func_array($cb['callback'], $cb['arguments']);
        }
    }

    /*
     * Execute queries and return a resultset, otherwise throw an exception
     */
    public function directQuery($string) {
        $ziz = self::getInstance();
        
        $result = $ziz->pdo->query($string, PDO::FETCH_ASSOC);
        self::$query_count++;
        return $result;
    }

    /*
     * Execute queries and return an associative array containing "column_name" => "value" for each entry
     */
    public function directQueryArray($string) {
        $ziz = self::getInstance();
        $result = $ziz->directQuery($string);
        return $result->fetchAll();
    }

    /*
     * Disable auto commit
     */
    public function disableAutoCommit() {
        $ziz = self::getInstance();
        $ziz->directQuery("SET autocommit=0;");
    }
    
    public function getCache() {
        return $this->cache;
    }

    public function getFieldEnumValues($tablename, $fieldname) {
        $ziz = self::getInstance();
        
        $q = "SHOW COLUMNS FROM $tablename LIKE '$fieldname'";
        $res = $ziz->queryArray($q);
        
        if (empty($res)) {
            throw new Exception("The field '$fieldname' in table '$tablename' doesn't exists.");
        }
        
        if (!startsWith(strtolower($res[0]['type']), "enum")) {
            throw new Exception("The field '$fieldname' in table '$tablename' is not ENUM.");
        }
        
        $matches = [];
        
        if (false === preg_match_all("/'(.*?)'/", $res[0]['type'], $matches)) {
            return [];
        }
        
        return $matches[1];
    }

    /*
     * Returns tables in db, filtering views (or not)
     */
    public function getTables($show_views = false) {
        $ziz = self::getInstance();
        $tables = [];
        
        $t_db = $ziz->queryArray("SHOW FULL TABLES;", []);
        
        foreach ($t_db as $table) {
            if (!$show_views && $table['Table_type'] === "VIEW") {
                continue;
            }
            
            $tables[] = $table['Tables_in_' . $ziz->db];
        }
        
        return $tables;
    }

    public function inTransaction() {
        $ziz = self::getInstance();
        return $ziz->in_transaction;
    }

    public function onCommit($callback, $_args = []) {
        $ziz = self::getInstance();
        $_args = func_get_args();
        array_shift($_args);
        $ziz->callbacks[] = [
            "arguments" => $_args,
            "callback" => $callback
        ];
    }
    
    public function onRollback($callback, $_args = []) {
        $ziz = self::getInstance();
        $_args = func_get_args();
        array_shift($_args);
        $ziz->rollback_callbacks[] = [
            "arguments" => $_args,
            "callback" => $callback
        ];
    }

    /**
     * @param string $string
     * @return PDOStatement
     */
    public function prepare($string) {
        $ziz = self::getInstance();
        return $ziz->pdo->prepare($string);
    }

    /*
     * Execute prepared queries and return a resultset, otherwise throw an exception
     */
    public function query($string, array $data = [], $return_ret = false) {
        $inizio = microtime(true);

        $ziz = self::getInstance();
        
        if($ziz->inTransaction() && preg_match("/^\s*select\s+/is", $string)) {
            $string = preg_replace('/;\s*$/is', "", $string);
            $string .= " FOR UPDATE";
        }

        $md5 = md5($string);
        
        if (!isset($ziz->cache[$md5])) {
            $statement = $ziz->pdo->prepare($string);
            $ziz->cache[$md5] = [
                'statement' => $statement,
                'count' => 1,
                'query' => $string,
                'time' => 0
            ];
        } else {
            $statement = $ziz->cache[$md5]['statement'];
            $ziz->cache[$md5]['count']++;
        }

        $ret = $statement->execute($data);
        
        if (!$ret) {
            $err = $statement->errorInfo();
            $str_data = debug($data, false, false);
            
            throw new SQLException("La seguente query ha generato un errore non previsto: \"" . $string . "\" with data $str_data - " . $err[2], $string, $data, $err);
        }

        $fine = microtime(true);

        $ziz->cache[$md5]['time'] += $fine - $inizio;

        self::$query_count++;
        
        return $return_ret ? $ret : $statement;
    }

    /*
     * Execute prepared queries and return an associative array containing "column_name" => "value" for each entry
     */
    public function queryArray($string, array $data = [], $data_types = null) {
        $ziz = self::getInstance();
        
        $statement = $ziz->query($string, $data, $data_types);
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        $arr = $statement->fetchAll();
        $statement->closeCursor();
        
        return $arr;
    }

    /*
     * Execute prepared queries and return the last inserted id
     */
    public function queryLastId($string, array $data = [], $data_types = null) {
        $ziz = self::getInstance();
        $ziz->query($string, $data, $data_types);
        $id = $ziz->pdo->lastInsertId();
        
        return $id;
    }

    /*
     * Rollbacks executed queries
     */
    public function rollback() {
        $ziz = self::getInstance();
        
        if (!$ziz->in_transaction) {
            return;
        }
        
        $ziz->in_transaction = false;
        
        // Perform rollback
        $ziz->pdo->rollback();
        
        foreach ($ziz->rollback_callbacks as $cb) {
            call_user_func_array($cb['callback'], $cb['arguments']);
        }
        
        $ziz->callbacks = [];
        $ziz->rollback_callbacks = [];
    }

    public function startTransaction() {
        $ziz = self::getInstance();
        
        if ($ziz->in_transaction) {
            return;
        }
        
        $ziz->in_transaction = true;
        $ziz->pdo->beginTransaction();
        $ziz->callbacks = [];
        $ziz->rollback_callbacks = [];
    }
    
    // Public static methods
    /**
     * Retrieve the right reference to the (current, called statically, or the $this pointer, called non-statically)
     * @return DB
     */
    public static function getInstance($name = null) {
        $bt = debug_backtrace();
        
        if (!empty($bt[1]['object']) && is_a($bt[1]['object'], get_class())) {
            return $bt[1]['object'];
        }

        // Look for a specific database
        if (is_string($name)) {
            if (array_key_exists($name, self::$connections) && self::$connections[$name] instanceof DB) {
                return self::$connections[$name];
            }
        }

        if (is_null($instance = self::$current)) {
            throw new Exception("Current DB not set");
        }

        return $instance;
    }

    public static function likeStatement($field, $value, $type, $add_and = true) {
        $ziz = self::getInstance();
        $not = "";
        
        switch ($type) {
            case "nbw":
            case "nsw":
                $not = " NOT";
            case "bw":
            case "sw":
                $like = "LIKE '" . substr(substr($ziz->pdo->quote($value), 0, -1), 1) . "%'";
                break;
            case "nc":
                $not = " NOT";
            case "c":
                $like = "LIKE '%" . substr(substr($ziz->pdo->quote($value), 0, -1), 1) . "%'";
                break;
            case "new":
                $not = " NOT";
            case "ew":
                $like = "LIKE '%" . substr(substr($ziz->pdo->quote($value), 0, -1), 1) . "'";
                break;
            case 'nsl':
                $not = " NOT";
            case 'sl':
                $like = "SOUNDS LIKE " . $ziz->pdo->quote($value);
                break;
            case 'nre':
                $not = " NOT";
            case 're':
                $like = "REGEXP " . $ziz->pdo->quote($value);
                break;
        }
        return ($add_and ? " AND" : "") . "$not ($field $like)";
    }

    /*
     * Sets active connection by object or connection name
     */
    public static function setCurrent($db) {
        // Set connection by object
        if (is_a($db, "DB")) {
            self::$current = $db;
        } elseif (is_string($db)) {
            // Set connection by connection name
            // If the given connection exists, activate it
            if (!empty(DB::$connections[$db_name])) {
                DB::$current = DB::$connections[$db_name];
            } else {
                throw new Exception("Unable to activate the '$db_name' connection.");
            }
        } else {
            throw new Exception("Incorrect parameter given to set the current connection.");
        }
    }
}
