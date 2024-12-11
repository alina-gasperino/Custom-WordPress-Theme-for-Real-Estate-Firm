<?php

defined( 'ABSPATH' ) || die();

class TalkCondo_Ajax_Floorplan_Query
{
	private static $args = null;

	private static $per_page = 0;
	private static $sort = 'price';
	private static $sort_dir = 1;
	private static $limit = 0;
	private static $skip = 0;
	private static $page = 1;

	public static function get_results($args = '') {

		global $tcdb;

		self::$args = $args ?: $_REQUEST;

		self::$sort = self::get_param('sort', self::$sort);
		self::$sort_dir = self::get_param('sort_dir', 'asc') == 'desc' ? -1 : 1;

		self::$limit = (int) self::get_param('limit', self::$limit);

		self::$per_page = (int) self::get_param('per_page', self::$per_page);
		self::$page = (int) self::get_param('page', 1);
		self::$skip = (self::$page - 1) * self::$per_page;

		$cursor = $tcdb->aggregate( 'floorplans', self::prepare_query() );

		return array_column( $cursor->toArray(), null, 'id' );
	}

	public static function ajax_get_results() {
		timer_start();
		$results = self::get_results();
		@header('X-Exec-Time: ' . timer_stop() . 'ms');
		@header('X-Count: ' . count( $results ) );
		wp_send_json($results);
	}

	private static function prepare_query() {

		global $tcdb;

		$query = array();
		$project_match = array();
		$floorplan_match = array();

		if ($project_id = self::get_param('project_id')) {
			if (is_array($project_id)) {
				foreach ($project_id as $id) {
					$_in[] = (int) $id;
				}
			} else {
				$_in = [ (int) $project_id ];
			}
			$floorplan_match['post_id'] = [ '$in' => $_in ];
		}

		$floorplan_match['availability'] = 'Available';

		$project_match['$expr'] = [ '$eq' => [ '$post_id', '$$post_id' ] ];

		// $project_match[] = [ 'title' => [ '$ne' => 'Central Condos' ] ];

		$taxonomies = array( 'district', 'status', 'developer', 'occupancy_date', 'salesstatus', 'city', 'neighbourhood', 'type' );

		// Handle taxonomy queries
		foreach ( $taxonomies as $taxonomy ) {

			// skip certain ones when provided project_id
			$skip = ['city', 'neighbourhood', 'district'];
			if (self::get_param('project_id') && in_array($taxonomy, $skip)) continue;

			$_match = [];

			// Get the filter terms
			$terms = self::get_param( $taxonomy, [] );
			$terms = 'occupancy_date' == $taxonomy ? wp_parse_id_list( $terms ) : wp_parse_slug_list( $terms );

			// Handle Developer Sold Out & Resale Sales Statuses
			if ( 'salesstatus' === $taxonomy && ! in_array( 'resale', $terms ) ) {
				$elements = array_diff( array( 'resale', 'developer-sold-out' ), $terms );
				$_match['$nin'] = $elements;
			}

			if ( $terms ) {
				$_match['$in'] = $terms;
			}

			if( ! empty( $_match ) ){
				if( 'salesstatus' == $taxonomy || 'developer' == $taxonomy ){
					$_match = array( '$elemMatch' => $_match );
				}

				$project_match[$taxonomy] = $_match;
			}
		}

		// Handle some floorplan filtering realness
		$ranges = array( 'price', 'size', 'baths', 'beds', 'deposit', 'pricepersqft' );

		foreach ($ranges as $range) {
			$_match = [];

			$min = self::get_param('min_' . $range);
			$max = self::get_param('max_' . $range);

			if (in_array($range, ['beds', 'baths'])) {
				$min = (float) $min;
				$max = (float) $max;
			} else {
				$min = (int) $min;
				$max = (int) $max;
			}

			if ($min) $_match['$gte'] = $min;
			if ($max) $_match['$lte'] = $max;

			if ($_match) {
				if (in_array($range, ['deposit', 'pricepersqft'])) {
					$project_match[ $range ] = $_match;
				} else {
					$floorplan_match[ $range ] = $_match;
				}
			}
		}

		if ( $exposure = self::get_param( 'exposure' ) ) {
			$floorplan_match[ 'exposure' ] = [ '$in' => $exposure ];
		}

		if ( self::get_param('min_lat') && self::get_param('min_lng') && self::get_param('max_lat') && self::get_param('max_lng') ) {
			$project_match['location.coordinates.0'] = [
				'$lte' => (float) self::get_param('max_lng'),
				'$gte' => (float) self::get_param('min_lng'),
			];
			$project_match['location.coordinates.1'] = [
				'$lte' => (float) self::get_param('max_lat'),
				'$gte' => (float) self::get_param('min_lat'),
			];
			unset($project_match['city']);
			unset($project_match['district']);
			unset($project_match['neighbourhood']);
		}

		$query = [];
		$query[] = [ '$match' => $floorplan_match ];
		if (in_array(self::$sort, ['price', 'size', 'baths', 'beds', 'pricepersqft'])) {
			$query[] = [ '$sort' => [ self::$sort => self::$sort_dir ] ];
		}
		$query[] = [ '$lookup' => [
			'from' => $tcdb->projects,
			'let' => [ 'post_id' => '$post_id' ],
			'pipeline' => [
				[ '$match' => $project_match ],
				[ '$addFields' => [ 'id' => '$post_id'] ],
				[ '$project' => [
					'_id' => 0,
				] ],
			],
			'as' => 'projects',
		] ];
		$query[] = [ '$match' => [ 'projects' => [ '$ne' => [] ] ] ];
		$query[] = [ '$addFields' => [ 'project' => [ '$arrayElemAt' => [ '$projects', 0 ] ], ] ];
		$query[] = [ '$project' => [ 'projects' => 0 ] ];
		if (in_array(self::$sort, ['deposit'])) {
			$query[] = [ '$sort' => [ "project." . self::$sort => self::$sort_dir ] ];
		}

		if (self::$per_page) {

			$query[] = [ '$group' => [
				'_id' => null,
				'total' => [ '$sum' => 1 ],
				'results' => [ '$push' => '$$ROOT' ],
			] ];
			$query[] = [ '$addFields' => [
				'per_page' => self::$per_page,
				'page' => self::$page,
			] ];
			$query[] = [ '$project' => [
				'_id' => 0,
				'total' => 1,
				'per_page' => 1,
				'page' => 1,
				'results' => [ '$slice' => [ '$results', self::$skip, self::$per_page ] ],
			] ];

		} elseif (self::$limit) {
			$query[] = [ '$limit' => self::$limit ];
		}

		// echo '<pre>'; print_r($query); echo '</pre>'; die();

		return $query;

	}


	/**
	 * Gets the request parameter.
	 *
	 * @param string $key The query parameter
	 * @param mixed $default The default value to return if not found
	 *
	 * @return mixed  The request parameter.
	 */
	private static function get_param( $key, $default = false ) {
		if ( ! isset( self::$args[ $key ] ) || empty( self::$args[ $key ] ) ) {
			return $default;
		}

		// Apply some basic sanitation
		$value = wp_unslash( self::$args[ $key ] );

		return is_array( $value ) ? array_map( 'strip_tags', $value ) : strip_tags( $value );
	}
}

add_action( 'wp_ajax_talkcondo_floorplan_query', array( 'TalkCondo_Ajax_Floorplan_Query', 'ajax_get_results' ) );
add_action( 'wp_ajax_nopriv_talkcondo_floorplan_query', array( 'TalkCondo_Ajax_Floorplan_Query', 'ajax_get_results' ) );
