<?php

class User extends Entity {
    // Constants
    const OBJECT_NAME = "User";
    
    const TABLE_NAME = "user";
    const TABLE_ALIAS = "u";
    const FIELD_PREFIX_US = "user_";
    const FIELD_PREFIX = "user";
    const KEY_FIELD = "id";
    
    const TYPE_BUSINESS = "business";
    const TYPE_PRIVATE = "private";

    // Static property
    static $instances = [];        // array of instances
    static $editable_fields = [
        "uuid",
        "type",
        "firstname",
        "lastname",
        "company_name",
        "company_sdi_code",
        "company_pec",
        "company_vat_number",
        "fiscal_code",
        "birthdate",
        "qualification_id",
        "email",
        "salt",
        "password",
        "deleted",
        "privacy_policy",
        "promo",
        "newsletter",
        "activation_code",
        "password_recovery_code",
        "is_admin"
    ];

    // Public properties
    public $id;
    public $uuid;
    public $type;
    public $firstname;
    public $lastname;
    public $company_name;
    public $company_sdi_code;
    public $company_pec;
    public $company_vat_number;
    public $fiscal_code;
    public $birthdate;
    public $qualification_id;
    public $email;
    public $salt;
    public $password;
    public $deleted;
    public $privacy_policy;
    public $promo;
    public $newsletter;
    public $activation_code;
    public $password_recovery_code;
    public $is_admin;
    
    // Magic methods
    public function __get($property) {
        switch ($property) {
            case "qualification":
                return $this->$property = Qualification::resolve($this->qualification_id);
            default:
                trigger_error("Undefined property '$property' in " . get_class($this));
                break;
        }
    }

    public static function _reset () {
        self::$instances = [];
    }
    
    public function edit ($v = []) {
        $sql = "UPDATE " . self::TABLE_NAME . " SET " . self::FIELD_PREFIX_US . self::KEY_FIELD . " = ?";
        $params = [$this->id];

        foreach (static::$editable_fields as $field) {
            $key = self::FIELD_PREFIX_US . $field;
            
            if (array_key_exists($key, $v)) {
                $sql .= ", $key = ?";
                $params[] = $v[$key];
            }
        }

        $sql .= " WHERE " . self::FIELD_PREFIX_US . self::KEY_FIELD . " = ?";
        $params[] = $this->id;
        
        DB::query($sql, $params);
        $this->update();
    }

    /**
     * @return User
     */
    public static function findInstance($id, $data = null) {
        return parent::findClassInstance(get_class(), [self::FIELD_PREFIX_US . self::KEY_FIELD => $id], $data);
    }
    
    public static function getByUuid ($uuid) {
        return collectionGetValue(self::search([
            "user_uuid" => $uuid
        ]), 0, null);
    }
    
    public static function getEnums ($field) {
        return parent::getEnums(self::TABLE_NAME, $field);
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
    
    public function setPassword ($password) {
        $password = trim($password);
        
        if (empty($password)) {
            return false;
        }
        
        $salt = generateCode();
        $passwd = md5($salt . md5($password) . $salt);
        
        $this->edit([
            "user_salt" => $salt,
            "user_password" => $passwd
        ]);
        
        return true;
    }

    /**
     * @param array $v
     * @param bool $count
     * @return User[]
     */
    public static function search ($v = [], $count = false) {
        $def_sorting = [["id", "ASC"]];
        $numeric_fields = ["birthdate", "qualification_id", "deleted", "privacy_policy", "promo", "newsletter", "is_admin"];
        $string_fields = ["uuid", "type", "firstname", "lastname", "company_name", "company_sdi_code", "company_pec", "company_vat_number", "fiscal_code", "email", "salt", "password", "activation_code", "password_recovery_code"];

        $custom_fields = [];
        $custom_params = [];

        $custom_join = null;
        $custom_select = null;
        
        return parent::_search(get_class(), $v, $count, $def_sorting, $numeric_fields, $string_fields, $custom_fields, $custom_params, $custom_join, $custom_select);
    }

}
