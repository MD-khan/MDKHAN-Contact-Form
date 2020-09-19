<?php
/**
 * @package MDKHAN_CONTACT_FORM
 * @version 1.0.0
 */
/*
Plugin Name: MDKHAN Contact Form
Plugin URI: http://monirkhan.net/plugins/mdkhan-contact-form
Description: Very Simple Contact Form
Version: 1.0.0
Author URI: http:monirkhan.net
Text Domain: mdkhan-contact-form
*/

defined('ABSPATH') or die('Sorry! You are not allow here.');

// Require once the Composer Autoload
if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}


/**
 * Plugin activation
 */
function mdkhan_cf_plugin_activate() {
	App\Activate::activate();
}
register_activation_hook( __FILE__, 'mdkhan_cf_plugin_activate' );


/**
 * Plugin deactivation
 */
function mdkhan_cf_plugin_deactivate() {
	App\Deactivate::deactivate();
}
register_deactivation_hook( __FILE__, 'mdkhan_cf_plugin_deactivate' );



if( class_exists('App\\Bootstrap') ) {

    App\Bootstrap::register_services(); 
    //App\Bootstrap::class; // also you can only call class here
}
