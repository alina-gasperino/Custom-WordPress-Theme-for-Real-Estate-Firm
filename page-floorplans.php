<?php
global $avia_config;

add_filter( 'avf_title_tag', 'test_filter', 20, 2 );
function test_filter( $title, $description ) {
	$fpproject = get_query_var('fpproject');
	$fpfolder = get_query_var('fpfolder');

	$args = array(
		'post_type' => 'project',
		'name' => $fpproject,
		'meta_key' => 'floorplanslink',
		'meta_compare' => 'like',
		'meta_value' => "$fpfolder",
	);

	if (!is_live()) {
		unset($args['meta_key']);
		unset($args['meta_compare']);
		unset($args['meta_value']);
	}

	$qry = new WP_Query($args);

	if ($qry->have_posts()) {
		$qry->the_post();
		$title = get_the_title() . ' ' . $title;
	}

	wp_reset_query();

	return $title;

}

get_header();

if( get_post_meta(get_the_ID(), 'header', true) != 'no') echo avia_title();

$fpproject = get_query_var('fpproject');
$fpfolder = get_query_var('fpfolder');

$args = array(
	'post_type' => 'project',
	'name' => $fpproject,
	'meta_key' => 'floorplanslink',
	'meta_compare' => 'like',
	'meta_value' => "$fpfolder",
);

if (!is_live()) {
	unset($args['meta_key']);
	unset($args['meta_compare']);
	unset($args['meta_value']);
}

$qry = new WP_Query($args);

if ($qry->have_posts()) {
	$qry->the_post();

	$floorplansfolder = get_post_meta( get_the_ID(), 'floorplansfolder', true);
	$googledrive = (strpos($floorplansfolder, 'drive.google.com') !== false);

	if ($googledrive) {
		$a = parse_url($floorplansfolder, PHP_URL_QUERY);
		parse_str($a, $b);
		$id = $b['id'];
	} else {
		$layout = 'fullsize';
		$avia_config['layout']['current'] = $avia_config['layout'][$layout];
		$avia_config['layout']['current']['main'] = $layout;
	}

	if ($googledrive) {
		get_template_part( 'templates/template', 'floorplans-files' );
	} else {
		get_template_part( 'templates/template', 'floorplans-soldout' );
	}

} else {

	get_template_part( 'library/legacy/includes/loop', 'page' );

}

?>

