<?php

add_action( 'wp_ajax_tag_attachments_with_google_docid', 'tag_attachments_with_google_docid' );
add_action( 'wp_ajax_nopriv_tag_attachments_with_google_docid', 'tag_attachments_with_google_docid' );

function tag_attachments_with_google_docid() {

	global $wpdb;

	$q = "select post_id, meta_value as pdfs from $wpdb->postmeta where meta_key = 'floorplanspdfs' and meta_value <> ''";
	$projects = $wpdb->get_results($q);

	$singles = [];

	foreach ($projects as $project) {
		$urls = explode("\n", $project->pdfs);
		if ( count($urls) == 1 ) {
		}
	}

	die();

}
