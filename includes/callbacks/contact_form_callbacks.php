<?php 
/**
 * @package MDKHAN_CONTACT_FORM
 * 
 */
namespace App\callbacks;


use App\controllers\Base_Controller;


class Contact_Form_Callbacks extends Base_Controller{

    public function short_code_page() {

        return require_once("$this->plugin_path/views/back-end/contact-form-shotcode.php");
    }
}