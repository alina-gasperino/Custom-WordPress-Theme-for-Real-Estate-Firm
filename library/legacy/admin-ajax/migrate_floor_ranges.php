<?php

function tc_migrate_floor_ranges() {

	global $wpdb, $post;

	$ids = $wpdb->get_col( "select ID from $wpdb->posts
		where post_type = 'project'
		and post_status = 'publish'
		and exists (select * from $wpdb->postmeta where $wpdb->posts.ID = $wpdb->postmeta.post_id and meta_key like '%floor_range')
		and not exists (select * from $wpdb->postmeta where $wpdb->posts.ID = $wpdb->postmeta.post_id and meta_key like '%floor_ranges')
		order by ID" );

	foreach ($ids as $id) {

		$post = get_post($id);
		setup_postdata( $post );

		$n = 0;
		while (have_rows('floorplans')) {
			the_row();
			$n++;
			$range = get_sub_field('floor_range');

			if ($range) {
				delete_sub_row( 'floor_ranges' );
				add_sub_row( 'floor_ranges', [ 'range' => $range ] );
			}

		}
	}

}

if ( defined('WP_CLI') && WP_CLI ) {
	WP_CLI::add_command('tc migrate_floorplan_ranges', 'tc_migrate_floor_ranges');
}