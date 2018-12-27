<?php

    //include_once(__DIR__."./../Config/Database.php");
    class SQLPdo {

        private $conn;

        //construct a pdo class by passing the instantiated database
        function __construct($database) {
            if($database != null) {
                try {
                    $this->conn = $database->connect();
                } catch (Exception $e) {
                    echo $e;
                }
            } else {
                throw new Exception("Database is null");
            }
        }
        
        // DESCRIPTION
        // insert a record in a table
        // PARAMETERS 
        // tabeName: the table for the insert
        // where: optional key-value array indicating the conditions for which the select must be done
        // RETURNS
        // if successfull returns an associative array having the key with field name and the value with the field value, null otherwise
        public function select($tableName, $where=null, $orderBy=null) {
            try {
                $query  = "SELECT * FROM $tableName";

                // set where clause if present
                if($where != null) {
                    $query .= $this->buildWhereClause($where);
                }

                if($orderBy != null) {
                    $query .= $this->buildOrderByClause($orderBy);
                }
                $stmt = $this->conn->prepare($query);
                $stmt->execute();
                return $stmt->fetchAll();
            } catch (Exception $e) {
                echo $e;
                return null;
            }
        }

        // DESCRIPTION
        // insert a record in a table
        // PARAMETERS 
        // tabeName: the table for the insert
        // what: key-value array describing the field names and field values of the entity
        // RETURNS
        // 0 if the insert has been successfull, -1 otherwise
        public function insert($tableName, $what) {
            $query = "INSERT INTO $tableName";
            $fieldNames    = "";
            $fieldValues   = "";

            foreach($what as $fieldName => $fieldValue) {

                $fieldNames     .= (strlen($fieldNames)  == 0 ? "" : ",") . $fieldName;
                $fieldValues    .= (strlen($fieldValues) == 0 ? "" : ",") . ($fieldValue == null ? 'NULL' : (is_string($fieldValue) ? "'" . $fieldValue . "'" : $fieldValue));
            }
            $query .= " (" . $fieldNames . ") VALUES (" . $fieldValues . ")";
            try {
                $stmt = $this->conn->prepare($query);
                $stmt->execute();
                return 0;
            } catch (Exception $e) {
                echo $e;
                return -1;
            }
        }

        // DESCRIPTION
        // updates a table
        // PARAMETERS 
        // tabeName: the table to be updated
        // what: key-value array indicating which fields have to be updated with which values
        // where: key-value array indicating the conditions for which the update must be done
        // RETURNS
        // the number of row affected
        public function update($tableName, $what, $where=null) {

            // initialize update query for table
            $query  = "UPDATE $tableName";

            // initialize set and where strings which will be concatenated to the final query
            $wheres = "";
            $sets   = "";

            // set new values
            foreach($what as $fieldName => $fieldValue) {
                $sets .= (strlen($sets)  == 0 ? "" : ",") . $fieldName . "=" . ($fieldValue == null ? 'NULL' : (is_string($fieldValue) ? "'" . $fieldValue . "'" : $fieldValue));
            }

            //build entire query
            $query .= " SET $sets";

            if($where != null) {
                $query .= $this->buildWhereClause($where);
            }

            // try executing the update 
            try {
                $stmt = $this->conn->prepare($query);
                $stmt->execute();
                return $stmt->rowCount();
            } catch (Exception $e) {
                echo $e;
                return -1;
            }
        }

        // DESCRIPTION
        // build where clause
        // if last char of fieldName id a star it perform a like search
        public static function buildWhereClause($where) {
            $wheres = " WHERE ";
            if($where != null) {
                foreach($where as $fieldName => $fieldValue) {
                    if($fieldValue != null) {
                        substr($fieldName, -1) == "*" ? $operator = " LIKE " : $operator = " = ";
                        if(is_string($fieldValue)) {
                            if($operator == " LIKE ") {
                                $fieldValue = "%" . $fieldValue . "%";
                            }
                            $fieldValue = "'" . $fieldValue . "'";
                        }
                    } else {
                        $operator = " IS ";
                        $fieldValue = "NULL";
                    }
                    $wheres .= ($wheres == " WHERE " ? "" : ",") . ($operator == " LIKE " ? substr($fieldName, 0, -1) : $fieldName) . $operator . $fieldValue;
                }
            }
            return $wheres;
        }



        // DESCRIPTION
        // build orderby clause
        public static function buildOrderByClause($orderBy) {
            $orderByQuery = " ORDER BY ";
            if($orderBy != null) {
                foreach($orderBy as $fieldName => $orderType) {
                    $comma = $orderByQuery != " ORDER BY " ? ',' : '';
                    $orderByQuery .= $comma . $fieldName . " " . $orderType; 
                }
            }
            return $orderByQuery;
        }

    }
?>