<?php

add_action( 'wp_ajax_project_updates', 'ajax_project_updates' );
add_action( 'wp_ajax_nopriv_project_updates', 'ajax_project_updates' );

function ajax_project_updates() {

	$posts_per_page = ($_GET['count']) ? filter_var($_GET['count'], FILTER_SANITIZE_NUMBER_INT) : 5;
	$paged = ($_GET['paged']) ? filter_var($_GET['paged'], FILTER_SANITIZE_NUMBER_INT ) : 1;

	// 1184, 788
	$args = array(
		'post_type' => 'post',
		'posts_per_page' => $posts_per_page,
		'post_status' => 'publish',
		'paged' => $paged,
		'category__in' => [788, 1179], // in the news (788) or project updates (1179) categories
		);

	$project_updates = new WP_Query($args);

	if ($project_updates->have_posts()) {
		while ($project_updates->have_posts()) {
			$project_updates->the_post();
			include get_stylesheet_directory() . '/templates/loop-recently-updated-projects.php';
		}
	}

	wp_reset_query();
	wp_die();

}


function ajax_project_updates_custom() {

	global $wpdb;

	$posts_per_page = ($_GET['count']) ? filter_var($_GET['count'], FILTER_SANITIZE_NUMBER_INT) : 5;
	$paged = ($_GET['paged']) ? filter_var($_GET['paged'], FILTER_SANITIZE_NUMBER_INT ) : 1;

	$q = "select * from $wpdb->posts as p
	left join $wpdb->term_relationships as tr on tr.object_id = p.ID
	left join $wpdb->term_taxonomy as tt on tt.term_taxonomy_id = tr.term_taxonomy_id
	left join $wpdb->terms as t on t.term_id = tt.term_id
	where p.post_type = 'project_update'
	or (p.post_type = 'post' and t.slug in ('news'))
	order by p.post_date_gmt desc
	limit %d offset %d;
	";

	$offset = ($paged - 1) * $posts_per_page;
	$q = $wpdb->prepare( $q, $posts_per_page, $offset );

	$project_updates = $wpdb->get_results($q, OBJECT);

	if ($project_updates) {
		global $post;
		foreach ($project_updates as $post) {
			setup_postdata($post);
			include get_stylesheet_directory() . '/templates/loop-recently-updated-projects.php';
		}
	}

	wp_reset_query();
	wp_die();

}


function ajax_project_updates_old() {

	$posts_per_page = ($_GET['count']) ? filter_var($_GET['count'], FILTER_SANITIZE_NUMBER_INT) : 5;
	$paged = ($_GET['paged']) ? filter_var($_GET['paged'], FILTER_SANITIZE_NUMBER_INT ) : 1;

	$args = array(
		'post_type' => 'post',
		'posts_per_page' => $posts_per_page,
		'paged' => $paged,
		);

	$project_updates = new WP_Query($args);

	if ($project_updates->have_posts()) {
		while ($project_updates->have_posts()) {
			$project_updates->the_post();
			include get_stylesheet_directory() . '/templates/loop-recently-updated-projects.php';
		}
	}
	wp_die();
}


