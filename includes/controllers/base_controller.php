<?php 
/**
 * @package MDKHAN_CONTACT_FORM
 * 
 */
namespace App\controllers;

class Base_Controller {

    public $plugin_path;
    public $plugin_url;
    public $plugin;

     /**
      * Define plugin essantial paths
      */
    public function __construct() {

        $this->plugin_path = plugin_dir_path( dirname(__FILE__, 2) ); // 2 = mdkhan-contact-form.php
        $this->plugin_url = plugin_dir_url( dirname( __FILE__, 2 ) ); 
        $this->plugin = $this->plugin_basename( 3 );
    }


    private function plugin_basename( $level ) {

        $basename = basename( dirname(__FILE__, $level ) ).".php";
    	$files = glob( $this->plugin_path.$basename );
    	return plugin_basename( $files[0] );

    }

    public function dd( $input )
  	{
  		echo "<pre>";
		    var_dump($input);
		echo "</pre>";
		die();
  	}

 }