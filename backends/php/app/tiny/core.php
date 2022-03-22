<?php namespace tiny;

// find depth, array multiple
function c(array $obj, string ...$params) {

    $temp = $obj;
    foreach ($params as $key) {
        if (array_key_exists($key, $temp)) {
            $temp = $temp[$key];
        } else {
            return null;
        }
    }
    return $temp;
}