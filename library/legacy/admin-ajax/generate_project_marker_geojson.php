<?php

// add_action( 'wp_ajax_generate_project_marker_geojson', 'generate_project_marker_geojson' );
// add_action( 'wp_ajax_nopriv_generate_project_marker_geojson', 'generate_project_marker_geojson' );

function generate_project_marker_geojson( $qry = null ) {

	if (!$qry) return false;

	$queried_object = $qry->get_queried_object();

	$data = [
		'type' => 'FeatureCollection',
		'features' => []
	];

	while ($qry->have_posts()) {
		$qry->the_post();

		$id = get_the_ID();
		if (!$maplink = get_post_meta( $id, 'map', true )) continue;
		list( $lat, $lng, $zoom ) = parseMapLink($maplink);

		$feature = [
			'type' => 'Feature',
			'geometry' => [
				'type' => 'Point',
				'coordinates' => [$lat, $lng]
			],
			'properties' => [
				'id' => $id,
				'title' => get_the_title(),
				'permalink' => get_permalink($id),
				'thumbnail' => has_post_thumbnail($id) ? get_the_post_thumbnail( $id, 'portfolio_small' ) : null
			]
		];

		$feature['properties']['logo'] = wp_get_attachment_image( get_field('logo', $id), 'project-logo' );
		if (!$project['logo']) $project['logo'] = '<span class="orange-icon"><i class="far fa-building"></i></span>';
		$feature['properties']['pricedfrom'] = get_post_meta( $id, 'pricedfrom', true);
		// $feature['properties']['infusionsoftform'] = get_post_meta( $id, 'infusionsoftform', true );

		$data['features'][] = $feature;

	}

	return json_encode($data);

}

