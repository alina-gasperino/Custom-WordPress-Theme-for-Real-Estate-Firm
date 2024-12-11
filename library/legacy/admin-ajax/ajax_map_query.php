<?php

defined( 'ABSPATH' ) || die();

class TalkCondo_Ajax_Map_Query
{
	private static $args = null;

	private static $include_floorplans;

	public static function get_projects($args = '') {

		global $tcdb;

		self::$args = $args ?: $_REQUEST;

		$pipeline = self::prepare_query();
		$options  = self::prepare_options();

		if (self::get_param( 's' ) ) {
			$cursor = $tcdb->find_many( 'projects', $pipeline, $options );
		} else {
			$cursor = $tcdb->aggregate( 'projects', $pipeline );
		}

		return array_column( $cursor->toArray(), null, 'id' );
	}

	public static function ajax_get_projects() {
		timer_start();
		$projects = self::get_projects();
		@header('X-Exec-Time: ' . timer_stop() . 'ms');
		@header('X-Count: ' . count( $projects ) );
		wp_send_json($projects);
	}

	private static function prepare_options() {
		$options = [];

		if( $s = self::get_param( 's' ) ){
			$options = array( 'limit' => 10 );

			// Sort by relevance if fulltext searching is enabled
			if( defined( 'TC_FULLTEXT_SEARCH' ) && TC_FULLTEXT_SEARCH ) {
				$options = array_merge( $options, array(
					'projection' => array( 'score' => array( '$meta' => 'textScore' ) ),
					'sort'       => array( 'score' => array( '$meta' => 'textScore' ) )
				) );
			}
		}

		return $options;
	}

	private static function prepare_query() {

		global $tcdb;

		self::$include_floorplans = wp_validate_boolean( self::get_param('include_floorplans') );

		// Handle the special case of searches
		if( $s = self::get_param( 's' ) ){

			$s = filter_var($s, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_AMP );
			$s = str_replace('&#38;', '&#038;', $s);
			$s = str_replace('&#39;', '&#8217;', $s);

			// Whether to use fulltext search or partial search
			if( defined( 'TC_FULLTEXT_SEARCH' ) && TC_FULLTEXT_SEARCH ) {
				$query = array( '$text' => array( '$search' => sprintf( '"%s"', $s ) ) );
			} else {
				$query = array(
					'$or' => array(
						array( 'title' => array( '$regex' => "{$s}", '$options' => 'i' ) ),
						array( 'address' =>  array( '$regex' => "{$s}", '$options' => 'i' ) )
					)
				);
			}

			return $query;

		}

		$project_match = array();

		if ( $id = self::get_param('id') ) {
			$project_match = [ 'post_id' => (int) $id ];
		}

		$taxonomies = array( 'district', 'status', 'developer', 'occupancy_date', 'salesstatus', 'city', 'neighbourhood', 'type' );

		// Handle taxonomy queries
		foreach ( $taxonomies as $taxonomy ) {
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

		$floorplan_match = [ '$expr' => [ '$eq' => [ '$post_id', '$$post_id' ] ] ];

		// Handle some floorplan filtering realness
		$ranges = array( 'price', 'size', 'baths', 'beds', 'deposit', 'pricepersqft' );

		foreach ($ranges as $range) {
			$_match = [];

			$min = self::get_param('min_' . $range);
			$max = self::get_param('max_' . $range);

			if (in_array($range, ['lat', 'lng', 'beds', 'baths'])) {
				$min = (float) $min;
				$max = (float) $max;
			} else {
				$min = (int) $min;
				$max = (int) $max;
			}

			if ($min) {
				$_match['$gte'] = $min;

				if (in_array($range, ['price', 'size'])) {
					$project_match[ $range . '.max' ]['$gte'] = $min;
				}
			}

			if ($max) {
				$_match[ '$lte' ] = $max;

				if (in_array($range, ['price', 'size'])) {
					$project_match[ $range . '.min' ]['$lte'] = $max;
				}
			}

			if ($_match) {
				if (in_array($range, ['deposit', 'pricepersqft'])) {
					$project_match[ $range ] = $_match;
				} else {
					$floorplan_match[ $range ] = $_match;
				}
			}
		}

		$exposure = self::get_param( 'exposure' );
		if ( $exposure ) {
			$floorplan_match[ 'exposure' ] = [ '$in' => $exposure ];
		}

		// Only search projects with floorplans to minimize overhead
		if ( $min_fp = (int) self::get_param('min_floorplans') ) {
			$project_match['available_floorplans']['$gte'] = $min_fp;
		} elseif ( self::$include_floorplans ) {
			$project_match['available_floorplans']['$gte'] = 0;
		}

		if (self::get_param('min_lat') && self::get_param('min_lng') && self::get_param('max_lat') && self::get_param('max_lng')) {
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

		// Build the Query
		$query = [ [ '$match' => $project_match ] ];

		$query[] = [
			'$sort' => [
				'sort_priority' => -1,
			]
		];

		if (self::$include_floorplans) {
			$query[] = [
				'$lookup' => [
					'from'     => $tcdb->floorplans,
					'let'      => [ 'post_id' => '$post_id' ],
					'pipeline' => [
						[ '$match' => $floorplan_match ],
						[ '$sort' => [ 'price' => 1 ] ],
					],
					'as'       => 'floorplans'
				]
			];
			$query[] = [ '$match' => [ 'floorplans' => [ '$ne' => [] ] ] ];

			$_project = [
				'floorplans' => 1,
				'size.min' => [ '$min' => '$floorplans.size' ],
				'size.max' => [ '$max' => '$floorplans.size' ],
				'price.min' => [ '$min' => '$floorplans.price' ],
				'price.max' => [ '$max' => '$floorplans.price' ],
				'pricepersqft' => [ '$avg' => '$floorplans.pricepersqft' ],

                'available_floorplans' => 1,
                'address'              => 1,
                'pricepersqft'         => 1,
                'occupancy_date'       => 1,
			];
		} else {
			$_project = [
				'image'                => 1,
				'available_floorplans' => 1,
				'address'              => 1,
				'leadpageslink'        => 1,
				'size'                 => 1,
				'price'                => 1,
				'pricepersqft'         => 1,
				'occupancy_date'       => 1,
				'updated'              => 1
			];
		}

		$query[] = [
			'$project' => array_merge( array(
				'_id'             => 0,
				// 'id'              => '$post_id',
				'post_id'         => 1,
				'index'           => '$id',
				'title'           => 1,
				'thumbnail'       => 1,
				'permalink'       => 1,
				'terms'           => 1,
				'strings'         => 1,
				'featured'        => 1,
				'sort_priority'   => 1,
				'platinum_access' => 1,
				'hide_pricing'    => 1,
				'coords'          => '$location.coordinates',
			), $_project )
		];

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

add_action( 'wp_ajax_talkcondo_map_query', array( 'TalkCondo_Ajax_Map_Query', 'ajax_get_projects' ) );
add_action( 'wp_ajax_nopriv_talkcondo_map_query', array( 'TalkCondo_Ajax_Map_Query', 'ajax_get_projects' ) );
