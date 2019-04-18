<?php

class DB {

    public static $query_count = 0;              // Global counter for queries
    public static $current = null;              // Reference to active connection
    private static $connections = array();      // List of active connections

    /**
     * @var PDO
     */
    public $pdo;                               // Reference to PDO connection
    /** @var MongoClient */
    public $mongo_client;

    /** @var MongoDB */
    public $mongo_db;
    private $cache = array();
    private $db = "";
    private $in_transaction = false;
    private $callbacks = array();
    private $rollback_callbacks = array();

    /**
     * Instantiate a new connection towards the given database 
     * @param type $host
     * @param type $user
     * @param type $password
     * @param type $db
     * @param type $port
     * @param type $type
     * @param type $init_scripts
     * @param type $connection_name
     * @throws Exception
     */
    public function __construct($host, $user, $password, $db, $port = null, $type = "mysql", $init_scripts = array(), $connection_name = "default", $auth_source = NULL) {
        // Check connection name existance
        if (isset(self::$connections[$connection_name]))
            throw new Exception("Unable to create a new database connection called $connection_name: already exists.");

        // Store DB connection
        self::$connections[$connection_name] = $this;

        // Activate first connection
        if (count(self::$connections) === 1)
            self::$current = $this;

        $use_mongodb = false;

        // Try connection
        switch ($type) {
            case "mongodb":
                $dsn = "$type://" . (!empty($user) ? ($user . (!empty($password) ? ":" . $password : "") . "@") : "") . "$host" . (empty($port) ? "" : ":$port") . "/$db" . (!empty($auth_source) ? "?authSource=$auth_source" : "");
                $use_mongodb = true;
                break;
            case "mysql":
                $dsn = "$type:dbname=$db;host=$host" . (empty($port) ? "" : ";port=$port");
                $options[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES \'UTF8\'';
                break;
            default:
                throw new Exception("Unable to handle '$type' database connection");
        }

        if ($use_mongodb) {
//            $this->mongo_client = new MongoClient($dsn);
//            $this->mongo_db = new MongoDB($this->mongo_client, $db);
            
            $this->mongo_client = new MongoDB\Client($dsn);
            $this->mongo_db = $this->mongo_client->$db;
        } else {
            $this->pdo = new PDO($dsn, $user, $password, $options);
        }

        $this->db = $db;

        if (is_array($init_scripts)) {
            foreach ($init_scripts as $init_script) {
                if ($init_script != "") {
                    $this->query($init_script);
                }
            }
        }
    }

    public function __destruct() {
        //foreach($this->cache as $st)
        //    $st['statement']->free();
    }

    public function getCache() {
        return $this->cache;
    }

    /**
     * Retrieve the right reference to the (current, called statically, or the $this pointer, called non-statically)
     * @return DB
     */
    public static function getInstance($name = null) {
        $bt = debug_backtrace();
        if (!empty($bt[1]['object']) && is_a($bt[1]['object'], get_class()))
            return $bt[1]['object'];

        // Look for a specific database
        if (is_string($name)) {
            if (array_key_exists($name, self::$connections) && self::$connections[$name] instanceof DB)
                return self::$connections[$name];
        }

        if (is_null($instance = self::$current))
            throw new Exception("Current DB not set");

        return $instance;
    }

    /*
     * Sets active connection by object or connection name
     */

    public static function setCurrent($db) {
        // Set connection by object
        if (is_a($db, "DB"))
            self::$current = $db;
        // Set connection by connection name
        elseif (is_string($db)) {
            // If the given connection exists, activate it
            if (!empty(DB::$connections[$db_name]))
                DB::$current = DB::$connections[$db_name];
            else
                throw new Exception("Unable to activate the '$db_name' connection.");
        } else
            throw new Exception("Incorrect parameter given to set the current connection.");
    }

    /*
     * Execute queries and return a resultset, otherwise throw an exception
     */

    public function directQuery($string) {
        $ziz = self::getInstance();
        if (!empty($ziz->mongo_db)) {
            throw new Exception("TBI");
        } else {
            $result = $ziz->pdo->query($string, PDO::FETCH_ASSOC);
            self::$query_count++;
            return $result;
        }
    }

    /*
     * Execute queries and return an associative array containing "column_name" => "value" for each entry
     */

    public function directQueryArray($string) {
        $ziz = self::getInstance();
        $result = $ziz->directQuery($string);
        return $result->fetchAll();
    }

    /**
     * 
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

    public function query($string, array $data = array(), $data_types = null, $return_ret = false) {
        $inizio = microtime(true);

        $ziz = self::getInstance();
        
        if($ziz->inTransaction() && preg_match("/^\s*select\s+/is", $string)) {
            $string = preg_replace('/;\s*$/is', "", $string);
            $string .= " FOR UPDATE";
        }

        if (!empty($ziz->mongo_db)) {
            throw new Exception("TBI");
        } else {
            $md5 = md5($string);
            if (!isset($ziz->cache[$md5])) {
                $statement = $ziz->pdo->prepare($string);
                $ziz->cache[$md5] = array('statement' => $statement, 'count' => 1, 'query' => $string, 'time' => 0);
            } else {
                $statement = $ziz->cache[$md5]['statement'];
                $ziz->cache[$md5]['count'] ++;
            }

            $ret = $statement->execute($data);
            if (!$ret) {
                $err = $statement->errorInfo();
                $str_data = debug($data, false, false);
                throw new SQLException("La seguente query ha generato un errore non previsto: \"" . $string . "\" with data $str_data - " . $err[2], $string, $data, $err);
                //throw new Exception("La seguente query ha generato un errore non previsto: \"".$string."\" with data $str_data - ".$err[2]);
            }

            $fine = microtime(true);

            $ziz->cache[$md5]['time'] += $fine - $inizio;

            self::$query_count++;
            if($return_ret) {
                return $ret;
            } else {
                return $statement;
            }
        }
    }

    /*
     * Execute prepared queries and return an associative array containing "column_name" => "value" for each entry
     */

    public function queryArray($string, array $data = array(), $data_types = NULL) {
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

    public function queryLastId($string, array $data = array(), $data_types = NULL) {
        $ziz = self::getInstance();
        $ziz->query($string, $data, $data_types);
        $id = $ziz->pdo->lastInsertId();
        return $id;
    }

    /*
     * Disable auto commit
     */

    public function disableAutoCommit() {
        $ziz = self::getInstance();
        $ziz->directQuery("SET autocommit=0;");
    }

    public function onCommit($callback, $_args = []) {
        $ziz = self::getInstance();
        $_args = func_get_args();
        array_shift($_args);
        $ziz->callbacks[] = array("callback" => $callback, "arguments" => $_args);
    }
    
    public function onRollback($callback, $_args = []) {
        $ziz = self::getInstance();
        $_args = func_get_args();
        array_shift($_args);
        $ziz->rollback_callbacks[] = array("callback" => $callback, "arguments" => $_args);
    }

    /*
     * Commits executed queries
     */

    public function commit() {
        $ziz = self::getInstance();
        
        if (!empty($ziz->mongo_db))
            throw new Exception("TBI");
        
        $ziz->in_transaction = false;
        // Perform commit
        $ziz->pdo->commit();

        foreach ($ziz->callbacks as $cb)
            call_user_func_array($cb['callback'], $cb['arguments']);
    }

    /*
     * Rollbacks executed queries
     */

    public function rollback() {
        $ziz = self::getInstance();
        
        if (!empty($ziz->mongo_db))
            throw new Exception("TBI");
        
        if (!$ziz->in_transaction)
            return;
        
        $ziz->in_transaction = false;
        // Perform rollback
        $ziz->pdo->rollback();
        
        foreach ($ziz->rollback_callbacks as $cb)
            call_user_func_array($cb['callback'], $cb['arguments']);
        
        $ziz->callbacks = array();
        $ziz->rollback_callbacks = array();
    }

    public function inTransaction() {
        $ziz = self::getInstance();
        return $ziz->in_transaction;
    }

    /**
     * Starts a new transaction, passing a string as a savepoint identifier
     * @param string $savepoint Name of a savepoint to set
     * @throws Exception 
     */
    public function startTransaction() {
        $ziz = self::getInstance();
        if ($ziz->in_transaction)
            return;
        $ziz->in_transaction = true;
        $ziz->pdo->beginTransaction();
        $ziz->callbacks = array();
        $ziz->rollback_callbacks = array();
    }

    /*
     * Returns tables in db, filtering views (or not)
     */

    public function getTables($show_views = false) {
        $ziz = self::getInstance();
        
        if (!empty($ziz->mongo_db))
            throw new Exception("TBI");
        
        $tables = array();
        $t_db = $ziz->queryArray("SHOW FULL TABLES;", array());
        foreach ($t_db as $table) {
            if (!$show_views && $table['Table_type'] === "VIEW")
                continue;
            $tables[] = $table['Tables_in_' . $ziz->db];
        }
        return $tables;
    }

    public function getFieldEnumValues($tablename, $fieldname) {
        $ziz = self::getInstance();
        
        if (!empty($ziz->mongo_db))
            throw new Exception("TBI");
        
        $q = "SHOW COLUMNS FROM $tablename LIKE '$fieldname'";
        $res = $ziz->queryArray($q);
        if (empty($res))
            throw new Exception("The field '$fieldname' in table '$tablename' doesn't exists.");
        if (!startsWith(strtolower($res[0]['type']), "enum"))
            throw new Exception("The field '$fieldname' in table '$tablename' is not ENUM.");
        $matches = array();
        if (false === preg_match_all("/'(.*?)'/", $res[0]['type'], $matches))
            return array();
        return $matches[1];
    }

    public static function likeStatement($field, $value, $type, $add_and = true) {
        $ziz = self::getInstance();
        
        if (!empty($ziz->mongo_db))
            throw new Exception("TBI");
        
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

    public function __call($name, $arguments) {
        if (!empty($this->mongo_db)) {
            return call_user_func_array($this->mongo_db->$name, $arguments);
        } else {
            trigger_error("Undefined function '$name' in " . $this::getClass());
        }
    }
    
    public function __get($name) {
        if (!empty($this->mongo_db)) {
            return $this->mongo_db->$name;
        } else {
            trigger_error("Undefined property '$name' in " . $this::getClass());
        }
    }
}
