<?php

add_action( 'wp_ajax_card_latest_project_updates', 'card_latest_project_updates' );
add_action( 'wp_ajax_nopriv_card_latest_project_updates', 'card_latest_project_updates' );

function card_latest_project_updates( $qry = null ) {

	$id = filter_var($_GET['projectid'], FILTER_SANITIZE_NUMBER_INT);
	$paged = filter_var($_GET['paged'], FILTER_SANITIZE_NUMBER_INT);
	$posts_per_page = filter_var($_GET['count'], FILTER_SANITIZE_NUMBER_INT);

	if ($results = get_project_updates($id, $posts_per_page, $paged)) {
		while ($results->have_posts()): $results->the_post();
			get_template_part( 'templates/template', 'related-project');
		endwhile;
	}

	wp_reset_postdata();
	wp_die();

}

