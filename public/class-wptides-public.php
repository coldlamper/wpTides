<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/coldlamper
 * @since      1.0.0
 *
 * @package    Wptides
 * @subpackage Wptides/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wptides
 * @subpackage Wptides/public
 * @author     Brian Keith <bskeith@gmail.com>
 */
class Wptides_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wptides_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wptides_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wptides-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wptides_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wptides_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wptides-public.js', array( 'jquery' ), $this->version, false );

	}

	public function wptides_shortcodes_init() {
		add_shortcode( 'wptides_display', [$this , 'display'] );
	}

	public function format_json_tide_prediction(string $response, string $station_time_zone) {

		$tide_predictions = json_decode( $response, true );
		$tide_predictions = $tide_predictions['predictions'];

		$timezones = Wptides_Noaa_Tides::get_timezones();

		$timezone = new DateTimeZone($timezones[$station_time_zone]);

		$station_datetime = new DateTime("now", $timezone);
		$station_timestamp = $station_datetime->getTimestamp();

		$next_tide_found = false;
		foreach( $tide_predictions as $index=>$prediction )
		{
			if ($index > 3 and $next_tide_found)
			{
				$tide_predictions = array_slice($tide_predictions, 0, $index);
				break;
			}

			$prediction_dt = new DateTime($prediction['t']);
			$tide_timestamp = $prediction_dt->getTimestamp();

			$next_tide = false;
			if ( $station_timestamp < $tide_timestamp and ! $next_tide_found )
			{
				$next_tide = true;
				$next_tide_found = true;
			}

			//$prediction_dt->setTimezone($timezone);
			$local_time = $prediction_dt->format('g:i A');

			$tide_predictions[$index]['local_time'] = $local_time;
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
		$timezones = Wptides_Noaa_Tides::get_timezones();

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

		$response = $noaa_api->tide_predictions($request_params);

		$tide_predictions = $this->format_json_tide_prediction($response, $station['timezone']);


		ob_start();
		include( plugin_dir_path( __DIR__ ) . 'public/partials/wptides-public-display.php' );
		return ob_get_clean();
	}

}
