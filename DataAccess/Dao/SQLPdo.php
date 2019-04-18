<?php

namespace DataAccess\Dao;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class SQLPdo {

    public $pdo;

    //construct a pdo class by passing the instantiated database
    public function __construct($pdo) {
        if ($pdo != null) {
            $this->pdo = $pdo;
        } else {
            die("PDO IS NULL");
        }
    }

    // DESCRIPTION
    // insert a record in a table
    // PARAMETERS
    // tabeName: the table for the insert
    // where: optional key-value array indicating the conditions for which the select must be done
    // RETURNS
    // if successfull returns an associative array having the key with field name and the value with the field value, null otherwise
    public function select($tableName, $where = null, $orderBy = null) {
        try {
            $query = "SELECT * FROM $tableName";

            // set where clause if present
            if ($where != null) {
                $query .= static::buildWhereClause($where);
            }

            if ($orderBy != null) {
                $query .= $this->buildOrderByClause($orderBy);
            }

            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            if (!($stmt->errorInfo()[0] === '00000')) {
                die($stmt->errorInfo()[2]);
            }
            $r = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $r;
        } catch (Exception $e) {
            echo $e;
            return null;
        }
    }

    public function selectJoin($tablesNames, $joinCriteria, $where = null, $orderBy = null) {
        try {
            $query = "";
            for ($i = 0; $i < sizeof($tablesNames); $i++) {
                if ($i == 0) {
                    $query .= $tablesNames[$i];
                    continue;
                }
                $query .= " LEFT JOIN " . $tablesNames[$i];
                $query .= " ON " . $joinCriteria[$i - 1][0] . " = " . $joinCriteria[$i - 1][1];
            }
            $query = "SELECT * FROM " . $query;
            try {

                // set where clause if present
                if ($where != null) {
                    $query .= $this->buildWhereClause($where);
                }

                if ($orderBy != null) {
                    $query .= $this->buildOrderByClause($orderBy);
                }


                $stmt = $this->pdo->prepare($query);

                $stmt->execute();

                return $stmt->fetchAll();
            } catch (Exception $e) {
                echo "Error while parsing where clauses";
            }
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
        if (gettype($what) == "string") {
            $what = json_decode($what, true);
        }
        if (!$what) {
            return false;
        }

        $query = "INSERT INTO $tableName";
        $fieldNames = "";
        $fieldValues = "";

        $what[$tableName . "_id"] = null;
        foreach ($what as $fieldName => $fieldValue) {

            if ($fieldValue == null)
                $fieldValue = 'NULL';
            $fieldNames .= (strlen($fieldNames) == 0 ? "" : ",") . $fieldName;
            $fieldValues .= (strlen($fieldValues) == 0 ? "" : ",") . ($fieldValue == 'NULL' ? 'NULL' : (is_string($fieldValue) ? "'" . $fieldValue . "'" : $fieldValue));
        }
        $query .= " (" . $fieldNames . ") VALUES (" . $fieldValues . ")";

        try {

            $stmt = $this->pdo->prepare($query);
            $queryResult = $stmt->execute();

            if ($queryResult) {
                return $this->pdo->lastInsertId();
            }
            return false;

            //     $lastIdQuery = "SELECT LAST_INSERT_ID() as LAST_INSERT_ID";
            //     $stmt = $this->pdo->prepare($lastIdQuery);
            //     if ($stmt->execute()) {
            //         $res = $stmt->fetchAll()[0];
            //     }
            //     if ($res != null) {
            //         return $res["LAST_INSERT_ID"];
            //     }
            //     return false;
            // }
            // return true;
        } catch (Exception $e) {
            return false;
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
    public function update($tableName, $what, $where = null) {

        // initialize update query for table
        $query = "UPDATE $tableName";

        // initialize set and where strings which will be concatenated to the final query
        $wheres = "";
        $sets = "";

        // set new values
        foreach ($what as $fieldName => $fieldValue) {
            $sets .= (strlen($sets) == 0 ? "" : ",") . $fieldName . "=" . ($fieldValue == null ? 'NULL' : (is_string($fieldValue) ? "'" . $fieldValue . "'" : $fieldValue));
        }

        //build entire query
        $query .= " SET $sets";

        if ($where != null) {
            $query .= $this->buildWhereClause($where);
        }

        // try executing the update
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            return $stmt->rowCount();
        } catch (Exception $e) {
            echo $e;
            return -1;
        }
    }

    // DESCRIPTION
    // delete a row in a table
    // PARAMETERS
    // tabeName: the table to be updated
    // where: key-value array indicating the conditions for which the row must be delete
    // RETURNS
    // the number of row affected
    public function delete($tableName, $where = null) {

        // initialize delete query for table
        $query = "DELETE FROM $tableName";

        // initialize where strings which will be concatenated to the final query
        $wheres = "";

        if ($where != null) {
            $query .= $this->buildWhereClause($where);
        }

        // try executing the update
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            return $stmt->rowCount();
        } catch (Exception $e) {
            echo $e;
            return -1;
        }
    }

    public function customQuery($query) {
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            echo $e;
            return -1;
        }
    }

    // DESCRIPTION
    // build where clause
    // if last char of fieldName id a star it perform a like search
    private static function buildWhereClause($where) {
        $wheres = " WHERE ";
        if ($where != null) {
            foreach ($where as $fieldName => $fieldValue) {
                if ($fieldValue != null) {
                    substr($fieldName, -1) == "*" ? $operator = " LIKE " : $operator = " = ";
                    substr($fieldName, -1) == "|" ? $logicalOperator = " OR " : $logicalOperator = " AND ";
                    if (is_string($fieldValue)) {
                        if ($operator == " LIKE ") {
                            $fieldValue = "%" . $fieldValue . "%";
                        }
                        $fieldValue = "'" . $fieldValue . "'";
                    }
                } else {
                    $operator = " IS ";
                    $fieldValue = "NULL";
                }
                $wheres .= ($wheres == " WHERE " ? "" : $logicalOperator) . ($operator == " LIKE " ? substr($fieldName, 0, -1) : $fieldName) . $operator . $fieldValue;
            }
        }
        return $wheres;
    }

    // DESCRIPTION
    // build orderby clause
    private static function buildOrderByClause($orderBy) {
        $orderByQuery = " ORDER BY ";
        if ($orderBy != null) {
            foreach ($orderBy as $fieldName => $orderType) {
                $comma = $orderByQuery != " ORDER BY " ? ',' : '';
                $orderByQuery .= $comma . $fieldName . " " . $orderType;
            }
        }
        return $orderByQuery;
    }

    // get table by field prefix
    private static function getTableNameFromField($field) {
        return explode("_", $field)[0];
    }

    // starting from a flat array of mixed types such as product, category, media
    // it takes the first entity prefix as the main entity (product)
    // and creates a structured json with the following entities (one entity per prefix, thus category and media)
    // as properties of the main entity (product.category and product.media)
    private function parseJoinedResult($result) {
        $tables = [];
        foreach ($result[0] as $k => $v) {
            $tableName = $this->getTableNameFromField($k);
            if (!array_key_exists($tableName, $tables)) {
                $tables[$tableName] = array();
            }
        }

        $first = "";
        foreach ($result as $record) {
            $last_table_name = $this->getTableNameFromField(key($record));
            $obj = [];
            foreach ($record as $k => $v) {
                $table_name = explode("_", $k)[0];
                if ($table_name != $last_table_name) {
                    $tables[$k][] = $obj;
                    $last_table_name = $table_name;
                }
                $obj[$k] = $v;
            }
        }
    }

    public function beginTransaction() {
        $this->pdo->beginTransaction();
    }

    public function commit() {
        $this->pdo->commit();
    }

    public function rollback() {
        $this->pdo->rollback();
    }

}
