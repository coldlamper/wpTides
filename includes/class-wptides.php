<?php

/**
 * The core plugin class.
 *
 * @since      1.0.0
 * @package    Wptides
 * @subpackage Wptides/includes
 * @author     Brian Keith <bskeith@gmail.com>
 */
class Wptides {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wptides_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	public function __construct() {
		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wptides-loader.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wptides-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wptides-public.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wptides-noaa-tides.php';

		$this->loader = new Wptides_Loader();
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Wptides_Admin();

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'wptides_admin_menu' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'wptides_register_admin_menu_settings' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Wptides_Public();

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		// We need the css in the admin as well
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_public, 'enqueue_styles' );

		$plugin_public->wptides_shortcodes_init();

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Wptides_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}
}
