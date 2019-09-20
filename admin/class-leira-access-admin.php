<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/arielhr1987
 * @since      1.0.0
 *
 * @package    Leira_Restrict_Content
 * @subpackage Leira_Restrict_Content/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Leira_Restrict_Content
 * @subpackage Leira_Restrict_Content/admin
 * @author     Ariel <arielhr1987@gmail.com>
 */
class Leira_Access_Admin{

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
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		function sidebar_plugin_register() {
			wp_register_script(
				'leira-access',
				plugin_dir_url( __FILE__ ) . 'js/leira-access-admin.js',
				array( 'wp-plugins', 'wp-edit-post', 'wp-element' )
			);
		}

		add_action( 'init', 'sidebar_plugin_register' );

		function sidebar_plugin_script_enqueue() {
			wp_enqueue_script( 'leira-access' );
		}

		add_action( 'enqueue_block_editor_assets', 'sidebar_plugin_script_enqueue' );
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
		 * defined in Leira_Access_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Leira_Access_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/leira-access-admin.css', array(), $this->version, 'all' );

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
		 * defined in Leira_Access_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Leira_Access_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		//wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/leira-access-admin.js', array( 'jquery' ), $this->version, false );


	}

	/**
	 * Render the form to select content visibility
	 *
	 * @param array  $roles
	 * @param string $id
	 * @param array  $options Possible options are:
	 *                        'show_label' => true,// show the label
	 *                        'label'      => __( 'Visible to', 'leira-access' ), //the label to show
	 *                        'add_nonce'  => true //add nonce or not
	 */
	public function form( $roles, $id = '', $options = array() ) {
		$options = array_merge( array(
			'show_label' => true,
			'label'      => __( 'Visible to', 'leira-access' ),
			'add_nonce'  => true
		), $options );

		$wp_roles = wp_roles();
		/**
		 * Pass the menu item to the filter function.
		 * This change is suggested as it allows the use of information from the menu item (and
		 * by extension the target object) to further customize what filters appear during menu
		 * construction.
		 */
		$display_roles = apply_filters( 'leira-access_available_roles', $wp_roles->role_names, $id ); //TODO: Add $item or item type (post_type)
		/**
		 * If no roles are being used, don't display the role selection radio buttons at all.
		 * Unless something deliberately removes the WordPress roles from this list, nothing will
		 * be functionally altered to the end user.
		 * This change is suggested for the benefit of users constructing granular admin permissions
		 * using extensive custom roles as it is an effective means of stopping admins with partial
		 * permissions to the menu from accidentally removing all restrictions from a menu item to
		 * which they do not have access.
		 */
		if ( ! $display_roles ) {
			return;
		}
		/* Get the roles saved for the post. */
		//$roles = get_post_meta( $id, '_leira-access', true );
		// By default nothing is checked (will match "everyone" radio).
		$status = '';
		// Specific roles are saved as an array, so "in" or an array equals "in" is checked.
		if ( is_array( $roles ) or $roles == 'in' ) {
			$status = 'in';
		} else if ( $roles == 'out' ) {
			$status = 'out';
		}
		// The specific roles to check.
		$checked_roles = is_array( $roles ) ? $roles : false;

		$html_name_id = empty( $id ) ? "" : "[$id]";
		$html_for_id  = empty( $id ) ? "" : "-$id";

		?>


        <div class="leira-access-container">
			<?php if ( $options['show_label'] ): ?>
                <p class="">
					<?php echo $options['label']; ?>
                </p>
			<?php endif; ?>

            <div class="leira-access-controls">
                <input type="hidden" name="leira-access-nonce"
                       value="<?php echo wp_create_nonce( 'leira-access-nonce-name' ); ?>"/>

                <!--                <input type="hidden" class="leira-access-item-id" value="-->
				<?php //echo $id; ?><!--"/>-->

                <input type="radio" class="leira-access-status"
                       name="leira-access-status<?php echo $html_name_id; ?>"
                       id="leira-access-for<?php echo $html_for_id; ?>" <?php checked( '', $status ); ?>
                       value=""/>
                <label for="leira-access-for<?php echo $html_for_id; ?>">
					<?php _e( 'Everyone', 'leira-access' ); ?>
                </label>
                <br>

                <input type="radio" class="leira-access-status"
                       name="leira-access-status<?php echo $html_name_id; ?>"
                       id="leira-access-out-for<?php echo $html_for_id; ?>" <?php checked( 'out', $status ); ?>
                       value="out"/>
                <label for="leira-access-out-for<?php echo $html_for_id; ?>">
					<?php _e( 'Logged Out Users', 'leira-access' ); ?>
                </label>
                <br>

                <input type="radio" class="leira-access-status"
                       name="leira-access-status<?php echo $html_name_id; ?>"
                       id="leira-access-in-for<?php echo $html_for_id; ?>" <?php checked( 'in', $status ); ?>
                       value="in"/>
                <label for="leira-access-in-for<?php echo $html_for_id; ?>">
					<?php _e( 'Logged In Users', 'leira-access' ); ?>
                </label>

                <div class="leira-access-roles">
                    <p class="">
						<?php _e( "Restrict item to a minimum role", 'leira-access' ); ?>
                    </p>

					<?php
					$i = 1;
					/* Loop through each of the available roles. */
					foreach ( $display_roles as $role => $name ) {
						/* If the role has been selected, make sure it's checked. */
						$checked = checked( true, ( is_array( $checked_roles ) && in_array( $role, $checked_roles ) ), false );
						?>
                        <input type="checkbox"
                               name="leira-access-role<?php echo $html_name_id; ?>[<?php echo $i; ?>]"
                               id="leira-access-<?php echo $role; ?>-for<?php echo $html_for_id; ?>" <?php echo $checked; ?>
                               value="<?php echo $role; ?>"/>
                        <label for="leira-access-<?php echo $role; ?>-for<?php echo $html_for_id; ?>">
							<?php echo esc_html( $name ); ?>
							<?php $i ++; ?>
                        </label>
                        <br>

					<?php } ?>

                </div>
            </div>
        </div>

		<?php
	}

	/**
	 * Save the access options to metadata or options if is a widget
	 *
	 * @param string $id          The id
	 * @param string $type        The object type you are saving
	 * @param bool   $use_post_id Use or not the id post
	 *
	 * @return bool
	 */
	public function save( $id, $type = 'post', $use_post_id = true ) {

		// Verify this came from our screen and with proper authorization.
		if ( ! isset( $_POST['leira-access-nonce'] ) || ! wp_verify_nonce( $_POST['leira-access-nonce'], 'leira-access-nonce-name' ) ) {
			return false;
		}

		$wp_roles      = wp_roles();
		$allowed_roles = apply_filters( 'leira-access_available_roles', $wp_roles->role_names );
		$saved_data    = false;

		$status = false;
		$roles  = array();
		if ( $use_post_id ) {
			$status = isset( $_POST['leira-access-status'][ $id ] ) ? $_POST['leira-access-status'][ $id ] : $status;
			$roles  = ( ! empty( $_POST['leira-access-role'][ $id ] ) ) ? $_POST['leira-access-role'][ $id ] : $roles;
		} else {
			$status = isset( $_POST['leira-access-status'] ) ? $_POST['leira-access-status'] : $status;
			$roles  = ( ! empty( $_POST['leira-access-role'] ) ) ? $_POST['leira-access-role'] : $roles;
		}


		if ( ! empty( $status ) ) {

			if ( $status == 'in' && ! empty ( $roles ) ) {
				$custom_roles = array();
				// Only save allowed roles.
				foreach ( $roles as $role ) {
					if ( isset( $allowed_roles[ $role ] ) ) {
						$custom_roles[] = $role;
					}
				}
				if ( ! empty ( $custom_roles ) ) {
					$saved_data = $custom_roles;
				}
			} else if ( in_array( $status, array( 'in', 'out' ) ) ) {
				$saved_data = $status;
			}
		}


		switch ( $type ) {
			case 'post':
				if ( $saved_data ) {
					update_post_meta( $id, '_leira-access', $saved_data );
				} else {
					delete_post_meta( $id, '_leira-access' );
				}
				break;
			case 'term':
				if ( $saved_data ) {
					update_term_meta( $id, '_leira-access', $saved_data );
				} else {
					delete_term_meta( $id, '_leira-access' );
				}
				break;
			case 'widget':
				return $saved_data;
				break;
			default:

		}

		return true;
	}

}