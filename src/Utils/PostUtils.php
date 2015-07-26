<?php

namespace Utils;

class PostUtils {
    
    static public function hasData($value) {
        return (isset($value) && is_string($value) && strlen(trim($value)) > 0);
    }

    static public function getPostField($postField, $defaultValue) {
        return (self::hasData($postField) && isset($_POST[$postField]))
            ? $_POST[$postField]
            : $defaultValue;
    }
}
