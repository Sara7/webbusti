<?php

//    include_once("utilities.media.php");

    class UserUtils {
        /* @var $pdo \DataAccess\Dao\SQLPdo */
        const TABLE = "user";
        
        /**
         * Return array with names of the 'type' elements
         */
        public static function getTypeEnum($pdo) {
            $sql = "SHOW COLUMNS FROM " . static::TABLE . " WHERE field = 'user_type'";
            $ret = $pdo->customQuery($sql);
            
            $enums = $ret[0]["Type"];
            $str = substr($enums, strpos($enums, "(") + 1, -1);

            $str = str_replace("\"", "@#double-quotes#@", $str);
            $str = str_replace("\\'", "@#single-quotes#@", $str);
            $str = str_replace("'", "\"", $str);
            $str = str_replace("@#single-quotes#@", "'", $str);
            $str = str_replace("@#double-quotes#@", "\\\"", $str);

            return $v = json_decode("[" . $str . "]");
        }

        public static function setProductInfo($pdo, $product_id, $product_info) {

            foreach($product_info as $k => $v) {
                switch($k) {
                    case "availability":
                        self::setAvailability($pdo, $product_id, $v);
                        break;
                    case "features":
                        self::setFeatures($pdo, $product_id, $v);
                        break;
                    case "paired":
                        self::setPairedProducts($pdo, $product_id, $v);
                        break;
                    case "media":
                        self::setMedia($pdo, $product_id, $v);
                        break;
                }
            }
        }

        public static function getAvailability($pdo, $product_id) {
            $result = $pdo->select("product_availability", ["product_availability_product" => $product_id]);
            $av_array = [];
            if($result) {
                $result = $result[0];
                $av_array = explode("|", $result["product_availability_value"]);
            }
            return $av_array;
        }

        public static function setAvailability($pdo, $product_id, $availability) {
            $availability_string = implode("|", $availability);
            $result = $pdo->insert("product_availability", ["product_availability_product" => $product_id, "product_availability_value" => $availability_string]);
        }

        public static function getMediaPerProduct($pdo, $product_id) {
            $result = $pdo -> select("media_per_entity", ["media_per_entity_product" => $product_id]);
            $media = [];
            foreach($result as $row) {
                $media[] = MediaUtils::getMediaInfo($pdo, $row["media_per_entity_media"]);
            }
            return $media;
        }

        public static function setMedia($pdo, $product_id, $media_list) {
            foreach($media_list as $media) {
                $pdo -> insert("media_per_entity", ["media_per_entity_media" => $media["media_id"], "media_per_entity_role" => $media["media_role"], "media_per_entity_product" => $product_id]);
            }
        }

        public static function getPairedProducts($pdo, $product_id) {
            $result  = $pdo -> select("pairing", ["pairing_product_id_1" => $product_id]);
            $result2 = $pdo -> select("pairing", ["pairing_product_id_2" => $product_id]);
    
            $ids = [];
            $paired_products = [];
            foreach($result as $r) {
                $ids[] = $r["pairing_product_id_2"];
            }
            foreach($result2 as $r) {
                $ids[] = $r["pairing_product_id_1"];
            }
            foreach($ids as $id) {
                //TODO: modificare acquisizione info prodotto paired
                $paired_products[] = $pdo->select("product", ["product_id" => $id])[0];
            }
            return $paired_products;
        }

        public static function setPairedProducts($pdo, $product_id, $pairings) {
            foreach($pairings as $k=>$v) {
                $result = $pdo->insert("pairing", ["pairing_product_id_1" => $product_id, "pairing_product_id_2" => $v, "pairing_title_default" => $k]);
            }
        }

        public static function getFeaturesPerProduct($pdo, $product_id) {
            $result = $pdo -> select("feature_per_product", ["feature_per_product_product" => $product_id]);
            return $result ? $result : [];
        }

        public static function setFeatures($pdo, $product_id, $features) {
            foreach($features as $feature) {
                if($feature["feature_value"]){
                    foreach(explode("|", $feature["feature_value"]) as $val) {
                        $res = $pdo->insert("feature_per_product", ["feature_per_product_feature" => $feature["feature_id"], "feature_per_product_value" => $val, "feature_per_product_product" => $product_id]);
                        if(!$res) die("feature die");
                    } 
                } else {
                    $res = $pdo -> insert("feature_per_product", ["feature_per_product_feature" => $feature["feature_id"], "feature_per_product_value" => "NULL", "feature_per_product_product" => $product_id]);
                    if(!$res) die("feature die");
                }
            }
        }

        public static function getFeaturedProducts($pdo, $product_id) {
            $result =  $pdo->select ("featured_product", ["featured_product_product_id" => $product_id]);
            return $result ? $result : [];
        }

    }
