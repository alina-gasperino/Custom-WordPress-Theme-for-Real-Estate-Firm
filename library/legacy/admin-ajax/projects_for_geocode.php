<?php

add_action( 'wp_ajax_projects_for_geocode', 'projects_for_geocode' );
add_action( 'wp_ajax_nopriv_projects_for_geocode', 'projects_for_geocode' );

function projects_for_geocode() {

	$projects = posts_for_geocode();

	$resposne_data = [];

	foreach ($projects as $project) {
		$response_data[] = $project->ID;
	}

	return wp_send_json_success( $response_data );

}

