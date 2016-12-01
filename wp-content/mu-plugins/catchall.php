<?php
/* this is just so we can have all our requires in a single place
 * instead of a zillion files
 */

require_once('responsive-images/responsive-images.php');
require_once('zoninator/zoninator.php');
require_once('coauthors/co-authors-plus.php');
//global $coauthors_plus exists now
// $coauthors_plus->guest_authors should exist after 'init' wp hook
// god i hope functions get pulled off the init hook FIFO
// docs say that things added are FIFO, also look into priority arg if not
//
// we want to mess with get_guest_author_fields to add our stuff
add_action( 'init', 'rewrite_coauthors_guest_author_for_mojo' );
function rewrite_coauthors_guest_author_for_mojo() {
  global $coauthors_plus;
  class MJ_Guest_Authors extends CoAuthors_Guest_Authors {
    function get_guest_author_fields ( $groups = 'all' ) {
      $groups = (array) $groups;
      $global_fields = array(
          // Hidden (included in object, no UI elements)
          array(
              'key'      => 'ID',
              'label'    => __( 'ID', 'co-authors-plus' ),
              'group'    => 'hidden',
              'input'	   => 'hidden',
            ),
          // Name
          array(
              'key'      => 'display_name',
              'label'    => __( 'Display Name', 'co-authors-plus' ),
              'group'    => 'name',
              'required' => true,
            ),
          array(
              'key'      => 'first_name',
              'label'    => __( 'First Name', 'co-authors-plus' ),
              'group'    => 'name',
            ),
          array(
              'key'      => 'last_name',
              'label'    => __( 'Last Name', 'co-authors-plus' ),
              'group'    => 'name',
            ),
          array(
              'key'      => 'user_login',
              'label'    => __( 'Slug', 'co-authors-plus' ),
              'group'    => 'slug',
              'required' => true,
            ),
          // Contact info
          array(
              'key'      => 'user_email',
              'label'    => __( 'E-mail', 'co-authors-plus' ),
              'group'    => 'contact-info',
              'input'	   => 'email',
            ),
          array(
              'key'      => 'linked_account',
              'label'    => __( 'Linked Account', 'co-authors-plus' ),
              'group'    => 'slug',
            ),
  /*
          array(
              'key'      => 'website',
              'label'    => __( 'Website', 'co-authors-plus' ),
              'group'    => 'contact-info',
              'input'	   => 'url',
            ),
          array(
              'key'      => 'aim',
              'label'    => __( 'AIM', 'co-authors-plus' ),
              'group'    => 'contact-info',
            ),
          array(
              'key'      => 'yahooim',
              'label'    => __( 'Yahoo IM', 'co-authors-plus' ),
              'group'    => 'contact-info',
            ),
          array(
              'key'      => 'jabber',
              'label'    => __( 'Jabber / Google Talk', 'co-authors-plus' ),
              'group'    => 'contact-info',
            ),
  */
          array(
              'key'      => 'twitter',
              'label'    => __( 'Twitter', 'co-authors-plus' ),
              'group'    => 'contact-info',
            ),
            //bio fields
          array(
              'key'      => 'position',
              'label'    => __( 'Long Bio', 'co-authors-plus' ),
              'group'    => 'about',
              'sanitize_function' => 'wp_filter_post_kses',
            ),
          array(
              'key'      => 'long_bio',
              'label'    => __( 'Long Bio', 'co-authors-plus' ),
              'group'    => 'about',
              'sanitize_function' => 'wp_filter_post_kses',
            ),
          array(
              'key'      => 'short bio',
              'label'    => __( 'Short Bio', 'co-authors-plus' ),
              'group'    => 'about',
              'sanitize_function' => 'wp_filter_post_kses',
            ),
        );
      $fields_to_return = array();
      foreach ( $global_fields as $single_field ) {
        if ( in_array( $single_field['group'], $groups ) || 'all' === $groups[0] && 'hidden' !== $single_field['group'] ) {
          $fields_to_return[] = $single_field;
        }
      }

      return apply_filters( 'coauthors_guest_author_fields', $fields_to_return, $groups );
    }
  }
  $coauthors_plus->guest_authors = new MJ_Guest_Authors;
}

?>
