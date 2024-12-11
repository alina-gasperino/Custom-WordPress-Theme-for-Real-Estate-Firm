<?php

## Projects
add_action('init', 'register_project_post_type');
function register_project_post_type() {

	$singular = "Project";
	$plural = "Projects";

	$args = array(
		'label' => $plural,
		'labels' => array(
			'name' => $plural,
			'singular_name' => $singular,
			'menu_name' => $plural,
			'name_admin_bar' => "Add New $singular",
			'all_items' => "All $plural",
			'add_new' => "Add New $singular",
			'add_new_item' => "Add New $singular",
			'edit_item' => "Edit $singular",
			'new_item' => "New $singular",
			'view_item' => "View $singular",
			'search_items' => "Search $plural",
			'not_found' => "No $plural found",
			'not_found_in_trash' => "No $plural found in trash",
			'parent_item_colon' => "Parent $singular"
			),
		'description' => "",
		'public' => true,
		'menu_icon' => 'dashicons-category',
		'menu_position' => 5,
		'rewrite'		=> array( 'slug' => 'project', 'with_front' => false ),
		'capability_type' => 'post',
		'supports' => array('title', 'thumbnail', 'custom-fields'),
		'taxonomies' => array('post_tag'),

		);

	register_post_type('project', $args);

}

## Events
add_action('init', 'register_event_post_type');
function register_event_post_type() {

    $singular = "Event";
    $plural = "Events";

    $args = array(
        'label' => $plural,
        'labels' => array(
            'name' => $plural,
            'singular_name' => $singular,
            'menu_name' => $plural,
            'name_admin_bar' => "Add New $singular",
            'all_items' => "All $plural",
            'add_new' => "Add New $singular",
            'add_new_item' => "Add New $singular",
            'edit_item' => "Edit $singular",
            'new_item' => "New $singular",
            'view_item' => "View $singular",
            'search_items' => "Search $plural",
            'not_found' => "No $plural found",
            'not_found_in_trash' => "No $plural found in trash",
            'parent_item_colon' => "Parent $singular"
        ),
        'description' => "",
        'public' => true,
        'menu_icon' => 'dashicons-location-alt',
        'menu_position' => 5,
        'rewrite'		=> array( 'slug' => 'event', 'with_front' => false ),
        'capability_type' => 'post',
        'supports' => array('title', 'custom-fields'),
//        'taxonomies' => array('post_tag'),

    );

    register_post_type('event', $args);
}


## Assignments
add_action('init', 'register_assignment_post_type');
function register_assignment_post_type() {

	$singular = "Assignment";
	$plural = "Assignments";

	$args = array(
		'label' => $plural,
		'labels' => array(
			'name' => $plural,
			'singular_name' => $singular,
			'menu_name' => $plural,
			'name_admin_bar' => "Add New $singular",
			'all_items' => "All $plural",
			'add_new' => "Add New $singular",
			'add_new_item' => "Add New $singular",
			'edit_item' => "Edit $singular",
			'new_item' => "New $singular",
			'view_item' => "View $singular",
			'search_items' => "Search $plural",
			'not_found' => "No $plural found",
			'not_found_in_trash' => "No $plural found in trash",
			'parent_item_colon' => "Parent $singular"
			),
		'description' => "",
		'public' => true,
		'menu_position' => 5,
		'has_archive' => 'assignments',
		'rewrite'		=> array( 'slug' => 'assignment', 'with_front' => false ),
		'capability_type' => 'post',
		'supports' => array('title', 'thumbnail', 'custom-fields'),
		'taxonomies' => array(''),

		);

	register_post_type('assignment', $args);

}

## Project Update
// add_action('init', 'register_project_update_post_type');
function register_project_update_post_type() {

	$singular = "Project Update";
	$plural = "Project Updates";

	$args = array(
		'label' => $plural,
		'labels' => array(
			'name' => $plural,
			'singular_name' => $singular,
			'menu_name' => $plural,
			'name_admin_bar' => "Add New $singular",
			'all_items' => "All $plural",
			'add_new' => "Add New $singular",
			'add_new_item' => "Add New $singular",
			'edit_item' => "Edit $singular",
			'new_item' => "New $singular",
			'view_item' => "View $singular",
			'search_items' => "Search $plural",
			'not_found' => "No $plural found",
			'not_found_in_trash' => "No $plural found in trash",
			'parent_item_colon' => "Parent $singular"
			),
		'description' => "",
		'public' => true,
		'menu_position' => 5,
		'capability_type' => 'post',
		'supports' => array('title', 'editor', 'custom-fields', 'thumbnail'),
		'taxonomies' => array('category'),

		);

	register_post_type('project_update', $args);

}


## Learning Posts
add_action('init', 'register_guide_post_type');
function register_guide_post_type() {

	$singular = "Guide";
	$plural = "Guides";

	$args = array(
		'label' => $plural,
		'labels' => array(
			'name' => $plural,
			'singular_name' => $singular,
			'menu_name' => $plural,
			'name_admin_bar' => "Add New $singular",
			'all_items' => "All $plural",
			'add_new' => "Add New $singular",
			'add_new_item' => "Add New $singular",
			'edit_item' => "Edit $singular",
			'new_item' => "New $singular",
			'view_item' => "View $singular",
			'search_items' => "Search $plural",
			'not_found' => "No $plural found",
			'not_found_in_trash' => "No $plural found in trash",
			'parent_item_colon' => "Parent $singular"
			),
		'description' => "",
		'public' => true,
		'menu_position' => 5,
		'capability_type' => 'post',
		'supports' => array('title', 'thumbnail', 'editor', 'custom-fields'),
		'taxonomies' => array('category'),
		'rewrite' => array( 'slug' => 'condo-investing-guides' ),

		);

	register_post_type( 'guide', $args );

}


