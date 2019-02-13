<?php

    class Utility {
        public function getExt($filename) {
            $pattern = "/\.(\w*)$/";
            if(preg_match($pattern, $filename, $matches) && sizeOf($matches) > 0) {
                return strtolower($matches[1]);
            }
            return null;
        }

        public function getThumbName($filename) {
            $pattern = "/\.(\w*)$/";
            return preg_replace($pattern, '_thumb.$1', $filename);
        }

        public function Hello() {
            return "ciao";
        }
    }
?>