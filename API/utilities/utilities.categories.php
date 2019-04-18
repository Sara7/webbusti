<?php
    class CategoryUtils {
        public static function getCategoryInfo($pdo, $category_code) {
            $result = $pdo->select("category", ["category_code" => $category_code]);
            return $result ? $result[0] : [];
        }
    }