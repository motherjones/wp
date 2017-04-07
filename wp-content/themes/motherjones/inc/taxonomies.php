<?php
/**
 * Register the "Content Type" taxonomy
 */
if ( ! taxonomy_exists( 'mj_content_type' ) ) {
	register_taxonomy(
		'mj_content_type',
		array( 'post' ),
		array(
			'label' => __( 'Content Type', 'mj' ),
			'labels' => array(
				'name' => __( 'Content Type', 'mj' ),
				'singular_name' => __( 'Content Type', 'mj' ),
				'all_items' => __( 'All Content Types', 'mj' ),
				'edit_item' => __( 'Edit Content Type', 'mj' ),
				'update_item' => __( 'Update Content Type', 'mj' ),
				'view_item' => __( 'View Content Type', 'mj' ),
				'add_new_item' => __( 'Add New Content Type', 'mj' ),
				'new_item_name' => __( 'New Content Type Name', 'mj' ),
				'search_items' => __( 'Search Content Type', 'mj' ),
			),
			'public' => true,
			'show_in_nav_menus' => true,
			'hierarchical' => false,
			'show_admin_column' => true,
			'meta_box_cb' => 'mj_content_type_meta_box',
			'capabilites' => array(
				'assign_terms' => 'edit_posts',
				'edit_terms' => 'update_core',
				'delete_terms' => 'update_core',
			),
		)
	);
}
