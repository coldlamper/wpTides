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

class Wptides_Admin {

	public function __construct() {
	}

	public function enqueue_styles() {

		wp_enqueue_style( 'wpTides', plugin_dir_url( __DIR__ ) . 'public/css/wptides-public.css', [], '1.0.0', 'all' );

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

		$plugin_public = new Wptides_Public();
		$sample_output = $plugin_public->display();

		include_once(plugin_dir_path( __DIR__ ) . 'admin/partials/wptides-admin-display.php');

	}

}
