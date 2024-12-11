<?php

add_action( 'wp_ajax_geocode_project_location_data', 'ajax_geocode_project_location_data' );
add_action( 'wp_ajax_nopriv_geocode_project_location_data', 'ajax_geocode_project_location_data' );

function ajax_geocode_project_location_data() {

	$post_id = filter_var($_GET['project_id'], FILTER_SANITIZE_NUMBER_INT );

	geocode_project_location_data( $post_id );

	wp_die();
}