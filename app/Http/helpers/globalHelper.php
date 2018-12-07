<?php
/**
 * This file intended to create and use function globally through out the project. Called as Helper.
 */
if (!function_exists('unique_id_generator')) {
    /**
     * This function generates a unique id for users
     * @param $prefix string
     * @param $suffix string
     * @return string
     */
    function unique_id_generator ($prefix = null, $suffix = null) {
        return $prefix.'-'.substr((string)time(), 0,3).'-'.substr((string)time(), 3,6).'-'.substr((string)time(), 6,10).'-'.substr(uniqid(),8,13).'-'.$suffix;
    }
}