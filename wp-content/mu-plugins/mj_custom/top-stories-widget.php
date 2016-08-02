<?php
class top_stories_widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			// Base ID of your widget
			'top_stories_widget',

			// Widget name will appear in UI
			__('Top Stories Widget', 'mj_top_stories'),

			// Widget description
			array( 'description' => __( 'Display the top stories set in zoninator', 'mj_top_stories' ), )
		);
	}

	// Creating widget front-end
	// This is where the action happens
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		// before and after widget arguments are defined by themes
		echo $args['before_widget'];

		// This is where you run the code and display the output

		print '<div class="top-stories">';
		print '<h2 class="promo">Top Stories</h2>';
		print '<ul class="article-list">';
    $posts = z_get_zone_query('top_stories', array(
			'posts_per_page' => 3,
    ));
    $posts = new WP_Query( Array (
      'name' => 'blog-post-no-category',
      'post_type' => 'mj_blogpost',
      'tax_query' => Array ( Array (
        'taxonomy' => 'mj_blog_type',
        'field' => 'slug',
        'terms' => 'kevin-drum' ) ) 
    ));
		while ( $posts->have_posts() ) : $posts->the_post(); ?>
				<li class="article-item">
					<h3 class="hed">
						<a href="<?php print esc_url( get_permalink() ); ?>">
							<?php the_title(); ?>
						</a>
					</h3>
					<p class="byline">
					 <?php print mj_byline( get_the_ID() ); ?>
					</p>
				</li>
<?php
		endwhile;
		print '</ul></div>';
		echo $args['after_widget'];
	}

	// Widget Backend
	public function form( $instance ) {
?>
<p>
	Sorry. Nothing to configure here
</p>
<?php
	}

	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		return $new_instance;
	}
} // Class wpb_widget ends here

// Register and load the widget
function mj_top_stories_load_widget() {
	register_widget( 'top_stories_widget' );
}
add_action( 'widgets_init', 'mj_top_stories_load_widget' );

?>
