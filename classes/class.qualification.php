<?php

class Qualification extends Entity {
    // Constants
    const OBJECT_NAME = "Qualification";
    const TABLE_NAME = "qualification";
    const TABLE_ALIAS = "q";
    const FIELD_PREFIX_US = "qualification_";
    const FIELD_PREFIX = "qualification";
    const KEY_FIELD = "id";

    // Static property
    static $instances = [];        // array of instances
    static $editable_fields = [
        "uuid",
        "title"
    ];

    // Public properties
    public $id;
    public $uuid;
    public $title;
    
    // Magic methods
    public function __get ($property) {
        switch ($property) {
            default:
                trigger_error("Undefined property '$property' in " . get_class($this));
                break;
        }
    }

    public static function _reset() {
        self::$instances = [];
    }

    /**
     * @return Qualification
     */
    public static function findInstance($id, $data = null) {
        return parent::findClassInstance(get_class(), [self::FIELD_PREFIX_US . self::KEY_FIELD => $id], $data);
    }
    
    public static function getByUuid ($uuid) {
        return collectionGetValue(self::search([
            "qualification_uuid" => $uuid
        ]), 0, null);
    }
    
    public static function resolve ($item) {
        if (empty($item)) {
            return null;
        }
        
        if (is_object($item) && is_a($item, get_class())) {
            return $item;
        }
        
        if (is_scalar($item)) {
            $item = trim($item);
            
            if (is_numeric($item)) {
                return self::findInstance($item);
            }
            
            if (is_string($item)) {
                return self::getByUuid($item);
            }
        }
        
        return null;
    }

    /**
     * @param array $v
     * @param bool $count
     * @return Qualification[]
     */
    public static function search($v = [], $count = false) {
        $def_sorting = [["id", "ASC"]];
        $numeric_fields = [];
        $string_fields = ["uuid", "title"];

        $custom_fields = [];
        $custom_params = [];

        $custom_join = null;
        $custom_select = null;
        
        return parent::_search(get_class(), $v, $count, $def_sorting, $numeric_fields, $string_fields, $custom_fields, $custom_params, $custom_join, $custom_select);
    }

}
