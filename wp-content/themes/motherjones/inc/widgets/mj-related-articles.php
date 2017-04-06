<?php
/**
 * Related Articles Widget
 *
 * @package    WordPress
 * @subpackage Mother_Jones
 * @since      Mother Jones 1.0
 */
class mj_related_articles extends WP_Widget
{

    function __construct() 
    {
        /* Widget settings. */
        $widget_ops = array(
         'classname'     => 'mj-related-articles',
         'description'     => __('Display related articles at the bottom of articles.', 'mj'),
        );

        /* Create the widget. */
        parent::__construct('mj-related-articles-widget', __('Related Articles', 'mj'), $widget_ops);
    }

    /**
     * Render the widget output
     */
    function widget( $args, $instance ) 
    {
        global $post;
        extract($args);

        $related = get_post_meta(get_the_ID(), 'mj_related_articles', true);
        if (! empty($related) ) {
            $related_query = new WP_Query(
                array(
                'post__in' => $related,
                'post_type' => array( 'post' ),
                'post_status' => 'publish',
                'posts_per_page' => 2,
                ) 
            );
            $heading = 'Related';
        } else { // Just show most recent posts.
            $related_query = new WP_Query(
                array(
                'post_type' => array( 'post' ),
                'post_status' => 'publish',
                'posts_per_page' => 2,
                ) 
            );
            $heading = 'The Latest';
        }
        if ($related_query->have_posts() ) {
            echo $before_widget;
            echo '<h2 class="promo">' . esc_html($heading) . '</h2>';
            echo '<ul class="related-articles-list">';
            while ( $related_query->have_posts() ) : $related_query->the_post();
                get_template_part('template-parts/content');
            endwhile;
            echo '</ul>';
            echo $after_widget;
        }
    }

    /**
     * Widget update function: sanitizes title.
     */
    function update( $new_instance, $old_instance ) 
    {
        $instance = $old_instance;
        $instance['title'] = sanitize_text_field($new_instance['title']);
        return $instance;
    }

    /**
     * No options for this widget.
     */
    function form( $instance ) 
    {
        return true;
    }
}
