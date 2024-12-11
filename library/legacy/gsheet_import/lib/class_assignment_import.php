<?php

class assignment_import extends gsi_import
{

	public $ssid = '1ATDxsfSq2LA0Ppy8DYr1C6UlWwCzb4Tu_pv17Xn-BS0';
	public $sheet = 'Assignments';

	public function __construct($service) {

		$this->metaFields = array(
			'price',
			'suitenumber',
			'squarefootage',
			'balcony',
			'exposure',
			'bedrooms',
			'bathrooms',
			'depositpaid',
			'depositremaining',
			'occupancydate',
			'maintenancefees',
			'suitefeatures',
			'bonusincentives',
			'other',
			'storeys',
			'amenities',
			'infusionsoft'
		);

		parent::__construct($service);

	}


	public function getStatus() {

		foreach ($this->data as $row) {

			$post_title = trim($row['project'] . ' ' . $row['suitenumber']);

			if (!$row['postid'] > 0) {
				$this->new[] = $post_title;
				continue;
			}

			$newhash = md5(serialize($row));
			$oldhash = ($row['postid'] > 0) ? get_post_meta($row['postid'], 'hash', true) : false;

			if ($newhash != $oldhash) $this->updates[] = $post_title;

		}

	}


	public function import() {

		global $wpdb;

		$i = 0;
		$skipped = 0;

		foreach ($this->entries as $entry) {

			$pushRow = false;
			$i++;

			if ($this->forceUpdate || !$this->limit || $this->limit > $i) {

				$row = $entry->getValues();

				$post_title = trim($row['project'] . ' ' . $row['suitenumber']);

				if ($row['postid'] && !get_post($row['postid'])) {
					// echo '<p>invalid post id [' . $row['postid'] . '] for assignment: ' . $post_title . '</p>';
					continue;
				}

				$newhash = md5(serialize($row));
				$oldhash = ($row['postid']) ? get_post_meta($row['postid'], 'hash', true) : false;

				if ( (!$row['postid'] > 0) || ($oldhash != $newhash) || ($this->forceupdate) ) {

					$args = array(
						'ID' => $row['postid'],
						'post_content' => '',
						'post_title' => $post_title,
						'post_type' => 'assignment',
						'post_status' => ($row['trash'] == 1 || $row['trash'] == 'trash') ? 'trash' : 'publish',
						'post_category' => '',
					);

					$postid = wp_insert_post($args, false);

					if ($row['postid']) {
						// echo '<p>updating post: ' . $post_title . '</p>';
						$this->updates[] = $post_title;
					} else {
						// echo '<p>creating new post: ' . $post_title . '</p>';
						$this->new[] = $post_title;
						$row['postid'] = $postid;
						$pushRow = true;
						$regenerateHash = true;
					}

					// search for projects based on the project name
					$q = "SELECT * from $wpdb->posts where post_type = 'project' and post_title = '%s' limit 1";
					$q = $wpdb->prepare( $q, $row['project'] );
					$project = $wpdb->get_row( $q, OBJECT );

					// link project id
					$project_field_id = 'field_5482e6bdb5df4';
					update_field( $project_field_id, $project->ID, $postid);

					foreach ($this->metaFields as $f) {
						update_post_meta( $postid, $f, $row[$f] );
					}

					if ($row['metadescription']) update_post_meta( $postid, '_yoast_wpseo_metadesc', $row['metadescription'] );
					if ($row['seotitle']) update_post_meta( $postid, '_yoast_wpseo_title', $row['seotitle'] );

					if ($regenerateHash) $newhash = md5(serialize($row));
					update_post_meta( $postid, 'hash', $newhash );
					update_post_meta( $postid, 'last-updated', time() );

					// if ($pushRow) $this->ss->updateRow($entry, $row);
					if ($pushRow) $this->pushRow($row);

				} else {
					$skipped++;
				}


			}

		}

	}


}