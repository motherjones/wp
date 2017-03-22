<?php
/**
 * Blog Pager
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */
class mj_blog_pager extends WP_Widget {

	function __construct() {
		/* Widget settings. */
		$widget_ops = array(
			'classname' 	=> 'mj-blog-pager',
			'description' 	=> __( 'Display next/prev post links at the bottom of blog posts.', 'mj' ),
		);

		/* Create the widget. */
		parent::__construct( 'mj-blog-pager-widget', __( 'Blog Pager', 'mj' ), $widget_ops );
	}

	/**
	 * Render the widget output
	 */
	function widget( $args, $instance ) {
		global $post;

		// Bail if this isn't a blog post.
		if ( ! mj_is_article_type( 'blogpost', $post->ID ) ) {
			return;
		}
		?>
		<ul id="prev-next">
			<li class="previous">
				<?php echo previous_post_link(
					' <span class="label">Previous:</span> %link',
					'%title',
					true,
					' ',
					'mj_blog_type' );
				?>
			</li>
			<li class="next">
				<?php echo next_post_link(
					' <span class="label">Next:</span> %link',
					'%title',
					true,
					' ',
					'mj_blog_type' );
				?>
			</li>
		</ul>
		<?php
	}

	/**
	 * Widget update function: sanitizes title.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		return $instance;
	}

	/**
	 * No options for this widget.
	 */
	function form( $instance ) {
		return true;
	}
}
