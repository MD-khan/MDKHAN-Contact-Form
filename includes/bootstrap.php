<?php
/**
 * @package MDKHAN_CONTACT_FORM
 * 
 * This is the core class of this plugin
 * This class initates the all the classes which are needs to run the plugin
 */
namespace App;

 final class Bootstrap {


    /**
     *  You must add all of your class here in this array 
     * This is 
     */
    private static function get_services() {

        return [
            Enqueue::class,
            controllers\Contact_Form_Controller::class
        ];

    }


    /**
     * Loop through the clasess,
     * call the register method if it is exist in the class
     */
    public static function register_services() {

        foreach( self::get_services() as $class ) {
            $service = self::instantiate( $class );
            if( method_exists( $service, 'register' ) ) {
                $service->register();
            }
        }

    }


    /**
     * Return an obhect of a class
     */
    private static function instantiate( $class ) {

        $service = new $class(); 
        return $service;
    }

 }