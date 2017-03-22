<?php
/**
 * All the stuff pertaining to user profiles.
 *
 * FIELDS:
 * - description
 * - mj_user_twitter
 * - mj_user_full_bio
 * - mj_user_position
 * - mj_author_image_id
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

/**
 * Remove capabilities from editors.
 */
function mj_set_capabilities() {
	if ( ! function_exists( 'populate_roles' ) ) {
  	require_once( ABSPATH . 'wp-admin/includes/schema.php' );
	}
	populate_roles();

	global $wp_roles;
	//$wp_roles->remove_cap( 'editor', 'manage_categories' );
}
add_action( 'admin_init', 'mj_set_capabilities' );


/**
 * Modify the user profile screen
 * Remove old contact methods (yahoo, aol and jabber)
 * Add new ones (twitter, facebook, linkedin)
 *
 * @param array $contactmethods the contact methods associated with a user.
 */
function mj_contactmethods( $contactmethods ) {
	$contactmethods['mj_user_twitter'] = 'Twitter';
	return $contactmethods;
}
add_filter( 'user_contactmethods', 'mj_contactmethods' );
// Clean and validate twitter username when user profiles are updated.
add_action( 'edit_user_profile_update', 'clean_user_twitter_username' );
add_action( 'personal_options_update', 'clean_user_twitter_username' );
add_action( 'user_profile_update_errors', 'validate_twitter_username', 10, 3 );

/**
 * Display extra profile fields related to staff member status
 *
 * @param object $user The WP_User object for the current profile.
 */
function mj_user_custom_fields( $user ) {
	?>
	<h3><?php esc_html_e( 'More profile information', 'mj' ); ?></h3>
	<table class="form-table">
		<tr>
			<th><label for="mj_user_position"><?php esc_html_e( 'Position Title', 'mj' ); ?></label></th>
			<td>
				<input type="text" name="mj_user_position" id="mj_user_position" value="<?php echo esc_attr( get_the_author_meta( 'mj_user_position', $user->ID ) ); ?>" class="regular-text" /><br />
				<p class="description"><?php esc_html_e( 'Please enter your position title.', 'mj' ); ?></p>
			</td>
		</tr>
		<tr>
			<th><label for="mj_user_full_bio"><?php esc_html_e( 'Full Bio', 'mj' ); ?></label></th>
			<td>
				<textarea name="mj_user_full_bio" id="mj_user_full_bio" rows="5" cols="30"><?php echo esc_textarea( get_the_author_meta( 'mj_user_full_bio', $user->ID ) ); ?></textarea>
				<p class="description"><?php esc_html_e( 'Please enter a longer bio. This is used on your public author profile page.', 'mj' ); ?></p>
			</td>
		</tr>
		<?php do_action( 'mj_additional_user_profile_fields', $user ); ?>
	</table>
<?php
}
add_action( 'show_user_profile', 'mj_user_custom_fields' );
add_action( 'edit_user_profile', 'mj_user_custom_fields' );

/**
 * Save data from form elements added to profile via `more_profile_info`
 *
 * @param int $user_id The ID of the user for the profile being saved.
 */
function mj_save_user_custom_fields( $user_id ) {
	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return false;
	}
	$fields = array( 'mj_user_position', 'mj_user_full_bio' );

	foreach ( $fields as $field ) {
		update_user_meta( $user_id, $field, $_POST[ $field ] );
	}
}
add_action( 'personal_options_update', 'mj_save_user_custom_fields' );
add_action( 'edit_user_profile_update', 'mj_save_user_custom_fields' );

/**
 * Allow local upload of user avatar images.
 *
 * See also: has_avatar and has_gravatar in inc/helpers.php.
 */
class MJ_Avatar {

	const META_FIELD = 'mj_author_image_id';

	/**
	 * Get this party started.
	 */
	function __construct() {
		add_action( 'get_avatar', array( &$this, 'get_avatar_filter' ), 1, 5 );
		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( &$this, 'load_avatar_js' ) );
			add_action( 'user_edit_form_tag', array( &$this, 'add_edit_form_multipart_encoding' ) );
			add_action( 'edit_user_profile', array( &$this, 'add_avatar_field' ) );
			add_action( 'show_user_profile', array( &$this, 'add_avatar_field' ) );
			add_action( 'edit_user_profile_update', array( &$this, 'save_avatar_field' ) );
			add_action( 'personal_options_update', array( &$this, 'save_avatar_field' ) );
			add_action( 'wp_ajax_remove_avatar', array( &$this, 'remove_avatar' ) );
		}
	}

	/**
	 * Filter get_avatar() to use our custom avatar images.
	 *
	 * @param array      $avatar the current avatar.
	 * @param int|string $id_or_email the unique identifier of the user.
	 * @param int        $size size of the avatar image.
	 * @param array      $default the default avatar image.
	 * @param string     $alt the alt text for tha avatar image.
	 */
	function get_avatar_filter( $avatar, $id_or_email, $size, $default, $alt ) {
		$image_src = $this->get_avatar_src( $id_or_email, $size );
		if ( ! empty( $image_src ) ) {
			return '<img src="' . $image_src[0] . '" alt="' . $alt . '" width="' . $size . '" height="' . $size . '" class="avatar avatar-' . $size . ' photo" />';
		} else {
			return $avatar;
		}
	}

	/**
	 * Load admin JS
	 *
	 * @param string $hook the admin view we're looking at.
	 */
	function load_avatar_js( $hook ) {
		if ( 'profile.php' === $hook ) {
			wp_enqueue_script(
				'avatar_js',
				get_template_directory_uri() . '/js/avatars.js',
				array( 'jquery' )
			);
		}
	}

	/**
	 * Add multipart encoding for our file upload.
	 */
	function add_edit_form_multipart_encoding() {
		echo ' enctype="multipart/form-data"';
	}

	/**
	 * Generate the markup for our custom avatar field.
	 *
	 * @param object $user the user object being edited.
	 */
	function add_avatar_field( $user ) {
		$image_src = $this->get_avatar_src( $user->ID, '128' );
		$this->print_avatar_admin_css();
	?>
		<h3>User avatar</h3>
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="<?php echo esc_attr( self::META_FIELD ); ?>"><?php esc_html_e( 'Current avatar', 'mj' ); ?></label></th>
					<td>
						<p id="avatar-display">
							<?php
							if ( ! empty( $image_src ) ) { ?>
								<img src="<?php echo esc_attr( $image_src[0] ); ?>" width="<?php echo esc_attr( $image_src[1] ); ?>" height="<?php echo esc_attr( $image_src[2] ); ?>" /><br />
								<a href="<?php echo esc_url( get_edit_post_link( $this->get_user_avatar_id( $user->ID ) ) ); ?>">
									<?php esc_html_e( 'Edit', 'mj' ); ?></a> | <a id="remove-avatar" href="#"><?php esc_html_e( 'Remove', 'mj' ); ?>
								</a>
							<?php }
							if ( empty( $image_src ) && has_gravatar( $user->user_email ) ) {
								echo get_avatar( $user->ID );
								echo '<br />' . __( 'Currently using Gravatar. Change at <a href="http://gravatar.com/">gravatar.com</a> or choose a different image below.', 'mj' );
							}
							?>
						</p>

						<p id="avatar-input" <?php if ( ! empty( $image_src ) ) { ?>style="display:none;"<?php } ?>>
							<input type="file" name="<?php echo esc_attr( self::META_FIELD ); ?>" id="<?php echo esc_attr( self::META_FIELD ); ?>" />
						</p>
					</td>
				</tr>
			</tbody>
		</table>
	<?php
	}

	/**
	 * Save the avatar custom field.
	 *
	 * @param int $user_id the id of the user we're saving an avatar for.
	 */
	function save_avatar_field( $user_id ) {
		if ( $this->has_files_to_upload( self::META_FIELD ) ) {
			if ( isset( $_FILES[ self::META_FIELD ] ) ) {
				$file = wp_upload_bits(
					$_FILES[ self::META_FIELD ]['name'], null,
					@file_get_contents( $_FILES[ self::META_FIELD ]['tmp_name'])
				);
				if ( false === $file['error'] ) {
					$mime_type = wp_check_filetype( $file['file'] );
					$args = array(
						'guid' => $file['url'],
						'post_mime_type' => $mime_type['type'],
						'post_title' => preg_replace( '/\.[^.]+$/', '', basename( $file['file'] ) ),
						'post_content' => '',
						'post_status' => 'inherit',
					);
					$id = wp_insert_attachment( $args, $file['file'] );

					if ( ! is_wp_error( $id ) ) {
						require_once( ABSPATH . 'wp-admin/includes/image.php' );
						// Generate the metadata for the attachment, and update the database record.
						$metadata = wp_generate_attachment_metadata( $id, $file['file'] );
						$update = wp_update_attachment_metadata( $id, $metadata );
						$this->associate_avatar_with_user( $user_id, $id );
					}
				}
			}
		}
	}

	/**
	 * Remove the avatar images when that option is selected.
	 */
	function remove_avatar() {
		$user_id = false;
		if ( ! empty( $_POST['user_id'] ) ) {
			$user_id = $_POST['user_id'];
		}
		$ret = $this->remove_user_avatar( $user_id );
		if ( ! empty( $ret ) ) {
			echo wp_json_encode( array( 'success' => true ) );
		} else {
			echo wp_json_encode( array( 'success' => false ) );
		}
		wp_die();
	}

	/**
	 * Get the avatar image HTML for the given user id/email and size
	 *
	 * @param int|string $id_or_email a wordpress user ID or user email address.
	 * @param int        $size The size of the avatar.
	 */
	function get_avatar_src( $id_or_email, $size ) {
		// get the user ID.
		if ( is_numeric( $id_or_email ) ) {
			$id = (int) $id_or_email;
		} elseif ( is_object( $id_or_email ) ) {
			if ( ! empty( $id_or_email->user_id ) ) {
				$id = (int) $id_or_email->user_id;
			}
		} else {
			$user = get_user_by( 'email', $id_or_email );
			$id = $user->ID;
		}
		$avatar_id = $this->get_user_avatar_id( $id );

		if ( empty( $avatar_id ) ) {
			return false;
		}

		$closest_square_size = $this->get_closest_square_image_size( $size );

		return wp_get_attachment_image_src( $avatar_id, $closest_square_size );
	}

	/**
	 *  Get the avatar associated with a give user ID
	 *
	 * @param int $user_id a user ID.
	 */
	function get_user_avatar_id( $user_id ) {
		return get_user_meta( $user_id, self::META_FIELD, true );
	}

	/**
	 *  See if we have a custom avatar image to upload (or not).
	 *
	 * @param int $id the upload ID.
	 */
	function has_files_to_upload( $id ) {
		return ( ! empty( $_FILES ) ) && isset( $_FILES[ $id ] );
	}

	/**
	 *  Save the avatar (attachment) ID as user_meta.
	 *
	 * @param int $user_id the user ID.
	 * @param int $avatar_id the avatar ID.
	 */
	function associate_avatar_with_user( $user_id, $avatar_id ) {
		return update_user_meta( $user_id, self::META_FIELD, $avatar_id );
	}

	/**
	 *  Remove the avatar associated with a given user.
	 *
	 * @param int $user_id the user ID.
	 */
	function remove_user_avatar( $user_id = false ) {
		if ( empty( $user_id ) ) {
			$user = wp_get_current_user();
			$user_id = $user->ID;
		}
		return update_user_meta( $user_id, self::META_FIELD, false );
	}

	/**
	 * Get size information for a specific image size.
	 *
	 * @global $_wp_additional_image_sizes
	 * @uses   get_image_sizes()
	 * @param  string $size The requested image size.
	 * @return array $closest_square the closest square image available to the size requested.
	 */
	function get_closest_square_image_size( $size ) {
		global $_wp_additional_image_sizes;

		$sizes = get_image_sizes();

		$square_image_sizes = array_filter( $sizes, 'is_square' );

		$requested_size = array( $size, $size );

		foreach ( $square_image_sizes as $key => $val ) {
			if ( round( (float) ( $val['width'] / $val['height'] ), 1, PHP_ROUND_HALF_DOWN ) ===
				round( (float) ( $requested_size[0] / $requested_size[1]), 1, PHP_ROUND_HALF_DOWN ) ) {
				// Try to find an image size equal to or just slightly larger than what was requested.
				if ( $val['width'] >= $requested_size[0] ) {
					$closest_square = array( $val['width'], $val['height'] );
					break;
				}
				// If we can't find an image size, set the requested size to the largest of the
				// square sizes available.
				if ( end( $square_image_sizes ) === $square_image_sizes[ $key ] ) {
					$closest_square = array( $val['width'], $val['height'] );
				}
			}
		}
		return $closest_square;
	}
	/**
	 *  Hide the default profile image and show our custom version.
	 */
	function print_avatar_admin_css() { ?>
		<style type="text/css">
			.user-profile-picture {
				display: none;
			}
			#avatar-display img {
				padding: 4px;
				border: 1px solid #ccc;
				background: #fff;
			}
			#avatar-input {
				display: inline-block;
				margin: 10px 0;
				padding: 8px;
				background: #fff;
				border: 1px solid #ccc;
				border-radius: 4px;
			}
		</style>
	<?php
	}
}
new MJ_Avatar;
