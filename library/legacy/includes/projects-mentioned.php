<?php

// $reg = "/<a [^>]+>[^<]+<[^>]+>/";
$reg = "/(?<=href=\")[^\"]*(?=\")/";

$ret = preg_match_all($reg, get_the_content(), $matches);

$slugs = array();

foreach ($matches[0] as $match) {

	## eliminate external links
	if ( strpos($match, home_url()) !== 0 ) continue;

	## eliminate media files (things linked in the uploads folder)
	if ( strpos($match, 'wp-content/uploads') !== false ) continue;

	$match = rtrim($match, '/');
	$segments = explode('/', $match);
	$slug = array_pop($segments);
	$slugs[] = $slug;

	## eliminate media files
	// foreach ( array('.jpg', '.jpeg', '.png', '.pdf') as $exclusion ) {
		// if ( strpos( $slug, $exclusion ) !== false ) {
			// array_pop($slugs);
		// }
	// }

}

if ($slugs){
	$slugstring = "'" . implode("','", $slugs) . "'";	
	if ($slugstring) {

		$q = "select $wpdb->posts.ID from $wpdb->posts where $wpdb->posts.post_type = 'project' and $wpdb->posts.post_name in ($slugstring) ";
	
		$results = $wpdb->get_results($q, OBJECT);

		$ids = array();
	
		foreach ($results as $result) {
			$ids[] = $result->ID;
		}
	
		$args = array(
			'post_type' => 'project',
			'post__in' => $ids
			);
	
		$mentions = new WP_Query($args);
	
		if ($mentions->have_posts()) {
	
			$params = array(
				'linking' 	=> '',
				'columns' 	=> '4',
				'items'		=> '16',
				'contents' 	=> 'title',
				'sort' 		=> 'no',
				'paginate' 	=> 'no',
				'set_breadcrumb' => false,
			);
	
			$grid = new talk_project_grid( $params );
	
			$grid->set_entries( $mentions );
	
			echo '<div class="project-mentions clearfix"><div class="container">';
			echo '<h2>Projects mentioned in this article</h2>';
			echo $grid->html();
			echo '</div></div>';
	
		}
	
		wp_reset_query();
	
	}
}


