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
        "title"
    ];

    // Public properties
    public $id;
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
     * @param array $v
     * @param bool $count
     * @return Qualification[]
     */
    public static function search($v = [], $count = false) {
        $def_sorting = [["id", "ASC"]];
        $numeric_fields = [];
        $string_fields = ["title"];

        $custom_fields = [];
        $custom_params = [];

        $custom_join = null;
        $custom_select = null;
        
        return parent::_search(get_class(), $v, $count, $def_sorting, $numeric_fields, $string_fields, $custom_fields, $custom_params, $custom_join, $custom_select);
    }

}
