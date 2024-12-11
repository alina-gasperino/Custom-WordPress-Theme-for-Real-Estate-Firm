<?php

add_action( 'wp_ajax_project_gallery', 'ajax_project_gallery' );
add_action( 'wp_ajax_nopriv_project_gallery', 'ajax_project_gallery' );

function ajax_project_gallery() {

	$id = filter_input(INPUT_GET, 'project_id', FILTER_SANITIZE_NUMBER_INT);
	$size = filter_input(INPUT_GET, 'size', FILTER_SANITIZE_STRING);

	wp_send_json( get_project_gallery($id, $size) );

}


