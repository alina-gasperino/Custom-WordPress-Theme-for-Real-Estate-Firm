<?php

// add_action( 'wp_ajax_get_region_polygon_geojson', 'get_region_polygon_geojson' );
// add_action( 'wp_ajax_nopriv_get_region_polygon_geojson', 'get_region_polygon_geojson' );

function get_region_polygon_geojson() {

	global $wp_query;

	$qo = $wp_query->get_queried_object();

	$data = array();
	$data['type'] = 'FeatureCollection';
	$data['features'] = array();

	$term = 'neighbourhood';
	// if ($qo->taxonomy == 'district') $term = 'city';
	$regions = array();

	while ($wp_query->have_posts()) {

		$wp_query->the_post();

		$terms = get_the_terms(get_the_ID(), $term);
		if ($terms) {

			foreach ($terms as $term_id => $term_obj) {

				$term_obj->projects[ get_the_ID() ] = get_the_ID();

				if (isset($regions[$term_id])) continue;

				$regions[$term_id] = $term_obj;

				$geometry_coordinates = json_decode( get_field('field_557b53bc0c72f', $term_obj) );

				$feature = array(
					'id' => $term_id,
					'type' => 'Feature',
					'geometry' => array(
						'type' => 'Polygon',
						'coordinates' => $geometry_coordinates ?: array(),
						),
					'properties' => array(
						'name' => $term_obj->name,
						'taxonomy' => $term_obj->taxonomy,
						'count' => $term_obj->count,
						),
					);

				$data['features'][] = $feature;

			}
		}

	}

	return json_encode($data);

}


