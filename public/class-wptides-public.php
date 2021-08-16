<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @package    Wptides
 * @subpackage Wptides/public
 * @author     Brian Keith <bskeith@gmail.com>
 */
class Wptides_Public {

	public function __construct() {
	}

	public function enqueue_styles() {

		wp_enqueue_style( 'wpTides', plugin_dir_url( __FILE__ ) . 'css/wptides-public.css', array(), '1.0.0', 'all' );

	}

	public function wptides_shortcodes_init() {

		add_shortcode( 'wptides_display', [$this , 'display'] );

	}

	// Format and clean the data for use in the public facing tempate
	public function format_json_tide_prediction(string $response, string $station_time_zone) {

		$tide_predictions = json_decode( $response, true );
		$tide_predictions = $tide_predictions['predictions'];

		// Gets the timezone conversion array. Converts Noaa timezones to PHP timezones
		$timezones = Wptides_Noaa_Tides::get_timezones();

		// We want to use the stations timezone for comparing timestamps
		$timezone = new DateTimeZone($timezones[$station_time_zone]);

		$station_datetime = new DateTime('now', $timezone);
		$station_timestamp = $station_datetime->getTimestamp();

		$next_tide_found = false;
		foreach( $tide_predictions as $index=>$prediction )
		{
			// If we have all the tides for the day truncate the array
			// Checking for $next_tide_found will allow and extra tide(the next day's tide) to be displayed
			if ($index > 3 and $next_tide_found)
			{
				$tide_predictions = array_slice($tide_predictions, 0, $index);
				break;
			}

			$prediction_dt = new DateTime($prediction['t'], $timezone);
			$tide_timestamp = $prediction_dt->getTimestamp();

			$next_tide = false;
			if ( $station_timestamp < $tide_timestamp and ! $next_tide_found )
			{
				$next_tide = true;
				$next_tide_found = true;
			}

			// Format the time output
			$local_time = $prediction_dt->format('g:i A');

			$tide_predictions[$index]['local_time'] = $local_time;

			// Can be used to highlight the upcomming tide
			$tide_predictions[$index]['next_tide'] = $next_tide;
		}

		return 	$tide_predictions;

	}

	public function display() : string {

		$options = get_option( 'wptides_settings' );

		if (!isset($options['station'])) {
			return '';
		}

		$noaa_api = new Wptides_Noaa_Tides();
		// Gets the timezone conversion array. Converts Noaa timezones to PHP timezones
		$timezones = Wptides_Noaa_Tides::get_timezones();

		// Get the tide station info
		$response = $noaa_api->metadata_station_request( $options['station'] );
		if ($response)
		{
			$station = json_decode($response, TRUE);
			$station = $station['stations'][0];

			$station_datetime = new DateTime('now', new DateTimeZone($timezones[$station['timezone']]) );

		}

		$begin_date = $station_datetime->format('Ymd');
		$end_date = $station_datetime->modify('+1 day')->format('Ymd');
		$station_datetime->modify('-1 day');
		$request_params = [
			'begin_date' 	=> $begin_date,
			'end_date' 		=> $end_date,
			'station' 		=> $options['station'],
		];

		// Get tide predictions
		$response = $noaa_api->tide_predictions($request_params);

		$tide_predictions = $this->format_json_tide_prediction($response, $station['timezone']);

		// Load the partial template
		ob_start();
		include( plugin_dir_path( __DIR__ ) . 'public/partials/wptides-public-display.php' );
		return ob_get_clean();
	}

}
