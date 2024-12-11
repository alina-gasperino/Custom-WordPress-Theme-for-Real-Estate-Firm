<?php

add_action( 'wp_ajax_project_updates_by_region', 'ajax_project_updates_by_region' );
add_action( 'wp_ajax_nopriv_project_updates_by_region', 'ajax_project_updates_by_region' );

function ajax_project_updates_by_region() {

	global $wpdb;

	$posts_per_page = ($_GET['count']) ? filter_var($_GET['count'], FILTER_SANITIZE_NUMBER_INT) : 5;
	$paged = ($_GET['paged']) ? filter_var($_GET['paged'], FILTER_SANITIZE_NUMBER_INT ) : 1;

	$taxonomy = ($_GET['taxonomy']) ? filter_var($_GET['taxonomy'], FILTER_SANITIZE_STRING) : '';
	$slug = ($_GET['term']) ? filter_var($_GET['term'], FILTER_SANITIZE_STRING) : '';

	$q = "select u.*
		from $wpdb->posts as u
		inner join $wpdb->postmeta as um on um.post_id = u.ID
		inner join $wpdb->posts as p on p.ID = substring_index(substring_index(um.meta_value, '\"', -2), '\"', 1)
		inner join $wpdb->term_relationships as tr on tr.object_id = p.ID
		inner join $wpdb->term_taxonomy as tt on tt.term_taxonomy_id = tr.term_taxonomy_id
		inner join $wpdb->terms as t on t.term_id = tt.term_id
		where u.post_type = 'project_update'
		and um.meta_key = 'project'
		and tt.taxonomy = '%s'
		and t.slug = '%s'
		order by u.post_date_gmt desc
		limit %d offset %d;";

	$offset = ($paged - 1) * $posts_per_page;
	$q = $wpdb->prepare($q, $taxonomy, $slug, $per_page, $offset);

	$project_updates = $wpdb->get_results($q, OBJECT);

	if ($project_updates) {
		global $post;
		foreach ($project_updates as $post) {
			setup_postdata($post);
			include('templates/loop-recently-updated-projects.php');
		}
	}

	wp_reset_postdata();
	wp_reset_query();

	wp_die();

}

