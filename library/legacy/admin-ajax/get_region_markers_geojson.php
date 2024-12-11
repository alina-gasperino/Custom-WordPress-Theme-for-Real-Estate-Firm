<?php

// add_action( 'wp_ajax_get_region_markers_geojson', 'get_region_markers_geojson' );
// add_action( 'wp_ajax_nopriv_get_region_markers_geojson', 'get_region_markers_geojson' );

function get_region_markers_geojson() {

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

				if ($geometry_coordinates) {
					$lngs = array();
					$lats = array();

					foreach ($geometry_coordinates[0] as $point) {
						$lngs[] = $point[0];
						$lats[] = $point[1];
					}

					if ($lngs && $lats) {

						$term_obj->center = array( array_sum($lngs) / count($lngs), array_sum($lats) / count($lats) );

						$marker = array(
							'id' => $term_id,
							'type' => 'Feature',
							'geometry' => array(
								'type' => 'Point',
								'coordinates' => $term_obj->center,
								),
							'properties' => array(
								'name' => $term_obj->name,
								'taxonomy' => $term_obj->taxonomy,
								'count' => $term_obj->count,
								),
							);

						$data['features'][] = $marker;

					}
				}
			}
		}
	}

	return json_encode($data);

}
