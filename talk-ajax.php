<?php

//mimic the actual admin-ajax
define( 'DOING_AJAX', true );

if ( ! isset( $_REQUEST['action'] ) ) {
	die();
}

ini_set( 'html_errors', 0 );

//make sure we skip most of the loading which we might not need
//http://core.trac.wordpress.org/browser/branches/3.4/wp-settings.php#L99
define( 'SHORTINIT', true );

require_once( '../../../wp-load.php' );
//require_once( '../../../wp-blog-header.php' );

//Typical headers
header( 'Content-Type: application/json' );
send_nosniff_header();

//Disable caching
header( 'Cache-Control: no-cache' );
header( 'Pragma: no-cache' );

// ini_set('display_errors', 1);
// error_reporting(E_ALL);

// Only include the files and functions we need.
// require( ABSPATH . WPINC . '/formatting.php' );

require_once( __DIR__ . '/library/legacy/tc-db.php' );
require_once( __DIR__ . '/library/legacy/admin-ajax/ajax_map_query.php' );
require_once( __DIR__ . '/library/legacy/admin-ajax/ajax_floorplan_query.php' );

$action = esc_attr( trim( $_REQUEST['action'] ) );

if ( 'avia_ajax_search' == $action) {

	// Load the L10n library.
	require_once( ABSPATH . WPINC . '/l10n.php' );

	// Load additional dependencies
	require( ABSPATH . WPINC . '/class-wp-rewrite.php' );
	require( ABSPATH . WPINC . '/link-template.php' );
	// require( ABSPATH . WPINC . '/meta.php' );
	require( ABSPATH . WPINC . '/post.php' );
	// require( ABSPATH . WPINC . '/class-wp-meta-query.php' );
	require( ABSPATH . WPINC . '/post-formats.php' );
	require( ABSPATH . WPINC . '/rewrite.php' );
	require( ABSPATH . WPINC . '/class-wp-taxonomy.php' );
	require( ABSPATH . WPINC . '/class-wp-term.php' );
	require( ABSPATH . WPINC . '/class-wp-term-query.php' );
	//require( ABSPATH . WPINC . '/class-wp-tax-query.php' );
	require( ABSPATH . WPINC . '/taxonomy.php' );

	/**
	 * Holds the WordPress Rewrite object for creating pretty URLs
	 * @global WP_Rewrite $wp_rewrite
	 * @since 1.5.0
	 */
	$GLOBALS['wp_rewrite'] = new WP_Rewrite();

	// Register Custom Taxonomies
	//register_taxonomy( 'developer', 'project' );
	//register_taxonomy( 'neighbourhood', 'project' );

	require_once( __DIR__ . '/library/legacy/admin-ajax/avia_ajax_search.php' );

	talk_ajax_search();

} elseif ( 'talkcondo_map_query' == $action ) {
	TalkCondo_Ajax_Map_Query::ajax_get_projects();

} elseif ( 'talkcondo_floorplan_query' == $action ) {
	TalkCondo_Ajax_Floorplan_Query::ajax_get_results();
}

wp_die();
