<?php

/**
 * Noaa Tides and Currents API Interface
 *
 * @package    Wptides
 * @subpackage Wptides/includes
 * @author     Brian Keith <bskeith@gmail.com>
 */
class Wptides_Noaa_Tides {

	private static array $timezones = [
		'AST'  => 'America/New_York',
		'EST'  => 'America/New_York',
		'CST'  => 'America/Chicago',
		'MST'  => 'America/Denver',
		'PST'  => 'America/Los_Angeles',
		'AKST' => 'America/Anchorage',
		'HST'  => 'Pacific/Honolulu',
	];

	private string $noaa_co_ops_metadata_api_url = 'https://api.tidesandcurrents.noaa.gov/mdapi/prod/webapi/stations';

	private string $noaa_co_ops_data_api_url = 'https://api.tidesandcurrents.noaa.gov/api/prod/datagetter';


	public function __construct() {
	}

	public static function get_timezones() {
		return self::$timezones;
	}

	private function make_get_request(string $url) : string {
		$request = wp_remote_get( $url );
		$response_code = $request['response']['code'] ?? false;
		if ( $response_code != 200 or is_wp_error( $request )) {
			return '';
		}

		return wp_remote_retrieve_body( $request );
	}


	public function metadata_station_request(int $station_id = 0, string $extension = 'json', string $resource = '', string $type = '')
	{
		$url = $this->noaa_co_ops_metadata_api_url;
		if ( $station_id ) {
			$url .= '/' . $station_id;
			if ($resource) {
				$url .= '/' . $resource;
			}
		}

		$url .= '.' . $extension;
		if ( $type ) {
			$url .= '?type=' . $type;
		}

		$cache = get_transient($url);
		if ($cache) {
			return $cache;
		}

		$response = $this->make_get_request($url);
		set_transient($url, $response, 60 * 60 * 12);
		return $response;
	}

	public function data_request( array $request_params )
	{
		$url = $this->noaa_co_ops_data_api_url;
		$query_string = http_build_query($request_params);
		$url .= '?' . $query_string;

		$cache = get_transient($query_string);
		if ($cache) {
			return $cache;
		}

		$response = $this->make_get_request($url);
		set_transient($query_string, $response, 60 * 60 * 12);
		return $response;
	}

	public function tide_predictions( array $params )
	{
		$default_params = [
			'product'		=> 'predictions',
			'begin_date' 	=> date('Ymd'),
			'end_date' 		=> date("Ymd", strtotime("+1 day")),
			'datum'			=> 'MLLW',
			'station' 		=> 8658559,
			'time_zone'		=> 'lst_ldt',
			'units'			=> 'english',
			'interval'		=> 'hilo',
			'format'		=> 'json',
			'application'	=> 'wpTides'
		];

		$request_params = array_merge( $default_params, $params );
		return $this->data_request( $request_params );
	}
}
