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
 * Modify the user profile screen
 * Remove old contact methods (yahoo, aol and jabber)
 * Add new ones (twitter, facebook, linkedin)
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
