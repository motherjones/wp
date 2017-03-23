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
		} elseif ( function_exists( 'coauthors_posts_links' ) ) {
			return coauthors_posts_links( ', ', null, null, null, false );
		} else {
			return the_author_posts_link();
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

function mj_flat_email_button( $id ) {
	return sprintf(
		'<a href="mailto:?subject=%1$s&body=%2$s%0D%0A%3$s" target="_blank">
			<i class="fa fa-envelope-o"></i>
			<span class="share-text">Email</span>
		</a>',
		rawurlencode( html_entity_decode( get_the_title( $id ), ENT_QUOTES, 'UTF-8' ) ), // subject.
		rawurlencode( html_entity_decode( strip_tags( get_the_excerpt( $id ) ), ENT_QUOTES, 'UTF-8' ) ), // description.
		rawurlencode( html_entity_decode( get_the_permalink( $id ) ) ) // url.
	);
}

function mj_flat_print_button() {
	return
		'<a href="#" onclick="window.print()" title="' . esc_attr( __( 'Print this article', 'mj' ) ) . '" rel="nofollow">
	 		<i class="fa fa-print"></i>
			<span class="share-text">' . esc_attr( __( 'Print', 'mj' ) ) . '</span>
		</a>';
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
				<li class="twitter">%s</li>',
		esc_attr( $classes ),
		mj_flat_facebook_button( $id ),
		mj_flat_twitter_button( $id )
	);
	if ( 'bottom' !== $context ) {
		printf(
			'<li class="email">%s</li>',
			mj_flat_email_button( $id )
		);
	}
	if ( 'top' === $context ) {
		printf(
			'<li class="print">%s</li>',
			mj_flat_print_button()
		);
	}
	echo '</ul></div>';
}
