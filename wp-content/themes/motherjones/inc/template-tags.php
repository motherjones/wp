<?php
/**
 * Custom Mother Jones template tags
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

if ( ! function_exists( 'mj_entry_meta' ) ) {
/**
 * Prints HTML with meta information for the categories, tags.
 *
 * Create your own mj_entry_meta() function to override in a child theme.
 *
 * @since Mother Jones 1.0
 */
	function mj_entry_meta() {
		if ( 'post' === get_post_type() ) {
			$author_avatar_size = apply_filters( 'mj_author_avatar_size', 49 );
			printf( '<span class="byline"><span class="author vcard">%1$s<span class="screen-reader-text">%2$s </span> <a class="url fn n" href="%3$s">%4$s</a></span></span>',
				get_avatar( get_the_author_meta( 'user_email' ), $author_avatar_size ),
				_x( 'Author', 'Used before post author name.', 'mj' ),
				esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
				get_the_author()
			);
		}

		if ( in_array( get_post_type(), array( 'post', 'attachment' ) ) ) {
			mj_entry_date();
		}

		$format = get_post_format();
		if ( current_theme_supports( 'post-formats', $format ) ) {
			printf( '<span class="entry-format">%1$s<a href="%2$s">%3$s</a></span>',
				sprintf( '<span class="screen-reader-text">%s </span>', _x( 'Format', 'Used before post format.', 'mj' ) ),
				esc_url( get_post_format_link( $format ) ),
				get_post_format_string( $format )
			);
		}

		if ( 'post' === get_post_type() ) {
			mj_entry_taxonomies();
		}

	}
}

if ( ! function_exists( 'mj_entry_date' ) ) {
/**
 * Prints HTML with date information for current post.
 *
 * Create your own mj_entry_date() function to override in a child theme.
 *
 * @since Mother Jones 1.0
 */
	function mj_entry_date() {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';

		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf( $time_string,
			esc_attr( get_the_date( 'c' ) ),
			get_the_date(),
			esc_attr( get_the_modified_date( 'c' ) ),
			get_the_modified_date()
		);

		printf( '<span class="posted-on"><span class="screen-reader-text">%1$s </span><a href="%2$s" rel="bookmark">%3$s</a></span>',
			_x( 'Posted on', 'Used before publish date.', 'mj' ),
			esc_url( get_permalink() ),
			$time_string
		);
	}
}

if ( ! function_exists( 'mj_entry_taxonomies' ) ) {
/**
 * Prints HTML with category and tags for current post.
 *
 * Create your own mj_entry_taxonomies() function to override in a child theme.
 *
 * @since Mother Jones 1.0
 */
	function mj_entry_taxonomies() {
		$categories_list = get_the_category_list( _x( ', ', 'Used between list items, there is a space after the comma.', 'mj' ) );
		if ( $categories_list && mj_categorized_blog() ) {
			printf( '<span class="cat-links"><span class="screen-reader-text">%1$s </span>%2$s</span>',
				_x( 'Categories', 'Used before category names.', 'mj' ),
				$categories_list
			);
		}

		$tags_list = get_the_tag_list( '', _x( ', ', 'Used between list items, there is a space after the comma.', 'mj' ) );
		if ( $tags_list ) {
			printf( '<span class="tags-links"><span class="screen-reader-text">%1$s </span>%2$s</span>',
				_x( 'Tags', 'Used before tag names.', 'mj' ),
				$tags_list
			);
		}
	}
}

if ( ! function_exists( 'mj_post_thumbnail' ) ) {
/**
 * Displays an optional post thumbnail.
 *
 * Wraps the post thumbnail in an anchor element on index views, or a div
 * element when on single views.
 *
 * Create your own mj_post_thumbnail() function to override in a child theme.
 *
 * @since Mother Jones 1.0
 */
	function mj_post_thumbnail() {
		if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
			return;
		}

		if ( is_singular() ) :
		?>

		<div class="post-thumbnail">
			<?php the_post_thumbnail(); ?>
		</div><!-- .post-thumbnail -->

		<?php else : ?>

		<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true">
			<?php the_post_thumbnail( 'post-thumbnail', array( 'alt' => the_title_attribute( 'echo=0' ) ) ); ?>
		</a>

		<?php endif; // End is_singular()
	}
}

if ( ! function_exists( 'mj_excerpt' ) ) {
	/**
	 * Displays the optional excerpt.
	 *
	 * Wraps the excerpt in a div element.
	 *
	 * Create your own mj_excerpt() function to override in a child theme.
	 *
	 * @since Mother Jones 1.0
	 *
	 * @param string $class Optional. Class string of the div element. Defaults to 'entry-summary'.
	 */
	function mj_excerpt( $class = 'entry-summary' ) {
		$class = esc_attr( $class );

		if ( has_excerpt() || is_search() ) : ?>
			<div class="<?php echo $class; ?>">
				<?php the_excerpt(); ?>
			</div><!-- .<?php echo $class; ?> -->
		<?php endif;
	}
}

if ( ! function_exists( 'mj_excerpt_more' ) && ! is_admin() ) {
/**
 * Replaces "[...]" (appended to automatically generated excerpts) with ... and
 * a 'Continue reading' link.
 *
 * Create your own mj_excerpt_more() function to override in a child theme.
 *
 * @since Mother Jones 1.0
 *
 * @return string 'Continue reading' link prepended with an ellipsis.
 */
	function mj_excerpt_more() {
		$link = sprintf( '<a href="%1$s" class="more-link">%2$s</a>',
			esc_url( get_permalink( get_the_ID() ) ),
			/* translators: %s: Name of current post */
			sprintf( __( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'mj' ), get_the_title( get_the_ID() ) )
		);
		return ' &hellip; ' . $link;
	}
	add_filter( 'excerpt_more', 'mj_excerpt_more' );
}


/**
 * Determines whether blog/site has more than one category.
 *
 * Create your own mj_categorized_blog() function to override in a child theme.
 *
 * @since Mother Jones 1.0
 *
 * @return bool True if there is more than one category, false otherwise.
 */
function mj_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'mj_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'mj_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so mj_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so mj_categorized_blog should return false.
		return false;
	}
}



if ( ! function_exists( 'mj_post_metadata' ) ) {
	/**
	 * Schema.org article metadata we include in the header of each single post
	 *
	 * @param int  $post_id the post ID.
	 * @param bool $echo return the output or echo it.
	 */
	function mj_post_metadata( $post_id, $echo = true ) {
		$out = '<meta itemprop="description" content="' . strip_tags( get_the_excerpt() ) . '" />' . "\n";
		$out .= '<meta itemprop="datePublished" content="' . get_the_date( 'c', $post_id ) . '" />' . "\n";
		$out .= '<meta itemprop="dateModified" content="' . get_the_modified_date( 'c', $post_id ) . '" />' . "\n";
		if ( has_post_thumbnail( $post_id ) ) {
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'thumbnail' );
			$out .= '<meta itemprop="image" content="' . $image[0] . '" />';
		}
		if ( $echo ) {
			echo $out;
		} else {
			return $out;
		}
	}
}


/**
 * Flushes out the transients used in mj_categorized_blog().
 *
 * @since Mother Jones 1.0
 */
function mj_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'mj_categories' );
}
add_action( 'edit_category', 'mj_category_transient_flusher' );
add_action( 'save_post',     'mj_category_transient_flusher' );

if ( ! function_exists( 'mj_byline' ) ) {
	/**
	 * Create our bylines.
	 *
	 * @param int $id the post ID.
	 */
	function mj_byline( $id ) {
		$override = get_post_meta( $id, 'mj_byline_override', true );
		if ( trim( $override ) ) {
			$output = wp_kses(
				$override,
				array(
					'a' => array(
						'href' => array(),
						'title' => array(),
					),
					'br' => array(),
					'em' => array(),
					'strong' => array(),
				)
			);
			return $output;
		} else {
			return coauthors_posts_links( ', ', null, null, null, false );
		}
	}
}


if ( ! function_exists( 'mj_dateline' ) ) {
	/**
	 * Create our datelines.
	 *
	 * @param int $id the post ID.
	 */
	function mj_dateline( $id ) {
		$mj_dateline_format = 'M\. j\, Y g\:i A';

		if ( ! $id ) {
			$id = get_the_ID();
		}
		$override = get_post_meta( $id, 'mj_dateline_override', true );
		if ( trim( $override ) ) {
			return $override;
		}
		return get_post_time( $mj_dateline_format );
	}
}

if ( ! function_exists( 'get_disqus_thread' ) ) {
	/**
	 * Output comments.
	 */
	function get_disqus_thread() {
		comments_template( '', true );
	}
}

if ( ! function_exists( 'mj_flat_twitter_button' ) ) {
	/**
	 * Create a twitter button.
	 *
	 * @param int $id the post ID.
	 */
	function mj_flat_twitter_button( $id ) {
		$id = $id ? $id : get_the_ID();
		$social = trim( get_post_meta( $id, 'mj_social_hed', true ) );
		$status = $social ? $social : get_the_title( $id );
		$href = 'http://twitter.com/home?status=' . $status . ' '
			. esc_url( get_permalink( $id ) ) . ' via @MotherJones';
		return sprintf(
			'<a class="social" href="%s" target="_blank">
				<i class="fa fa-twitter fw"></i>
				<span class="share-text">Share on Twitter</span>
			</a>',
			$href
		);
	}
}

if ( ! function_exists( 'mj_flat_facebook_button' ) ) {
	/**
	 * Create a FB button.
	 *
	 * @param int $id the post ID.
	 */
	function mj_flat_facebook_button( $id ) {
		$id = $id ? $id : get_the_ID();
		$href = 'http://facebook.com/sharer.php?u=' . esc_url( get_permalink( $id ) );
		return sprintf(
			'<a class="social" href="%s" target="_blank">
				<i class="fa fa-facebook fw"></i>
				<span class="share-text">Share on Facebook</span>
			</a>',
			$href
		);
	}
}

/**
 * Output the share buttons
 *
 * @param string $context where we're outputting the buttons
 * 	(e.g. - top or bottom for articles.
 */
function mj_share_tools( $context ) {
	$classes = 'social-container group';
	$id = get_the_ID();
	if ( ! empty( $context ) ) {
		$classes .= ' ' . $context;
	}
	printf(
		'<div class="%s">
			<ul>
				<li class="facebook">%s</li>
				<li class="twitter">%s</li>
			</ul>
		</div>',
		esc_attr( $classes ),
		mj_flat_facebook_button( $id ),
		mj_flat_twitter_button( $id )
	);
}
