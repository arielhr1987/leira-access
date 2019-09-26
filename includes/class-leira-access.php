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
 * @package    Leira_Access
 * @subpackage Leira_Access/includes
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
 * @package    Leira_Access
 * @subpackage Leira_Access/includes
 * @author     Ariel <arielhr1987@gmail.com>
 *
 * @property Leira_Access_Admin admin
 */
class Leira_Access{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Leira_Access_Loader $loader Maintains and registers all hooks for the plugin.
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
	 * @since  1.0.0
	 * @access protected
	 * @var null
	 */
	protected static $instance = null;

	/**
	 * The Singleton method
	 *
	 * @return self
	 * @since  1.0.0
	 * @access public
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
	 * @access   protected
	 */
	protected function __construct() {
		if ( defined( 'LEIRA_ACCESS_VERSION' ) ) {
			$this->version = LEIRA_ACCESS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'leira-access';

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Leira_Access_Loader. Orchestrates the hooks of the plugin.
	 * - Leira_Access_i18n. Defines internationalization functionality.
	 * - Leira_Access_Admin. Defines all hooks for the admin area.
	 * - Leira_Access_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 */
	protected function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-leira-access-loader.php';

		/**
		 * The class responsible for defining internationalization functionality of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-leira-access-i18n.php';

		if ( is_admin() && current_user_can( 'manage_options' ) ) {

			/**
			 * The class responsible for defining all actions that occur in the admin area.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-leira-access-admin.php';

			/**
			 * Nav Menu
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-leira-access-admin-menu.php';

			/**
			 * Widgets
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-leira-access-admin-widget.php';

			/**
			 * Posts
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-leira-access-admin-post-type.php';

			/**
			 * Taxonomies
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-leira-access-admin-taxonomy.php';
		}

		/**
		 * The class responsible for defining all actions that occur in the public-facing side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-leira-access-public.php';

		/**
		 * Nav Menu
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-leira-access-public-menu.php';

		$this->loader = new Leira_Access_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Leira_Access_i18n class in order to set the domain and to register the hook with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 */
	protected function set_locale() {

		$plugin_i18n = new Leira_Access_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 */
	protected function define_admin_hooks() {

		if ( is_admin() && current_user_can( 'manage_options' ) ) {

			$plugin_admin = new Leira_Access_Admin( $this->get_plugin_name(), $this->get_version() );

			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );

			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

			//Add the admin class instance to the loader
			$this->loader->set( 'admin', $plugin_admin );

			/**
			 * Nav Menu
			 */
			$plugin_admin_menu = new Leira_Access_Admin_Menu();

			//Use custom admin walker.
			$this->loader->add_filter( 'wp_edit_nav_menu_walker', $plugin_admin_menu, 'edit_nav_menu_walker', 10, 2 );

			//Add new fields via hook. This is a custom filter fired from within the Walker_Nav_Menu_Edit class
			$this->loader->add_filter( 'wp_nav_menu_item_custom_fields', $plugin_admin_menu, 'form', 10, 4 );

			//Save the menu item metadata.
			$this->loader->add_action( 'wp_update_nav_menu_item', $plugin_admin_menu, 'update_nav_menu_item', 10, 2 );

			//add the admin menu class instance to de loader
			$this->loader->set( 'admin_menu', $plugin_admin_menu );

			/**
			 * Widgets
			 */
			$plugin_admin_widget = new Leira_Access_Admin_Widget();

			$this->loader->add_action( 'in_widget_form', $plugin_admin_widget, 'form', 20, 3 );

			$this->loader->add_filter( 'widget_update_callback', $plugin_admin_widget, 'update', 20, 4 );

			$this->loader->set( 'admin_widget', $plugin_admin_widget );

			/**
			 * Posts
			 */
			$plugin_admin_post_type = new Leira_Access_Admin_Post_Type();

			//Add meta-box to post edit page
			$this->loader->add_action( 'load-post.php', $plugin_admin_post_type, 'init' );

			//Add meta-box to edit post page
			$this->loader->add_action( 'load-post-new.php', $plugin_admin_post_type, 'init' );

			$post_types = $plugin_admin_post_type->get_post_types();

			foreach ( $post_types as $post_type ) {

				//Filters the columns displayed in the Posts list table for a specific post type.
				//This filter is documented in: wp-admin/includes/class-wp-posts-list-table.php
				$this->loader->add_filter( "manage_{$post_type}_posts_columns", $plugin_admin_post_type, 'custom_column_header' );

				//Fires for each custom column of a specific post type in the Posts list table.
				//This action is documented in: wp-admin/includes/class-wp-posts-list-table.php
				$this->loader->add_action( "manage_{$post_type}_posts_custom_column", $plugin_admin_post_type, 'custom_column_content', 10, 2 );
			}

			//add bulk quick edit fields
			//TODO: For future release
			//$this->loader->add_action( 'bulk_edit_custom_box', $plugin_admin_post_type, 'bulk_edit_custom_box', 10, 2 );

			//add quick edit fields
			$this->loader->add_action( 'quick_edit_custom_box', $plugin_admin_post_type, 'quick_edit_custom_box', 10, 2 );

			//save post
			$this->loader->add_action( 'save_post', $plugin_admin_post_type, 'save', 10, 2 );

			//enqueue scripts for quick edit list table
			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin_post_type, 'admin_enqueue_quick_edit_scripts' );

			//add to loader
			$this->loader->set( 'admin_post_type', $plugin_admin_post_type );

			/**
			 * Taxonomies
			 */
			$plugin_admin_taxonomy = new Leira_Access_Admin_Taxonomy();

			$taxonomies = $plugin_admin_taxonomy->get_taxonomies();

			foreach ( $taxonomies as $taxonomy ) {
				//custom column header
				$this->loader->add_filter( "manage_edit-{$taxonomy}_columns", $plugin_admin_taxonomy, 'custom_column_header', 10 );

				//add sortable columns
				//$this->loader->add_filter( "manage_edit-{$taxonomy}_sortable_columns", $plugin_admin_taxonomy, 'custom_column_sortable', 10 );

				//custom column content
				$this->loader->add_action( "manage_{$taxonomy}_custom_column", $plugin_admin_taxonomy, 'custom_column_content', 10, 3 );

				//add fields to taxonomy edit screen
				$this->loader->add_action( "{$taxonomy}_edit_form_fields", $plugin_admin_taxonomy, 'edit_form_fields' );

				$this->loader->add_action( "{$taxonomy}_add_form_fields", $plugin_admin_taxonomy, 'add_form_fields' );

				$this->loader->add_action( "edited_{$taxonomy}", $plugin_admin_taxonomy, 'edit', 10, 2 );

				$this->loader->add_action( "create_{$taxonomy}", $plugin_admin_taxonomy, 'save', 10, 2 );

			}

			//add quick edit fields to taxonomy list table
			$this->loader->add_action( 'quick_edit_custom_box', $plugin_admin_taxonomy, 'quick_edit_custom_box', 10, 2 );

			//enqueue scripts for quick edit list table
			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin_taxonomy, 'admin_enqueue_quick_edit_scripts' );

			//handle sort columns
			//$this->loader->add_filter( 'get_terms_fields', $plugin_admin_taxonomy, 'custom_column_sort_fields', 10, 3 );
			//$this->loader->add_filter( 'terms_clauses', $plugin_admin_taxonomy, 'custom_column_sort_clauses', 10, 3 );
			//$this->loader->add_filter( 'pre_get_terms', $plugin_admin_taxonomy, 'custom_column_sort', 10, 3 );

			//add to loader
			$this->loader->set( 'admin_taxonomy', $plugin_admin_taxonomy );

		}
	}

	/**
	 * Register all of the hooks related to the public-facing functionality of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 */
	protected function define_public_hooks() {

		$plugin_public = new Leira_Access_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->set( 'public', $plugin_public );

		/**
		 * Nav Menu
		 */
		$plugin_public_menu = new Leira_Access_Public_Menu();

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
	 * @access   public
	 */
	public function run() {
		add_action( 'init', array( $this, 'init' ) );
	}


	/**
	 * Load dependencies and set hooks
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function init() {

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
	 * @access    public
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    Leira_Access_Loader    Orchestrates the hooks of the plugin.
	 * @since     1.0.0
	 * @access    public
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 * @since     1.0.0
	 * @access    public
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
	 *
	 * @since     1.0.0
	 * @access    public
	 *
	 */
	public function __get(
		$key
	) {
		return $this->get_loader()->get( $key );
	}

	/**
	 * Sets an instance in the loader
	 *
	 * @param string $key
	 * @param mixed  $value
	 *
	 * @since     1.0.0
	 * @access    public
	 *
	 */
	public function __set(
		$key, $value
	) {
		$this->get_loader()->set( $key, $value );
	}

	/**
	 * What type of request is this?
	 *
	 * @param string $type admin, ajax, cron or frontend.
	 *
	 * @return bool
	 * @since     1.0.0
	 * @access    public
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
