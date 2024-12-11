<?php

/**
 * Functions
 *
 * The following file manages core theme functionality.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 */

/* For ElasticSearch indexing and setup */
if( defined( 'EP_SETUP' ) && EP_SETUP ) {
	function talkcondo_extend_http_reqest_timeout( $timeout ) {
		set_time_limit( 0 );

		return 300;
	}

	add_filter( 'http_request_timeout', 'talkcondo_extend_http_reqest_timeout' );

	function talkcondo_extend_http_api_curl( $handle ) {
		curl_setopt( $handle, CURLOPT_CONNECTTIMEOUT, 305 ); # new timeout
		curl_setopt( $handle, CURLOPT_TIMEOUT, 305 ); # new timeout
	}

	add_action( 'http_api_curl', 'talkcondo_extend_http_api_curl', 100, 1 );
}

include_once( dirname( __FILE__ ) . '/library/legacy.php' );      // Legacy
include_once( dirname( __FILE__ ) . '/library/engine.php' );      // Engine - DO NOT REMOVE
include_once( dirname( __FILE__ ) . '/library/support.php' );     // Theme Support
include_once( dirname( __FILE__ ) . '/library/sizes.php' );       // Image Sizes
include_once( dirname( __FILE__ ) . '/library/translation.php' ); // Translation
include_once( dirname( __FILE__ ) . '/library/navigation.php' );  // Navigation
include_once( dirname( __FILE__ ) . '/library/sidebars.php' );    // Sidebars
include_once( dirname( __FILE__ ) . '/library/scripts.php' );     // Scripts
include_once( dirname( __FILE__ ) . '/library/stylesheets.php' ); // Stylesheets
include_once( dirname( __FILE__ ) . '/library/types.php' );       // Custom Post Types
include_once( dirname( __FILE__ ) . '/library/walker.php' );      // Walker
include_once( dirname( __FILE__ ) . '/library/customizer.php' );  // Customizer
include_once( dirname( __FILE__ ) . '/library/woocommerce.php' ); // WooCommerce
include_once( dirname( __FILE__ ) . '/library/extras.php' );      // Custom PHP Functions


function tenpixelsleft_custom_posts_per_page($query) {
	if (!$query->is_main_query())
		return $query;
	elseif ($query->is_tax('developer'))
		$query->set('posts_per_page', '-1');
	return $query;
}

function update_like(){
	$args = array(
		'post_type' => 'project',
		'post_status' => 'publish',
		'posts_per_page' => -1
	);
		$query = new WP_Query($args);
	
	$plan = ($_REQUEST['which_plan']);
	$amount = ($_REQUEST['number']);
	
	if ($query->have_posts() ) : 
		while ( $query->have_posts() ) : $query->the_post();
			$rows = get_field('floorplans');
			$uniqueids = array_column($rows, 'suite_name');
			$found_row_id = array_search($plan, $uniqueids);
			if($found_row_id !== FALSE){
				$the_row = $found_row_id + 1;
				$newvalue = intval($amount) + 1;
				update_sub_field( array('floorplans', $the_row, 'likes'), $newvalue);
			}
		endwhile;
		wp_reset_postdata();
	endif;
}

add_action('wp_ajax_update_like', 'update_like'); 
add_action('wp_ajax_nopriv_update_like', 'update_like');

// Apply pre_get_posts filter - ensure this is not called when in admin
if (!is_admin()) {
	add_filter('pre_get_posts', 'tenpixelsleft_custom_posts_per_page');
}
