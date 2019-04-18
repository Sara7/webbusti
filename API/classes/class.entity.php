<?php

abstract class Entity implements JsonSerializable {
    // Public properties
    public static $debug = false;
    
    // Abstract methods
    abstract public static function _reset();

    abstract public static function search($v, $count);

    // Public methods
    public function __construct ($id = 0, $data = null) {
        $this::$instances[$id] = $this;
        $this->update($id, $data);
        
        if (isset($this->identifier)) {
            $this::$instances[$this->identifier] = $this;
        }
    }

    public function buildProperties($prefix, $data) {
        if (!empty($data)) {
            $keys = array_keys($data);
            
            if (is_numeric(collectionGetValue($keys, 0))) {
                $data = collectionGetArray($data, 0);
            }
        }

        foreach ($data as $key => $value) {
            $keyname = startsWith($key, $prefix) ? substr($key, strlen($prefix)) : $key;
            $this->$keyname = $value;
        }
        
        $idkey = $prefix . "id";
        $this->$idkey = $this->id;
    }
    
    public function jsonSerialize() {
        $obj = (array)$this;
        
        return $obj;
    }

    public function update($id = 0, $data = false, $deep = false, &$updated = []) {
        // Object name, avoids the infinite loop
        $key = $this::OBJECT_NAME . ":" . !empty($id) ? $id : $this->id;
        
        if (isset($updated[$key])) {
            return;
        }

        // Mark this object as "updated"
        $updated[$key] = true;

        if (!empty($data)) {
            $keys = array_keys($data);
            
            if (is_numeric(collectionGetValue($keys, 0))) {
                $data = collectionGetValue($data, 0);
            }
        }

        // Update this object
        foreach ($this as $property => $value) {
            if ($property != 'id') {
                if ($deep) {
                    if (is_object($value)) {
                        $value->update(null, false, $deep, $updated);
                    } elseif (is_array($value)) {
                        foreach ($value as $item) {
                            if (is_object($item)) {
                                $item->update(null, false, $deep, $updated);
                            }
                        }
                    }
                }
                
                unset($this->$property);
            }
        }
        
        if ($data === false || empty($data)) {
            if ($id == 0 && !empty($this->id)) {
                $id = $this->id;
            }
            
            $alias = $this::TABLE_ALIAS;
            $table_name = $this::TABLE_NAME;
            $prefix_us = $this::FIELD_PREFIX_US;
            $key_field = $this::KEY_FIELD;
            
            $sql = "SELECT {$alias}.* FROM $table_name as $alias WHERE {$prefix_us}{$key_field} = ? LIMIT 1";
            $params = [$id];
            $data = DB::queryArray($sql, $params);
            
            if (count($data)) {
                $data = collectionGetValue($data, 0);
            } else {
                throw new Exception("Unable to find " . $this::OBJECT_NAME . " with id $id.");
            }
        }

        $this->buildProperties($this::FIELD_PREFIX_US, $data);
    }
    
    // Public static methods
    public static function exists($class, array $ids, $limit = false) {
        $alias = $class::TABLE_ALIAS;
        $table_name = $class::TABLE_NAME;
        
        $params = [];
        
        $sql = "SELECT {$alias}.* FROM $table_name AS $alias WHERE 1";
        
        foreach ($ids as $key => $value) {
            $sql .= " AND {$alias}.{$key} = ?";
            $params[] = $value;
        }
        
        if ($limit) {
            $sql.= " LIMIT 1";
        }

        $res = DB::queryArray($sql, $params);
        
        return (count($res) > 0) ? $res : false;
    }

    /**
     * Try to find the required object by DB keys in the array of the instances
     * 
     * @param string $class The class name
     * @param array $ids The array of DB keys
     * @param array $data [optional] The data to fill the returned object
     * @return Entity The instance of the required object or null if not found
     */
    public static function findClassInstance($class, array $ids, $data = null) {
        if (empty($ids)) {
            return null;
        }

        // look for the specified instance
        $key = implode("_", $ids);
        
        if (isset($class::$instances[$key])) {
            return $class::$instances[$key];
        }

        // check if the instance exists
        $entity = $class::exists($ids);
        
        if (!empty($data) || $entity !== false) {
            // if the array of ids contains just one key, pass the scalar value
            if (count($ids) == 1) {
                $key = collectionGetValue(array_keys($ids), 0);
                $obj = new $class($ids[$key], is_null($data) ? $entity : $data);
                return $obj;
            }
            
            // if the array of ids contains more than one key, pass the array
            $obj = new $class($ids, is_null($data) ? $entity : $data);
            return $obj;
        } else {
            return null;
        }
    }

    /**
     * Return array with names of the enums elements inside $table for attribute $field
     * 
     * @param string $table The table name
     * @param string $field The field of type enum that must be parsed
     */
    public static function getEnums($table, $field) {
        $sql = "SHOW COLUMNS FROM $table WHERE field = ?";
        $ret = DB::queryArray($sql, [$field]);
        
        if (empty($ret)) {
            return [];
        }
        
        $enums = collectionGetValue($ret, "0,Type");
        
        if (!startsWith($enums, "enum(")) {
            return [];
        }
        
        $str = substr($enums, strpos($enums, "(") + 1, -1);

        $str = str_replace("\"", "@#double-quotes#@", $str);
        $str = str_replace("\\'", "@#single-quotes#@", $str);
        $str = str_replace("'", "\"", $str);
        $str = str_replace("@#single-quotes#@", "'", $str);
        $str = str_replace("@#double-quotes#@", "\\\"", $str);

        return json_decode("[" . $str . "]");
    }

    public static function reset() {
        foreach (get_declared_classes() as $class) {
            if (is_subclass_of($class, get_class()) && $class != get_class()) {
                $class::_reset();
            }
        }
    }
    
    // Protected static methods
    protected static function _search($class, array $criteria, $count, array $default_sorting, array $numeric_fields, array $string_fields, $custom_fields = [], $custom_params = [], $custom_join = null, $custom_select = null) {
        $custom_fields_question_marks = 0;
        
        foreach ($custom_fields as $cf) {
            $custom_fields_question_marks += substr_count($cf, "?");
        }
        
        if ($custom_fields_question_marks != count($custom_params)) {
            throw new Exception(count($custom_params) . " custom parameters passed, $custom_fields_question_marks expected");
        }

        $prefix_us = $class::FIELD_PREFIX_US;
        $table = $class::TABLE_NAME;
        $table_alias = $class::TABLE_ALIAS;
        $key_field = $class::KEY_FIELD;

        $params = [];
        
        if ($count) {
            $sql = "SELECT COUNT(DISTINCT {$table_alias}.{$prefix_us}{$key_field}) as cnt";
        } else {
            $sql = "SELECT " . (empty($custom_select) ? "{$table_alias}.*" : $custom_select);
        }

        $sql .= " FROM " . (empty($custom_join) ? "$table $table_alias" : $custom_join) . "
WHERE 1";

        // Equal_fields
        foreach (array_merge([
            $key_field
        ], $numeric_fields) as $field) {
            $table_alias_dotted = $table_alias . ".";

            foreach ([
                ["<", "lt"],
                ["<=", "lte"],
                ["=", ""],
                [">", "gt"],
                [">=", "gte"],
                ["<>", "ne"]
            ] as $comparator) {
                $operand_symbol = $comparator[0];
                $operand_acronym = $comparator[1];
                $suffix = empty($operand_acronym) ? "" : "_" . $operand_acronym;
                
                $key = $prefix_us . $field . $suffix;
                $table_field = $table_alias_dotted . $prefix_us . $field;

                if (array_key_exists($key, $criteria)) {
                    if (is_null($criteria[$key])) {
                        if ($operand_acronym == "") {
                            $sql .= " AND $table_field IS NULL";
                        }
                        
                        if ($operand_acronym == "ne") {
                            $sql .= " AND $table_field IS NOT NULL";
                        }
                    } else {
                        if (is_array($criteria[$key])) {
                            foreach ($criteria[$key] as $val) {
                                $sql .= " AND $table_field $operand_symbol ?";
                                $params[] = $val;
                            }
                        } else {
                            $sql .= " AND $table_field $operand_symbol ?";
                            $params[] = $criteria[$key];
                        }
                    }
                }
            }
        }

        foreach (array_merge([
            $key_field
        ], $string_fields) as $field) {
            $table_alias_dotted = ""; //$table_alias . ".";
            
            foreach ([
                "",
                "ne",
                "c",
                "nc",
                "bw",
                "sw",
                "nbw",
                "nsw",
                "ew",
                "new",
                "sl",
                "nsl",
                "re",
                "nre",
                "in",
                "nin"
            ] as $suffix) {
                $suffix = empty($suffix) ? "" : "_" . $suffix;
                
                $key = $prefix_us . $field . $suffix;
                $table_field = $table_alias_dotted . $prefix_us . $field;
                
                switch ($suffix) {
                    case "":
                        if (array_key_exists($key, $criteria)) {
                            if (is_null($criteria[$key])) {
                                $sql .= " AND $table_field IS NULL";
                            } else {
                                $sql .= " AND $table_field = ?";
                                $params[] = $criteria[$key];
                            }
                        }
                        break;
                    case "ne":
                        if (array_key_exists($key, $criteria)) {
                            if (is_null($criteria[$key])) {
                                $sql .= " AND $table_field IS NOT NULL";
                            } else {
                                $sql .= " AND $table_field <> ?";
                                $params[] = $criteria[$key];
                            }
                        }
                        break;
                    case "in":
                        if (array_key_exists($key, $criteria)) {
                            $arr = collectionGetArray($criteria, $key);
                            
                            if (is_array($arr) && count($arr)) {
                                $question_marks = [];
                                
                                foreach ($arr as $va) {
                                    $question_marks[] = "?";
                                    
                                    if (is_numeric($va)) {
                                        $vi = intval($va);
                                        $vf = floatValue($va);
                                        
                                        $params[] = $vi == $vf ? $vi : $vf;
                                    } else {
                                        $params[] = $va;
                                    }
                                }
                                
                                $sql .= " AND $table_field IN (" . implode(", ", $question_marks) . ")";
                            } else {
                                $sql .= " AND 1 = 0";
                            }
                        }
                        break;
                    case "nin":
                        if (array_key_exists($key, $criteria)) {
                            $arr = collectionGetArray($criteria, $key);
                            
                            if (is_array($arr) && count($arr)) {
                                $question_marks = [];
                                
                                foreach ($arr as $va) {
                                    $question_marks[] = "?";
                                    
                                    if (is_numeric($va)) {
                                        $vi = intval($va);
                                        $vf = floatValue($va);
                                        
                                        $params[] = $vi == $vf ? $vi : $vf;
                                    } else {
                                        $params[] = $va;
                                    }
                                }
                                
                                $sql .= " AND $table_field NOT IN (" . implode(", ", $question_marks) . ")";
                            }
                        }
                        break;
                    default:
                        if (array_key_exists($key, $criteria)) {
                            $sql .= " AND " . DB::likeStatement($table_field, $criteria[$key], $suffix, false);
                        }
                }
            }
        }

        foreach ($custom_fields as $cf) {
            $sql .= " AND ($cf)";
        }
        
        $params = array_merge($params, $custom_params);

        if (!$count) {
            $sortings = empty(collectionGetArray($criteria, "sorting")) ? $default_sorting : collectionGetArray($criteria, "sorting");
            $sorting = "";
            
            foreach ($sortings as $sortfield) {
                $sfield = $sortfield[0];
                $sorder = $sortfield[1];
                $table_alias_dotted = $table_alias . ".";
                $table_field = $table_alias_dotted . $prefix_us . $sfield;
                
                $sorting .= (empty($sorting) ? "" : ", ") . "$table_field $sorder";
            }
            
            $sorting = " ORDER BY " . $sorting;
            $sql .= $sorting;
            
            if (array_key_exists("l_limit", $criteria) && array_key_exists("u_limit", $criteria)) {
                $sql .= " LIMIT {$criteria['l_limit']}, {$criteria['u_limit']}";
            } elseif (array_key_exists("limit", $criteria)) {
                $sql .= " LIMIT {$criteria['limit']}";
            }
        }
        
        if (static::$debug) {
            die(debugSql($sql, $params));
        }
        
        $x = DB::queryArray($sql, $params);
        
        if ($count) {
            return intval(collectionGetValue($x, "0,cnt"));
        }
        
        $arr = [];
        
        for ($i = 0; $i < count($x); $i++) {
            $k = $prefix_us . $key_field;
            $elem = collectionGetValue($x, $i);
            
            $arr[] = $class::findInstance(collectionGetValue($elem, $k), $elem);
        }
        
        return $arr;
    }
}
