<?php
add_action( 'init', 'architect_taxonomy' );
function architect_taxonomy() {

	$singular = 'Architect';
	$plural = 'Architects';

	$labels = array(
		'name'              => _x( $singular, 'taxonomy general name' ),
		'singular_name'     => _x( $singular, 'taxonomy singular name' ),
		'search_items'      => __( "Search $plural" ),
		'all_items'         => __( "All $plural" ),
		'parent_item'       => __( "Parent $singular" ),
		'parent_item_colon' => __( "Parent $singular:" ),
		'edit_item'         => __( "Edit $singular" ),
		'update_item'       => __( "Update $singular" ),
		'add_new_item'      => __( "Add New $singular" ),
		'new_item_name'     => __( "New $singular Name" ),
		'menu_name'         => __( $plural ),
	);

	$args = array(
		'hierarchical'      => false,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'architect' ),
	);

	register_taxonomy( 'architect', 'project', $args );

}

add_action( 'init', 'interior_designer_taxonomy' );
function interior_designer_taxonomy() {

	$singular = 'Interior Designer';
	$plural = 'Interior Designers';

	$labels = array(
		'name'              => _x( $singular, 'taxonomy general name' ),
		'singular_name'     => _x( $singular, 'taxonomy singular name' ),
		'search_items'      => __( "Search $plural" ),
		'all_items'         => __( "All $plural" ),
		'parent_item'       => __( "Parent $singular" ),
		'parent_item_colon' => __( "Parent $singular:" ),
		'edit_item'         => __( "Edit $singular" ),
		'update_item'       => __( "Update $singular" ),
		'add_new_item'      => __( "Add New $singular" ),
		'new_item_name'     => __( "New $singular Name" ),
		'menu_name'         => __( $plural ),
	);

	$args = array(
		'hierarchical'      => false,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'interior-designer' ),
	);

	register_taxonomy( 'interior-designer', 'project', $args );

}

add_action( 'init', 'district_taxonomy' );
function district_taxonomy() {

	$singular = 'District';
	$plural = 'Districts';

	$labels = array(
		'name'              => _x( $singular, 'taxonomy general name' ),
		'singular_name'     => _x( $singular, 'taxonomy singular name' ),
		'search_items'      => __( "Search $plural" ),
		'all_items'         => __( "All $plural" ),
		'parent_item'       => __( "Parent $singular" ),
		'parent_item_colon' => __( "Parent $singular:" ),
		'edit_item'         => __( "Edit $singular" ),
		'update_item'       => __( "Update $singular" ),
		'add_new_item'      => __( "Add New $singular" ),
		'new_item_name'     => __( "New $singular Name" ),
		'menu_name'         => __( $plural ),
	);

	$args = array(
		'hierarchical'      => false,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'district' ),
	);

	register_taxonomy( 'district', 'project', $args );

}


add_action( 'init', 'city_taxonomy' );
function city_taxonomy() {

	$singular = 'City';
	$plural = 'Cities';

	$labels = array(
		'name'              => _x( $singular, 'taxonomy general name' ),
		'singular_name'     => _x( $singular, 'taxonomy singular name' ),
		'search_items'      => __( "Search $plural" ),
		'all_items'         => __( "All $plural" ),
		'parent_item'       => __( "Parent $singular" ),
		'parent_item_colon' => __( "Parent $singular:" ),
		'edit_item'         => __( "Edit $singular" ),
		'update_item'       => __( "Update $singular" ),
		'add_new_item'      => __( "Add New $singular" ),
		'new_item_name'     => __( "New $singular Name" ),
		'menu_name'         => __( $plural ),
	);

	$args = array(
		'hierarchical'      => false,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'city' ),
	);

	register_taxonomy( 'city', 'project', $args );

}


add_action( 'init', 'neighbourhood_taxonomy' );
function neighbourhood_taxonomy() {

	$singular = 'Neighbourhood';
	$plural = 'Neighbourhoods';

	$labels = array(
		'name'              => _x( $singular, 'taxonomy general name' ),
		'singular_name'     => _x( $singular, 'taxonomy singular name' ),
		'search_items'      => __( "Search $plural" ),
		'all_items'         => __( "All $plural" ),
		'parent_item'       => __( "Parent $singular" ),
		'parent_item_colon' => __( "Parent $singular:" ),
		'edit_item'         => __( "Edit $singular" ),
		'update_item'       => __( "Update $singular" ),
		'add_new_item'      => __( "Add New $singular" ),
		'new_item_name'     => __( "New $singular Name" ),
		'menu_name'         => __( $plural ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
        'show_in_rest'      => true,
        'rest_base'         => 'neighbourhoods',
		'rewrite'           => array(
			'slug' => 'neighbourhood',
			'with_front' => false,
			'hierarchical' => true
			),
	);

	register_taxonomy( 'neighbourhood', 'project', $args );

}


add_action( 'init', 'developer_taxonomy' );
function developer_taxonomy() {

	$singular = 'Developer';
	$plural = 'Developers';

	$labels = array(
		'name'              => _x( $singular, 'taxonomy general name' ),
		'singular_name'     => _x( $singular, 'taxonomy singular name' ),
		'search_items'      => __( "Search $plural" ),
		'all_items'         => __( "All $plural" ),
		'parent_item'       => __( "Parent $singular" ),
		'parent_item_colon' => __( "Parent $singular:" ),
		'edit_item'         => __( "Edit $singular" ),
		'update_item'       => __( "Update $singular" ),
		'add_new_item'      => __( "Add New $singular" ),
		'new_item_name'     => __( "New $singular Name" ),
		'menu_name'         => __( $plural ),
	);

	$args = array(
		'hierarchical'      => false,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
        'show_in_rest'      => true,
        'rest_base'         => 'developers',
		'rewrite'           => array( 'slug' => 'developer' ),
	);

	register_taxonomy( 'developer', 'project', $args );

}


add_action( 'init', 'sales_status_taxonomy' );
function sales_status_taxonomy() {

	$singular = 'Sales Status';
	$plural = 'Sales Statuses';

	$labels = array(
		'name'              => _x( $singular, 'taxonomy general name' ),
		'singular_name'     => _x( $singular, 'taxonomy singular name' ),
		'search_items'      => __( "Search $plural" ),
		'all_items'         => __( "All $plural" ),
		'parent_item'       => __( "Parent $singular" ),
		'parent_item_colon' => __( "Parent $singular:" ),
		'edit_item'         => __( "Edit $singular" ),
		'update_item'       => __( "Update $singular" ),
		'add_new_item'      => __( "Add New $singular" ),
		'new_item_name'     => __( "New $singular Name" ),
		'menu_name'         => __( $plural ),
	);

	$args = array(
		'hierarchical'      => false,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'sales-status' ),
	);

	register_taxonomy( 'salesstatus', 'project', $args );

}


add_action( 'init', 'occupancy_date_taxonomy' );
function occupancy_date_taxonomy() {

	$singular = 'Occupancy Date';
	$plural = 'Occupancy Dates';

	$labels = array(
		'name'              => _x( $singular, 'taxonomy general name' ),
		'singular_name'     => _x( $singular, 'taxonomy singular name' ),
		'search_items'      => __( "Search $plural" ),
		'all_items'         => __( "All $plural" ),
		'parent_item'       => __( "Parent $singular" ),
		'parent_item_colon' => __( "Parent $singular:" ),
		'edit_item'         => __( "Edit $singular" ),
		'update_item'       => __( "Update $singular" ),
		'add_new_item'      => __( "Add New $singular" ),
		'new_item_name'     => __( "New $singular Name" ),
		'menu_name'         => __( $plural ),
	);

	$args = array(
		'hierarchical'      => false,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'occupancy-date' ),
	);

	register_taxonomy( 'occupancy-date', 'project', $args );

}


add_action( 'init', 'status_taxonomy' );
function status_taxonomy() {

	$singular = 'Status';
	$plural = 'Statuses';

	$labels = array(
		'name'              => _x( $singular, 'taxonomy general name' ),
		'singular_name'     => _x( $singular, 'taxonomy singular name' ),
		'search_items'      => __( "Search $plural" ),
		'all_items'         => __( "All $plural" ),
		'parent_item'       => __( "Parent $singular" ),
		'parent_item_colon' => __( "Parent $singular:" ),
		'edit_item'         => __( "Edit $singular" ),
		'update_item'       => __( "Update $singular" ),
		'add_new_item'      => __( "Add New $singular" ),
		'new_item_name'     => __( "New $singular Name" ),
		'menu_name'         => __( $plural ),
	);

	$args = array(
		'hierarchical'      => false,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'status' ),
	);

	register_taxonomy( 'status', 'project', $args );

}


add_action( 'init', 'type_taxonomy' );
function type_taxonomy() {

	$singular = 'Type';
	$plural = 'Types';

	$labels = array(
		'name'              => _x( $singular, 'taxonomy general name' ),
		'singular_name'     => _x( $singular, 'taxonomy singular name' ),
		'search_items'      => __( "Search $plural" ),
		'all_items'         => __( "All $plural" ),
		'parent_item'       => __( "Parent $singular" ),
		'parent_item_colon' => __( "Parent $singular:" ),
		'edit_item'         => __( "Edit $singular" ),
		'update_item'       => __( "Update $singular" ),
		'add_new_item'      => __( "Add New $singular" ),
		'new_item_name'     => __( "New $singular Name" ),
		'menu_name'         => __( $plural ),
	);

	$args = array(
		'hierarchical'      => false,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'type' ),
	);

	register_taxonomy( 'type', 'project', $args );

}