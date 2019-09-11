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
class Leira_Restrict_Content_Admin{

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
		 * defined in Leira_Restrict_Content_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Leira_Restrict_Content_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/leira-restrict-content-admin.css', array(), $this->version, 'all' );

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
		 * defined in Leira_Restrict_Content_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Leira_Restrict_Content_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/leira-restrict-content-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function form( $roles, $id ) {
		$wp_roles = wp_roles();
		/**
		 * Pass the menu item to the filter function.
		 * This change is suggested as it allows the use of information from the menu item (and
		 * by extension the target object) to further customize what filters appear during menu
		 * construction.
		 */
		$display_roles = apply_filters( 'leira_restrict_content_available_roles', $wp_roles->role_names, $id ); //TODO: Add $item or item type (post_type)
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
		//$roles = get_post_meta( $id, '_leira-restrict-content', true );
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
		// Whether to display the role checkboxes.
		$hidden = $status == 'in' ? '' : 'display: none;';
		?>


        <div class="leira-restrict-content-container description description-wide">
            <p class="description">
				<?php _e( "Visible to:", 'leira-restrict-content' ); ?>
            </p>

            <input type="hidden" name="leira-restrict-content-nonce"
                   value="<?php echo wp_create_nonce( 'leira-restrict-content-nonce-name' ); ?>"/>

            <input type="hidden" class="leira-restrict-content-item-id" value="<?php echo $id; ?>"/>

            <input type="radio" class="leira-restrict-content-status"
                   name="leira-restrict-content-status[<?php echo $id; ?>]"
                   id="leira-restrict-content-for-<?php echo $id; ?>" <?php checked( '', $status ); ?>
                   value=""/>
            <label for="leira-restrict-content-for-<?php echo $id; ?>">
				<?php _e( 'Everyone', 'leira-restrict-content' ); ?>
            </label>
            <br>

            <input type="radio" class="leira-restrict-content-status"
                   name="leira-restrict-content-status[<?php echo $id; ?>]"
                   id="leira-restrict-content-out-for-<?php echo $id; ?>" <?php checked( 'out', $status ); ?>
                   value="out"/>
            <label for="leira-restrict-content-out-for-<?php echo $id; ?>">
				<?php _e( 'Logged Out Users', 'leira-restrict-content' ); ?>
            </label>
            <br>

            <input type="radio" class="leira-restrict-content-status"
                   name="leira-restrict-content-status[<?php echo $id; ?>]"
                   id="leira-restrict-content-in-for-<?php echo $id; ?>" <?php checked( 'in', $status ); ?>
                   value="in"/>
            <label for="leira-restrict-content-in-for-<?php echo $id; ?>">
				<?php _e( 'Logged In Users', 'leira-restrict-content' ); ?>
            </label>

            <div class="leira-restrict-content-roles description">
                <p class="description">
					<?php _e( "Restrict menu item to a minimum role", 'leira-restrict-content' ); ?>
                </p>

				<?php
				$i = 1;
				/* Loop through each of the available roles. */
				foreach ( $display_roles as $role => $name ) {
					/* If the role has been selected, make sure it's checked. */
					$checked = checked( true, ( is_array( $checked_roles ) && in_array( $role, $checked_roles ) ), false );
					?>
                    <input type="checkbox" name="leira-restrict-content-role[<?php echo $id; ?>][<?php echo $i; ?>]"
                           id="leira-restrict-content-<?php echo $role; ?>-for-<?php echo $id; ?>" <?php echo $checked; ?>
                           value="<?php echo $role; ?>"/>
                    <label for="leira-restrict-content-<?php echo $role; ?>-for-<?php echo $id; ?>">
						<?php echo esc_html( $name ); ?>
						<?php $i ++; ?>
                    </label>
                    <br>

				<?php } ?>

            </div>
        </div>

		<?php
	}

}
