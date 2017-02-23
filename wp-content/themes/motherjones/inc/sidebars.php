<?php
/**
 * Register all the sidebars
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */
function mj_widgets_init() {
	$sidebars = array (
		// the default widget areas
		array (
			'name'          => __( 'Sidebar', 'motherjones' ),
	 		'id'            => 'sidebar',
	 		'description'   => __( 'Shows up on article pages', 'motherjones' )
		),
		array (
			'name'          => __( 'Ticker', 'motherjones' ),
	 		'id'            => 'ticker',
	 		'description'   => __( 'Shows up right beneath the top menu', 'motherjones' )
		),
		array (
			'name'          => __( 'End of content', 'motherjones' ),
	 		'id'            => 'content-end',
	 		'description'   => __( 'Between the comments and the footer', 'motherjones' )
		),
		array (
			'name'          => __( 'End of page', 'motherjones' ),
	 		'id'            => 'page-end',
	 		'description'   => __( 'after EVERYTHING else.', 'motherjones' )
		),
		array (
			'name'          => __( 'Top of page', 'motherjones' ),
	 		'id'            => 'page-top',
	 		'description'   => __( 'before EVERYTHING else.', 'motherjones' )
		)
	);
	foreach ( $sidebars as $sidebar ) {
		register_sidebar( array(
			'name' 					=> $sidebar['name'],
			'description' 	=> $sidebar['desc'],
			'id' 						=> $sidebar['id'],
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
	 		'after_widget'  => '</section>',
	 		'before_title'  => '<h2 class="widget-title">',
	 		'after_title'   => '</h2>',
		) );
	}
}
add_action( 'widgets_init', 'mj_widgets_init' );
