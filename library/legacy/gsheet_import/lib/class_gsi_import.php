<?php

class gsi_import
{

	private $service;
	public $data;
	public $cells = '';
	public $response;
	public $new = [];
	public $updates = [];

	function __construct($service) {

		$this->service = $service;

	}

	public function fetchData()
	{

		$this->range = implode('!', array_filter([$this->sheet, $this->cells]));
		$this->response = $this->service->spreadsheets_values->get($this->ssid, $this->range);
		// $this->response = $this->service->spreadsheets_values->get($this->ssid, $this->range, ['valueRenderOption' => 'UNFORMATTED_VALUE']);

		$this->prepareData();

		return $this->data;

	}


	public function fetchRow($row)
	{
		$options = [
			'ranges' => ['1:1', "$row:$row"],
			'valueRenderOption' => 'FORMATTED_VALUE',
		];

		$this->response = $this->service->spreadsheets_values->batchGet($this->ssid, $options);

		$this->rawData = $this->response->getValueRanges();

		$headers = $this->rawData[0]->getValues()[0];
		$this->setHeaders($headers);

		$row = $this->rawData[1]->getValues()[0];
		$this->data = array_combine($this->headers, array_pad($row, count($this->headers), ''));

		return $this->data;
	}


	function prepareData()
	{
		$this->rawData = $this->response->getValues();

		$this->data = $this->rawData;
		$this->setHeaders(array_shift($this->data));

		$this->data = array_map(function($row) {
			return array_combine($this->headers, array_pad($row, count($this->headers), ''));
		}, $this->data);
	}


	function setHeaders($headers)
	{
		$this->originalHeaders = $headers;

		$headers = array_map(function ($title) {
			$title = preg_replace('/[^\.a-zA-Z0-9]/', '', $title);
			$title = strtolower($title);
			return $title;
		}, $headers);

		$this->headerMap = array_combine($headers, $this->originalHeaders);

		$this->headers = $headers;
	}

	function pushRow($n, $row)
	{
		if ( ! is_live() ) return;

		$range = "{$this->sheet}!$n:$n";

		$body = new Google_Service_Sheets_ValueRange([
			'values' => [array_values($row)]
		]);

		$params = [
			'valueInputOption' => 'RAW',
		];

		return $this->service->spreadsheets_values->update($this->ssid, $range, $body, $params);
	}

	function cacheData()
	{
		file_put_contents( $this->cachefile, json_encode($this->data, JSON_PRETTY_PRINT) );
	}


	function fetchCachedData()
	{
		$this->data = json_decode( file_get_contents($this->cachefile) );
	}

}