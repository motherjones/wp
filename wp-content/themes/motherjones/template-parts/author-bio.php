<?php
/**
 * Author bios.
 * This is used in archive.php for the author profile header.
 * And on single articles at the bottom of the article.
 * Support multiple authors on articles if coauthors plus is enabled.
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

if ( is_singular() || is_author() ) {
	if ( is_singular() ) {
		if ( function_exists( 'get_coauthors' ) ) {
			$authors = get_coauthors( get_queried_object_id() );
		} else {
			$authors = array( get_user_by( 'id', get_queried_object()->post_author ) );
		}
	} else {
		$authors = array( get_queried_object() );
	}
	if ( ! empty( $authors ) ) {
		if ( is_singular() ) {
			echo '<ul class="author-bios article end group">';
		}
		foreach ( $authors as $author ) { ?>
			<li class="author-bio group vcard">
				<div class="author-image"></div>
				<div class="author-data">
				<?php
					if ( is_author() ) {
						echo '<span class="byline"><span class="fn n">' . esc_html( $author->display_name ) . '</span>';
					} else {
						printf( __( '<span class="byline"><span class="fn n"><a class="url" href="%1$s" rel="author" title="See all posts by %2$s">%2$s</a></span></h3>', 'mj' ),
							esc_url( get_author_posts_url( $author->ID, $author->user_nicename ) ),
							esc_attr( $author->display_name )
						);
					}
					if ( $twitter = $author->mj_user_twitter ) {
						$twitter_url = 'https://twitter.com/' . twitter_url_to_username( $twitter );
						printf(
							'<a class="social-icon" href="%s"><i class="fa fa-twitter fw"></i></a>',
							esc_url( $twitter_url )
						);
					}
					echo '</span>';
					if ( is_author() ) {
						echo '<p class="author-bio-text">' . esc_html( $author->mj_user_full_bio ) . '</p>';
					} else {
						echo '<p class="author-bio-text">' . esc_html( $author->description ) . '</p>';
					}
				?>
				</div>
			</li>
		<?php
		}
		if ( is_singular() ) {
			echo '</ul>';
		}
	}
}
