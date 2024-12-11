<?php

add_action( 'wp_ajax_ajax_get_map_data', 'ajax_get_map_data' );
add_action( 'wp_ajax_nopriv_ajax_get_map_data', 'ajax_get_map_data' );

function ajax_get_map_data() {
	// wp_send_json( get_map_data() );
	@header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
	@header( 'Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + (60 * 60 * 24)));
	echo json_encode( get_map_data() );
	wp_die();
}


