<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://nerds.inn.org
 * @since      1.0.0
 *
 * @package    Largo_Related_Posts
 * @subpackage Largo_Related_Posts/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Largo_Related_Posts
 * @subpackage Largo_Related_Posts/admin
 * @author     INN Nerds <nerds@inn.org>
 */
class Largo_Related_Posts_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Largo_Related_Posts_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Largo_Related_Posts_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( 'jquery-ui-autocomplete', '', array( 'jquery-ui-widget', 'jquery-ui-position' ), '1.8.6' );

	}

	/**
	 * Add javascript to trigger ajax search for manual related posts
	 *
	 * @since    1.0.0
	 */
	public function related_posts_ajax_js() {
		?>
		<script type="text/javascript">
			var se_ajax_url = '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>';

			jQuery(document).ready(function($) {

				$('input#se_search_element_id').autocomplete({
					source: se_ajax_url + '?action=related_posts_ajax_search',
					select: function (event, ui) {

						// Reset the search value
						$("input#se_search_element_id").val('');

						// Add the selected search term to the list below
						$("#related-posts-saved ul").append("<li data-id='" + ui.item.value + "' data-title='" + ui.item.label + "'><a href='" + ui.item.permalink + "'>" + ui.item.label + "</a> | <a class='remove-related'>Remove</a></li>");

						// Select all items in the list
						var optionTexts = [];
						$("#related-posts-saved ul li").each(function() { optionTexts.push( [ $(this).attr('data-id'), $(this).attr('data-title') ] ) });

						// Save the list in it's current state
						jQuery.post( ajaxurl, {
							action: 'related_posts_ajax_save',
							data: optionTexts,
							post_id: $('#post_ID').val(),
							largo_related_posts_nonce:  $('#largo_related_posts_nonce').attr('value'),
						});
						return false;
					}
				});

				$('#related-posts-saved').on( "click", '.remove-related', function(evt) {

					// Select all items in the list
					var optionTexts = [];
					$("#related-posts-saved ul li").each(function() { optionTexts.push( [ $(this).attr('data-id'), $(this).attr('data-title') ] ) });

						// Save the list without the new item
					$.post(ajaxurl, {
						action: 'related_posts_ajax_save',
						data:  optionTexts,
						post_id: $('#post_ID').val(),
						remove: jQuery(this).parent().attr("data-id"),
						largo_related_posts_nonce:  $('#largo_related_posts_nonce').attr('value'),
					});
					$(this).parent().remove();
				});

			});
		</script>
		<?php
	}

	/**
	 * Perform ajax search using jQuery Autocomplete
	 *
	 * @since    1.0.0
	 */
	public function related_posts_ajax_search() {
		global $wpdb;
		$search = like_escape( $_REQUEST['term'] );
		$post_types = apply_filters( 'largo_related_posts_types', array( 'post' ) );
		$post_statuses = apply_filters( 'largo_related_posts_statuses', array( 'publish', 'draft', 'future' ) );

		$query =
		'
		SELECT post_title, ID
		FROM wp_posts
		WHERE post_title LIKE \'%' . $search . '%\'
			AND `post_status` IN ("' . implode( '", "', $post_statuses ) . '")
			AND `post_type` IN ("' . implode( '", "', $post_types ) . '")
		ORDER BY ID DESC
		LIMIT 50
		';

		$suggestions = array();

		foreach ( $wpdb->get_results( $query ) as $row ) {
			$suggestion['value'] = $row->ID;
			$suggestion['label'] = $row->post_title;
			$suggestion['permalink'] = get_permalink( $row->ID );
			$suggestions[] = $suggestion;
		}

		$response = wp_json_encode( $suggestions );
		echo $response;
		die();
	}

	/**
	 * Perform ajax save
	 *
	 * @since    1.0.0
	 */
	public function related_posts_ajax_save() {

		// Verify form submission is coming from WordPress using a nonce
		if ( ! isset( $_POST['largo_related_posts_nonce'] ) || ! wp_verify_nonce( $_POST['largo_related_posts_nonce'], basename( __FILE__ ) ) ) {
			return;
		}

		$data = array();
		foreach ( $_POST['data'] as $item ) {

			// Skip over removed item, if set.
			if ( isset( $_POST['remove'] ) && $item[0] == $_POST['remove'] ) {
				continue;
			} else {
				$data[] = $item[0];
			}

		}

		update_post_meta( $_POST['post_id'], 'mj_related_articles', $data );
		die();
	}

	/**
	 * Register the related posts metabox
	 *
	 * @since    1.0.0
	 */
	public function largo_add_related_posts_meta_box() {
		add_meta_box(
			'largo_related_posts',
			__( 'Related Posts', 'largo' ),
			array( $this, 'largo_related_posts_meta_box_display' ),
			'post',
			'side',
			'core'
		);
	}

	/**
	 * Related posts metabox callback
	 *
	 * Allows the user to set custom related posts for a post.
	 *
	 * @param object $post the post.
	 */
	public function largo_related_posts_meta_box_display( $post ) {

		// Make sure the form request comes from WordPress.
		wp_nonce_field( basename( __FILE__ ), 'largo_related_posts_nonce' );

		$value = get_post_meta( $post->ID, 'largo_custom_related_posts', true );

		echo esc_html__( 'Start typing to search by post title.', 'mj' ) . '</p>';
		echo '<input type="text" id="se_search_element_id" name="se_search_element_id" value="" />';

		echo '<div id="related-posts-saved">';
			echo '<ul>';
			$related_posts = get_post_meta( $post->ID, 'mj_related_articles', true );
			if ( $related_posts ) {
				foreach ( $related_posts as $related_post ) {
					$title = get_the_title( $related_post );
					$link = get_permalink( $related_post );
					echo '<li data-id="' . esc_attr( $related_post ) . '" data-title="' . esc_html( $title ) . '"><a href="' . esc_url( $link ) . '">' . esc_html( $title ) . '</a> | <a class="remove-related">Remove</a></li>';
				}
			}
			echo '</ul>';
		echo '</div>';

		do_action( 'largo_related_posts_metabox' );
	}

}
