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
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/largo-related-posts-admin.css', array(), $this->version, 'all' );

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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/largo-related-posts-admin.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script('jquery-ui-autocomplete', '', array('jquery-ui-widget', 'jquery-ui-position'), '1.8.6');

	}

	/**
	 * Add javascript to trigger ajax search for manual related posts 
	 *
	 * @since    1.0.0
	 */
	public function related_posts_ajax_js() {
		?>
		<script type="text/javascript">
			var se_ajax_url = '<?php echo admin_url('admin-ajax.php'); ?>';

			jQuery(document).ready(function($) {
			
				$('input#se_search_element_id').autocomplete({
					source: se_ajax_url + '?action=related_posts_ajax_search',
					select: function (event, ui) {

						// Reset the search value
						$("input#se_search_element_id").val('');

						// Add the selected search term to the list below
						$("#related-posts-saved ul").append("<li data-id='" + ui.item.value + "' data-title='" + ui.item.label + "'>" + ui.item.label + " | <a class='remove-related'>Remove</a></li>");

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
		$search = like_escape($_REQUEST['term']);
		$post_types = apply_filters( 'largo_related_posts_types', array( 'post' ) );

		$query = 'SELECT post_title, ID FROM wp_posts
		WHERE post_title LIKE \'%' . $search . '%\'
		AND `post_status` LIKE \'publish\'
		AND `post_type` IN ("' . implode( '", "', $post_types ) . '")';

		$suggestions = array();

		foreach ($wpdb->get_results($query) as $row) {
			$suggestion['value'] = $row->ID;
			$suggestion['label'] = $row->post_title;
			
			$suggestions[] = $suggestion;
		}

		$response = json_encode( $suggestions );
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
		if ( !isset( $_POST['largo_related_posts_nonce'] ) || !wp_verify_nonce( $_POST['largo_related_posts_nonce'], basename( __FILE__ ) ) ){
			return;
		}

		$data = array();
		foreach ( $_POST['data'] as $item ) {

			// Skip over removed item, if set
			if ( isset( $_POST['remove'] ) && $item[0] == $_POST['remove'] ) {
				continue;
			} else {
				// post_id as key, post title as value
				$data[$item[0]] = esc_html( $item[1] );
			}

		}

		update_post_meta( $_POST['post_id'], 'manual_related_posts', $data );
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
	 * @global $post
	 */
	public function largo_related_posts_meta_box_display( $post ) {

		// make sure the form request comes from WordPress
		wp_nonce_field( basename( __FILE__ ), 'largo_related_posts_nonce' );

		$value = get_post_meta( $post->ID, 'largo_custom_related_posts', true );

		echo '<p><strong>' . __('Related Posts', 'largo') . '</strong><br />';
		echo __('To override the default related posts functionality,  enter post titles to manually select below.') . '</p>';
		echo '<input type="text" id="se_search_element_id" name="se_search_element_id" value="" />';

		echo '<div id="related-posts-saved">';
			echo '<ul>';
				$manual_related_posts = get_post_meta( $post->ID, 'manual_related_posts', true );

				if ( $manual_related_posts ) {
					foreach ( $manual_related_posts as $key => $title ) {
						echo '<li data-id="' . $key . '" data-title="' . $title . '">' . $title . ' | <a class="remove-related">Remove</a></li>';
					}
				}	
			echo '</ul>';
		echo '</div>';

		do_action( 'largo_related_posts_metabox' );
	}


}
