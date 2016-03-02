<?php

/*
Plugin Name: Custom Archive Widget
Plugin URI:
Description: Adds an archive widget that uses javascript to show or hide months.
Version: 1.0.0
Author: Andreas Jönsson
Author URI: http://consid.se
*/



// Register JavaScript
function ccaw_enqueue_script() {
	$plugins_url = plugins_url('', __FILE__);
	wp_register_script( 
		'custom-archive-widget', 
		$plugins_url . '/js/custom-archive-widget.js', 
		array( 'jquery' )
	);
	wp_enqueue_script( 'custom-archive-widget' );
}
add_action('wp_enqueue_scripts', 'ccaw_enqueue_script');



class custom_archive_widget extends WP_Widget {

	function __construct() {
		parent::__construct(
		// Base ID of your widget
		'custom_archive_widget',

		// Widget name will appear in UI
		__('Anpassat akriv', 'custom_archive_widget_huddinge'),

		// Widget description
		array( 'description' => __( 'En widget som skriver ut akrivet efter år och månader.', 'custom_archive_widget_huddinge' ), )
		);
	}

	// Creating widget front-end
	// This is where the action happens
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if(!empty($title)) echo $args['before_title'] . $title . $args['after_title'];
		?>
		<ul class="list-unstyled list-striped list-levels nested-archive">
			<?php
			/* Get all years that have posts */
			global $wpdb;
			$years = $wpdb->get_col("SELECT DISTINCT YEAR(post_date) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' ORDER BY post_date DESC");
			?>
			<?php foreach($years as $year) : ?>
				<li>
					<a href="<?php echo get_year_link($year); ?> "><?php echo $year; ?></a>
					<ul class="list-child">
						<?php $months = $wpdb->get_col("SELECT DISTINCT MONTH(post_date) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' AND YEAR(post_date) = '".$year."' ORDER BY post_date DESC"); ?>
						<?php foreach($months as $month) : ?>
							<?php $countposts = get_posts("year=$year&monthnum=$month"); ?>
							<li><a href="<?php echo get_month_link($year, $month); ?>"><?php echo strftime( '%B', mktime(0, 0, 0, $month) );?> (<?php echo count($countposts); ?>)</a></li>
						<?php endforeach;?>
					</ul>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php
		echo $args['after_widget'];
	}

	// Widget Backend
	public function form( $instance ) {
		//Set up some default widget settings.
		$defaults = array( 'title' => __('Arkiv', 'example'), 'show_info' => true );
		$instance = wp_parse_args( (array) $instance, $defaults );
		// Widget admin form
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo (isset($instance['title'])) ? $instance['title'] : ''; ?>" />
		</p>
		<?php
	}

	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}

} // Class wpb_widget ends here

// Register and load the widget
function custom_archive_load_widget() {
	register_widget( 'custom_archive_widget' );
}
add_action( 'widgets_init', 'custom_archive_load_widget' );