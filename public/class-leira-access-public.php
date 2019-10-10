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
	 * List of all visible post type IDs to the current user
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var null
	 */
	protected $visible_post_type_ids = null;

	/**
	 * List of all hidden post type IDs to the current user
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var null
	 */
	protected $hidden_post_type_ids = null;

	/**
	 * List of all visible term IDs to the current user
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var null
	 */
	protected $visible_term_ids = null;

	/**
	 * List of all hidden term IDs to the current user
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var null
	 */
	protected $hidden_term_ids = null;

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
	 * @param $item
	 *
	 * @return bool
	 * @since  1.0.0
	 * @access public
	 */
	public function check_access( $item ) {

		$visible = true;
		$access  = '';
		$type    = '';

		if ( $item instanceof WP_Term ) {
			$access = get_term_meta( $item->term_id, Leira_Access::META_KEY, true );
			$type   = 'term';
		} elseif ( $item instanceof WP_Post ) {
			$access = get_post_meta( $item->ID, Leira_Access::META_KEY, true );
			$type   = 'post';
			if ( isset( $item->post_type ) && $item->post_type == 'nav_menu_item' ) {
				$type = 'menu_item';
			}
		} elseif ( $item instanceof WP_Widget && isset( $item->leira_access ) ) {
			$access = $item->leira_access;
			$type   = 'widget';
		} else if ( is_string( $item ) || is_array( $item ) ) {
			$access = $item;
			$type   = 'block';
		}

		if ( is_array( $access ) || in_array( $access, array( 'in', 'out' ) ) ) {
			switch ( $access ) {
				case '':
					$visible = true;
					break;
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
		}

		$visible = apply_filters( "leira_access_{$type}_visibility", $visible, $item );

		return $visible;
	}


	/**
	 * Check if the given Term is visible or not to the current user.
	 * This method will check for term ancestors (recursively)
	 *
	 * @param WP_Term $term The term to check
	 *
	 * @return bool
	 */
	public function check_term_access( $term ) {

		$visible              = true;
		$available_taxonomies = leira_access()->get_taxonomies();
		$taxonomy             = ( isset( $term->taxonomy ) && ! empty( $term->taxonomy ) ) ? $term->taxonomy : false;

		if ( $term instanceof WP_Term && in_array( $taxonomy, $available_taxonomies ) ) {

			$visible = leira_access()->public->check_access( $term );

			/**
			 * Check for ancestors
			 */
			$is_taxonomy_available = isset( $term->taxonomy ) && in_array( $term->taxonomy, leira_access()->get_taxonomies() );

			if ( $visible && is_taxonomy_hierarchical( $term->taxonomy ) && $is_taxonomy_available ) {

				$check_ancestors = false;

				$check_ancestors = apply_filters( 'leira_access_check_term_ancestors', $check_ancestors, $term );

				$check_ancestors = apply_filters( "leira_access_check_term_{$term->taxonomy}_ancestors", $check_ancestors );

				if ( $check_ancestors && isset( $term->parent ) && $term->parent > 0 ) {

					$parent = get_term( $term->parent );

					$visible = leira_access()->public->check_term_access( $parent );
				}
			}
		}

		return $visible;
	}

	/**
	 * Check if the given post is visible or not to the current user.
	 * This method will check for post ancestors (recursively)
	 *
	 * @param WP_Post $post
	 *
	 * @return bool
	 */
	public function check_post_type_access( $post ) {

		$visible = true;

		$available_post_types = leira_access()->get_post_types();

		$post_type = ( isset( $post->post_type ) && ! empty( $post->post_type ) ) ? $post->post_type : false;

		if ( $post instanceof WP_Post && in_array( $post_type, $available_post_types ) ) {

			/**
			 * Check current post access
			 */
			$visible = leira_access()->public->check_access( $post );

			/**
			 * Check for post type ancestors
			 */
			if ( $visible && is_post_type_hierarchical( $post_type ) ) {

				$check_ancestors = false;

				$check_ancestors = apply_filters( "leira_access_check_post_type_{$post_type}_ancestors", $check_ancestors );

				$check_ancestors = apply_filters( 'leira_access_check_post_type_ancestors', $check_ancestors, $post );

				if ( $check_ancestors && isset( $post->post_parent ) && $post->post_parent > 0 ) {

					$parent = get_post( $post->post_parent );

					$visible = leira_access()->public->check_post_type_access( $parent );
				}
			}

			/**
			 * Check post terms
			 */
			$check_taxonomies = false;

			$check_taxonomies = apply_filters( 'leira_access_check_post_type_parent_taxonomies', $check_taxonomies, $post );

			$check_taxonomies = apply_filters( "leira_access_check_post_type_{$post->post_type}_parent_taxonomies", $check_taxonomies );

			if ( $visible && $check_taxonomies ) {

				$available_taxonomies = leira_access()->get_taxonomies();

				$parent_terms = wp_get_post_terms( $post->ID, array_keys( $available_taxonomies ) );

				if ( is_array( $parent_terms ) ) {

					foreach ( $parent_terms as $term ) {

						$visible = leira_access()->public->check_term_access( $term );

						if ( ! $visible ) {
							break;
						}
					}
				}
			}
		}

		return $visible;
	}

	/**
	 * Returns the meta query object to exclude posts from lists
	 *
	 * @return array
	 * @since  1.0.0
	 * @access public
	 */
	public function get_meta_query() {

		$logged_in = is_user_member_of_blog() || is_super_admin();

		if ( $logged_in ) {
			/**
			 * User is logged in, show post types with "_leira-access" meta value equal to "in",
			 * meta doesn't exist or meta contains any of the current user role
			 */
			$user  = wp_get_current_user();
			$roles = ( array ) $user->roles;

			$meta_query = array(
				'relation' => 'OR',
				array(
					'key'     => Leira_Access::META_KEY,
					'value'   => 'in',
					'compare' => '=',

				),
				array(
					'key'     => Leira_Access::META_KEY,
					'compare' => 'NOT EXISTS',
				)
			);

			//in case the user has more than 1 role assigned
			foreach ( $roles as $role ) {
				$meta_query[] = array(
					'key'     => Leira_Access::META_KEY,
					'value'   => "$role",
					'compare' => 'LIKE',
				);
			}

		} else {
			/**
			 * User is not logged in, show post types with "_leira-access" meta value equal to "out"
			 * or meta doesn't exist
			 */
			$meta_query = array(
				'relation' => 'OR',
				array(
					'key'     => Leira_Access::META_KEY,
					'value'   => 'out',
					'compare' => '=',

				),
				array(
					'key'     => Leira_Access::META_KEY,
					'compare' => 'NOT EXISTS',
				)
			);
		}

		return $meta_query;
	}

	/**
	 * Redirect user. This method is called when user has no access to certain page
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function redirect() {
		$redirect_to = get_option( 'leira_redirect_to', '' );

		$redirect_to = isset( $redirect_to['leira_redirect_to'] ) ? $redirect_to['leira_redirect_to'] : '';

		$redirect_to = apply_filters( 'leira_access_redirect_url', $redirect_to );

		if ( empty( $redirect_to ) ) {
			global $wp;

			$url         = home_url( $wp->request );
			$redirect_to = wp_login_url( $url );

		}

		wp_redirect( $redirect_to );

		die();
	}

	/**
	 * Returns a list with all post type ids that are accessible to the current user
	 *
	 * @return array
	 * @since    1.0.0
	 * @access   public
	 */
	public function get_visible_post_type_ids() {

		if ( $this->visible_post_type_ids === null ) {

			$post_types = leira_access()->get_post_types();

			$query_args = array(
				'orderby'          => 'date',
				'order'            => 'DESC',
				'posts_per_page'   => - 1,
				'post_type'        => $post_types,
				'post_status'      => 'publish',
				'offset'           => 0,
				'fields'           => 'ids',
				'suppress_filters' => true,
				'meta_query'       => leira_access()->public->get_meta_query()
			);
			$get_posts  = new WP_Query;

			$this->visible_post_type_ids = $get_posts->query( $query_args );
		}

		return $this->visible_post_type_ids;
	}

	/**
	 * Returns a list with all post type ids that are accessible to the current user
	 *
	 * @return array
	 * @since    1.0.0
	 * @access   public
	 */
	public function get_hidden_post_type_ids() {

		if ( $this->hidden_post_type_ids === null ) {

			$post_types = leira_access()->get_post_types();

			$query_args                 = array(
				'orderby'          => 'date',
				'order'            => 'DESC',
				'posts_per_page'   => - 1,
				'post_type'        => $post_types,
				'post_status'      => 'publish',
				'offset'           => 0,
				'fields'           => 'ids',
				'suppress_filters' => true,
				'post__not_in'     => $this->get_visible_post_type_ids()
			);
			$get_posts                  = new WP_Query;
			$this->hidden_post_type_ids = $get_posts->query( $query_args );
		}

		return $this->hidden_post_type_ids;
	}

	/**
	 * @return array
	 */
	public function get_visible_term_ids() {

		if ( $this->visible_term_ids === null ) {

			$this->visible_term_ids = get_terms( array(
				'fields'     => 'ids',
				'taxonomy'   => leira_access()->get_taxonomies(),
				'meta_query' => $this->get_meta_query()
			) );
		}

		return $this->visible_term_ids;
	}

}
