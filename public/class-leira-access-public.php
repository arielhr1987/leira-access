<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/arielhr1987
 * @since      1.0.0
 *
 * @package    Leira_Access
 * @subpackage Leira_Access/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Leira_Access
 * @subpackage Leira_Access/public
 * @author     Ariel <arielhr1987@gmail.com>
 */
class Leira_Access_Public{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version     The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Leira_Access_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Leira_Access_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/leira-access-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Leira_Access_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Leira_Access_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/leira-access-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Determine if access to the current resource is granted according to the give $access configuration
	 *
	 * @param $access
	 * @param $item
	 *
	 * @return bool
	 * @since  1.0.0
	 * @access public
	 */
	public function check_access( $access, $item ) {
		if ( is_string( $access ) && empty( trim( $access ) ) ) {
			//Its an empty string, visible to Everyone
			return true;
		}
		switch ( $access ) {
			case 'in' :
				/**
				 * Multisite compatibility.
				 *
				 * For the logged in condition to work,
				 * the user has to be a logged in member of the current blog
				 * or be a logged in super user.
				 */
				$visible = is_user_member_of_blog() || is_super_admin() ? true : false;
				break;
			case 'out' :
				/**
				 * Multisite compatibility.
				 *
				 * For the logged out condition to work,
				 * the user has to be either logged out
				 * or not be a member of the current blog.
				 * But they also may not be a super admin,
				 * because logged in super admins should see the internal stuff, not the external.
				 */
				$visible = ! is_user_member_of_blog() && ! is_super_admin() ? true : false;
				break;
			default:
				$visible = false;
				if ( is_array( $access ) && ! empty( $access ) ) {
					foreach ( $access as $role ) {
						if ( current_user_can( $role ) ) {
							$visible = true;
						}
					}
				}
				break;
		}

		return $visible;
	}

}
