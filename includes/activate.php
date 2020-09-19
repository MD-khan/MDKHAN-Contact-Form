<?php
/**
 * @package MDKHAN_CONTACT_FORM
 * 
 */
namespace App;

 class Activate {

    public static function activate() {
        flush_rewrite_rules();
    }
 }