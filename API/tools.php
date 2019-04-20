<?php

function classAutoLoad($classname) {
    $classname = strtolower($classname);
    $parts = explode("_", $classname);
    $class = array_pop($parts);
    $directory = realpath(dirname(__FILE__) . "/../classes");
    $inner_path = implode("/", $parts);

    if ($inner_path != "") {
        $inner_path .= "/";
    }

    $full_path = realpath($directory . "/" . $inner_path . "class.$class.php");

    if (is_file($full_path)) {
        require_once($full_path);
    }
}

/**
 * Return the value of the given key in the given collection or [].
 * 
 * @param array|object $collection
 * @param string $key nested keys are allowed separe keys by comma
 * @return []
 */
function collectionGetArray($collection, $key) {
    return collectionGetSubItem($collection, $key, []);
}

/**
 * Return the value of the given key in the given collection or default.
 * 
 * @param array|object $collection
 * @param string $key nested keys are allowed separe keys by comma
 * @param mixed $default [optional; defaults to NULL]
 * @return mixed
 */
function collectionGetSubItem(&$collection, $key, $default = NULL) {
    $parts = explode(",", $key);
    $vett = &$collection;
    
    for ($p = 0; $p < count($parts); $p++) {
        if (is_object($vett)) {
            $member_name = $parts[$p];
            $func = method_exists($vett, $member_name);
            $retval = ($func ? @$vett->$member_name() : @$vett->$member_name) or NULL;
            
            if (is_null($retval)) {
                return $default;
            }
            
            if ($p == count($parts) - 1) {
                return $func ? $vett->$member_name() : $vett->$member_name;
            } else {
                $vett =& $func ? $vett->$member_name() : $vett->$member_name;
            }
        } elseif (is_array($vett) && array_key_exists($parts[$p], $vett)) {
            if ($p == count($parts) - 1) {
                return $vett[$parts[$p]];
            } else {
                $vett = &$vett[$parts[$p]];
            }
        } else {
            return $default;
        }
    }
}

/**
 * Return the value of the given key in the given collection or default.
 * 
 * @param array|object $collection
 * @param string $key nested keys are allowed separe keys by comma
 * @param mixed $default [optional; defaults to ""]
 * @return mixed
 */
function collectionGetValue($collection, $key, $default = "") {
    return collectionGetSubItem($collection, $key, $default);
}

function debug($data, $pre = true, $output = true) {
    if (!$output) {
        ob_start();
    }
    
    if ($pre) {
        echo "<pre>";
    }
    
    if (is_null($data)) {
        echo "NULL";
    } else {
        print_r($data);
    }
    
    if ($pre) {
        echo "</pre>";
    }
    
    if (!$output) {
        return ob_get_clean();
    }
}

function debugSql($sql, $params, $pre = true, $output = true) {
    return debug(parseSql($sql, $params), $pre, $output);
}

/**
 * Search $what at the end of $where. $what can be a string or an array. If
 * $what is an array, the function checks if $where ends with any of the
 * values in the array. As soon as it finds a match the function returns true.
 * Return false if $where (or none of the value in the $where array) is/are
 * not found at the ending of $what.
 *  
 * @param string $where
 * @param mixed $what
 * @return boolean
 */
function endsWith($where, $what) {
    if (is_scalar($what)) {
        return strrpos($where, $what) === strlen($where) - strlen($what);
    }
    
    foreach ($what as $n) {
        if (strrpos($where, $n) === strlen($where) - strlen($n)) {
            return true;
        }
    }
    
    return false;
}

function generateCode($length = 32) {
    $o = "";
    
    while (strlen($o) < $length) {
        $r1 = rand(0, 100000000);
        $t = microtime();
        $r2 = rand(0, 100000000);
        $c = md5($r1 . $t . $r2);
        $o .= substr($c, 0, min($length - strlen($o), strlen($c)));
    }
    
    return $o;
}

function parseSql($sql, $params) {
    return preg_replace_callback("/\?/", function($i) use($params) {
        static $index = 0;
        $v = $params[$index++];
        
        return is_numeric($v) ? $v : "\"" . mysql_escape_string($v) . "\"";
    }, $sql);
}

/**
 * Search $what at the start of $where. $what can be a string or an array. If
 * $what is an array, the function checks if $where starts with any of the
 * values in the array. As soon as it finds a match the function returns true.
 * Return false if $where (or none of the value in the $where array) is/are
 * not found at the beginning of $what.
 *  
 * @param string $where
 * @param mixed $what
 * @return boolean
 */
function startsWith($where, $what) {
    if (is_scalar($what)) {
        return strpos($where, $what) === 0 ? true : false;
    }
    
    foreach ($what as $n) {
        if (strpos($where, $n) === 0) {
            return true;
        }
    }
    
    return false;
}
