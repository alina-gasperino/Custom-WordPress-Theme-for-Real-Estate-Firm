<?php
/**
 * Plugin Name: GSheet Import
 * Plugin URI: http://beardo.co
 * Description: Import data from google gsheets
 * Version: 1.0
 * Author: Erin Heimer
 * Author URI: http://beardo.co
 * License: GPL2
 */

add_action('admin_init', 'register_gsi_settings' );
function register_gsi_settings() {
 	register_setting( 'gsi_settings', 'gsi_username' );
 	register_setting( 'gsi_settings', 'gsi_password' );
}

add_action('admin_menu', 'gsi_menu');
function gsi_menu() {
	add_menu_page( 'GSheet Import', 'GSheet Import', 'manage_options', 'gsheet-import', 'gsi_settings_page', '', null);
}

add_action('init', 'process_google_oath_code');
function process_google_oath_code() {

	if ( !is_admin() || !isset($_GET['page']) || $_GET['page'] != 'gsheet-import') return;

	if (isset($_GET['code'])) {
		$client = gsi_get_google_client();
		$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
		gsi_save_token(json_encode($token));
		wp_redirect(admin_url('admin.php?page=gsheet-import'));
		exit;
	}

	if (isset($_GET['revoke_token'])) {
		$client = gsi_get_google_client();
		$client->revokeToken();
		gsi_save_token(null);
		wp_redirect(admin_url('admin.php?page=gsheet-import'));
		exit;
	}

	try {
		$client = gsi_get_google_client();
		$client->setAccessToken(gsi_get_token());
		if ($client->isAccessTokenExpired()) {
			$client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
			gsi_save_token(json_encode($client->getAccessToken()));
		}
	} catch (Exception $e) {
		gsi_save_token(null);

		if( is_live() ) {
			wp_redirect( $client->createAuthUrl() );
			exit;
		}
	}

}

function gsi_settings_page() {

	gsi_includes();

	include('templates/settings-page.php');

}


function gsi_save_token($val) {
	if ($val) $val = json_encode($val);
	$file = get_stylesheet_directory() . '/library/legacy/gsheet_import/google-client-credentials.json';
	file_put_contents($file, $val);
}


function gsi_get_token() {
	$file = get_stylesheet_directory() . '/library/legacy/gsheet_import/google-client-credentials.json';
	return json_decode( file_get_contents($file), true );
}


function gsi_get_spreadsheet_service() {

	try {
		$client = gsi_get_google_client();
		$client->setAccessToken(gsi_get_token());
		$service = new Google_Service_Sheets($client);
	} catch ( Exception $e ) {
		echo $e->getMessage();
		gsi_save_token(null);
		refresh_page();
		exit;
	}

	return $service;
}

add_action( 'wp_ajax_gsi_get_columns', 'gsi_get_columns' );
function gsi_get_columns() {

	gsi_includes();

	$sheet = $_GET['sheet'];
	$class = "${sheet}_import";

	if (!class_exists($class)) {
		wp_send_json( ['error' => "class does not exist"] );
	}

	$token = gsi_get_token();

	if (!$token) {
		$auth_url = gsi_generate_auth_url();
		wp_send_json( ['error' => 'invalid token', 'auth_url' => $auth_url] );
	}

	try {

		$import = new $class($token);
		$import->fetchData();
		$import->getColumns();
		$response = $import->getColumns();
		wp_send_json( $response );

	} catch (Exception $e) {

		$response = [
			'error' => $e->getMessage()
			];

		wp_send_json( $response );

	}

	wp_die();
}

add_action( 'wp_ajax_gsi_check', 'gsi_ajax_check' );
function gsi_ajax_check() {

	gsi_includes();

	$sheet = $_GET['sheet'];
	$class = "${sheet}_import";

	if (!class_exists($class)) {
		wp_send_json( ['error' => "class does not exist"] );
	}

	try {
		$service = gsi_get_spreadsheet_service();
	} catch (Exception $e) {
		$auth_url = gsi_generate_auth_url();
		wp_send_json( ['error' => 'invalid token', 'auth_url' => $auth_url] );
	}

	try {

		$import = new $class($service);
		$import->fetchData();

		$import->getStatus();

		$response = [
			'action' => 'check',
			'success' => 'finished checking updates',
			'sheet' => $import->sheet,
			'new' => $import->new,
			'updates' => $import->updates,
			'total' => count($import->data),
		];

	} catch (Exception $e) {
		$response = [
			'action' => 'check',
			'error' => $e->getMessage(),
		];
	}

	wp_send_json( $response );

}


add_action( 'wp_ajax_nopriv_gsi_import', 'gsi_ajax_import' );
add_action( 'wp_ajax_gsi_import', 'gsi_ajax_import' );
function gsi_ajax_import() {

	gsi_includes();

	## clear out the cache files
	clear_map_data();

	$sheet = filter_var($_GET['sheet'], FILTER_SANITIZE_STRING);
	$class = "${sheet}_import";

	if (!class_exists($class)) {
		wp_send_json( ['error' => "class does not exist"] );
	}

	try {
		$service = gsi_get_spreadsheet_service();
	} catch (Exception $e) {
		$auth_url = gsi_generate_auth_url();
		wp_send_json( ['error' => 'invalid token', 'auth_url' => $auth_url] );
	}

	try {

		$import = new $class($service);

		$data = $import->fetchData();

		if ( isset($_GET['post_id']) ) {
			$post_ids = wp_parse_id_list($_GET['post_id']);
			$import->post_ids = $post_ids;
			$import->forceUpdate = true;
		}
		$import->import();

		flush_rewrite_rules();

		$response = [
			'action' => 'import',
			'success' => 'finished import',
			'sheet' => $import->sheet,
			'new' => $import->new,
			'updates' => $import->updates,
			'total' => count($import->data),
            'data' => $data,
		];

		wp_send_json( $response );

	} catch (Exception $e) {

		$response = [
			'error' => $e->getMessage()
		];

		wp_send_json( $response );

	}

	wp_die();

}


add_action( 'wp_ajax_gsi_regenerate_map_data', 'gsi_ajax_regenerate_map_data' );
function gsi_ajax_regenerate_map_data() {

	ini_set('display_errors', '1');
	ini_set('max_execution_time', '300');
	error_reporting(E_ALL & ~E_NOTICE);

	clear_map_data();
	get_map_data();

	wp_send_json( ['success' => 'Done!'] );

}

add_action( 'wp_ajax_gsi_regenerate_project_data', 'gsi_regenerate_project_data' );
function gsi_regenerate_project_data() {

	ini_set('display_errors', '1');
	error_reporting(E_ALL & ~E_NOTICE);

	$args = array(
		'post_type'              => 'project',
		'posts_per_page'         => - 1,
		'no_rows_found'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
		'fields'                 => 'ids',
	);

	if ( ! empty( $_REQUEST['post_ids'] ) ) {
		$args['post__in'] = wp_parse_id_list( $_GET['post_ids'] );
	}

	$posts = get_posts( $args );

	global $tcdb;

	$projects = [];
	$floorplans = [];

	foreach ( $posts as $post_id ){
		maybe_set_project_image( $post_id );
		$project = rebuild_project_data( $post_id );

		if( ! empty( $project['floorplans'] ) ) {
			$floorplans = array_merge( $floorplans, $project['floorplans'] );
		}

		unset($project['floorplans']);

		$projects[] = $project;
	}

	$tcdb->empty( 'floorplans' );
	$tcdb->insert( 'floorplans', $floorplans );
	$tcdb->get_collection('floorplans')->dropIndexes();
	$tcdb->get_collection('floorplans')->createIndex(['post_id' => 1], ['name' => 'floorplans_post_id']);
	$tcdb->get_collection('floorplans')->createIndex(['availability' => 1], ['name' => 'floorplans_availability']);
	$tcdb->get_collection('floorplans')->createIndex(['price' => 1], ['name' => 'floorplans_price']);
	$tcdb->get_collection('floorplans')->createIndex(['size' => 1], ['name' => 'floorplans_size']);
	$tcdb->get_collection('floorplans')->createIndex(['baths' => 1], ['name' => 'floorplans_baths']);
	$tcdb->get_collection('floorplans')->createIndex(['beds' => 1], ['name' => 'floorplans_beds']);
	$tcdb->get_collection('floorplans')->createIndex(['exposure' => 1], ['name' => 'floorplans_exposure']);

	$tcdb->empty( 'projects' );
	$tcdb->insert( 'projects', $projects );
	$tcdb->get_collection('projects')->dropIndexes();
	$tcdb->get_collection('projects')->createIndex(['post_id' => 1], ['name' => 'projects_post_id']);
	$tcdb->get_collection('projects')->createIndex(['sort_priority' => 1], ['name' => 'projects_sort_priority']);
	$tcdb->get_collection('projects')->createIndex(['location.coordinates' => '2d'], ['name' => 'projects_location']);
	$tcdb->get_collection('projects')->createIndex(['deposit' => 1], ['name' => 'projects_deposit']);
	$tcdb->get_collection('projects')->createIndex(['pricepersqft' => 1], ['name' => 'projects_pricepersqft']);

	$taxonomies = array( 'district', 'status', 'developer', 'occupancy_date', 'salesstatus', 'city', 'neighbourhood', 'type' );
	foreach ($taxonomies as $tax) {
		$tcdb->get_collection('projects')->createIndex([$tax => 1], ['name' => 'projects_' . $tax]);
	}

	wp_send_json( ['success' => 'Done!'] );
}

function project_googledrive_files( $post_id = '' )
{
	global $post;

	if (!$post_id) $post_id = $post->ID;

	$files_json = get_field('googledrive_files', $post_id);
	$floorplansfolder = get_field('floorplansfolder', $post_id);

	if ( !$files_json && $floorplansfolder) {
		$files_json = json_encode(fetch_project_googledrive_files($post_id));
		update_post_meta( $post_id, 'googledrive_files', $files_json );
	}

	return json_decode($files_json);
}


function fetch_project_googledrive_files( $post_id = '' )
{
	global $post;

	if (!$post_id) $post_id = $post->ID;

	$floorplansfolder = get_field('floorplansfolder', $post_id);
	$googledrive = (strpos($floorplansfolder, 'drive.google.com') !== false);

	if ($googledrive) {
		$a = parse_url($floorplansfolder, PHP_URL_QUERY);
		parse_str($a, $b);
		$id = $b['id'];
	}

	try {
		$client = gsi_get_google_client();
		$client->setAccessToken(gsi_get_token());
		$service = new Google_Service_Drive($client);
		$optParams = array(
			'pageSize' => 10,
			'fields' => 'nextPageToken, files(id, name, parents, mimeType, thumbnailLink)',
			'q' => "'{$id}' in parents",
		);
		$results = $service->files->listFiles($optParams);
		return $results->getFiles();
	} catch (Exception $e) {
		return $results = [];
	}

}


function gsi_get_google_client() {

	define( 'GOOGLE_CLIENT_SECRET', get_stylesheet_directory() . '/library/legacy/gsheet_import/google-client-secret.json' );

	$client = new Google_Client();
	$client->setAuthConfig(GOOGLE_CLIENT_SECRET);
	$client->setScopes(implode(' ', [
		Google_Service_Sheets::SPREADSHEETS,
		Google_Service_Drive::DRIVE_READONLY,
		Google_Service_Drive::DRIVE_METADATA_READONLY,
	]));
	$client->setRedirectUri(admin_url('admin.php?page=gsheet-import'));
	$client->setApprovalPrompt("force");
	$client->setAccessType('offline');

	return $client;

}


function gsi_includes() {

	require_once 'lib/class_gsi_import.php';
	require_once 'lib/class_developer_import.php';
	require_once 'lib/class_project_import.php';
	require_once 'lib/class_assignment_import.php';

}
