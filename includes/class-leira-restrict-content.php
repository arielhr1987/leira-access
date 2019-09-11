<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/arielhr1987
 * @since      1.0.0
 *
 * @package    Leira_Restrict_Content
 * @subpackage Leira_Restrict_Content/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Leira_Restrict_Content
 * @subpackage Leira_Restrict_Content/includes
 * @author     Ariel <arielhr1987@gmail.com>
 */
class Leira_Restrict_Content{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Leira_Restrict_Content_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Singleton instance
	 *
	 * @var null
	 */
	protected static $instance = null;

	/**
	 * The Singleton method
	 *
	 * @return self
	 */
	public static function instance() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	protected function __construct() {
		if ( defined( 'LEIRA_RESTRICT_CONTENT_VERSION' ) ) {
			$this->version = LEIRA_RESTRICT_CONTENT_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'leira-restrict-content';

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Leira_Restrict_Content_Loader. Orchestrates the hooks of the plugin.
	 * - Leira_Restrict_Content_i18n. Defines internationalization functionality.
	 * - Leira_Restrict_Content_Admin. Defines all hooks for the admin area.
	 * - Leira_Restrict_Content_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-leira-restrict-content-loader.php';

		/**
		 * The class responsible for defining internationalization functionality of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-leira-restrict-content-i18n.php';

		if ( is_admin() ) {
			/**
			 * The class responsible for defining all actions that occur in the admin area.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-leira-restrict-content-admin.php';

			/**
			 * Nav Menu.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-leira-restrict-content-admin-menu.php';

		}

		/**
		 * The class responsible for defining all actions that occur in the public-facing side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-leira-restrict-content-public.php';

		/**
		 * Nav Menu
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-leira-restrict-content-public-menu.php';

		$this->loader = new Leira_Restrict_Content_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Leira_Restrict_Content_i18n class in order to set the domain and to register the hook with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Leira_Restrict_Content_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		if ( is_admin() ) {
			$plugin_admin = new Leira_Restrict_Content_Admin( $this->get_plugin_name(), $this->get_version() );

			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

			//Add the admin class instance to the loader
			$this->loader->set( 'admin', $plugin_admin );

			/**
			 * Nav Menu
			 */
			$plugin_admin_menu = new Leira_Restrict_Content_Admin_Menu();

			//Use custom admin walker.
			$this->loader->add_filter( 'wp_edit_nav_menu_walker', $plugin_admin_menu, 'edit_nav_menu_walker', 10, 2 );
			//Add new fields via hook. This is a custom filter fired from within the Walker_Nav_Menu_Edit class
			$this->loader->add_filter( 'wp_nav_menu_item_custom_fields', $plugin_admin_menu, 'nav_menu_item_custom_fields', 10, 4 );
			//Save the menu item metadata.
			$this->loader->add_action( 'wp_update_nav_menu_item', $plugin_admin_menu, 'update_nav_menu_item', 10, 2 );

			//add the admin menu class instance to de loader
			$this->loader->set( 'admin_menu', $plugin_admin_menu );
		}
	}

	/**
	 * Register all of the hooks related to the public-facing functionality of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Leira_Restrict_Content_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->set( 'public', $plugin_public );

		/**
		 * Nav Menu
		 */
		$plugin_public_menu = new Leira_Restrict_Content_Public_Menu();

		//Add metadata to menu items
		$this->loader->add_filter( 'wp_setup_nav_menu_item', $plugin_public_menu, 'setup_nav_menu_item' );
		//Add custom filter to check visibility of menu items
		$this->loader->add_filter( 'wp_get_nav_menu_items', $plugin_public_menu, 'exclude_menu_items', 20 );
		//filter menu item visibility
		$this->loader->add_filter( 'nav_menu_item_visibility', $plugin_public_menu, 'filter_menu_item_visible', 20, 2 );

		$this->loader->set( 'public_menu', $plugin_public_menu );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the plugin.
	 * @since     1.0.0
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    Leira_Restrict_Content_Loader    Orchestrates the hooks of the plugin.
	 * @since     1.0.0
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 * @since     1.0.0
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Gets an instance from the loader
	 *
	 * @param string $key
	 *
	 * @return mixed|null The instance
	 * @since     1.0.0
	 *
	 */
	public function __get( $key ) {
		return $this->get_loader()->get( $key );
	}

	/**
	 * Sets an instance in the loader
	 *
	 * @param string $key
	 * @param mixed  $value
	 *
	 * @since     1.0.0
	 *
	 */
	public function __set( $key, $value ) {
		$this->get_loader()->set( $key, $value );
	}

	/**
	 * What type of request is this?
	 *
	 * @param string $type admin, ajax, cron or frontend.
	 *
	 * @return bool
	 */
	public function is_request( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_admin();
			case 'ajax':
				return defined( 'DOING_AJAX' );
			case 'cron':
				return defined( 'DOING_CRON' );
			case 'frontend':
				return ( ! is_admin() or defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}

}
