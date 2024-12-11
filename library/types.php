<?php
	
/**
 * Post Type: Assignment
 *
 * @link https://codex.wordpress.org/Post_Types
 * @link https://codex.wordpress.org/Function_Reference/register_post_type
 */

if( !function_exists( 'register_type_assignment' ) ):

function register_type_assignment() {

	// Declare post type labels.
	$labels = array(
		'name'                  => __( 'Assignments', 'talk' ),
		'singular_name'         => __( 'Assignment', 'talk' ),
		'add_new'               => __( 'Add New', 'talk' ),
		'add_new_item'          => __( 'Add New Assignment', 'talk' ),
		'edit_item'             => __( 'Edit Assignment', 'talk' ),
		'new_item'              => __( 'New Assignment', 'talk' ),
		'view_item'             => __( 'View Assignment', 'talk' ),
		'search_items'          => __( 'Search Assignment', 'talk' ),
		'not_found'             => __( 'Nothing found in the Database.', 'talk' ),
		'not_found_in_trash'    => __( 'Nothing found in Trash', 'talk' ),
		'all_items'             => __( 'All Assignments', 'talk' ),
		'insert_into_item'      => __( 'Insert into Assignment', 'talk' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Assignment', 'talk' ),
	);

	// Setup registration arguments.
	$args = array( 
		'labels'              => $labels,
		'description'         => __( 'Custom post type used for Assignments.', 'talk' ),
		'public'              => true,
		'hierarchical'        => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 10,
		'menu_icon'           => 'dashicons-media-text',
		'has_archive'         => 'assignments',
		'rewrite'             => array( 'slug' => 'assignment', 'with_front' => false ),
		'capability_type'     => 'post',
		'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'sticky' ),
	);

	// Register the custom post type.
	register_post_type( 'assignment', $args );

}

endif;

add_action( 'init', 'register_type_assignment', 0 );




	
/**
 * Post Type: Guide
 *
 * @link https://codex.wordpress.org/Post_Types
 * @link https://codex.wordpress.org/Function_Reference/register_post_type
 */

if( !function_exists( 'register_type_guide' ) ):

function register_type_guide() {

	// Declare post type labels.
	$labels = array(
		'name'                  => __( 'Guides', 'talk' ),
		'singular_name'         => __( 'Guide', 'talk' ),
		'add_new'               => __( 'Add New', 'talk' ),
		'add_new_item'          => __( 'Add New Guide', 'talk' ),
		'edit_item'             => __( 'Edit Guide', 'talk' ),
		'new_item'              => __( 'New Guide', 'talk' ),
		'view_item'             => __( 'View Guide', 'talk' ),
		'search_items'          => __( 'Search Guide', 'talk' ),
		'not_found'             => __( 'Nothing found in the Database.', 'talk' ),
		'not_found_in_trash'    => __( 'Nothing found in Trash', 'talk' ),
		'all_items'             => __( 'All Guides', 'talk' ),
		'insert_into_item'      => __( 'Insert into Guide', 'talk' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Guide', 'talk' ),
	);

	// Setup registration arguments.
	$args = array( 
		'labels'              => $labels,
		'description'         => __( 'Custom post type used for Guides.', 'talk' ),
		'public'              => true,
		'hierarchical'        => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 10,
		'menu_icon'           => 'dashicons-book-alt',
		'has_archive'         => 'guides',
		'rewrite'             => array( 'slug' => 'guide', 'with_front' => false ),
		'capability_type'     => 'post',
		'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'sticky' ),
	);

	// Register the custom post type.
	register_post_type( 'guide', $args );

}

endif;

add_action( 'init', 'register_type_guide', 0 );

// Add Guide Categories
register_taxonomy( 
	'category',
	array( 'guide' ),
	array( 
		'labels' => array(
			'name'              => __( 'Categories', 'talk' ),
			'singular_name'     => __( 'Category', 'talk' ),
			'all_items'         => __( 'All Categories', 'talk' ),
			'edit_item'         => __( 'Edit Category', 'talk' ),
			'view_item'         => __( 'View Category', 'talk' ),
			'update_item'       => __( 'Update Category', 'talk' ),
			'add_new_item'      => __( 'Add New Category', 'talk' ),
			'new_item_name'     => __( 'New Category Name', 'talk' ),
			'parent_item'       => __( 'Parent Category', 'talk' ),
			'parent_item_colon' => __( 'Parent Category:', 'talk' ),
			'search_items'      => __( 'Search Categories', 'talk' ),
			'popular_items'     => __( 'Popular Categories', 'talk' ),
		),
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'show_in_nav_menus' => true,
		'show_admin_column' => true,
		'hierarchical' => false,		
		'query_var' => true,
		'rewrite' => array( 'slug' => 'category' ),
	)
); 


