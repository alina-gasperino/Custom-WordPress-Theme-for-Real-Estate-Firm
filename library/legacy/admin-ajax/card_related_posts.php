<?php

add_action( 'wp_ajax_card_related_posts', 'card_related_posts' );
add_action( 'wp_ajax_nopriv_card_related_posts', 'card_related_posts' );

function card_related_posts( $qry = null ) {

	$id = filter_var($_GET['projectid'], FILTER_SANITIZE_NUMBER_INT);
	$paged = filter_var($_GET['paged'], FILTER_SANITIZE_NUMBER_INT);
	$posts_per_page = filter_var($_GET['count'], FILTER_SANITIZE_NUMBER_INT);

	if ($results = get_related_posts_condo($id, $posts_per_page, $paged)) {
		while ($results->have_posts()): $results->the_post();
			get_template_part( 'templates/template', 'related-project');
		endwhile;
	}

	wp_reset_postdata();
	wp_die();

}
