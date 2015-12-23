<?php
/*
Plugin Name: DJD Site Post
Plugin URI: http://www.djdesign.de/djd-site-post/
Description: Write, publish and edit posts on the front end
Version: 0.9.3
Author: Dirk Jarzyna
Author URI: http://www.djdesign.de
License: GPL2

  Copyright 2013 Dirk Jarzyna

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

if (!class_exists("DjdSitePost")) {
	class DjdSitePost {

	/*--------------------------------------------*
	 * Constructor
	 *--------------------------------------------*/

	/**
	 * Initializes the plugin by setting localization, filters, and administration functions.
	 */
	function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'plugin_textdomain' ) );

		// Register site styles and scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_scripts' ) );

		// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
		register_uninstall_hook( __FILE__, array( 'djd-site-post', 'uninstall' ) );

		//Hooking in to setup admin settings page and settings menu
		add_action('admin_init', array($this, 'admin_init'));
		add_action('admin_menu', array($this, 'add_menu'));

		/**
		 * Custom actions
		 */

		// Setup Ajax Support
		add_action('wp_ajax_process_site_post_form', array( $this, 'process_site_post_form' ) );
		add_action('wp_ajax_nopriv_process_site_post_form', array( $this, 'process_site_post_form' ) );

		// Hide Toolbar
		add_action('init', array($this, 'hide_toolbar'));

		// Redirect non admin users from dashboard
		//add_action( 'admin_init', array($this, 'redirect_nonadmin_fromdash'), 1);
	
		// Register a widget to show the post form in a sidebar
		add_action( 'widgets_init', array($this,'register_form_widget' ));
		 
		// Save an auto-draft to get a valid post-id
		add_action ('save_djd_auto_draft', array($this, 'save_djd_auto_draft'));

		/**
		 * Custom filter
		 */

		// Print an edit post on front end link whenever an edit post link is printed on front end.
		add_filter('edit_post_link', array($this, 'edit_post_link'), 10, 2);

		// Redirect non admin users from dashboard
		// add_filter('login_redirect', array($this, 'djd_login_redirect'), 10, 3);
		
		//Call our shortcode handler
		add_shortcode('djd-site-post', array($this, 'handle_shortcode'));
		
	} // end constructor

	/**
	 * Fired when the plugin is activated.
	 *
	 * @param	boolean	$network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog
	 */

	public function activate( $network_wide ) {
		$this->set_default_options();
	} // end activate

	/**
	 * Loads the plugin text domain for translation
	 */
	public function plugin_textdomain() {
		$domain = 'djd-site-post';
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
		load_textdomain( $domain, WP_LANG_DIR.'/'.$domain.'/'.$domain.'-'.$locale.'.mo' );
		load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	} // end plugin_textdomain

	/**
	 * Registers and enqueues admin-specific styles.
	 */
	public function register_admin_styles() {
		wp_enqueue_style( 'djd-site-post-admin-styles', plugins_url( 'djd-site-post/css/admin.css' ) );
	} // end register_admin_styles

	/**
	 * Registers and enqueues admin-specific JavaScript.
	 */
	public function register_admin_scripts() {
		wp_enqueue_script( 'djd-site-post-admin-script', plugins_url( 'djd-site-post/js/admin.js' ) );
	} // end register_admin_scripts

	/**
	 * Registers and enqueues plugin-specific styles.
	 */
	public function register_plugin_styles() {
		wp_enqueue_style( 'djd-site-post-styles', plugins_url( 'djd-site-post/css/display.css' ) );
	} // end register_plugin_styles

	/**
	 * Registers and enqueues plugin-specific scripts.
	 */
	public function register_plugin_scripts() {
		wp_enqueue_script( 'djd-site-post-script', plugins_url( 'djd-site-post/js/display.js' ) );
		wp_enqueue_script( 'djd-site-post-ajax-script', plugins_url( 'djd-site-post/js/script.js' ) );
	} // end register_plugin_scripts

	/**
	 * Registers our post form widget.
	 */
	public function register_form_widget() {
		require(sprintf("%s/inc/djdsp-widget.php", dirname(__FILE__)));
		register_widget( 'djd_site_post_widget' );
	} // end register_form_widget
	
	/*
	 * Hook into WP's admin_init action hook
	 */
	public function admin_init() {
		// Set up the settings for this plugin
		$this->init_settings();
	} // end public static function activate

	/*
	 * Redirect non admin users from dashboard
	 */
	public function redirect_nonadmin_fromdash() {
		$djd_options = get_option('djd_site_post_settings');

		if ( $_SERVER['PHP_SELF'] == '/wp-admin/async-upload.php' ) {
			/* allow users to upload files */
			return true;
		} else if ( $djd_options['djd-no-backend'] && ( current_user_can('contributor') ||  current_user_can('subscriber')) ) {
			/* custom function get_user_role() checks user role, 
			requires administrator, else redirects */
			wp_safe_redirect(site_url());
			exit;
		}
	}

	public function djd_login_redirect( $redirect_to, $request, $user  ) {
		return ( is_array( $user->roles ) && in_array( 'administrator', $user->roles ) ) ? admin_url() : site_url();
	}

	/*
	 * Setting default values and store them in db
	 */
	public function set_default_options() {
		$defaultAdminOptions = array(
			'djd-form-name' => '',
			'djd-publish-status' => 'publish',
			'djd-post-confirmation' => '',
			'djd-post-fail' => '',
			'djd-redirect' => '',
			'djd-mail' => '1',
			'djd-hide-toolbar' => '1',
			'djd-hide-edit' => '0',
			'djd-login-link' => '1',
			'djd-post-format' => '0',
			'djd-allow-guest-posts' => '0',
			'djd-guest-account' => '1',
			'djd-guest-cat-select' => '0',
			'djd-guest-cat' => '',
			'djd-categories' => 'list',
			'djd-default-category' => '1',
			'djd-allow-new-category' => '0',
			'djd-category-order' => 'id',
			'djd-title-required' => '1',
			'djd-show-excerpt' => '1',
			'djd-allow-media-upload' => '1',
			'djd-upload-no-content' => '1',
			'djd-show-tags' => '0',
			'djd-guest-info' => '1',
			'djd-title' => '',
			'djd-excerpt' => '',
			'djd-content' => '',
			'djd-editor-style' => 'rich',
			'djd-upload' => '',
			'djd-tags' => '',
			'djd-categories-label' => '',
			'djd-create-category' => '',
			'djd-send-button' => ''
		);
		// Check for previous options that might be stored in db
		$dbOptions = get_option('djd_site_post_settings');
		if (!empty($dbOptions)) {
			foreach ($dbOptions as $key => $option)
				$defaultAdminOptions[$key] = $option;
		}
		update_option('djd_site_post_settings', $defaultAdminOptions);
	} // end set_default_options()

	/*
	 * Initialize some custom settings
	 */
	public function init_settings() {
		// Register the settings for this plugin
		register_setting('djd_site_post_template_group', 'djd_site_post_settings', array($this, 'djd_site_post_validate_input'));
	} // end public function init_custom_settings()

	/*
	 * Add a menu for our settings page
	 */
	public function add_menu() {
		add_options_page(__('DJD Site Post Settings', 'djd-site-post'), __('DJD Site Post', 'djd-site-post'), 'manage_options', 'djd-site-post-settings', array($this, 'plugin_settings_page'));
	} // end public function add_menu()

	/*
	 * Admin menu callback
	 */
	public function plugin_settings_page() {
		if(!current_user_can('manage_options')) {
			wp_die(__('You do not have sufficient permissions to access this page.', 'djd-site-post'));
		}
		// Render the settings template
		include(sprintf("%s/views/admin.php", dirname(__FILE__)));
	} // end public function plugin_settings_page()

	//function plugin_options_tabs() {
	//	$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'djdspp_general_settings';
	//
	//	screen_icon();
	//	echo '<h2 class="nav-tab-wrapper">';
	//	foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
	//		$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
	//		echo '<a class="nav-tab ' . $active . '" href="?page=' . 'djd-site-post-settings' . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';	
	//	}
	//	echo '</h2>';
	//}

	/*
	 * Validate input
	 */
	public function djd_site_post_validate_input($input) {

		// Create our array for storing the validated options
		$output = array();

		// Loop through each of the incoming options
		foreach( $input as $key => $value ) {

		    // Check to see if the current option has a value. If so, process it.
		    if( isset( $input[$key] ) ) {
			// Strip all HTML and PHP tags and properly handle quoted strings
			$output[$key] = esc_attr(strip_tags( stripslashes( $input[ $key ] ) ) );
		    }
		}
		// Return the array processing any additional functions filtered by this action
		return apply_filters( 'djd_site_post_validate_input', $output, $input );
	}

	// Following two functions make sure that image attachment gets the right post-id
	public function djd_insert_media_fix( $post_id ) {
		global $djd_media_post_id;
		global $post_ID; 
	
		/* WordPress 3.4.2 fix */
		$post_ID = $post_id; 
	
		/* WordPress 3.5.1 fix */
		$djd_media_post_id = $post_id;
		add_filter( 'media_view_settings', array($this, 'djd_insert_media_fix_filter'), 10, 2 );
	}

	public function djd_insert_media_fix_filter( $settings, $post ) {
		global $djd_media_post_id;
	
		$settings['post']['id'] = $djd_media_post_id;
		$settings['post']['nonce'] = wp_create_nonce( 'update-post_' . $djd_media_post_id );
		return $settings;
	}
	
	/*---------------------------------------------*
	 * Core Functions
	 *---------------------------------------------*/

	/*
	 * Print a link to edit post on front end whenever an edit post link is printed on front end.
	 */
	function edit_post_link($link, $post_id) {
		if ( 'page' != get_post_type($post_id) ) {
			$djd_options = get_option('djd_site_post_settings');
			if ( $djd_options['djd-edit-page'] ) {
				if ( $djd_options['djd-hide-edit'] ) {
					$link = '<a class="post-edit-link" href="' . home_url('/') . '?page_id='.$djd_options['djd-edit-page'] . '&post_id='.$post_id . '" title="Frontend Edit">Frontend Edit</a>';
				} else {
					$link = $link . ' | <a class="post-edit-link" href="' . home_url('/') . '?page_id='.$djd_options['djd-edit-page'] . '&post_id='.$post_id . '" title="Frontend Edit">Frontend Edit</a>';
				}
			}
		}
		return $link;
	}

	/*
	 * Format error messages for output.
	 */
	function format_error_msg($message, $type = '',  $source = ''){
		$html = '<p style="color:red"><em>';
		if(!$type)
			$type = __("Error", 'djd-site-post');
		$html .= "<strong>" . htmlspecialchars($type) . "</strong>: ";
		$html .= $message;
		$html .= '</em></p>';
		if($source){
			$html .= '<pre style="margin-left:5px; border-left:solid 1px red; padding-left:5px;"><code class="xhtml malformed">';
			$html .= htmlspecialchars($source);
			$html .= '</code></pre>';
		}
		return $html;
	}

	/*
	 * Hide the WordPress Toolbar.
	 */
	function hide_toolbar() {
		$djd_options = get_option('djd_site_post_settings');
		if ( $djd_options['djd-hide-toolbar'] ) {
			add_filter('show_admin_bar', '__return_false');
		}
	}

	/*
	 * Get current user info. If user is not logged in we check if guest posts are permitted and set variables accordingly.
	 */
	function verify_user() {
		$djd_userinfo = array ();
		$djd_options = get_option('djd_site_post_settings');

		if (is_user_logged_in()) {
			global $current_user;
			get_currentuserinfo();
			$djd_userinfo['djd_user_id'] = $current_user->ID;
			$djd_userinfo['djd_user_login'] = $current_user->user_login;
			if ( current_user_can('publish_posts') )
				$djd_userinfo['djd_can_publish_posts'] = true;
			if ( current_user_can('manage_categories') )
				$djd_userinfo['djd_can_manage_categories'] = true;
				
			if ( current_user_can('contributor') ) {
				$contributor = get_role('contributor');
				$contributor->add_cap('upload_files');
				$contributor->add_cap('read');
				$contributor->add_cap('edit_posts');
				$contributor->add_cap('edit_published_pages');
				$contributor->add_cap('edit_others_pages');
				$djd_userinfo['media_upload'] = true;
			}
			return $djd_userinfo;

		} elseif ( (!is_user_logged_in()) && ($djd_options['djd-allow-guest-posts']) ) {
			$user_query = get_userdata($djd_options['djd-guest-account']);
			$djd_userinfo['djd_user_id'] = $user_query->ID;
			$djd_userinfo['djd_user_login'] = $user_query->user_login;
			
			// We give guests rights as a subscriber. Very limited, no media uploads.
			$djd_userinfo['djd_can_manage_categories'] = false;
			$djd_userinfo['djd_can_publish_posts'] = true;
			$djd_userinfo['publish_status'] = 'pending';
			$djd_userinfo['media_upload'] = false;

			return $djd_userinfo;
		}
		return false;
	} // end verify_user()

	function djd_check_user_role( $role, $user_id = null ) {
	 
		if ( is_numeric( $user_id ) )
			$user = get_userdata( $user_id );
		else
			$user = wp_get_current_user();
	 
		if ( empty( $user ) )
			return false;
		return in_array( $role, (array) $user->roles );
	}

	function save_djd_auto_draft( $error_msg = false ) {

		global $djd_post_id;
	
		if (!function_exists('get_default_post_to_edit')){
			require_once(ABSPATH . "wp-admin" . '/includes/post.php');
		}
	
		/* Check if a new auto-draft (= no new post_ID) is needed or if the old can be used */
		$last_post_id = (int) get_user_option( 'dashboard_quick_press_last_post_id' ); // Get the last post_ID
		if ( $last_post_id ) {
			$post = get_post( $last_post_id );
			if ( empty( $post ) || $post->post_status != 'auto-draft' ) { // auto-draft doesn't exists anymore
				$post = get_default_post_to_edit( 'post', true );
				update_user_option( get_current_user_id(), 'dashboard_quick_press_last_post_id', (int) $post->ID ); // Save post_ID
			} else {
				$post->post_title = ''; // Remove the auto draft title
			}
		} else {
			$post = get_default_post_to_edit( 'post' , true);
			$user_id = get_current_user_id();
			// Don't create an option if this is a super admin who does not belong to this site.
			if ( ! ( is_super_admin( $user_id ) && ! in_array( get_current_blog_id(), array_keys( get_blogs_of_user( $user_id ) ) ) ) )
				update_user_option( $user_id, 'dashboard_quick_press_last_post_id', (int) $post->ID ); // Save post_ID
		}
	
		$djd_post_id = (int) $post->ID;
	
		// Getting the right post-id for media attachments
		$this->djd_insert_media_fix( $djd_post_id );
	
	}

	/*
	 * Registers the shortcode that has a required @name param indicating the function which returns the HTML code for the shortcode.
	 *
	 * Shortcode: [djd-site-post] With parameters: [djd-site-post success_url="url" success_page_id="id"]
	 * Parameters:
	 * 	success_url: URL of the page to redirect to after the post.
	 * 	success_page_id: ID of the page to redirect to after the post. Overwrites success_url if set.
	 */
	function handle_shortcode($atts, $content = null){

		global $shortcode_cache, $post, $djd_post_id;
		
		extract(shortcode_atts(array(
			'success_url' => '',
			'success_page_id' => 0,
			'called_from_widget' => '0',
		), $atts));
		$form_name = 'site_post_form';
		$djd_options = get_option('djd_site_post_settings');

		// Check for user logged in or guest posts permitted.
		if(!$user_verified = $this->verify_user())
			return $this->format_error_msg(__("Please login or register to use this function.", 'djd-site-post'),__("Notice", 'djd-site-post'));

		do_action ('save_djd_auto_draft');
			
		// success_page_id overwrites success_url.
		if($success_page_id)
			$success_url = get_permalink($success_page_id);

		// Shortcode 'success_url' attribute. This has priority over redirect set in admin panel.
		if(!$success_url) {
			$success_url = $djd_options['djd-redirect'];
			if (empty($success_url)) $success_url = home_url() . "/";
		}

		// Call the function and grab the results (if nothing, output a comment noting that it was empty).
		// This one calls the form presented to the user.
		return call_user_func_array(array($this, $form_name), array($atts, $content, $user_verified, $djd_post_id, $called_from_widget));

	}

	function process_site_post_form() {
		if( isset($_POST) ){

			$djd_options = get_option('djd_site_post_settings');
			
			if ( !empty ($_POST["djd-our-id"])) $djd_post_id = $_POST["djd-our-id"];
	
				// Create post object with defaults
				$my_post = array(
					'ID' => $djd_post_id,
					'post_title' => '',
					'post_status' => 'publish',
					'post_author' => '',
					'post_category' => '',
					'comment_status' => 'open',
					'ping_status' => 'open',
					'post_content' => '',
					'post_excerpt' => '',
					'post_type' => 'post',
					'tags_input' => '',
					'to_ping' =>  ''
				);
	
				//Fill our $my_post array
				$my_post['post_title'] = wp_strip_all_tags($_POST['djd_site_post_title']);

				if( array_key_exists('djdsitepostcontent', $_POST)) {
					$my_post['post_content'] = $_POST['djdsitepostcontent'];
				}
				if( array_key_exists('djd_site_post_excerpt', $_POST)) {
					$my_post['post_excerpt'] = wp_strip_all_tags($_POST['djd_site_post_excerpt']);
				}
				if( array_key_exists('djd_site_post_select_category', $_POST)) {
					$ourCategory = 	array($_POST['djd_site_post_select_category']);
				}
				if( array_key_exists('djd_site_post_checklist_category', $_POST)) {
					$ourCategory = 	$_POST['djd_site_post_checklist_category'];
				}
				if( array_key_exists('djd_site_post_new_category', $_POST)) {
					if (!empty( $_POST['djd_site_post_new_category']) ) {
						require_once(WP_PLUGIN_DIR . '/../../wp-admin/includes/taxonomy.php');
						if ($newCatId = wp_create_category(wp_strip_all_tags($_POST['djd_site_post_new_category']))) {
							$ourCategory = 	array($newCatId);
						} else {
							throw new Exception(__('Unable to create new category. Please try again or select an existing category.', 'djd-site-post'));
						}
					}
				}
				
				if ( ! is_user_logged_in() && ! $djd_options['djd-guest-cat-select'] ) {
					$ourCategory = array( $djd_options['djd-guest-cat'] );
				}
				
				$my_post['post_category'] = $ourCategory;

				if ( !empty ($_POST["djd-our-author"])) {
					$my_post['post_author'] =  $_POST["djd-our-author"];
				} else {
					$my_post['post_author'] = $user_verified['djd_user_id'];
				}
	
				if( array_key_exists('djd_site_post_tags', $_POST)) {
					$my_post['tags_input'] = wp_strip_all_tags($_POST['djd_site_post_tags']);
				}
	
				if( $djd_options['djd-publish-status']) {
					$my_post['post_status'] = $djd_options['djd-publish-status'];
				}
				if( array_key_exists('djd-priv-publish-status', $_POST)) {
					$my_post['post_status'] = wp_strip_all_tags($_POST['djd-priv-publish-status']);
				}

				// Insert the post into the database
				$post_success = wp_update_post( $my_post );

				if($post_success === false) {
					$result = "error";
				}
				else {
					$result = "success";
					//if ( 'publish' == $my_post['post_status'] ) do_action('publish_post');
					if (isset($_POST['djd-post-format'])) {
						set_post_format( $post_success, wp_strip_all_tags($_POST['djd-post-format']));
					} else {
						set_post_format( $post_success, wp_strip_all_tags($djd_options['djd-post-format-default']));
					}
				}				

				if( array_key_exists('djd_site_post_guest_name', $_POST)) {
					add_post_meta( $post_success, 'guest_name', wp_strip_all_tags($_POST['djd_site_post_guest_name']), true ) || update_post_meta( $post_success, 'guest_name', wp_strip_all_tags($_POST['djd_site_post_guest_name']) );
				}
				if( array_key_exists('djd_site_post_guest_email', $_POST)) {
					add_post_meta( $post_success, 'guest_email', wp_strip_all_tags($_POST['djd_site_post_guest_email']), true ) || update_post_meta( $post_success, 'guest_name', wp_strip_all_tags($_POST['djd_site_post_guest_name']) );
				}
				
				if(apply_filters('form_abort_on_failure', true, $form_name))
					$success = $post_success;
				if($success){
					if($djd_options['djd-mail']) {
						$this->djd_sendmail($post_success, wp_strip_all_tags($_POST['djd_site_post_title']));
					}
					if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
						echo $result;
					} else {
						setcookie('form_ok', 1,  time() + 10, '/');
						header("Location: ".$_SERVER["HTTP_REFERER"]);
						die();
					}
				}
				else {
					throw new Exception( $djd_options['djd-post-fail'] ? $djd_options['djd-post-fail'] : __('We were unable to accept your post at this time. Please try again. If the problem persists tell the site owner.', 'djd-site-post'));
				}
		} // isset($_POST)
		die();
	} //function process_site_post_form
	
	/**
	 * Notify admin about new post via email
	 */
	function djd_sendmail ($post_id, $post_title) {
		$blogname = get_option('blogname');
		$email = get_option('admin_email');
		$headers = "MIME-Version: 1.0\r\n" . "From: ".$blogname." "."<".$email.">\n" . "Content-Type: text/HTML; charset=\"" . get_option('blog_charset') . "\"\r\n";
		$content = '<p>'.__('New post submitted from frontend to', 'djd-site-post').' '.$blogname.'.'.'<br/>' .__('To view the entry click here:', 'djd-site-post') . ' '.'<a href="'.get_permalink($post_id).'"><strong>'.$post_title.'</strong></a></p>';
		wp_mail($email, __('New frontend post to', 'djd-site-post') . ' ' . $blogname . ': ' . $post_title, $content, $headers);
	}
	
	/**
	 * Print the post form at the front end
	 */
	function site_post_form($attrs, $content = null, $verified_user, $djd_post_id, $called_from_widget){
		ob_start();
		global $current_user; //Global WordPress variable that stores what get_currentuserinfo() returns.
		get_currentuserinfo();
		$djd_options = get_option('djd_site_post_settings'); //Read the plugin's settings out of wpdb table wp_options.

		// Render the form html
		
		include_once (sprintf("%s/views/display.php", dirname(__FILE__)));

		$ret = ob_get_contents();
		ob_end_clean();
		return $ret;
	}

	/**
	* Send debug code to the Javascript console
	*/
	function dtc($data) {
		if(is_array($data) || is_object($data))
		{
			echo("<script>console.log('PHP: ".json_encode($data)."');</script>");
		} else {
			echo("<script>console.log('PHP: ".$data."');</script>");
		}
	}

    } // end class
} // end if (!class_exists)

$djd_site_post = new DjdSitePost();
