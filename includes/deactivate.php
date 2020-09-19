<?php
/**
 * @package MDKHAN_CONTACT_FORM
 * 
 */
namespace App;

 class Deactivate {

    public static function deactivate() {
        flush_rewrite_rules();
    }
 }