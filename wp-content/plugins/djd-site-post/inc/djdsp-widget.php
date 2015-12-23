<?php
/**
 * Makes a custom Widget for displaying a post form in the seidebar
 *
 *
 * @package DJD Site Post
 * @since DJD Site Post 0.6
*/
class DJD_Site_Post_Widget extends WP_Widget {

	/**
	 * Constructor
	 *
	 * @return void
	 **/
	function djd_site_post_widget() {
		$widget_ops = array( 'classname' => 'djd_site_post_widget', 'description' => __( 'Use this widget to include a form to write and publish articles from the sidebar.', 'djd-site-post' ) );
		$this->WP_Widget( 'djd_site_post_widget', __( 'DJD Site Post Widget', 'djd-site-post' ), $widget_ops );
		$this->alt_option_name = 'djd_site_post_widget';

		add_action( 'save_post', array(&$this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array(&$this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array(&$this, 'flush_widget_cache' ) );
	}

	/**
	 * Outputs the HTML for this widget.
	 *
	 * @param array An array of standard parameters for widgets in this theme
	 * @param array An array of settings for this widget instance
	 * @return void Echoes it's output
	 **/
	function widget( $args, $instance ) {
		$cache = wp_cache_get( 'djd_site_post_widget', 'widget' );

		if ( !is_array( $cache ) )
			$cache = array();

		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = null;

		if ( isset( $cache[$args['widget_id']] ) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		ob_start();
		extract( $args, EXTR_SKIP );

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'DJD Site Post', 'djd-site-post' ) : $instance['title'], $instance, $this->id_base);

		echo $before_widget;
		echo $before_title;
		echo $title; // Can set this with a widget option, or omit altogether
		echo $after_title;

		echo do_shortcode('[djd-site-post called_from_widget = "1"]');

		echo $after_widget;

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set( 'djd_site_post_widget', $cache, 'widget' );
	}

	/**
	 * Deals with the settings when they are saved by the admin. Here is
	 * where any validation should be dealt with.
	 **/
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset( $alloptions['djd_site_post_widget'] ) )
			delete_option( 'djd_site_post_widget' );

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete( 'djd_site_post_widget', 'widget' );
	}

	/**
	 * Displays the form for this widget on the Widgets page of the WP Admin area.
	 **/
	function form( $instance ) {
		$title = isset( $instance['title']) ? esc_attr( $instance['title'] ) : '';
		?>
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'djd-site-post' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>
		<?php
	}
}