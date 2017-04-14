<?php
/**
 * The sidebar containing the main widget area
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

global $mj;
if ( ! is_active_sidebar( 'sidebar' ) ) {
	return;
}
?>
<aside id="sidebar-right" class="grid__col-4 hidden-sm hidden-xs hidden-xxs">
	<?php
	if ( mj_is_content_type( 'blogpost', get_the_ID() ) ) {
			dynamic_sidebar( 'sidebar-blog' );
	} elseif ( is_tag( 'bite' ) ) {
			dynamic_sidebar( 'sidebar-bite' );
	} elseif ( is_tag( 'inquiring-minds' ) ) {
			dynamic_sidebar( 'sidebar-inquiring-minds' );
	} else {
			dynamic_sidebar( 'sidebar' );
	}
	the_widget(
		'mj_ad_unit_widget',
		array(
			'placement' => 'RightTopROS300x600',
			'height' => 529,
			'docwrite' => 1,
			'desktop' => 1,
		),
		array(
			'before_widget' => '',
			'after_widget' => '',
		)
	);
	?>
</aside>
