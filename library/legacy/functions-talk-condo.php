<?php

defined( 'ABSPATH' ) || die();

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/tc-db.php';

if ('https://www.talkcondo.com' == home_url()) {
	define('GOOGLE_MAPS_API_BROWSER_KEY', 'AIzaSyD2AoffC7XLsF4AgViZ53cOFIpYUq9xoxE');
	define('GOOGLE_MAPS_API_SERVER_KEY', 'AIzaSyDvQZUNNmJwP4jy1fv2IOTZmwB5D4BJh5g');
} else {
	define('GOOGLE_MAPS_API_BROWSER_KEY', 'AIzaSyB15SMXB4Z7iQ1iISxnyIf7HQbAJLSi2oc');
	define('GOOGLE_MAPS_API_SERVER_KEY', 'AIzaSyDvQZUNNmJwP4jy1fv2IOTZmwB5D4BJh5g');
}

class CustomGoogleProvider extends \Geocoder\Provider\GoogleMaps {

	public function __construct($adapter = '', $locale = 'en', $region = 'ca', $useSsl = true, $apiKey = GOOGLE_MAPS_API_SERVER_KEY) {
		$adapter = new \Ivory\HttpAdapter\CurlHttpAdapter;
		parent::__construct($adapter, $locale, $region, $useSsl, $apiKey);
	}

	public function setApiKey($apiKey) {
		$this->apiKey = $apiKey;
	}

	protected function buildQuery($query) {
		$query = parent::buildQuery($query);
		$query = sprintf('%s&bounds=%s', $query, '43.60359581629954,-79.93012224648436|43.93778824520336,-78.55683123085936');
		return $query;
	}

}

require_once 'custom_post_types.inc.php';
require_once 'custom_taxonomies.inc.php';
require_once 'shortcodes/talk_project_grid.php';
require_once 'shortcodes/talk_assignment_grid.php';

## Image Thumbnails
add_image_size('project-logo', 9999, 60, false);
add_image_size('flexslider', 560, 355, array('center', 'center'));
add_image_size('flexsliderthumb', 85, 85, array('center', 'center'));

if( ! defined( 'SHORTINIT' ) && ! 'SHORTINIT' ) {
## Sidebars
	register_sidebar( array(
		'id'            => 'sidebar-7',
		'name'          => 'Project Sidebar',
		'before_widget' => '<div id="%1$s" class="widget clearfix %2$s">',
		'after_widget'  => '<span class="seperator extralight-border"></span></div>',
		'before_title'  => '<h3 class="widgettitle">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'id'            => 'sidebar-8',
		'name'          => 'Assignment Sidebar',
		'before_widget' => '<div id="%1$s" class="widget clearfix %2$s">',
		'after_widget'  => '<span class="seperator extralight-border"></span></div>',
		'before_title'  => '<h3 class="widgettitle">',
		'after_title'   => '</h3>',
	) );
}


add_filter( 'manage_project_update_posts_columns', 'project_update_add_project_column', 5 );
function project_update_add_project_column($cols) {
	unset( $cols['date'] );
	return array_merge( $cols, array(
		'project' => "Project",
		'date' => "Date"
	));
}

add_action( 'manage_project_update_posts_custom_column', 'custom_project_update_column', 10, 2 );
function custom_project_update_column( $col, $post_id ) {

	if ($col == 'project') {
		$arr = get_post_meta( $post_id, 'project', true);
		$project = get_post($arr[0]);
		echo $project->post_title;
	}

}

add_filter('post_link', 'custom_blog_post_permalink', 1, 3);
function custom_blog_post_permalink($permalink, $post_id, $leavename) {

	$post = get_post($post_id);

	if ( $post && $post->post_type == 'post' ) {
		$permalink =  get_home_url() . '/blog/' . $post->post_name;
	}

	return $permalink;

}

add_filter('post_type_link', 'moi_city_permalink', 1, 3);
function moi_city_permalink($permalink, $post_id, $leavename) {
	$post = get_post($post_id);
	if (!$post) return $permalink;
	if ( $post->post_type == 'project' ) {
		// Get taxonomy terms
		$terms = wp_get_object_terms( $post->ID, 'city');
		if (!is_wp_error($terms) && ! empty($terms[0]->slug)) {
			$permalink = str_replace('/project/', '/' . $terms[0]->slug . '/', $permalink);
		}
	} elseif ( $post->post_type == 'assignment' ) {
		// $permalink = str_replace('/assignment/', '/', $permalink);
	} elseif ( $post->post_type == 'project_update' ) {
		// $permalink = str_replace('/project_update/', '/', $permalink);
	}
	return $permalink;
}


## add custom rewrites for urls like /{city}/project-name/
add_filter('rewrite_rules_array', 'filter_project_rewrite_rules');
function filter_project_rewrite_rules($rules) {

	$before_rules = array();

	$before_rules["blog/([^/]+)/?$"] = 'index.php?name=$matches[1]';
	$before_rules["news/([^/]+)/?$"] = 'index.php?category=$matches[1]';
	$before_rules["^floorplans/([^/]+)/([^/]+)/?$"] = 'index.php?pagename=floorplans&fpproject=$matches[1]&fpfolder=$matches[2]';
	// $before_rules["floorplans/([^/]+)/([^/]+)/?$"] = 'index.php?project=$matches[1]&paged=2&floorplans=$matches[2]';
	$before_rules["^([^/]+)/floorplans/?$"] = 'index.php?project=$matches[1]&floorplans=1';
	// $before_rules["^([^/]+)/floorplans/?$"] = 'index.php';

	$terms = get_terms('city');
	foreach ($terms as $city) {
		$before_rules["^($city->slug)/page/([0-9]{1,})?$"] = 'index.php?city=$matches[1]&paged=$matches[2]';
		// $before_rules["($city->slug)/(.+)/floorplans/(.+)/?$"] = 'index.php?project=$matches[2]&floorplans=$matches[3]';
		$before_rules["^($city->slug)/([^/]+)/floorplans/?$"] = 'index.php?project=$matches[2]&floorplans=1';
		$before_rules["^($city->slug)/([^/]+)/floorplans/([^/]+)/reserve/?$"] = 'index.php?project=$matches[2]&floorplan=$matches[3]&reserve=true';
		$before_rules["^($city->slug)/([^/]+)/floorplans/([^/]+)/?$"] = 'index.php?project=$matches[2]&floorplan=$matches[3]';
		$before_rules["^($city->slug)/([^/]+)/?$"] = 'index.php?project=$matches[2]';
		$before_rules["^($city->slug)/?$"] = 'index.php?city=$matches[1]';
	}

	$after_rules = array();

	return $before_rules + $rules + $after_rules;

}


// add_action( 'pre_get_posts', 'show_all_posts' );
// function show_all_posts( $query ) {
// 	if ( $query->is_main_query() ) {
// 		$query->set( 'posts_per_page', '-1' );
// 	}
// }


// add_action('save_post', 'save_gallery_as_featured_image');
// function save_gallery_as_featured_image( $post_id ) {

// 	if ( wp_is_post_revision($post_id) ) return;

// 	$images = get_field('gallery', $post_id);

// 	if ($images) {
// 		set_post_thumbnail($post_id, $images[0]['id']);
// 	}

// }

// add_action('save_post', 'calculate_floorplan_totals');
// function calculate_floorplan_totals( $post_id ) {
// if ( wp_is_post_revision($post_id) ) return;
// $floorplans = get_field('floorplans', $post_id);
// }


apply_filters("kriesi_backlink", "");

add_filter('avia_post_grid_query', 'avf_custom_post_grid_query');
function avf_custom_post_grid_query ( $query ) {
	$query['orderby'] = 'rand';
	return $query;
}


add_theme_support('avia_template_builder_custom_post_type_grid');


function get_developers( $id ) {
	$terms = get_the_terms( $id, 'developer' );
	if (is_array($terms)) {
		$developers = array();
		foreach ($terms as $dev) {
			$developers[] = $dev->name;
		}
		return get_the_excerpt($id) . ' by: ' . implode(' & ', $developers);
	}
}

function custom_cat_link( $taxonomy, $id = '' ) {
	global $post;
	$id = ($id) ?: get_the_ID();
	return get_the_term_list( $id, $taxonomy, '', ' & ', '' );
}

function custom_cat_text( $taxonomy, $id = '' ) {
	global $post;
	$id = ($id) ?: get_the_ID();
	$terms = get_the_terms( $id, $taxonomy );
	if ( !$terms || is_wp_error($terms) ) return;
	$names = [];
	foreach ($terms as $term) {
		$names[] = $term->name;
	}
	return implode(' & ', $names);
}

function custom_cat_slug( $taxonomy, $id = '' ) {
    global $post;
    $id = ($id) ?: get_the_ID();
    $terms = get_the_terms( $id, $taxonomy );
    return (is_array($terms)) ? reset($terms)->slug : '';
}


// Replaces the excerpt "more" text by a link
add_filter('excerpt_more', 'new_excerpt_more');
function new_excerpt_more($more) {
	global $post;
	return '<a class="readmore" href="'. get_permalink($post->ID) . '"> Read more...</a>';
}


function parse_google_map_url($map_url) {

	preg_match('/@[^z]*z/', $map_url, $matches);

	$data = array();

	list($lat, $lng, $zoom) = explode(',', rtrim(ltrim($matches[0], '@'), 'z'));

	return array(
		'lat' => $lat,
		'lng' => $lng,
		'zoom' => $zoom
	);

}


function address_from_map_url($map) {

	$address = substr($map, strpos($map, '/place/') + strlen('/place/'));
	$address = substr($address, 0, strpos($address, '/@'));
	$address = urldecode($address);

	return $address;

}


function map_shortcode_from_url($map_url) {

	list( $lat, $lng, $zoom ) = parse_google_map_url($map_url);

	$zoom = 15;
	$title = get_the_title();
	$map_shortcode  = "[av_google_map height='300px' zoom='$zoom' saturation='' hue='#dd9933' zoom_control='aviaTBzoom_control' pan_control='aviaTBpan_control' mobile_drag_control='aviaTBmobile_drag_control'] [av_gmap_location long='$lng' lat='$lat' marker='' attachment='' imagesize='40'] $title [/av_gmap_location] [/av_google_map]";

	return do_shortcode($map_shortcode);

}

function get_project_labels($post_id = '') {

	global $post;
	if (!$post_id) $post_id = $post->ID;

	$labels = '';
	$terms = get_the_terms( $post_id, 'salesstatus' );
	$salesstatus = (is_array($terms)) ? reset($terms)->name : '';

	if ( $salesstatus ) {
		$labels .= '<div class="salesstatus"><span class="project-label">' . $salesstatus . '</span></div>';
	}

	// explode the extra info labels field
	foreach ( explode( '&', get_post_meta( $post_id, 'customtags', true ) ) as $tag ) {
		if ($tag) {
			$labels .= '<span class="project-label infotag">' . $tag . '</span>';
		}
	}

	if ($labels) $labels = "<div class='project-labels'>$labels</div>";

	return $labels;

}


add_filter( 'avia_breadcrumbs_trail', 'project_breadcrumbs', 2, 5 );
function project_breadcrumbs($trail, $args) {
    global $post;

	$taxonomies = array('district','city','neighbourhood');

	if (is_tax() && in_array(get_queried_object()->taxonomy, $taxonomies)) {

		$trail = array();
		$trail[] = '<a href="' . home_url() . '" title="' . esc_attr( get_bloginfo( 'name' ) ) . '" rel="home" class="trail-begin">' . __( 'Home', 'avia_framework' ) . '</a>';

		foreach ($taxonomies as $tax) {

			if ($tax == get_queried_object()->taxonomy) {
				$trail['trail_end'] = get_queried_object()->name;
				break;
			}

			if ( $terms = get_the_terms($post->ID, $tax) ) {
				foreach ($terms as $term) {
					$trail[] = '<a href="' . get_term_link($term->term_id, $tax) . '">' . $term->name . '</a>';
				}
			}

		}

	}

	if (is_single() && get_queried_object()->post_type == 'project') {

		$trail = array();
		$trail[] = '<a href="' . home_url() . '" title="' . esc_attr( get_bloginfo( 'name' ) ) . '" rel="home" class="trail-begin">' . __( 'Home', 'avia_framework' ) . '</a>';

		foreach ($taxonomies as $tax) {
			if ( $terms = get_the_terms($post->ID, $tax) ) {
				foreach ($terms as $term) {
					$trail[] = '<a href="' . get_term_link($term->term_id, $tax) . '">' . $term->name . '</a>';
				}
			}
		}

		$trail['trail_end'] = get_queried_object()->post_title;

	}

	return $trail;

}

function regenerate_project_data( $post = '' ){
	global $wpdb;

	$post = get_post( $post );

	if( $post->post_type !== 'project' ){
		return false;
	}

	$floorplans = get_field( 'floorplans', $post->ID );

	if ( $floorplans && is_array( $floorplans ) ) {
		$data = array_reduce( $floorplans, function ( $data, $floorplan ){
			if ( $floorplan['availability'] !== 'Available' ){
				return $data;
			}

			if( $price = (int) $floorplan['price'] ){
				$data['min_price'] = $data['min_price'] ? min( $data['min_price'], $price ) : $price;
				$data['max_price'] = $data['max_price'] ? max( $data['max_price'], $price ) : $price;
			}

			if( $size = (int) $floorplan['size'] ){
				$data['min_size'] = $data['min_size'] ? min( $data['min_size'], $size ) : $size;
				$data['max_size'] = $data['max_size'] ? max( $data['max_size'], $size ) : $size;
			}

			if( $beds = (float) $floorplan['beds'] ){
				$data['min_beds'] = $data['min_beds'] ? min( $data['min_beds'], $beds ) : $beds;
				$data['max_beds'] = $data['max_beds'] ? max( $data['max_beds'], $beds ) : $beds;
			}

			if( $baths = (float) $floorplan['baths'] ){
				$data['min_baths'] = $data['min_baths'] ? min( $data['min_baths'], $baths ) : $baths;
				$data['max_baths'] = $data['max_baths'] ? max( $data['max_baths'], $baths ) : $baths;
			}

			return $data;
		}, [] );

		$wpdb->query('START TRANSACTION');
		foreach ( $data as $key => $val ){
			update_post_meta( $post->ID, '_' . $key, $val );
		}

		return $wpdb->query('COMMIT');
	}

	return false;
}

// add_filter( 'avia_breadcrumbs_trail', 'region_breadcrumbs' );
function region_breadcrumbs($trail) {

	global $wp_query;
	global $post;

	/* Get some taxonomy and term variables. */
	$term = $wp_query->get_queried_object();
	$taxonomy = get_taxonomy( $term->taxonomy );

	if ( is_tax() && in_array($term->taxonomy, array('district', 'city', 'neighbourhood')) ) {

		$trail = array();
		$trail[] = '<a href="' . home_url() . '" title="' . esc_attr( get_bloginfo( 'name' ) ) . '" rel="home" class="trail-begin">' . __( 'Home', 'avia_framework' ) . '</a>';

		if (have_posts()) {
			$district = (is_array(get_the_terms( $post->ID, 'district' ))) ? reset(get_the_terms( $post->ID, 'district' )) : '';
			$city = (is_array(get_the_terms( $post->ID, 'city' ))) ? reset(get_the_terms( $post->ID, 'city' )) : '';
			$neighbourhood = (is_array(get_the_terms( $post->ID, 'neighbourhood' ))) ? reset(get_the_terms( $post->ID, 'neighbourhood' )) : '';
		}

		if ($term->taxonomy == 'city' && $district) {
			$trail[] = '<a href="' . get_term_link($district, 'district') . '">' . $district->name . '</a>';
		}

		if ($term->taxonomy == 'neighbourhood' && $city) {
			$trail[] = '<a href="' . get_term_link($city, 'city') . '">' . $city->name . '</a>';
		}

		$trail['trail_end'] = $term->name;

	}

	return $trail;

}


// add_action('ava_after_main_container', 'home_project_updates_banner');
function home_project_updates_banner() {

	if ( is_front_page() ) {

		$args = array(
			'post_type' => 'project_update',
			'orderby' => 'date',
			'order' => 'desc',
		);

		$updates = new WP_Query($args);
		$recent_updates = array();

		if ($updates->have_posts()) {

			while ($updates->have_posts()) {
				$updates->the_post();

				$threshold = strtotime('-1 month',time());

				if (strtotime(get_the_date()) > $threshold) {
					$recent_updates[strtotime(get_the_date())] = get_the_title();
				}

			}

			wp_reset_query();

		} ?>


		<?php if ($recent_updates): ?>
            <div class="container">
                <div id="recent-updates">
					<?php $latest = reset($recent_updates); ?>
					<?php $count = count($recent_updates) ?>
                    <span class='badge'><?php echo $count ?></span>NEW PROJECT UPDATE<?php if ($count > 1) echo 'S' ?>! <a href='#recently-updated-projects'>Click here to read the latest update: <?php echo $latest ?></a>
                </div>
            </div>
		<?php endif; ?>
		<?php
	}

}


add_filter( 'query_vars', 'custom_query_vars' , 10, 1 );
function custom_query_vars( $qvars ) {
	$qvars[] = 'city';
	$qvars[] = 'neighbourhood';
	$qvars[] = 'fpproject';
	$qvars[] = 'fpfolder';
	$qvars[] = 'reserve';
	$qvars[] = 'floorplans';
	$qvars[] = 'floorplan';
	return $qvars;
}


function getPolygons( $file = '' ) {

	if (!$file) $file = get_stylesheet_directory() . '/library/storage/geojson.json';
	if (!file_exists($file)) return false;
	$geojson = json_decode(file_get_contents($file));

	return $geojson;
}


function calculateCenter($coordinates) {
	if ( empty( $coordinates ) || ! is_array( $coordinates ) ) {
		return false;
	}

	$lngs = array_column( $coordinates, 0 );
	$lats = array_column( $coordinates, 1 );

	if ($lngs && $lats) {
		// $center = array( array_sum($lngs) / count($lngs), array_sum($lats) / count($lats) );
		$center = array(
			'lat' => array_sum($lats) / count($lats),
			'lng' => array_sum($lngs) / count($lngs),
		);
	}

	return $center;
}


function parseYoutubeUrl($url) {

	parse_str(parse_url($url, PHP_URL_QUERY), $vars);

	if( isset( $vars['v'] ) ) {
		return $vars['v'];
	}

	return null;

}


function parseMapLink($link) {
	$link = substr( $link, strpos($link, '@'));
	$link = ltrim( $link, '@');
	$link = substr( $link, 0, strpos($link, 'z'));

	return explode(',', $link);
}

require_once('gsheet_import/gsheet_import.php');


function map_data_folder() {
	return get_stylesheet_directory() . "/library/legacy/gsheet_import/data/";
}


function get_map_data($tax = '', $term = '') {

	$filename = ($tax && $term) ? "mapData-{$tax}-{$term}.json" : "mapData.json";
	$datafile = map_data_folder() . $filename;

	if (file_exists($datafile)) {

		$mapdata = json_decode( file_get_contents($datafile) );

	} else {

		$params = array(
			'post_type' => 'project',
			'posts_per_page' => -1,
			'order_by' => ['title' => 'asc'],
			'no_rows_found' => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'fields' => 'ids',
		);

		if ($tax && $term) {
			$params['tax_query'] = array(
				array(
					'taxonomy' => $tax,
					'field' => 'slug',
					'terms' => $term,
				)
			);
		}

		$qry = new WP_Query($params);

		$mapdata = generate_map_data($qry);

		if (is_local()) {
			file_put_contents( $datafile, json_encode( $mapdata, JSON_PRETTY_PRINT ) );
		} else {
			file_put_contents( $datafile, json_encode( $mapdata ) );
		}

	}

	return $mapdata;

}


function clear_map_data() {

	$files = glob( map_data_folder() . "mapData*.json");
	foreach ($files as $file) {
		unlink($file);
	}

	$timestamp = round(microtime(true));
	update_option('talkmap_timestamp', $timestamp);

}


function get_project_gallery($id, $size = '') {
	$data = [];
	if (!$gallery = get_field('gallery', $id)) {
		return false;
	}
	foreach ($gallery as $image) {
		$data[] = ($size) ? $image['sizes'][$size] : $image['url'];
	}
	return $data;
}


function extract_leadpageslink($leadpagesform) {
	preg_match('/http[^"]+(?=")/', $leadpagesform, $matches);
	return $matches[0];
}


function project_available_floorplans( $post_id = '' ) {
	global $post;

	$post_id = ($post_id) ?: $post->ID;

	$floorplans = get_field('floorplans', $post_id);

	$set = [];
	if (is_array($floorplans)) {
		$set = array_filter($floorplans, function ($floorplan) {
			return $floorplan['availability'] == 'Available';// && $floorplan['price'];
		});
	}

	return $set;
}


function project_soldout_floorplans( $post_id = '' ) {
	global $post;

	$post_id = ($post_id) ?: $post->ID;

	$floorplans = get_field('floorplans', $post_id);

	$set = [];
	if (is_array($floorplans)) {
		$set = array_filter($floorplans, function ($floorplan) {
			return $floorplan['availability'] == 'Sold Out';
		});
	}

	return $set;
}


function generate_map_data( $qry = null ) {

	if (!$qry) return false;

	$queried_object = $qry->get_queried_object();

	$data['taxonomy'] = $queried_object->taxonomy;

	$data = array();
	$data['home_url'] = trailingslashit( home_url() );
	$data['theme_url'] = trailingslashit( get_stylesheet_directory_uri() );
	$data['uploads_url'] = $data['home_url'] . 'wp-content/uploads/';
	$data['projects'] = array();
	$soldout = $top = $other = array();

	$taxonomies = ['district', 'city', 'neighbourhood', 'status', 'salesstatus', 'occupancy-date', 'developer', 'type'];

	## if local, override to live site to be able to pull remote images
	if (is_local()) $data['uploads_url'] = 'https://www.talkcondo.com/wp-content/uploads/';

	// $data['projects'] = array_merge($top, $other, $soldout);
	// $data['projects'] = $qry->posts;


	// taxonomies
	$data['taxonomies'] = array();
	foreach (get_terms($taxonomies) as $term_obj) {
	    $tax = ( $term_obj->taxonomy == 'occupancy-date' ) ? 'occupancy_date' : $term_obj->taxonomy;

		$data['taxonomies'][$tax][$term_obj->term_id] = $term_obj;
		if ($tax == 'neighbourhood') {
			$center = json_decode( get_field('center', $term_obj) );
			$coordinates = json_decode( get_field('map_geometry', $term_obj) );

			if ( $coordinates ) {
				$data['taxonomies'][$tax][$term_obj->term_id]->coordinates = $coordinates;

			    if( ! $center ) {
				    $center = calculateCenter( $coordinates );
				    update_field( 'center', json_encode( $center ), $term_obj );
			    }
			}

			if ($center) $data['taxonomies'][$tax][$term_obj->term_id]->center = $center;
		}
	}
	return $data;
}

function pointInPolygon($point, $vertices)
{

	// close the loop
	if ( $vertices[0] != $vertices[count($vertices) - 1]) {
		$vertices[] = $vertices[0];
	}

	// Check if the point sits exactly on a vertex
	foreach($vertices as $vertex) {
		if ($point == $vertex) {
			return true;
		}
	}

	$x = $point[0];
	$y = $point[1];

	// Check if the point is inside the polygon or on the boundary
	$intersections = 0;
	$vertices_count = count($vertices);

	for ($i=1; $i < $vertices_count; $i++) {
		$x1 = $vertices[$i-1][0];
		$y1 = $vertices[$i-1][1];
		$x2 = $vertices[$i][0];
		$y2 = $vertices[$i][1];

		// Check if point is on an horizontal polygon boundary
		if ($y1 == $y2 and $y1 == $y and $x > min($x1, $x2) and $x < max($x1, $x2)) {
			return true;
		}

		if ($y > min($y1, $y2) and $y <= max($y1, $y2) and $x <= max($x1, $x2) and $y1 != $y2) {
			$xinters = ($y - $y1) * ($x2 - $x1) / ($y2 - $y1) + $x1;
			if ($xinters == $x) { // Check if point is on the polygon boundary (other than horizontal)
				return true;
			}
			if ($x1 == $x2 || $x <= $xinters) {
				$intersections++;
			}
		}

	}

	// If the number of edges we passed through is odd, then it's in the polygon.
	return ($intersections % 2);

}


function get_similar_projects( $post_id ) {

	$terms = array();

	if ($cities_objects = get_the_terms( $post_id, 'city' )) {
		foreach ($cities_objects as $city_object) {
			$terms[$city_object->slug] = 'city';
		}
	}

	if ($neighbourhood_objects = get_the_terms( $post_id, 'neighbourhood' )) {
		foreach ($neighbourhood_objects as $neighbourhood_object ) {
			$terms[$neighbourhood_object->slug] = 'neighbourhood';
		}
	}

	$terms = array_reverse($terms);

	foreach ($terms as $term => $taxonomy) {
		$args = array(
			'post_type' => 'project',
			'posts_per_page' => '4',
			'orderby' => 'rand',
			'tax_query' => array(
				array(
					'taxonomy' => $taxonomy,
					'field' => 'slug',
					'terms' => $term,
				)
			)
		);
		$similar_projects = new WP_Query($args);
		if ($similar_projects->have_posts()) return $similar_projects;
	}
	return false;
}


function get_platinum_projects( $post_id ) {

	$args = array(
		'post_type' => 'project',
		'posts_per_page' => 12,
		'tax_query' => array(
			array(
				'taxonomy' => 'salesstatus',
				'field' => 'slug',
				'terms' => 'platinum-access',
			)
		),
	);

	$platinum = new WP_Query($args);

	if ($platinum->have_posts()) return $platinum;

	return false;
}


function get_project_assignments( $post_id ) {

	$args = array(
		'post_type' => 'assignment',
		'meta_query' => array(
			array(
				'key' => 'project',
				'value' => $post_id
			)
		),
	);

	$assignments = new WP_Query( $args );

	return ($assignments->have_posts()) ? $assignments : false;

}


function get_project_updates( $post_id, $posts_per_page = -1, $paged = 1 ) {

	$args = array(
		'post_type' => ['post'],
		'posts_per_page' => $posts_per_page,
		'paged' => $paged,
		'orderby' => ['date' => 'desc'],
		'meta_query' => array(
			array(
				'key' => 'project',
				'value' => '"' . $post_id . '"',
				'compare' => 'LIKE'
			)
		)
	);

	$updates = new WP_Query($args);

	return ($updates->have_posts()) ? $updates : false;

}


function get_recent_updates ( $post_id ) {

	$args = array(
		'post_type' => ['post'],
		'orderby' => ['date' => 'desc'],
		'posts_per_page' => -1,
		'date_query' => [
			'inclusive' => true,
			'after' => '-1 month',
		],
		'meta_query' => array(
			array(
				'key' => 'project',
				'value' => '"' . $post_id . '"',
				'compare' => 'LIKE'
			)
		)
	);

	$updates = new WP_Query($args);

	return ($updates->have_posts()) ? $updates : false;
}

function get_related_posts_condo($post_id, $posts_per_page = -1, $paged = 1) {

	$terms = get_the_terms( $post_id, 'neighbourhood' );
	if (!is_array($terms)) return false;
	$tmp = get_the_terms( $post_id, 'neighbourhood' );
	$neighbourhood = reset($tmp);
	$args = [
		'post_type' => ['post', 'guide'],
		'posts_per_page' => $posts_per_page,
		'paged' => $paged,
		'orderby' => ['type' => 'ASC', 'date' => 'DESC'],
		'meta_query' => [
			[
				'key' => 'neighbourhood',
				'value' => '"' . $neighbourhood->term_id . '"',
				'compare' => 'LIKE'
			]
		],
	];
	$results = new WP_Query($args);

	return ($results->have_posts()) ? $results : false;
}

function floorplan_price_per_sqft( $floorplan = '' ) {

	if (is_array($floorplan) && $floorplan['price'] && $floorplan['size']) {
		return round($floorplan['price'] / $floorplan['size']);
	}

	return 0;
}

function project_suite_size_range( $post_id = '' ) {

	global $post;

	$post_id = ($post_id) ?: $post->ID;
	$floorplans = get_field('floorplans', $post_id);
	if (!is_array($floorplans)) return;

	$sizes = [];
	foreach ($floorplans as $floorplan)	{
		if ($floorplan['size']) $sizes[] = $floorplan['size'];
	}

	$min = min($sizes);
	$max = max($sizes);

	if ( $min && $min != $max ) {
		$size_range = sprintf("%s sq.ft to <br>%s sq.ft", $min, $max);
	} elseif ($min) {
		$size_range = sprintf("%s sq.ft", $min);
	}

	return ($size_range) ?: '-';
}

function project_avg_suite_size( $post_id = '' ) {

	global $post;

	$post_id = ($post_id) ?: $post->ID;
	$floorplans = get_field('floorplans', $post_id);
	if (!is_array($floorplans)) return;

	$sizes = [];
	foreach ($floorplans as $floorplan)	{
		if ($floorplan['size']) $sizes[] = $floorplan['size'];
	}

	if ($sizes) return round( array_sum($sizes) / count($sizes) );
}

function project_avg_price_per_sqft( $post_id = '' ) {

	global $post;

	$post_id = ($post_id) ?: $post->ID;
	$floorplans = get_field('floorplans', $post_id);
	if (!is_array($floorplans)) return;

	$avgs = [];
	foreach ($floorplans as $floorplan) {
		if ($floorplan['price'] && $floorplan['size']) {
			$avgs[] = $floorplan['price'] / $floorplan['size'];
		}
	}

	if ($avgs) return round( array_sum($avgs) / count($avgs) );
}

function project_sqftfrom( $post_id = '' ) {
	global $post;
	$post_id = ($post_id) ?: $post->ID;

	$floorplans = get_field('floorplans', $post_id);
	if ( is_array($floorplans) ) {
		$floorplans = array_filter($floorplans, function ($floorplan) {
			return ($floorplan['size']);
		});

		usort( $floorplans, function ($a, $b) {
			return ($a['size'] < $b['size']) ? -1 : 1;
		});

		$f = ($floorplans) ? reset($floorplans) : null;
		return $f['size'];
	} else {
		return get_field('sq.ftfrom');
	}
}

function project_sqftto( $post_id = '' ) {
	global $post;
	$post_id = ($post_id) ?: $post->ID;

	$floorplans = get_field('floorplans', $post_id);
	if ( is_array($floorplans) ) {
		$floorplans = array_filter($floorplans, function ($floorplan) {
			return ($floorplan['size']);
		});

		usort( $floorplans, function ($a, $b) {
			return ($a['size'] > $b['size']) ? -1 : 1;
		});

		$f = ($floorplans) ? reset($floorplans) : null;
		return $f['size'];
	} else {
		return get_field('sq.ftto');
	}
}

function project_pricedfrom( $post_id = '' ) {
	global $post;
	$post_id = ($post_id) ?: $post->ID;
	// $floorplans = get_field('floorplans', $post_id);
	$floorplans = project_available_floorplans($post_id);

	if ( is_array($floorplans) ) {
		$floorplans = array_filter($floorplans, function ($floorplan) {
			return ($floorplan['price']);
		});

		usort( $floorplans, function ($a, $b) {
			return $a['price'] - $b['price'];
		});

		$f = ($floorplans) ? reset($floorplans) : null;
		return ($f) ? '$' . number_format($f['price']) : '';

	} elseif ( get_field('pricedfrom') ) {
		// return preg_replace('/[^0-9]/', '', get_field('pricedfrom') );
		return get_field('pricedfrom');
	}
}

function project_locked() {
	if( get_queried_object()->post_name == 'floorplans' ) {
		// return false;
	}
	return false;
}

function project_pricedto( $post_id = '' ) {
	global $post;
	$post_id = ($post_id) ?: $post->ID;
	// $floorplans = get_field('floorplans', $post_id);
	$floorplans = project_available_floorplans($post_id);

	if ( is_array($floorplans) ) {
		$floorplans = array_filter($floorplans, function ($floorplan) {
			return ($floorplan['price']);
		});

		usort( $floorplans, function ($a, $b) {
			return $b['price'] - $a['price'];
		});

		$f = ($floorplans) ? reset($floorplans) : null;
		return ($f) ? '$' . number_format($f['price']) : '';

	} elseif ( get_field('pricedto') ) {
		// return preg_replace('/[^0-9]/', '', get_field('pricedto') );
		return get_field('pricedto');
	}
}

function project_text() {


	$post_id = get_the_ID();
	$output = get_transient( 'project_text_' . $post_id );

	if( $output === false ){

		$title = get_the_title();
		$developers = custom_cat_text('developer');
		$status = custom_cat_text('status');
		$salesstatus = custom_cat_text('salesstatus');
		$address = get_field('address');
		$city = custom_cat_text('city');
		$neighbourhood = custom_cat_text('neighbourhood');
		$occupancydate = custom_cat_text('occupancy-date');
		$storeys = get_field('storeys');
		$suites = get_field('suites');
		$architect = get_field('architect');
		$interiordesigner = get_field('interiordesigner');
		$meters = get_field('heightm');
		$feet = get_field('heightft');
		$areafrom = project_sqftfrom();
		$areato = project_sqftto();
		$pricedfrom = project_pricedfrom();
		$pricedto = project_pricedto();
		$cityrank = project_height_rank('city');
		$neighbourhoodrank = project_height_rank('neighbourhood');

		$sentences = [];

		$sentence = '';
		$sentence .= "$title is a new condominium development by $developers";
		if (strtolower($status) == 'complete') {
			$sentence .= " that is now complete";
		} elseif (strtolower($status) == 'pre-construction') {
			$sentence .= " currently in pre-construction";
		} elseif (strtolower($status) == 'under construction') {
			$sentence .= " currently under construction";
		}
		$sentence .= " located at $address, $city in the $neighbourhood neighbourhood";

		if (get_field('walk_score') && get_field('transit_score')) {
			$walkscore = json_decode(get_field('walk_score'));
			$transitscore = json_decode(get_field('transit_score'));
			$sentence .= " with a {$walkscore->walkscore}/100 walk score and a {$transitscore->transit_score}/100 transit score";
		} elseif (get_field('walk_score')) {
			$walkscore = json_decode(get_field('walk_score'));
			$sentence .= " with a {$walkscore->walkscore}/100 walk score";
		} elseif (get_field('transit_score')) {
			$transitscore = json_decode(get_field('transit_score'));
			$sentence .= " with a {$transitscore->transit_score}/100 transit score";
		}
		$sentences[] = $sentence;

		$sentence = '';
		if ($architect && $interiordesigner) {
			$sentence .= "$title is designed by {$architect} and will feature interior design by {$interiordesigner}";
		} elseif ($architect) {
			$sentence .= "$title is designed by {$architect}";
		} elseif ($interiordesigner) {
			$sentence .= "$title will feature interior design by {$interiordesigner}";
		}
		$sentences[] = $sentence;

		if ($occupancydate) {
			$sentences[] = "Development is scheduled to be completed in $occupancydate";
		}

		$sentence = '';
		if ($storeys && $meters && $feet && $suites) {
			$sentence .= "The project is $storeys storeys tall ({$meters}m, {$feet}ft) and has a total of $suites suites";
		} elseif ($storeys && $meters && $feet && !$suites) {
			$sentence .= "The project is $storeys storeys tall ({$meters}m, {$feet}ft)";
		} elseif ($storeys && $suites) {
			$sentence .= "The project is $storeys storeys tall and has a total of $suites suites";
		} elseif (!$storeys && $suites) {
			$sentence .= "The project has a total of $suites suites";
		}
		if ($areafrom && $areato) {
			$sentence .= " ranging from $areafrom sq.ft to $areato sq.ft";
		} elseif ($areafrom) {
			$sentence .= " ranging from $areafrom sq.ft";
		}
		$sentences[] = $sentence;

		$sentence = '';
		if ($cityrank && $neighbourhoodrank) {
			$sentence .= "$title is the #{$cityrank['rank']} tallest condominium in {$cityrank['region']} and the #{$neighbourhoodrank['rank']} tallest condominium in {$neighbourhoodrank['region']}";
		} elseif ($cityrank) {
			$sentence .= "$title is the #{$cityrank['rank']} tallest condominium in {$cityrank['region']}";
		} elseif ($neighbourhoodrank) {
			$sentence .= "$title is the #{$neighbourhoodrank['rank']} tallest condominium in {$neighbourhoodrank['region']}";
		}
		$sentences[] = $sentence;

		if (strtolower($salesstatus) == 'registration phase') {
			$sentences[] = "$title is currently in Registration Phase";
		} elseif (strtolower($salesstatus) == 'sold out') {
			$sentences[] = "$title is currently Sold Out";
		} elseif ($pricedfrom && $pricedto) {
			$sentences[] = "Suites are priced from $pricedfrom to $pricedto";
		} elseif ($pricedfrom) {
			$sentences[] = "Suites are priced from $pricedfrom";
		}

		$output = implode('. ', array_filter($sentences) ) . '.';

		set_transient( 'project_text_' . $post_id, $output, 12 * HOUR_IN_SECONDS );

	}

	return $output;

}

function column_list($text) {

	$arr = array_filter(explode("\n", $text));
	if (empty($arr)) return '';

	$half = ceil( count($arr) / 2 );

	$col1 = [];
	$col2 = [];
	foreach ($arr as $key => $val) {
		$arr[$key] = preg_replace("/-\s?/", '', $arr[$key]);
		$arr[$key] = '<li>' . trim($arr[$key]) . '</li>';
		if ($key < $half) $col1[] = $arr[$key];
		if ($key >= $half) $col2[] = $arr[$key];
	}

	$output = '';
	$output .= '<ul>' . implode('', $col1) . '</ul>';
	$output .= '<ul>' . implode('', $col2) . '</ul>';
	return $output;
}

function project_height_rank( $taxonomy, $post_id = '' ) {
	global $post, $wpdb;

	$post_id = absint( $post_id ) ?: $post->ID;

	$terms = get_the_terms( $post_id, $taxonomy );

	if ( $terms ) {
		$term   = reset( $terms );

		$sql = "
            SELECT COUNT(distinct p.ID)
            FROM {$wpdb->posts} AS p
              LEFT JOIN {$wpdb->term_relationships} AS tr
                ON p.ID = tr.object_id
              INNER JOIN {$wpdb->postmeta} AS pm
                ON p.ID = pm.post_id
            WHERE tr.term_taxonomy_id = %d
                AND pm.meta_key = 'heightm'
                AND pm.meta_value > 0
                AND pm.meta_value < %f
                AND p.post_type = 'project'
                AND p.post_status = 'publish'
        ";

		$q = $wpdb->prepare($sql, $term->term_taxonomy_id, get_field( 'heightm', $post_id ) );

		if ( $rank = $wpdb->get_var( $q ) ) {
			return (int) $rank;
		}
	}

	return null;
}

function sort_floorplans( $floorplans ) {
	if (!is_array($floorplans)) return false;
	usort( $floorplans, function ($a, $b) {
		if ($a['availability'] == 'Available' && $b['availability'] != 'Available') {
			return -1;
		}
		if ($b['availability'] == 'Available' && $a['availability'] != 'Available') {
			return 1;
		}
		return (int)$a['size'] - (int)$b['size'];
	});
	return $floorplans;
}

function get_floorplans_link( $attachment_id = null, $post = null ) {
    $post = get_post( $post );

	$permalink = rtrim( get_permalink( $post->ID ), '/');
	if (!$attachment_id) return "{$permalink}/floorplans/";
	$attachment = get_post( $attachment_id );
	return "{$permalink}/floorplans/{$attachment->post_name}/";
}

function leadpages_form_button($leadpagesform) {
    $leadpagesform = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $leadpagesform);
    return $leadpagesform;
}

function leadpages_form_button_extract_url($leadpagesform) {
    $pos_start = strpos($leadpagesform, 'href="') + 6;
    $pos_end = strpos($leadpagesform, '"', $pos_start+1);
    return substr($leadpagesform, $pos_start, $pos_end);
}

function leadpages_form_url( $post_id = '' ) {
	global $post;
	if (!$post_id) $post_id = $post->ID;
	$leadpagesform = get_field('leadpagesform', $post_id);
	preg_match('/(?<=href=[\'"])[^\'"]*(?=[\'"])/', $leadpagesform, $matches);
	return reset( $matches );
}

function leadpages_form_data_id( $post_id = '' ) {
	global $post;
	if (!$post_id) $post_id = $post->ID;
	$leadpagesform = get_field('leadpagesform', $post_id);
	preg_match('/(?<=data-leadbox=[\'"])[^\'"]*(?=[\'"])/', $leadpagesform, $matches);
	return reset( $matches );
}

add_action('ava_after_main_container', 'home_search');
function home_search() {
	if (is_front_page()) get_template_part('templates/home/search');
}

function region_heirarchy() {

	$file = get_stylesheet_directory() . '/gsheet_import/data/region_heirarchy.json';

	if (file_exists($file)) {
		$json = file_get_contents($file);
		return json_decode($json);
	}

	$q = new WP_Query([
		'post_type' => 'project',
		'posts_per_page' => -1
	] );

	$arr = [];

	while ($q->have_posts()) {

		$q->the_post();

		$district = get_the_terms( get_the_ID(), 'district' )[0]->name;
		$city = get_the_terms( get_the_ID(), 'city' )[0]->name;
		$hood = get_the_terms( get_the_ID(), 'neighbourhood' )[0]->name;

		$arr[$district][$city][] = $hood;
		$arr[$district][$city] = array_unique($arr[$district][$city]);
		sort($arr[$district][$city]);
	}

	wp_reset_postdata();
	file_put_contents($file, json_encode($arr, JSON_PRETTY_PRINT));

	return json_decode(json_encode($arr));
}


function region_data() {

	$file = get_stylesheet_directory() . '/gsheet_import/data/region_data.json';

	$q = new WP_Query([
		'post_type' => 'project',
		'posts_per_page' => -1
	] );

	$arr = [];

	while ($q->have_posts()) {

		$q->the_post();

		$district = get_the_terms( get_the_ID(), 'district' )[0]->name;
		$city = get_the_terms( get_the_ID(), 'city' )[0]->name;
		$hood = get_the_terms( get_the_ID(), 'neighbourhood' )[0]->name;

		if (!$hood) continue;

		if (!isset($arr[$hood])) {
			$arr[$hood]['city'] = [];
			$arr[$hood]['district'] = [];
		}

		$arr[$hood]['city'][] = $city;
		$arr[$hood]['district'][] = $district;
		$arr[$hood]['city'] = array_unique($arr[$hood]['city']);
		$arr[$hood]['district'] = array_unique($arr[$hood]['district']);
	}

	wp_reset_postdata();
	file_put_contents($file, json_encode($arr, JSON_PRETTY_PRINT));

	return json_decode(json_encode($arr));
}


function geocode_geojson() {

	$file = file_get_contents( get_template_directory() . '/library/storage/geojson.json' );

	$data = json_decode($file);

	foreach ($data->features as $feature) {

		if (!isset($feature->properties->neighbourhood)) {
			list( $store_id, $shape_id, $name ) = explode(',', $feature->properties->name);
			$name = substr($name, strpos($name, '=') + 1);
			$name = urldecode($name);
			$feature->properties->neighbourhood = $name;
		}


		if (!isset($feature->properties->city) && !isset($feature->properties->district)) {

			$geocoder = new CustomGoogleProvider();

			echo $feature->properties->name . '<br>';

			try {

				$locations = $geocoder->geocode($feature->properties->neighbourhood);

				$location = $locations->first();

				$feature->properties->city = $location->getLocality();
				$feature->properties->subLocality = $location->getSubLocality();

				foreach ($location->getAdminLevels() as $level) {
					if ($level->getLevel() == 2) {
						$district = $level->getName();
						if ($district === 'Toronto Division') $district = 'Greater Toronto Area';
						$district = str_replace('Regional Municipality', 'Region', $district);
						$feature->properties->district = $district;
					}
				}

			} catch (\Geocoder\Exception\QuotaExceeded $e) {

				echo $e->getMessage() . '<br>';
				break;

			} catch (Exception $e) {

				echo $e->getMessage() . '<br>';
				continue;

			}

		}

	}

	if (is_local()) {
		file_put_contents( get_template_directory() . '/library/storage/geojson.json', json_encode($data, JSON_PRETTY_PRINT) );
	} else {
		file_put_contents( get_template_directory() . '/library/storage/geojson.json', json_encode($data) );
	}

}


function set_post_taxonomy($post_id, $term, $tax) {

	if (!$t = term_exists($term, $tax)) {
		$t = wp_insert_term($term, $tax, ['parent' => 0]);
	}

	if (!is_wp_error($t)) {
		$term_ids = [(int)$t['term_id']];
		wp_set_object_terms($post_id, $term_ids, $tax, false);
	}

}


function html_data_attributes($data) {

	$data = array_map( function($key, $val) {
		return "data-$key='$val'";
	}, array_keys($data), $data );

	return implode(' ', $data);

}


function project_map_link( $post_id = '', $floorplan_link = false ) {

	global $post;

	$post_id = ($post_id) ?: $post->id;

	$data = [
		'lat' => get_field('lat', $post_id),
		'lng' => get_field('lng', $post_id),
		'projectid' => $post_id,
        'include_floorplans' => $floorplan_link
	];

	return home_url() . "/city/toronto/?" . http_build_query($data);
}

function project_map_jump_to_link( $post_id = '') {

    global $post;

    $post_id = ($post_id) ?: $post->id;

    $data = [
        'lat' => get_field('lat', $post_id),
        'lng' => get_field('lng', $post_id),
        'zoom' => 17,
    ];

    return home_url() . "/city/toronto/?" . http_build_query($data);
}



function dump_pdf_links() {
	$projects = new WP_Query([
		'post_type' => 'project',
		'posts_per_page' => '-1',
	]);

	$file = get_stylesheet_directory() . '/gsheet_import/data/pdf-ids.txt';

	$ids = [];
	while ($projects->have_posts()) {
		$projects->the_post();

		if ( ! get_field('floorplanspdfs') ) continue;

		$pdfs = explode("\n", get_field('floorplanspdfs') );

		foreach ($pdfs as $url) {
			$ids[] = extract_google_doc_id_from_url($url);
		}
	}

	// echo '<pre>'; print_r($ids); echo '</pre>';
	$string = implode("\n", $ids);
	echo nl2br($string);
	$file = get_stylesheet_directory() . '/gsheet_import/data/pdf-ids.txt';
	file_put_contents($file, $string);


}

function posts_for_geocode() {
	return new WP_Query([
		'post_type' => 'project',
		'posts_per_page' => -1,
		'meta_key' => 'geocode',
		'meta_value' => '1',
	]);
}


add_action('acf/input/admin_footer', 'custom_floorplans_acf_layout');
function custom_floorplans_acf_layout() {
	?>

    <style type="text/css">
        .floorplans-leftcol, .floorplans-rightcol {
            float: left;
            padding: 20px;
            box-sizing: border-box;
        }
        .floorplans-leftcol {
            width: 60%;
        }
        .floorplans-rightcol {
            width: 40%;
        }
    </style>

    <script type="text/javascript">
        (function($){
            $('.floorplanscontainer').find('td.acf-fields').each(function() {
                $this = $(this);
                var $left = $('<div class="floorplans-leftcol"></div>');
                var $right = $('<div class="floorplans-rightcol"></div>');
                $this.prepend($right).prepend($left);
                $this.find('.floorplans-left').appendTo($left);
                $this.find('.floorplans-right').appendTo($right);
            });
        })(jQuery);
    </script>

	<?php
}


## add duplicate functionality to acf repeater field: call with priority > 10 to run after acf fields are saved
add_action( 'acf/save_post', 'tc_acf_duplicate', 99 );
function tc_acf_duplicate( $post_id ) {

	if ( ! function_exists('media_handle_sideload') ) {
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
	}

	while ( have_rows( 'floorplans', $post_id ) ) { the_row();

		if (get_sub_field('duplicate')) {

			$copy = get_row(true);
			$copy['duplicate'] = '0';

			if ( $attachment_id = get_sub_field('image') ) {
				## duplicate the image
				$file = get_attached_file( $attachment_id );

				if ( file_exists($file) ) {

					$tmpfile = floorplans_temp_dir() . basename($file);

					copy( $file, $tmpfile );

					$file_array = [
						'name' => basename($file),
						'tmp_name' => $tmpfile,
						'type' => 'image/jpeg',
					];

					$new_attachment_id = media_handle_sideload( $file_array, $post_id, null );
					$copy['image'] = $new_attachment_id;

				}
			}

			add_row( 'floorplans', $copy, $post_id );

			update_sub_field( 'duplicate', '0', $post_id );
		}

	}

}


function floorplans_temp_dir() {
	return get_stylesheet_directory() . '/library/legacy/gsheet_import/floorplans/';
}


function extract_google_doc_id_from_url( $url ) {

	$url_array = parse_url( trim($url) );

	$googledoc_id = false;

	if ( $url_array['host'] == 'drive.google.com' && preg_match('~(?<=/file/d/)[^/]*(?=[/$])~', $url_array['path'], $matches) ) {
		$googledoc_id = $matches[0];
	} elseif ( $url_array['host'] == 'drive.google.com' && $url_array['query'] ) {
		parse_str($url_array['query'], $query_args);
		$googledoc_id = $query_args['id'];
	}

	return $googledoc_id;

}


function google_download_link ($googledoc_id) {
	return "https://drive.google.com/uc?export=download&id={$googledoc_id}";
}


function geocode_project_location_data( $post_id ) {

	if (!get_field('map', $post_id )) return;

	try {
		$address = address_from_map_url(get_field('map', $post_id));
		$geocoder = new CustomGoogleProvider();
		$result = $geocoder->geocode($address)->first();

		$lat = $result->getLatitude();
		$lng = $result->getLongitude();
		update_post_meta( $post_id, 'lat', $lat );
		update_post_meta( $post_id, 'lng', $lng );
		// update_post_meta( $post_id, 'zoom', $zoom );

		$city = $result->getLocality();
		update_post_meta( $post_id, 'city', $city );
		set_post_taxonomy( $post_id, $city, 'city' );

		$district = $result->getAdminLevels()->get(2)->getCode();
		update_post_meta( $post_id, 'district', $district );
		$districtShortNames = [
			'Toronto Division' => 'Greater Toronto Area',
			'Durham Regional Municipality' => 'Durham Region',
			'Halton Regional Municipality' => 'Halton Region',
			'Peel Regional Municipality' => 'Peel Region',
			'Waterloo Regional Municipality' => 'Waterloo Region',
			'York Regional Municipality' => 'York Region',
		];
		if ($districtShortNames[$district]) $district = $districtShortNames[$district];
		set_post_taxonomy( $post_id, $district, 'district' );

		$term_ids = array();

		$geojson = getPolygons();
		foreach ($geojson->features as $feature) {

			if ( $inside = pointInPolygon( array($lat, $lng), $feature->geometry->coordinates[0]) ) {

				if ($feature->properties->neighbourhood) {
					if ( ! $term = term_exists( $feature->properties->neighbourhood, 'neighbourhood' ) ) {
						$term = wp_insert_term( $feature->properties->neighbourhood, 'neighbourhood', array('parent' => 0) );
					}

					if (!is_object($term)) {
						wp_set_object_terms($post_id, [(int)$term['term_id']], 'neighbourhood', false);
						update_field('field_557b53bc0c72f', json_encode($feature->geometry->coordinates[0]), get_term($term['term_id'], 'neighbourhood'));
						update_field('field_563be14fec26a', json_encode(calculateCenter($feature->geometry->coordinates[0])), get_term($term['term_id'], 'neighbourhood'));
					}
				}

				break;

			}

		}

	} catch (Exception $e) {
		return $e->getMessage();
	}

}


function project_walk_score( $post_id = '' ) {

	$output = get_transient( 'project_walk_score_' . $post_id );

	if( $output === false ){

		global $post;

		$post_id = ($post_id) ?: $post->ID;

		if (!$json = get_field('walk_score')) {
			$address = trim(get_field('address') . ' ' . custom_cat_text('city'));
			$data = [
				'format' => 'json',
				'lat' => get_field('lat'),
				'lon' => get_field('lng'),
				'address' => $address,
				'wsapikey' => 'fb9d9adf17c363c4900c5fa783f37424',
				'transit' => '1',
				'bike' => '1',
			];

			if (!$data['lat'] || !$data['lon'] || !$data['address']) return;

			$url = "http://api.walkscore.com/score?" . http_build_query($data);

			$h = curl_init($url);
			curl_setopt($h, CURLOPT_RETURNTRANSFER, true);
			$json = curl_exec($h);

			update_post_meta( $post_id, 'walk_score', $json );
		}

		$score = json_decode($json);
		// $output = "<a href='$score->help_link'><img src='{$score->logo_url}'> {$score->walkscore}</a>";
		$output = $score->walkscore ." / 100";
		set_transient( 'project_walk_score_' . $post_id, $output, 12 * HOUR_IN_SECONDS );

	}

	return $output;
}


function project_transit_score( $post_id = '' ) {

	$output = get_transient( 'project_transit_score_' . $post_id );

	if( $output === false ){

		global $post;

		$post_id = ($post_id) ?: $post->ID;

		if (!$json = get_field('transit_score')) {
			$data = [
				'format' => 'json',
				'lat' => get_field('lat'),
				'lon' => get_field('lng'),
				'city' => custom_cat_text('city'),
				'country' => 'CA',
				'wsapikey' => 'fb9d9adf17c363c4900c5fa783f37424',
			];

			if (!$data['lat'] || !$data['lon'] || !$data['city']) return;

			$url = "http://transit.walkscore.com/transit/score/?" . http_build_query($data);

			$h = curl_init($url);
			curl_setopt($h, CURLOPT_RETURNTRANSFER, true);
			$json = curl_exec($h);

			update_post_meta( $post_id, 'transit_score', $json );
		}

		$score = json_decode($json);
		// $output = "<a href='$score->help_link'><img src='{$score->logo_url}'> {$score->transit_score}</a>";
		$output = $score->transit_score ." / 100";

		set_transient( 'project_transit_score_' . $post_id, $output, 12 * HOUR_IN_SECONDS );

	}

	return $output;
}


function trim_title( $text, $cutoff = '' )
{
	if ($cutoff && strlen($text) > $cutoff) {
		$text = substr($text, 0, $cutoff-3) . "...";
	}

	return $text;
}


function platinum_access( $post_id = '' ) {
	global $post;
	$post_id = ($post_id) ?: $post->ID;
	return get_field('platinum_access', $post_id);
}


foreach ( glob( __DIR__ . '/admin-ajax/*.php') as $file ) {
	require_once $file;
}


add_action('wp_head', 'tc_no_index');
function tc_no_index() {

	$pages = [
		'map',
	];

	$obj = get_queried_object();
	if ( get_class($obj) != 'WP_Post' ) return;

	if ($pages && in_array($obj->post_name, $pages)) {
		echo '<meta name="robots" content="noindex">';
	}

}

add_action('wp_head', 'tc_no_index_reservation_pages');
function tc_no_index_reservation_pages() {

	global $floorplan;

	if (get_queried_object()->post_type == 'project' && $floorplan && preg_match("~/reserve/$~", $_SERVER['REQUEST_URI'])) {
		echo '<meta name="robots" content="noindex">';
	}

}

/**
 * Rebuilds and returns project data
 *
 * @param int $post_id
 * @return array|null project data array or null on failure
 */
function rebuild_project_data( $post_id ) {
    $post = get_post( $post_id );

    if( ! $post || 'project' != $post->post_type ) return null;

    $post_id = $post->ID;
	$attachment = get_post_thumbnail_id( $post_id );

	$project = array(
		'post_id'              => $post_id,
		'title'                => get_the_title( $post ),
		'hide_pricing'         => get_field( 'hide_pricing', $post_id),
		'available_floorplans' => 0,
		'sort_priority'        => 0,
		'featured'             => false,
		'platinum_access'      => false,
		'permalink'            => get_post_permalink( $post_id ),
		'leadpageslink'        => leadpages_form_url( $post_id ),
		'updated'              => (int) get_post_meta( $post_id, 'last-updated', true ),
		'address'              => get_field( 'address', $post_id ),
		'deposit'              => project_deposit_percent( $post_id ),
		'image'                => wp_get_attachment_image_url( $attachment, 'flexslider' ),
		'thumbnail'            => wp_get_attachment_image_url( $attachment, 'portfolio_map' ),
		'price' => array(
            'min' => (float) get_post_meta( $post_id, '_min_price', true ),
            'max' => (float) get_post_meta( $post_id, '_max_price', true )
		),
		'size' => array(
			'min' => (int) get_field('sq.ftfrom'),
			'max' => (int) get_field('sq.ftto')
		),
		'location' => array(
			'type' => 'Point',
            'coordinates' => array( (float) get_field( 'lng', $post_id ), (float) get_field( 'lat', $post_id ) ),
        ),
	);

	$floorplans = get_field( 'floorplans', $post_id );

	if ( $floorplans && is_array( $floorplans ) ) {

		$count = 0;
		$avgs  = [];

		$min_price = $max_price = 0;
		$sqft_to = $sqft_from = 0;

		foreach ( $floorplans as $floorplan ) {
			$avg = null;
			$_price = $floorplan['price'];

			if ( ! $_price || 'Available' != $floorplan['availability'] ) {
				continue;
			}

			if ( $floorplan['size'] ) {
			    $_size = $floorplan['size'];

				$avgs[] = $avg = $_price / $_size;

				if( ! $sqft_from || $_size < $sqft_from ){
				    $sqft_from = $_size;
                }

                $sqft_to = max( $_size, $sqft_to );
			}

			if( ! $min_price || $_price < $min_price ) {
				$min_price = $_price;
			}

			$max_price = max( $_price, $max_price );

			$_floorplan = array(
                'post_id'      => $post_id,
				'suite_name'   => $floorplan['suite_name'],
				'price'        => (int) $_price,
				'size'         => (int) $floorplan['size'],
				'baths'        => (float) $floorplan['baths'],
				'beds'         => (float) $floorplan['beds'],
				'exposure'     => is_array( $floorplan['exposure'] ) ? reset( $floorplan['exposure'] ) : $floorplan['exposure'],
				'pricepersqft' => round( $avg ),
				'availability' => $floorplan['availability'],
				'url'          => get_floorplans_link( $floorplan['image'], $post ),
				'fullimage'    => wp_get_attachment_image_url( $floorplan['image'], 'fullimage' ),
				'thumbnail'    => wp_get_attachment_image_url( $floorplan['image'], 'thumbnail' ),
				'medium'       => wp_get_attachment_image_url( $floorplan['image'], 'medium' ),
				'alt'          => floorplan_alt_text( $floorplan, $post )
			);

			$project['floorplans'][] = $_floorplan;
			$count ++;
		}

		$_tmp = array(
			'available_floorplans' => $count,
			'pricepersqft'      => $avgs ? round( array_sum( $avgs ) / count( $avgs ) ) : null,
		);

		// Update Min/Max prices
		if( $min_price ) {
			$_tmp['price'] = [ 'min' => (float) $min_price, 'max' => (float) $max_price ];
		}

		// Update Sq. Ft. Range
        if( $sqft_from ) {
            $_tmp['size'] = [ 'min' => (int) $sqft_from, 'max' => (int) $sqft_to ];
        }

		$project = array_merge( $project, $_tmp );
	}

	$taxes = array( 'district', 'city', 'salesstatus', 'occupancy-date', 'developer', 'neighbourhood', 'status', 'type' );


    /** @var WP_Term $term */
	foreach ( wp_get_object_terms( $post_id, $taxes ) as $term ) {
	    $tax = $term->taxonomy;

	    if ($tax === 'salesstatus') {
	        $project['terms'][] = $term->slug;

	        if (in_array($term->slug, ['platinum-access', 'launching-soon'])) {
		        $project['featured'] = true;
	        }

	        if ($term->slug === 'platinum-access') $project['platinum_access'] = true;

	        if ($term->slug === 'platinum-access') $project['sort_priority'] += 8;
	        if ($term->slug === 'special-incentives') $project['sort_priority'] += 4;
	        if ($term->slug === 'launching-soon') $project['sort_priority'] += 2;
	        if ($term->slug === 'selling') $project['sort_priority'] += 1;
	    }

	    // Handle strings
	    if(!in_array($tax, ['status', 'occupancy-date', 'type'])) {
		    if ( 'developer' == $tax && $term->count ) {
			    $term_string = "$term->name ($term->count)";
		    } else {
			    $term_string = $term->name;
		    }

            if ( ! empty( $project[ $tax ] ) ) {
                $project['strings'][ $tax ] .= ' & ' . $term_string;
            } else {
                $project['strings'][ $tax ] = $term_string;
            }
	    }

	    // Handle Values
		if ( 'occupancy-date' == $tax ) {
			$project['occupancy_date'] = (int) $term->slug;

		} elseif( 'salesstatus' != $tax && 'developer' != $tax ) {
			$project[ $tax ] = $term->slug;

		} else {
			$project[ $tax ][] = $term->slug;
		}
	}

	return $project;
}

/**
 * Set a project's featured image if not set
 *
 * @param int $post_id The project ID
 * @return bool True if featured image exists or is set, false otherwise
 */
function maybe_set_project_image( $post_id ){
    $has_thumbnail = has_post_thumbnail( $post_id );

	if( ! $has_thumbnail ){
		// Check if gallery images exist
		$images = get_post_meta( $post_id, 'gallery', true );

		if( $images ) {
			$thumb_id = reset( $images );
			$has_thumbnail = set_post_thumbnail( $post_id, $thumb_id );
		}
	}

	return $has_thumbnail;
}

function talk_ajax_url(){
	return get_template_directory_uri() . '/talk-ajax.php';
}
add_filter('avia_ajax_url_filter', 'talk_ajax_url');


function project_deposit_percent( $post_id ) {
	$deposits = get_field( 'deposit_structure', $post_id );

	if( empty( $deposits  ) ) {
		return null;
	}

	$total = array_reduce( $deposits, function ( $total, $deposit ) {
		if ( 'amount' != $deposit['deposit_type'] && 'occupancy' != $deposit['deposit_due_on'] ) {
			$total += (float) $deposit['deposit_amount'];
		}

		return $total;
	}, 0 );

	return $total;
}

function get_project_data( $post_id ) {
    global $tcdb;

    $hdl = $tcdb->get_collection('projects');

    $project = $hdl->findOne( array( 'id' => (int) $post_id ) );

	if ( ! $project && ( ! defined( 'SHORTINIT' ) || ! SHORTINIT ) ) {
		$project = sync_project_data( $post_id );
	}

	return $project;
}

function get_project_field( $post_id, $field ){
    $project = get_project_data( $post_id );
    $value =  false;

    if( is_array( $field ) ){
        $value = wp_array_slice_assoc( $project, $field );
    } elseif ( isset( $project['field'] ) ){
        $value = $project['field'];
    }

    return $value;
}

add_filter( 'wpseo_title', 'tc_wpseo_title', 99, 1 );
add_filter( 'wpseo_twitter_title', 'tc_wpseo_title', 99, 1 );
add_filter( 'wpseo_opengraph_title', 'tc_wpseo_title', 99, 1 );
function tc_wpseo_title( $title ) {

	global $post, $project_floorplan;

	$obj = get_queried_object();

	if ($obj->post_type == 'project' && $project_floorplan) {

		$title = "{$post->post_title} | Floor Plans, Prices, Availability - TalkCondo";

		$floorplan = [];
		foreach (get_field('floorplans') as $f) {
			if ($f['image'] == $project_floorplan->ID) {
				$floorplan = $f;
				break;
			}
		}

		if (!$floorplan) return $title;

		$suite_name = ($floorplan['suite_name']) ?: '';
		$sqft = ($floorplan['size']) ? $floorplan['size'] . ' sq.ft.' : '';
		$beds = ($floorplan['beds'] > 1) ? $floorplan['beds'] . ' bedrooms' : $floorplan['beds'] . ' bedroom';

		$title = "{$post->post_title} | {$suite_name} | {$sqft} | {$beds}";

	} elseif ($obj->post_type == 'project') {

		$title = "{$post->post_title} | Floor Plans, Prices, Availability - TalkCondo";

	} elseif ( $obj->taxonomy == 'developer' ) {

		$title = "{$obj->name} | Condo Developer | TalkCondo";

	}

	return $title;
}

add_filter( 'wpseo_metadesc', 'tc_wpseo_metadesc', 99, 1 );
add_filter( 'wpseo_opengraph_desc', 'tc_wpseo_metadesc', 99, 1 );
add_filter( 'wpseo_twitter_description', 'tc_wpseo_metadesc', 99, 1 );
function tc_wpseo_metadesc( $desc ) {

	global $post, $project_floorplan, $wp_query;
	$obj = get_queried_object();
	if ($post->post_type == 'project' && $project_floorplan) {

		$floorplan = [];
		foreach (get_field('floorplans') as $fp) {
			if ($fp['image'] == $project_floorplan->ID) {
				$floorplan = $fp;
			}
		}

		if (!$floorplan) return $desc;

		$suite_name = ($floorplan['suite_name']) ?: null;
		$sqft = ($floorplan['size']) ? $floorplan['size'] . ' sq.ft.' : null;
		$beds = ($floorplan['beds'] > 1) ? $floorplan['beds'] . ' bedrooms' : $floorplan['beds'] . ' bedroom';
		$desc = "{$suite_name} at {$post->post_title}. {$sqft} {$beds} condo suite. View floor plan, availability and price for this and thousands of other new condos in Toronto and GTA.";

	} elseif ($post->post_type == 'project') {

		$title = $post->post_title;
		$address = get_post_meta( $post->ID, 'address', true );
		$developers = strip_tags( get_the_term_list( $post, 'developer' ) );

		$desc = "{$post->post_title} is a new Condo project located at {$address} by {$developers}. Get the latest floor plans & prices here!";

	}
	if($obj->taxonomy == 'developer'){
		// taxonomy "develop"
		$developername = $obj->name;
		$numberprojects = count($wp_query->posts);
		$desc = "$developername is a condo developer with $numberprojects condominiums across the Toronto Area.  Review all of their complete, active and pre-construction condo developments.";
		return $desc;
	}else if($obj->taxonomy == 'neighbourhood'){
		// taxonomy "develop"
		$neighbourhood = $obj->name;
		$numberprojects = count($wp_query->posts);
		$desc = "$neighbourhood is a neighbourhood in city with $numberprojects condominium buildings. See all complete, active and pre-construction condo developments and listings in this neighbourhood.";
		return $desc;
	}

	return $desc;
}


add_action( 'wp', 'tc_get_project_floorplan');
function tc_get_project_floorplan() {

	global $wpdb, $post, $project_floorplan;

	if ($post->post_type == 'project' && get_query_var('floorplan')) {
		$id = $wpdb->get_var( $wpdb->prepare( "select id from $wpdb->posts where post_name = %s limit 1", get_query_var("floorplan") ) );
		$project_floorplan = get_post($id);
	}

}

/**
 * Syncs project data with mongodb
 *
 * @param int $post_id
 * @return array|null project data or null on failure
 */
function sync_project_data( $post_id ) {
    $post_id = (int) $post_id;
	$post = get_post( $post_id );

	if (!$post || 'project' !== $post->post_type) {
		return null;
	}

	global $tcdb;

    $data = rebuild_project_data( $post_id );

    if ($data) {
	    $hdl = $tcdb->get_collection('projects');

	    if ( ! empty( $data['floorplans'] ) ) {
		    sync_project_floorplans( $post_id, $data['floorplans'] );
	    }

	    $_data = $data;
	    unset( $_data['floorplans'] );

	    $result = $hdl->replaceOne( [ 'post_id' => $post_id ], $_data, [ 'upsert' => true ] );

	    if ( (int) $result->getModifiedCount() > 0  ) {
		    return $data;
	    }
    }

    return null;
}

/**
 * Syncs floor plan data with mongodb
 *
 * @param int $id
 * @param array $floorplans
 */
function sync_project_floorplans( $post_id, $floorplans ) {
	if ( empty( $floorplans ) ) {
		return;
	}

	global $tcdb;

	$post_id  = absint( $post_id );
	$hdl = $tcdb->get_collection('floorplans');

	$hdl->deleteMany( [ 'post_id' => $post_id ] );
	$hdl->insertMany( $floorplans );
}

if( ! function_exists( 'sanitize_key' ) ) {
	function sanitize_key( $key ) {
		$key = strtolower( $key );
		return preg_replace( '/[^a-z0-9_\-]/', '', $key );
	}
}

if( ! function_exists( 'get_object_taxonomies' ) ){
    function get_object_taxonomies( $object ){
        $taxonomies = [];

        if( 'project' == $object ){
            $taxonomies = ['district', 'city', 'neighbourhood', 'developer', 'salesstatus', 'occupancy-date', 'status', 'type'];
        }

        return $taxonomies;
    }
}

function get_market_data( $city = 'toronto', $beds = - 1 ) {
	global $tcdb;

	$floorplan_match = [
		'$expr' => [
			'$eq' => [ '$post_id', '$$post_id' ]
		],
		'price' => [ '$gt' => 0 ],
	];

	if ( (float) $beds > 0 ) {
		$floorplan_match['beds'] = (float) $beds;
	}

	$pipeline = [
		[
			'$match' => [
				'available_floorplans' => [ '$gt' => 0 ],
				'city'                 => sanitize_key( $city )
			]
		],
		[
			'$lookup' => [
				'from'     => $tcdb->floorplans,
				'let'      => [ 'post_id' => '$post_id' ],
				'pipeline' => [
					[ '$match' => $floorplan_match ],
					[
						'$group' => [
							'_id'   => null,
							'price' => [ '$sum' => '$price' ],
							'size'  => [ '$sum' => '$size' ],
							'count' => [ '$sum' => 1 ]
						]
					]
				],
				'as'       => 'floorplans'
			]
		],
		[
			'$unwind' => [
				'path'                       => '$floorplans',
				'preserveNullAndEmptyArrays' => false
			]
		],
		[
			'$group' => [
				'_id'   => null,
				'price' => [ '$sum' => '$floorplans.price' ],
				'size'  => [ '$sum' => '$floorplans.size' ],
				'count' => [ '$sum' => '$floorplans.count' ]
			]
		]
	];

	$cursor = $tcdb->aggregate( 'projects', $pipeline );

	if( ! is_wp_error( $cursor ) ){
	    $data = reset( $cursor->toArray() );

	    if( $data->count && $data->price && $data->size ) {
		    return [
			    'avg_price' => round( $data->price / $data->count, 2 ),
                'avg_size' => round( $data->size / $data->count, 2 ),
                'avg_sqft_price' => round( $data->price / $data->size, 2 )
		    ];
	    }
    }

	return false;

}

function filter_slider( $slider, $args = '' ) {
	$defaults = array(
		'type'  => [ 'floorplan' ],
		'label' => '',
		'data'  => 'int',
		'min'   => null,
		'max'   => null,
	);

	$args = wp_parse_args( $args, $defaults );

	$fpln = in_array( 'floorplan', $args['type'] );
	$proj = in_array( 'project', $args['type'] );

	$hide = $fpln && ! $proj && empty( $_REQUEST['include_floorplans'] ) ? 'style="display: none;"' : '';

	$format = ' data-%s="%' . ($args['data'] == 'float' ? 'f' : 'd') . '"';

	$data = '';
	$el_classes = '';

	if( ! empty( $_REQUEST[ 'min_' . $slider ] ) ){
		$data .= sprintf( $format, 'init_min', $_REQUEST[ 'min_' . $slider ] );
		$el_classes = ' filtered ';
	}

	if( ! empty( $_REQUEST[ 'max_' . $slider ] ) ){
		$data .= sprintf( $format, 'init_max', $_REQUEST[ 'max_' . $slider ] );
		$el_classes = 'filtered ';
	}

	if( $args['min'] ){
		$data .= sprintf( $format, 'min', $args['min'] );
	}

	if( $args['max'] ){
		$data .= sprintf( $format, 'max', $args['max'] );
	}

	// Handle any extra classes for dropdown button
	$el_classes .= $fpln ? 'floorplan-filter ' : '';
	$el_classes .= $proj ? 'project-filter ' : '';

	?>
    <div class="floorplans__filter-sliders filter-button <?php echo $el_classes ?>" <?php echo $hide?>>
        <button class="toggle <?php echo $proj ? '' : 'btn btn-sm' ?>">
            <span class="title" data-placeholder="<?php echo $args['label'] ?>"><?php echo $args['label'] ?></span>
            <span class="count"></span>
            <i class="fa fa-fw fa-caret-down"></i>
        </button>
        <div class="submenu">
            <h4 class="popup-title"><?php echo $args['label'] ?></h4>
            <div class="filter-option" style="display: none;"></div>
            <div class="range-labels">
                <span class="lower-label"></span>
                <span class="divider">-</span>
                <span class="upper-label"></span>
            </div>
            <div id="<?php echo $slider ?>_filter-slider" class="<?php echo $proj ? 'filter-section' : '' ?> floorplans__filter-sliders">
                <div class="floorplans__filter-slider" <?php echo $proj ? 'style="display: block;"' : '' ?>>
                    <div id="map-<?php echo $slider ?>slider" <?php echo $proj ? 'style="margin: 0; width: 100%;"' : '' ?><?php echo $data ?>></div>
                </div>
            </div>
        </div>
    </div>
	<?php
}

/**
 * Helper function for determining if a term is active
 * @param object $term Term data
 * @return string
 */
function term_selected( $term ) {
	$selected = false;

	$queried = get_queried_object();
	if ( $queried->slug == $term->slug && $queried->taxonomy == $term->taxonomy ) {
		$selected = true;
	} elseif ( ! empty( $_REQUEST[ $term->taxonomy ] ) ) {
		$terms = wp_parse_slug_list( $_REQUEST[ $term->taxonomy ] );

		if ( in_array( $term->slug, $terms ) ) {
			$selected = true;
		}
	}

	return $selected ? 'active' : '';
}

function project_priceavg_tax( $taxonomy, $term){
	$priceavg = get_transient('priceavg_'. $taxonomy . '_' . $term);
	if($priceavg) return $priceavg;

	$projects = get_posts(array(
	  'post_type' => 'project',
	  'numberposts' => -1,
	  'post_status' => 'publish',
	  'tax_query' => array(
	    array(
	      'taxonomy' => $taxonomy,
	      'field' => 'slug',
	      'terms' => $term,
	    )
	  )
	));
	foreach($projects as $project){
		$info = get_project_data($project->ID);
		if(!isset($info['pricepersqft']) || !$info['pricepersqft']) continue;
		$pricepersqft = $pricepersqft + $info['pricepersqft'];
		$count ++;
	}
	if($count>0){
		set_transient( 'priceavg_' . $taxonomy . '_' . $term, $pricepersqft / $count, 12 * HOUR_IN_SECONDS );
		return $pricepersqft / $count;
	}
}

add_filter( 'gform_validation', 'tc_floorplan_registration_form_validation_hook' );
function tc_floorplan_registration_form_validation_hook( $result ) {
	$_POST['gform_is_valid'] = $result['is_valid'];
	return $result;
}


//add_action('acf/save_post', 'after_event_save', 20);
add_action('acf/save_post', 'after_event_save', 20);
function after_event_save( $post_id ) {

    if (get_post_type($post_id) != 'event') {
        return;
    }

    // Get newly saved values.
    $event_fields = get_fields( $post_id );

    $calendly_event_slug = get_field('calendly_event_slug', $post_id);


    if( $calendly_event_slug ) {
        try{
            $project_post = get_field('linked_project', $post_id)[0];
            if(empty($project_post) || gettype($project_post) != 'object') {
                throw new Exception('project_post should be an object, ' . gettype($project_post) . ' given');
            }
            $event = calendly_get_event($calendly_event_slug);
            update_field('calendly_event_id', $event['id'], $post_id);
            update_field('calendly_event_full_link', $event['attributes']['url'], $post_id);

            // refresh it
            $event_fields = get_fields( $post_id );

            // populate embed + full link, and do the project meta

            delete_post_meta($project_post->ID, 'calendly_upcoming_events');
            $project_events = get_post_meta($project_post->ID, 'calendly_upcoming_events', true);
            if(!is_array($project_events))
                $project_events = [];

            foreach ($project_events as $key => $project_event){
                if($project_event['calendly_event_slug'] == $event_fields['calendly_event_slug']){
                    unset($project_events[$key]);
                }
            }

            foreach ($event_fields['intervals'] as $i => $dates){
                if($event_fields['calendly_event_full_link'] == '')
                    continue;
                $new_event = $event_fields;
                unset($new_event['intervals']);
                $new_event['linked_project'] = $event_fields['linked_project'][0]->ID;
                $new_event['start_time'] = $event_fields['intervals'][$i]['date'].' '.$event_fields['intervals'][$i]['time_start'];
                $new_event['end_time'] = $event_fields['intervals'][$i]['date'].' '.$event_fields['intervals'][$i]['time_end'];

                $project_events[] = $new_event;
            }

            if( count($project_events) > 0) {
                $start_time_column = array_column($project_events, 'start_time');
                array_multisort($start_time_column, SORT_ASC, $project_events);

                update_post_meta($project_post->ID, 'calendly_upcoming_events', $project_events);
            }
            else
                delete_post_meta($project_post->ID, 'calendly_upcoming_events');
        }catch (Exception $e){
            echo "<pre>";
            var_dump($e->getMessage());
            var_dump($e->getTrace());
            echo "</pre>";
            die();
        }
    }
}


function calendly_get_event($slug){
    $raw = calendly_api_v1_get_events();
    $data = json_decode($raw, true)['data'];
    foreach ($data as $event){
        if($slug == $event['attributes']['slug'])
            return $event;
    }
}


// calendly api calls
function calendly_api_v1_get_events(){
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://calendly.com/api/v1/users/me/event_types",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
//            "X-TOKEN: GIIKGMDICTZG2O2B46OFN7WHHA3B62PS" // dev
            "X-TOKEN: HBKGAEKADCSXDVN4I2IWYI2NPYTFNODH" // live
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return $response;
}

if(!function_exists('formatTimeAmPm')) {
    function formatTimeAmPm($date_string)
    {
        if (date('i', strtotime($date_string)) != "00")
            return 'g:i a';
        else
            return 'ga';
    }
}

//handle links like: www.talkcondo.com/toronto/1-yorkville-condos/register (/register being the addition)
add_action( 'init', 'rewrite_rule_my' );
function rewrite_rule_my(){
    add_rewrite_rule( '^(register)/([^/]*)/?', 'index.php?pagename=$matches[1]&project_slug=$matches[2]', 'top' );
    add_rewrite_rule( '^([^/]*)/([^/]*)/(register)/?', 'index.php?pagename=$matches[3]&project_slug=$matches[2]&project_city=$matches[1]', 'top' );
    add_rewrite_rule( '^([^/]*)/(register)/?', 'index.php?pagename=$matches[2]&project_slug=$matches[1]', 'top' );
    add_rewrite_tag( '%project_slug%', '([^&]+)' );
    add_rewrite_tag( '%project_city%', '([^&]+)' );
}
