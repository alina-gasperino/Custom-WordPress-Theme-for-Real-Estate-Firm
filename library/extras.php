<?php

/**
 * Extras
 *
 * Additional extras used to create the theme functionality.
 */

/**
 * Filter the SQL queries to support various repeaters.
 */

function posts_where_dates( $where ) {

	$where = str_replace("meta_key = 'floorplans_$", "meta_key LIKE 'floorplans_%", $where);

	return $where;

}

add_filter( 'posts_where', 'posts_where_dates' );


/**
 * Ratings
 *
 * Include the ratings plugin and overwrite stuff.
 */

function add_custom_postratings_images_folder( $folders ) {
    $folders['stars_crystal']    = array( get_template_directory_uri() . '/assets/images/rating', '.gif' );
    return $folders;
}
add_filter( 'wp_postratings_images', 'add_custom_postratings_images_folder' );

/**
 * Noindex certain theme views.
 */

function talkcondo_head_no_follow() {
	if ( false !== strpos( $_SERVER['REQUEST_URI'], "/project_update/" ) || is_page_template( 'templates/template-map.php' ) || is_tax( 'city' ) || is_tax( 'neighbourhood' ) ) {
		noindex();
	}
}

add_action( 'wp_head', 'talkcondo_head_no_follow' );

/**
 * Handles rebuilding project transients on save
 *
 * @param int $post_id
 * @param WP_Post $post
 */
function talkcondo_save_project($post_id, $post){

	if ( 'project' === $post->post_type ){

		maybe_set_project_image( $post_id );
		sync_project_data( $post_id );
	}
}

add_action( 'save_post', 'talkcondo_save_project', 99, 2 );

function talkcondo_update_floorplan_price($value, $post_id, $field ){
	if( $value && $value !== get_post_meta($post_id, $field['name'], true )) {
		$_row = explode( '_', $field['name'] );

		if( isset( $_row[1] ) ) {
			$row = array('history_price' => $value, 'history_date' => current_time('Y-m-d'));
			$selector = array( 'floorplans', absint( $_row[1] ) + 1, 'price_history' );

			add_action('acf/save_post', function ($post_id) use($row, $selector){
				add_sub_row( $selector, $row, $post_id );
			}, 12);
		}
	}

	return $value;
}
add_filter('acf/update_value/key=field_585bef536caa9', 'talkcondo_update_floorplan_price', 10, 3);


function talkcondo_deposit_total( $field ){
	$total = project_deposit_percent( get_the_ID() );

	if( $total ){
		$field['label'] = $total . '%';

		return $field;
	}

	return false;
}
add_filter('acf/prepare_field/key=field_5b995b0a67f14', 'talkcondo_deposit_total');
