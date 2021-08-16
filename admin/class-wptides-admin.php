<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/coldlamper
 * @since      1.0.0
 *
 * @package    Wptides
 * @subpackage Wptides/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wptides
 * @subpackage Wptides/admin
 * @author     Brian Keith <bskeith@gmail.com>
 */
class Wptides_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wptides-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wptides-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function wptides_admin_menu() {
		add_menu_page(
			'wpTides Plugin',
			'wpTides Plugin',
			'activate_plugins',
			'wptides-plugin-main',
			[$this, 'get_admin_content'],
			'',
			100
		);
	}

	public function wptides_register_admin_menu_settings() {
		//register our settings
		register_setting( 'wptides_settings', 'wptides_settings', 'wptides_validate_settings' );
		// register_setting( 'wptides-settings-group', 'latitude' );
		// register_setting( 'wptides-settings-group', 'longitude' );

		add_settings_section( 'wptides_settings', 'Settings', ['Wptides_Admin', 'wptides_settings_section_text'], 'wptides-plugin-main' );

		add_settings_field( 'station', 'Station ID', ['Wptides_Admin', 'wptides_settings_render_input_field'], 'wptides-plugin-main', 'wptides_settings',
			[
				'type' => 'text',
				'group' => 'wptides_settings',
				'field' => 'station'
			]
		);
	}

	public static function wptides_settings_section_text() : void {
		echo '';
	}

	public static function wptides_settings_render_input_field(array $args) : void {
		$options = get_option( 'wptides_settings' ) ?: [$args['field'] => ''];
		printf(
			'<input type="text" name="%s" value="%s" />',
			esc_attr( $args['group'] . '['  . $args['field'] . ']' ),
			esc_attr( $options[$args['field']] )
		);
	}

	function wptides_validate_settings( array $input ) : array {
		// TODO add validation
		return $input;
	}

	public function get_admin_content() {
		include_once(plugin_dir_path( __DIR__ ) . 'admin/partials/wptides-admin-display.php');

		$plugin_public = new Wptides_Public($this->plugin_name, $this->version);
		echo $plugin_public->display();
	}

}
