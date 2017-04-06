<?php
/**
 * Author Bio Widget
 *
 * @package    WordPress
 * @subpackage Mother_Jones
 * @since      Mother Jones 1.0
 */
class mj_author_bio_widget extends WP_Widget
{

    function __construct() 
    {
        /* Widget settings. */
        $widget_ops = array(
         'classname'     => 'mj-author-bio',
         'description'     => __('Display bio and contact info for author(s) on a single post.', 'mj'),
        );

        /* Create the widget. */
        parent::__construct('mj-author-bio-widget', __('Author Bio', 'mj'), $widget_ops);
    }

    /**
     * Render the widget output
     */
    function widget( $args, $instance ) 
    {

        global $post;
        extract($args);

        if (is_singular() || is_author() ) {
            if (is_singular() ) {
                if (function_exists('get_coauthors') ) {
                    $authors = get_coauthors(get_queried_object_id());
                } else {
                    $authors = array( get_user_by('id', get_queried_object()->post_author) );
                }
            } else {
                $authors = array( get_queried_object() );
            }
            if (! empty($authors) ) {
                echo $before_widget;
                if (is_singular() ) {
                    echo '<ul class="author-bios article end group">';
                }
                foreach ( $authors as $author ) { ?>
                    <li class="author-bio group vcard">
                        <?php
                        if (wp_get_attachment_image($author->mj_author_image_id, 96) ) {
                            printf(
                                '<div class="author-image">%s</div>',
                                wp_get_attachment_image($author->mj_author_image_id)
                            );
                        }
                        echo '<div class="author-data">';
                        if (is_author() ) {
                            echo '<span class="byline"><span class="fn n">' . esc_html($author->display_name) . '</span>';
                        } else {
                            printf(
                                __('<span class="byline"><span class="fn n"><a class="url" href="%1$s" rel="author" title="See all posts by %2$s">%2$s</a></span></h3>', 'mj'),
                                esc_url(get_author_posts_url($author->ID, $author->user_nicename)),
                                esc_attr($author->display_name)
                            );
                        }
                        if ($twitter = $author->mj_user_twitter ) {
                            $twitter_url = 'https://twitter.com/' . twitter_url_to_username($twitter);
                            printf(
                                '<a class="social-icon" href="%s"><i class="fa fa-twitter fw"></i></a>',
                                esc_url($twitter_url)
                            );
                        }
                        echo '</span>';
                        if (is_author() ) {
                            echo '<p class="author-bio-text">' . esc_html($author->mj_user_full_bio) . '</p>';
                        } else {
                            echo '<p class="author-bio-text">' . esc_html($author->description) . '</p>';
                        }
                        echo '</div>'; // author-data.
                        ?>

                    </li>
                    <?php
                }
                if (is_singular() ) {
                    echo '</ul>';
                }
                echo $after_widget;
            }
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
