<?php
    class Category {
        private $category_id;
        private $category_dic;

        function __construct($id, $dic) {
            $this->category_id = $id;
            $this->category_dic = $dic;
        }
    }
?>