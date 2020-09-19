<?php 
/**
 * @package MDKHAN_CONTACT_FORM
 *  This is the main contact form cotroller class
 * This class creates the following wordpress features
 * - Custom Post type
 * - Meta Boxes 
 * - Shortcode
 * - Create shortcode page
 * - Save contact details using ajax
 * - Customize the default admin columns, add new column to the post type display table
 * 
 *  
 */
namespace App\controllers;

use App\controllers\Base_Controller;
use App\api\Settings_Api;
use App\callbacks\Contact_Form_Callbacks;

class Contact_Form_Controller extends Base_Controller {

	public $post_type = 'mk_contact_form';
	public $settings;
	public $callbacks;

	public function register()
	{

		$this->settings = new Settings_Api();
		$this->callbacks = new Contact_Form_Callbacks();

		add_action('init', array( $this, 'mdkhn_contact_form_cpt') );

		add_action('add_meta_boxes', array($this, 'mk_cf_meta_box') );
		add_action('save_post', array($this, 'save_meta_box') );

		add_action('wp_ajax_submit_contact_form', array( $this, 'submit_contact_form') );
		add_action('wp_ajax_nopriv_submit_contact_form', array( $this, 'submit_contact_form') );

		// Create Shortcode
		add_shortcode('mk-contact-form', array( $this, 'create_contact_form') );

		// Add columns to custom post type culumns
		add_action('manage_'.$this->post_type.'_posts_columns', array($this, 'set_custome_columns') );

		// Add data to  custom the columns
		add_action("manage_{$this->post_type}_posts_custom_column", array($this, 'add_data_to_custom_columns'), 10,2);
		// sorting the clumn values
		add_filter("manage_edit-{$this->post_type}_sortable_columns", array( $this, 'make_cutom_column_sortable') );

		// call the sortcode display page 
		$this->set_shortcode_page();
	}

	public function mdkhn_contact_form_cpt() {
		
		$labels = array(
			'name' => 'MK Contact Form',
			'singular_name' => 'MK Contact Forms',
		);
		$args = array(
			'labels' => $labels,
			'public' => true,
			'has_archive' => false,
			'menu_icon'   => 'dashicons-testimonial',
			'exclude_from_search' => true,
			'publicly_queryable' => false, // not showing posts on the fron-end
			'supports' => array('title', 'editor'),
			'show_in_rest' => false
		);

		register_post_type( $this->post_type, $args );
	}

	public function mk_cf_meta_box() {
		add_meta_box(
			'mk_cf_meta_id', 
			__('MK CF Contact Form', 'mkcf'), 
			array( $this, 'render_meta_box_content'),
			$this->post_type, 
			'side',
			'default'
		);
	}

	public function render_meta_box_content( $post ){
		
		wp_nonce_field( 'mk_cf_contact_form', 'mk_cf_contact_form_nonce' );

		$data = get_post_meta( $post->ID, '_mk_cf_meta_box_key',true ); 

		$name = isset($data['name']) ? $data['name'] : '';
		$email = isset($data['email']) ? $data['email'] : '';
		?>
		<p>
			<label class="meta-label" for="mk-cf-name"> Name</label>
			<input type="text" id="mk-cf-name" name="mk-cf-name" class="widefat" value="<?php echo esc_attr( $name ); ?>">
		</p>

		<p>
			<label class="meta-label" for="mk-cf-email"> Email</label>
			<input type="email" id="mk-cf-email" name="mk-cf-email" class="widefat" value="<?php echo esc_attr( $email ); ?>">
		</p>
		
		<?php
	}

	// save meta data in the back-end
	public function save_meta_box( $post_id )
	{	
		
		if (! isset($_POST['mk_cf_contact_form_nonce'])) {
			return $post_id;
		}

		$nonce = $_POST['mk_cf_contact_form_nonce'];
		if (! wp_verify_nonce( $nonce, 'mk_cf_contact_form' )) {
			return $post_id;
		}
	
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if (! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		$data = array(
			'name' => sanitize_text_field( $_POST['mk-cf-name'] ),
			'email' => sanitize_email( $_POST['mk-cf-email'] ),
		);
	
		update_post_meta( $post_id, '_mk_cf_meta_box_key', $data );
	}


	// Front end fomr
	public function create_contact_form()
	{
		ob_start();

			echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$this->plugin_url/public/css/form.css\"/>";
			require_once("$this->plugin_path/views/front-end/contact-form.php");
		   echo "<script src=\"$this->plugin_url/public/js/form.js\"></script>";
		return ob_get_clean();
		
	}

	/**
	 * Create short code subpage section
	 */
	public function set_shortcode_page()
	{
		$subpage = array(
			array(
				'parent_slug' => 'edit.php?post_type='.$this->post_type.'',
				'page_title' => 'Form Shotcode',
				'menu_title' => 'Form Shotcode',
				'capability' => 'manage_options',
				'menu_slug' => $this->post_type.'slug',
				'callback' => array( $this->callbacks, 'short_code_page')
			)
		);	
		$this->settings->add_sub_pages( $subpage )->register();
	}

	// handle ajax request
	public function submit_contact_form()
	{
		if ( !DOING_AJAX || !check_ajax_referer('mk_cf_contact_form_nonce', 'nonce', false) ) {
			return $this->return_json('error');
		}
	
		$name = sanitize_text_field( $_POST['name'] );
		$email = sanitize_email( $_POST['email'] );
		$message = sanitize_textarea_field($_POST['message'] );

		$data = array(
			'name' => $name,
			'email' => $email
		);
		

		// define custom post data
		$args = array(
			'post_title' => 'A message from ' .$name,
			'post_content' => $message,
			'post_author' => 1, // it will be the admin, wp need an author
			'post_status' => 'publish',
			'post_type' => $this->post_type,
			'meta_input' => array('_mk_cf_meta_box_key' => $data )
		);
		
		$result = wp_insert_post( $args );
		
		//$this->dd( wp_mail( 'md.monir.khan707@gmail.com', 'do not reply', 'we got' ) );

		if ( $result ) { // success
			return $this->return_json('success');
			//$this->send_email( $to, $subject, $body, $header );
			wp_mail( $data['email'], 'do not reply', 'we got' );
			

		}

		return $this->return_json('error');
		
	}


	/**
	 * Return json status
	 * @param  [type] $status [description]
	 * @return [type]         [description]
	 */
	public function return_json( $status )
	{
		$return = array( 
				'status' => $status
			);

		wp_send_json($return);
		wp_die();
	}

	/**
	 * Adding custom columns
	 */
	public function set_custome_columns( $columns ) {

		$title = $columns['title'];
		$date = $columns['date'];
		unset($columns['title'], $columns['date']);

		$columns['name'] = 'Name';
		$columns['email'] = 'Email';
		$columns['title'] = $title;
		$columns['date'] = 'Date';
		return $columns;
	}

	/**
	 * 
	 */
	public function add_data_to_custom_columns( $column, $post_id ) {

		$data = get_post_meta( $post_id, '_mk_cf_meta_box_key', true );

		$name = isset($data['name']) ? $data['name'] : '';
		$email = isset($data['email']) ? $data['email'] : '';
		
		switch ($column) {
			case 'name':
				echo '<strong>'.$name.' </strong>';
				break;
			
			case 'email':
				echo '<a href="mailto:'.$email.'">' .$email. '</a>';
				break;
				
		}

	}
	
	/**
	 *
	 */
	public function make_cutom_column_sortable( $columns ) {
		
		$columns['name'] = 'name';
		$columns['email'] = 'approved';
		
		return $columns;
	}

	

 }