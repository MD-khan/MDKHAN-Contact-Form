<?php 
/**
 * @package MDKHAN_CONTACT_FORM
 * 
 * This class creates admin menue and pages. 
 * You should call it from the controller to create admin menu and page
 * 
 */
namespace App\api;



class Settings_Api {


    public $admin_pages = array();
    public $admin_subpages = array();

    public function register()
	{
		if ( ! empty($this->admin_pages) || ! empty($this->admin_subpages) ) {
			add_action( 'admin_menu', array( $this, 'create_admin_menu' ) );
        }
        
    }

    /**
     * 
     */
    public function create_admin_menu()
	{
		foreach ( $this->admin_pages as $page ) {
			add_menu_page( $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['callback'], $page['icon_url'], $page['position'] );
		}

        
		foreach ( $this->admin_subpages as $page ) {
			add_submenu_page( $page['parent_slug'], $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['callback'] );
		}
    }
    

    public function add_sub_pages( array $pages )
	{
		$this->admin_subpages = array_merge( $this->admin_subpages, $pages );

		return $this;
	}



}