<?php

// add_action( 'wp_ajax_tag_single_floorplans_with_google_id', 'ajax_tag_single_floorplans_with_google_id' );
// add_action( 'wp_ajax_nopriv_tag_single_floorplans_with_google_id', 'ajax_tag_single_floorplans_with_google_id' );

function ajax_tag_single_floorplans_with_google_id() {

	global $wpdb;

	## loop through projects that have only 1 pdf and if they don't have a custom field for the google doc id, add it
	$qry = $wpdb->prepare("SELECT * from $wpdb->posts as p where post_type = 'attachment' and post_mime_type = 'image/jpeg' and guid like '%%condos-floor-plan%%.jpg'");
	$results = $wpdb->get_results($qry);

	foreach ($results as $row) {
		$ids = get_project_googledoc_ids($row->post_parent);
		if (count($ids) == 1) {
			$googledoc_id = get_field('googledoc_id', $row->ID);
			if ( $googledoc_id != $ids[0] ) {
				update_post_meta( $row->ID, 'googledoc_id', $ids[0] );
			}
		}
	}

	$projects = [
		'964'   => '0B3aaMcUbIlPQMnFDaTRycmFvbjQ',
		'8263'  => '0B3aaMcUbIlPQekRmU2ZMelhtR3M',
		'8790'  => '0B3aaMcUbIlPQOWd6Y0E1TmNadzg',
		'9087'  => '0B3aaMcUbIlPQSXI0NlZzRzVVS28',
		'9251'  => '0B3aaMcUbIlPQamk3a0xHZC1fNkk',
		'11500' => '0B3aaMcUbIlPQcHZSVXY2MGFRWkE',
		'11677' => '0B3aaMcUbIlPQdnk1WWVTa29VUnM',
		'12202' => '0B3aaMcUbIlPQWTE3aVJRemVfZzA',
		'12375' => '0B3aaMcUbIlPQWG9wSzFDT2lnN1U',
	];

	foreach ($projects as $parent_id => $googledoc_id) {
		$qry = $wpdb->prepare("SELECT * from $wpdb->posts as p where post_type = 'attachment' and post_mime_type = 'image/jpeg' and guid like '%%condos-floor-plan%%.jpg' and post_parent = %s", $parent_id );
		$results = $wpdb->get_results($qry);

		foreach ($results as $row) {
			$field = get_field('googledoc_id', $row->ID);
			if ( $googledoc_id != $field ) {
				update_post_meta( $row->ID, 'googledoc_id', $googledoc_id );
			}
		}

	}

	wp_die();

}


