<?php

function patch_serialize_exposure() {

	global $wpdb;

	$results = $wpdb->get_results("SELECT * from $wpdb->postmeta where meta_key like 'floorplans%_exposure' and meta_value <> '' and meta_value not like 'a:%'");

	foreach ($results as $row) {

		echo '<pre>'; print_r($row); echo '</pre>';

		if ($row->meta_value == 'South East West') {

			$new_value = ['South East', 'West'];
		}

		if ($row->meta_value == 'South, North West') {
			$new_value = ['South', 'North West'];
		}

		if ($row->meta_value == 'East, West') {
			$new_value = ['East', 'West'];
		}

		if ($row->meta_value == 'North East South West') {
			$new_value = ['North East', 'South West'];
		}

		update_post_meta( $row->post_id, $row->meta_key, $new_value );

	}

	wp_die();

}
