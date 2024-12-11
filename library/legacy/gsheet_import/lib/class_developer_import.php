<?php

class developer_import extends gsi_import
{

	public $ssid = '1ATDxsfSq2LA0Ppy8DYr1C6UlWwCzb4Tu_pv17Xn-BS0';
	public $sheet = 'Developers';

	public $taxonomy = 'developer';
	public $taxonomies = array();
	public $customFields = array();

	function __construct($service)
	{

		$this->taxonomies = array(
			'developer' => 'developer'
		);

		$this->customFields = array(
			'url' => 'field_5482ce3d8b5e1',
			'hash' => 'field_5483425f97ee6',
		);

		parent::__construct($service);

	}


	function getStatus()
	{

		foreach ($this->data as $row) {

			$row['hash'] = md5(serialize($row));

			foreach ($this->taxonomies as $columnName => $taxonomy) {

				if (!$term = term_exists( $row[$columnName], $taxonomy)) {
					$this->new[] = $row[$columnName];
					continue;
				}

				if ( $termObject = get_term( $term['term_id'], $this->taxonomy )) {
				 	if ($this->customFields['hash'] && $row['hash'] != get_field( 'hash', $termObject)) {
						$updates[] = $termObject->name;
						continue;
					}
				}

			}

		}

	}


	function import()
	{

		$skipped = 0;

		foreach ($this->data as $row) {

			$row['hash'] = md5(serialize($row));

			foreach ($this->taxonomies as $columnName => $taxonomy) {

				$_term = $row[$columnName];

				if (!$_term) continue;

				$term = term_exists( $_term, $taxonomy);

				if (!$term) {

					$term = wp_insert_term( $_term, $taxonomy, array( 'parent' => 0 ) );

					if ( is_wp_error( $term ) ) {
						continue;
					};

					$termObject = get_term( $term['term_id'], $this->taxonomy );
					$this->new[] = $termObject->name;

				} else {

					$termObject = get_term( $term['term_id'], $this->taxonomy );

				}

				if ($this->customFields['hash'] && $row['hash'] != get_field( 'hash', $termObject )) {

		 			foreach ($this->customFields as $columnName => $fieldID) {
						update_field( $fieldID, $_term, $termObject );
					}

					$this->updates[] = $termObject->name;

				} else {
					$skipped++;
				}

			}

		}

	}

}
