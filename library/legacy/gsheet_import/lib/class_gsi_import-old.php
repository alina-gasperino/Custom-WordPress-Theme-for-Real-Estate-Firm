<?php

class gsi_import
{

	public $user;
	public $password;
	public $ssKey;
	public $wsName;
	public $wsKey;
	public $ss;
	public $query;
	public $data = array();
	public $taxonomies = array();
	public $worksheets = array();
	public $cacheData = true;

	public $limit = 999999;
	public $forceUpdate = false;


	function __construct($ssService = null) {

		$this->ssService = $ssService;

		$this->cachefile = get_stylesheet_directory() . "/library/legacy/gsheet_import/data/{$this->wsName}.json";

	}


	public function setSSKey( $key ) {

		$this->ssKey = $key;

	}

	public function setWSKey( $key ) {

		$this->wsKey = $key;

	}


	public function setSpreadsheet( $name ) {

		$this->ssName = $name;

	}


	public function setWorksheet( $name ) {

		$this->wsName = $name;

	}


	public function getColumns() {

	}

	public function fetchData() {

		try {

			$this->ssFeed = $this->ssService->getSpreadsheets();
			$this->ss = $this->ssFeed->getByTitle($this->ssName);
			$this->wsFeed = $this->ss->getWorksheets();
			$this->ws = $this->wsFeed->getByTitle($this->wsName);
			$this->listFeed = $this->ws->getListFeed();
			$this->entries = $this->listFeed->getEntries();

		} catch ( Google\Spreadsheet\UnauthorizedException $e ) {

			echo $e->getMessage();
			update_option( 'googleapi_access_token', null );

		}

		$this->data = array();
		foreach ($this->entries as $entry) {
			$this->data[] = $entry->getValues();
		}

		if ($this->cacheData) $this->cacheData();

	}


	function getWorksheetList() {

	}


	function convertListFeedToArray() {


	}


	function cacheData() {


		file_put_contents( $this->cachefile, json_encode($this->data) );

		$handle = fopen($this->cachefile, 'w');

		fwrite($handle, json_encode($this->data));

		fclose($handle);

	}


	function fetchCache() {

		$handle = fopen($this->cachefile, 'r');

		$this->data = json_decode( file_get_contents($handle) );

		fclose($handle);

	}


}