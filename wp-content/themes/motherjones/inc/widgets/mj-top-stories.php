<?php
class mj_top_stories_widget extends WP_Widget {

	function __construct() {
		/* Widget settings. */
		$widget_ops = array(
			'classname' 	=> 'mj_top_stories_widget',
			'description' 	=> __( 'Display the top stories set in zoninator.', 'mj' ),
		);

		/* Create the widget. */
		parent::__construct( 'mj_top_stories_widget', __( 'Top Stories', 'mj' ), $widget_ops );
	}

	// Creating widget front-end
	// This is where the action happens.
	public function widget( $args, $instance ) {
		extract( $args );
		echo $before_widget;

		echo '<h2><span class="promo">Top News</span></h2>';
		echo '<ul class="article-list">';
		$posts = z_get_zone_query( 'top_stories', array(
			'posts_per_page' => 3,
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
		echo '</ul>';
		echo $after_widget;
	}

	/**
	 * Updating widget replacing old instances with new.
	 */
	public function update( $new_instance, $old_instance ) {
		return $new_instance;
	}

	/**
	 * No options for this widget.
	 */
	function form( $instance ) {
		return true;
	}
}
