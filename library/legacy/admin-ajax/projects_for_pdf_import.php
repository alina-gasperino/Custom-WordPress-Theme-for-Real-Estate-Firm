<?php

add_action( 'wp_ajax_projects_for_pdf_import', 'projects_for_pdf_import' );
add_action( 'wp_ajax_nopriv_projects_for_pdf_import', 'projects_for_pdf_import' );

function projects_for_pdf_import() {

	global $wpdb;

	$ids = $wpdb->get_col( "select post_id from $wpdb->postmeta where meta_key = 'import_floorplans_pdfs' and meta_value = '1'" );

	$resposne_data = [];
	$response_data['ids'] = $ids;

	return wp_send_json_success( $response_data );

}
