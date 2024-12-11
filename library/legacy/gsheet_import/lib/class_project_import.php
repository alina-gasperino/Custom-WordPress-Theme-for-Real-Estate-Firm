<?php

class project_import extends gsi_import
{

	private $service;
	public $ssid = '1ATDxsfSq2LA0Ppy8DYr1C6UlWwCzb4Tu_pv17Xn-BS0';
	public $sheet = 'Projects';

	public function __construct($service) {

		if ( is_local() ) $this->ssid = '1lSHVL4QG29QgaO-7_ZnmyNMk-WB9JJhFckL--phUOiE';

		## column_in_sheets => taxonomy
		$this->taxonomy_fields = array(
			'developer' => 'developer',
			'type' => 'type',
			'currentstatus' => 'status',
			'completiondate' => 'occupancy-date',
			'salesstatus' => 'salesstatus'
			);

		$this->metaFields = array(
			'address',
			'amenities',
			'architect',
			'attentionbanner',
			'carouseltextoverride',
			'customtags',
			'featuresfinishes',
			'floorplansfolder',
			'floorplanslink',
			'floorplanspdfs',
			'h1',
			'h2',
			'heightft',
			'heightm',
			'infusionsoftform',
			'interiordesigner',
			'leadpagesform',
			'locker',
			'maintenancefeessq.ft',
			'majorintersection',
			'parking',
			'pricedfrom',
			'pricedto',
			'projecthighlights',
			'projectname',
			'projecttitleheadertext',
			'sq.ftfrom',
			'sq.ftto',
			'storeys',
			'suites',
			);

		parent::__construct($service);

	}


	public function getStatus() {

		foreach ($this->data as $row) {

			// $row = $entry->getValues();
			$post_title = $row['projectname'];

			$post_data = [
				'post_id' => $row['postid'],
				'post_title' => $post_title,
			];

			if (!$row['postid'] > 0) {
				$this->new[] = $post_data;
				continue;
			}

			$newhash = md5(serialize($row));
			$oldhash = ($row['postid'] > 0) ? get_post_meta($row['postid'], 'hash', true) : false;

			if ($oldhash != $newhash) $this->updates[] = $post_data;

		}

	}


	public function import() {

		global $wpdb;

		foreach ($this->data as $n => $row) {

			// if ($this->limit > 0 && $i >= $this->limit) break;
			if ($row['postid'] && !get_post($row['postid'])) continue;

			// if ( $this->post_ids && !$row['postid'] > 0 ) continue;
			if ( $this->post_ids && !in_array($row['postid'], $this->post_ids) ) continue;

			$newhash = md5(serialize($row));
			$oldhash = ($row['postid']) ? get_post_meta($row['postid'], 'hash', true) : false;
			// if ($oldhash == $newhash) continue;
			if ($oldhash == $newhash && ! $this->post_ids) continue;

			## create the post
			$post_title = $row['projecttitleheadertext'];
			$post_slug = trim($row['pageslugtalkcondo.com'], '/');

			$args = array(
				'ID' => $row['postid'],
				'post_content' => '',
				'post_title' => $post_title,
				'post_name' => $post_slug,
				'post_type' => 'project',
				'post_status' => ($row['trash'] == 1 || $row['trash'] == 'trash') ? 'trash' : 'publish',
				'post_category' => '',
			);

			$post_id = wp_insert_post($args, false);

			if ($row['postid']) {
				$this->updates[] = $post_title;
			} else {
				$this->new[] = $post_title;
				$row['postid'] = $post_id;
			}


			## Loop through taxonomy fields
			foreach ( $this->taxonomy_fields as $column => $taxonomy) {
				$term_ids = array();
				foreach (explode( '&', $row[$column] ) as $term) {
					$term = trim($term);
					if ($term != '') {
						if ($column == 'completiondate') {
							if (preg_match('/\d{4}/', $term, $matches)) {
								$term = $matches[0];
							}
						}
						if (!$term_object = term_exists($term, $taxonomy)) {
							$term_object = wp_insert_term( $term, $taxonomy, array( 'parent' => 0 ) );
						}
						$term_ids[] = (int)$term_object['term_id'];
					}
				}
				if (!$term_ids) $term_ids = null;
				wp_set_object_terms($post_id, $term_ids, $taxonomy, false);
			}


			## update separate soldout status
			update_post_meta( $post_id, 'soldout', (stripos($row['salesstatus'], 'sold out') !== false) ? 'true' : 'false' );


			## generate floorplans link
			if ($row['floorplansfolder'] && !$row['floorplanslink']) {
				$floorplansid = md5( $post_id . $post_slug . $post_title );
				// $floorplansid = substr( $floorplansid, 0, 16);
				update_post_meta( $post_id, 'floorplansid', $floorplansid );
				$floorplanslink = home_url() . "/floorplans/$post_slug/$floorplansid";
				update_post_meta( $post_id, 'floorplanslink', $floorplanslink );
				$row['floorplanslink'] = $floorplanslink;
			}

			if ($row['floorplansfolder']) {
				$files_json = json_encode( fetch_project_googledrive_files( $post_id ) );
				update_post_meta( $post_id, 'googledrive_files', $files_json );
			}


			## loop through and convert floorplans pdfs
			## skip for now
			$floorplans = get_post_meta($post_id, 'floorplanspdfs', true);
			if ($this->forceUpdate || $row['floorplanspdfs'] != $floorplans) {
				update_post_meta( $post_id, 'import_floorplans_pdfs', '1' );
			}

			## pull lat and lng from map
			if ($this->forceUpdate || $row['map'] != get_post_meta($post_id, 'map', true)) {
				// update_post_meta( $post_id, 'geocode', '1' );
				update_post_meta( $post_id, 'map', $row['map'] );
				geocode_project_location_data( $post_id );
			}

			## Loop through standard meta fields
			foreach ($this->metaFields as $f) {
				update_post_meta( $post_id, $f, $row[$f] );
			}

			## update some non-standard fields
			if ($row['luxury'] == 'YES') wp_set_object_terms($post_id, 'luxury', 'type', true);
			if ($row['metadescription']) update_post_meta( $post_id, '_yoast_wpseo_metadesc', $row['metadescription'] );
			if ($row['seotitle']) update_post_meta( $post_id, '_yoast_wpseo_title', $row['seotitle'] );

			// Set initial pricing and size data
			update_post_meta( $post_id, '_min_size',  (int) preg_replace("/[^0-9]/", "", $row['sq.ft.from'] ));
			update_post_meta( $post_id, '_max_size',  (int) preg_replace("/[^0-9]/", "", $row['sq.ftto'] ));
			update_post_meta( $post_id,'_min_price',  (int) preg_replace("/[^0-9]/", "", $row['pricedfrom'] ));
			update_post_meta( $post_id,'_max_price',  (int) preg_replace("/[^0-9]/", "", $row['pricedto'] ));

			// set launching soon fields

            $this->saveLaunchingSoonDate($post_id, $row);


			$finalhash = md5(serialize($row));
			update_post_meta( $post_id, 'hash', $finalhash );
			update_post_meta( $post_id, 'last-updated', time() );

			## if row has changed, push row to spreadsheet (only on live site)
			if ($finalhash != $newhash) $this->pushRow($n+2, $row);

		}

	}

	private function saveLaunchingSoonDate($post_id, $row){
	    $string = $row['launchingsoondate'];
        $overrideDay = $row['launchingsoonoverride'];

	    $isLaunchingSoon = $row['salesstatus'] == 'Launching Soon';
	    // cleanup if no longer "Launching Soon" or
	    if(!$isLaunchingSoon){
	        if(!empty(get_field('launch_year', $post_id))){
                delete_field('launch_year', $post_id);
                delete_field('launch_season', $post_id);
                delete_field('launch_month', $post_id);
                delete_field('launch_day', $post_id);
                delete_field('launch_day_override', $post_id);
            }
            return;
        }

//        $string = '2021 January 1 ';
        $season_months = ['spring', 'summer', 'fall']; // no winter here
        $short_months = [1 => 'jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];

        $r = ["year"=>null, "season"=>null, "month"=>null, "day"=>null];

        // parse
        foreach($season_months as $season){
            if(false !== stripos($string, $season)){
                $r["season"] = trim($season);
                break;
            }
        }

        foreach($short_months as $month){
            if(false !== stripos($string, $month)){
                $r["month"] = trim($month);
                break;
            }
        }

        preg_match('/\d{4}/', $string, $matches);
        if(!empty($matches[0])){
            $r["year"] = trim($matches[0]);
        }

        preg_match('/\s\d{1,2}\s*/', $string, $matches);
        if(!empty($matches[0])){
            $r["day"] = trim($matches[0]);
        }

        // validate and save
        if(empty($r["year"])){
            $r["year"] = $r["season"] = $r["month"] = $r["day"] = null;
        }

        if(empty($r["season"]) && empty($r["month"])){
            $r["season"] = $r["month"] = $r["day"] = null;
        }

        if(!empty($r["month"])){
            $r["season"] = null;
        }

        if(empty($r["day"])){
            $r["day"] = null;
        }

        update_field('launch_year', $r['year'], $post_id);
        update_field('launch_season', $r['season'], $post_id);
        update_field('launch_month', $r['month'], $post_id);
        update_field('launch_day', $r['day'], $post_id);

        if(empty($overrideDay)) {
            delete_field('launch_day_override', $post_id);
        } else {
            update_field('launch_day_override', $overrideDay, $post_id);
        }
    }

}
