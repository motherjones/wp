<?php
/**
 * The Largo Related class.
 * Used to dig through posts to find IDs related to the current post
 */
class Largo_Related {

	var $number;
	var $post_id;
	var $post_ids = array();
	var $post;

	/**
	 * Constructor.
	 * Sets up essential parameters for retrieving related posts
	 *
	 * @access public
	 *
	 * @param integer $number optional The number of post IDs to fetch. Defaults to 1
	 * @param integer $post_id optional The ID of the post to get related posts about. If not provided, defaults to global $post
	 * @return null
	 */
	function __construct( $number = 1, $post_id = '' ) {

		if ( ! empty( $number ) ) {
			$this->number = $number;
		}

		if ( ! empty( $post_id ) ) {
			$this->post_id = $post_id;
		} else {
			$this->post_id = get_the_ID();
		}

		$this->post = get_post($this->post_id);
	}

	/**
	 * Array sorter for organizing terms by # of posts they have
	 *
	 * @param object $a First WP term object
	 * @param object $b Second WP term object
	 * @return integer
	 */
	function popularity_sort( $a, $b ) {
		if ( $a->count == $b->count ) return 0;
		return ( $a->count < $b->count ) ? -1 : 1;
	}

	/**
	 * Performs cleanup of IDs list prior to returning it. Also applies a filter.
	 *
	 * @access protected
	 *
	 * @return array The final array of related post IDs
	 */
	protected function cleanup_ids() {
		//make things unique just to be safe
		$ids = array_unique( $this->post_ids );

		//truncate to desired length
		$ids = array_slice( $ids, 0, $this->number );

		//run filters
		return apply_filters( 'largo_related_posts', $ids );
	}

	/**
	 * Fetches posts contained within the series(es) this post resides in. Feeds them into $this->post_ids array
	 *
	 * @access protected
	 * @see largo_series_custom_order
	 */
	protected function get_series_posts() {

		//try to get posts by series, if this post is in a series
		$series = get_the_terms( $this->post_id, 'series' );

		if ( is_array($series) ) {

			//loop thru all the series this post belongs to
			foreach ( $series as $term ) {
				//start to build our query of posts in this series
				// get the posts in this series, ordered by rank or (if missing?) date
				$args = array(
					'post_type' => 'post',
					'posts_per_page' => $this->number,
					'taxonomy' => 'series',
					'term' => $term->slug,
					'orderby' => 'date',
					'order' => 'ASC',
					'ignore_sticky_posts' => 1,
					'date_query' => array(
						'after' => $this->post->post_date,
					),
				);

				// see if there's a post that has the sort order info for this series
				$cftl_query = new WP_Query( array(
					'post_type' => 'cftl-tax-landing',
					'tax_query' => array (
						'series' => $term->slug,
					),
					'posts_per_page' => 1
				));

				if ( $cftl_query->have_posts() ) {
					$cftl_query->next_post();
					$has_order = get_post_meta( $cftl_query->post->ID, 'post_order', TRUE );
					if ( !empty($has_order) ) {
						switch ( $has_order ) {
							case 'ASC':
								$args['order'] = 'ASC';
								break;
							// 'series_custom' and 'featured' are custom ones, caught with largo_series_custom_order in inc/wp-taxonomy-landing/functions/cftl-series-order.php
							case 'custom':
								$args['orderby'] = 'series_custom';
								break;
							case 'featured, DESC':
							case 'featured, ASC':
								$args['orderby'] = $has_order;
								break;
						}
					}
				}

				// build the query with the sort defined
				$series_query = new WP_Query( $args );

				// If not enough posts were added from after this post, look before this post
				if ( count($series_query->posts) < $this->number ) {

					// Store the returned posts from the after query
					$this->add_from_query( $series_query );

					// Change it to look backwards
					$args['date_query'] = array(
						'before' => $this->post->post_date,
					);

					// rerun the query
					$series_query = new WP_Query( $args );
				}

				// Store the posts
				if ( $series_query->have_posts() ) {
					$this->add_from_query( $series_query );
					if ( $this->have_enough_posts() ) {
						break;
					}
				}
			}
		}
	}

	/**
	 * Fetches posts contained within the categories and tags this post has. Feeds them into $this->post_ids array
	 *
	 * @access protected
	 */
	protected function get_term_posts() {

		//we've gone back and forth through all the post's series, now let's try traditional taxonomies
		$taxonomies = get_the_terms( $this->post_id, array('category', 'post_tag') );

		//loop thru taxonomies, much like series, and get posts
		if ( is_array($taxonomies) ) {
			//sort by popularity
			usort( $taxonomies, array(__CLASS__, 'popularity_sort' ) );

			foreach ( $taxonomies as $term ) {
				$args = array(
					'post_type' => 'post',
					'posts_per_page' => $this->number,
					'orderby' => 'date',
					'order' => 'ASC',
					'ignore_sticky_posts' => 1,
					'date_query' => array(
						'after' => $this->post->post_date,
					),
					'tax_query' => array(
						array(
							'taxonomy' => $term->taxonomy,
							'terms' => $term->slug,
							'field' => 'slug',
						)
					)
				);

				// run the query
				$term_query = new WP_Query( $args );

				// If not enough posts were added from after this post, look before this post
				if ( count($term_query->posts) < $this->number ) {

					// Store the returned posts from the after query
					$this->add_from_query( $term_query );

					// Change it to look backwards
					$args['date_query'] = array(
						'before' => $this->post->post_date,
					);

					// rerun the query
					$term_query = new WP_Query( $args );
				}

				// Store the returned posts
				if ( $term_query->have_posts() ) {
					$this->add_from_query( $term_query );
					if ( $this->have_enough_posts() ) {
						break;
					}
				}
			} // foreach
		}
	}

	/**
	 * Fetches recent posts. Used as a fallback when other methods have failed to fill post_ids to requested length
	 *
	 * @access protected
	 */
	protected function get_recent_posts() {

		$args = array(
			'post_type' => 'post',
			'posts_per_page' => $this->number,
			'post__not_in' => array( $this->post_id ),
		);

		$posts_query = new WP_Query( $args );

		if ( $posts_query->have_posts() ) {
			$this->add_from_query($posts_query);
		}
	}

	/**
	 * Loops through series, terms and recent to fill array of related post IDs. Primary means of using this class.
	 *
	 * @access public
	 *
	 * @return array An array of post ids related to the given post
	 */
	public function ids() {

		// see if this post has manually set related posts
		$post_ids = get_post_meta( $this->post_id, 'manual_related_posts', true );
		if ( ! empty( $post_ids ) ) {
			foreach ( $post_ids as $id => $post_title ) {
				$ids[] = $id;
			}
			$this->post_ids = $ids;
			if ( $this->have_enough_posts() ) {
				return $this->cleanup_ids();
			}
		}

		$this->get_series_posts();
		//are we done yet?
		if ( $this->have_enough_posts() ) return $this->cleanup_ids();

		$this->get_term_posts();
		//are we done yet?
		if ( $this->have_enough_posts() ) return $this->cleanup_ids();

		$this->get_recent_posts();
		return $this->cleanup_ids();
	}

	/**
	 * Takes a WP_Query result and adds the IDs to $this->post_ids
	 *
	 * @access protected
	 *
	 * @param object a WP_Query object
	 * @param boolean optional whether the query post order has been reversed yet. If not, this will loop through in both directions.
	 */
	protected function add_from_query( $q, $reversed = FALSE ) {
		// don't pick up anything until we're past our own post
		$found_ours = FALSE;

		while ( $q->have_posts() ) {
			$q->the_post();
			// add this post if it's new
			if ( ! in_array( $q->post->ID, $this->post_ids ) ) {	// only add it if it wasn't already there
				$this->post_ids[] = (int) trim($q->post->ID);
				// stop if we have enough
				if ( $this->have_enough_posts() ) return;
			}
		}
	}

	/**
	 * Counts to see if enough posts have been found
	 */
	protected function have_enough_posts() {
		if ( count( $this->post_ids ) >= $this->number )
			return true;

		return false;
	}
}
