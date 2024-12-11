<?php

// add_action( 'wp_ajax_list_floorplan_pdf_google_doc_ids', 'list_floorplan_pdf_google_doc_ids' );
// add_action( 'wp_ajax_nopriv_list_floorplan_pdf_google_doc_ids', 'list_floorplan_pdf_google_doc_ids' );

function list_floorplan_pdf_google_doc_ids() {

	global $wpdb;

	ini_set('error_reporting', 'E_ALL');
	ini_set('display_errors', '1');

	$results = $wpdb->get_col("select meta_value from $wpdb->postmeta where meta_key like '%floorplanspdfs%' and meta_value <> ''");

	$ids = [];
	foreach ($results as $result) {
		$links = explode("\n", $result);
		foreach ($links as $link) {
			$ids[] = extract_google_doc_id_from_url($link);
		}
	}

	$ids = array_unique($ids);
	sort($ids);
	$ids = array_values($ids);

	foreach ($ids as $id) {
		echo $id . PHP_EOL;
	}

	echo '<pre>'; print_r( count($ids) ); echo '</pre>';

	wp_die();

}


