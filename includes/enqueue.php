<?php
/**
 * @package MDKHAN_CONTACT_FORM
 * 
 */
namespace App;

use App\controllers\Base_Controller;

 class Enqueue extends Base_Controller{

    
    public function register() {
        add_action ('admin_enqueue_scripts', array( $this, 'enqueue' ) );
    }

    public function enqueue() {
        wp_enqueue_style('mdkhan_cf_style', $this->plugin_url. 'public/css/style.css' );
        wp_enqueue_script('mdkhan_cf_script', $this->plugin_url. 'public/js/script.js', array( 'jquery' ), true );
    }
 }