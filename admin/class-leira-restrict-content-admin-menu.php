<?php

/**
 * The admin-specific functionality for menu items.
 *
 * @since      1.0.0
 *
 * @package    Leira_Restrict_Content
 * @subpackage Leira_Restrict_Content/admin
 * @author     Ariel <arielhr1987@gmail.com>
 */
class Leira_Restrict_Content_Admin_Menu{
	/**
	 * Rfc_Menu constructor.
	 */
	public function __construct() {

	}

	/**
	 * Override default Admin Menu Walker.
	 * This could generate some interference with other plugins using the same technique to handle menu items edition
	 *
	 * @since 1.0
	 */
	public function edit_nav_menu_walker( $walker, $menu_id ) {
		$class = 'Leira_Restrict_Content_Walker_Nav_Menu_Edit';
		if ( ! class_exists( $class ) ) {
			//
			require_once( plugin_dir_path( __FILE__ ) . '/class-leira-restrict-content-walker-nav-menu-edit.php' );
		}

		return $class;
	}

	/**
	 * Add fields to hook added in Walker
	 * This will allow us to play nicely with any other plugin that is adding the same hook
	 *
	 * @params obj $item - the menu item
	 * @params array $args
	 *
	 * @since  1.6.0
	 */
	public function nav_menu_item_custom_fields( $item_id, $item, $depth, $args ) {
		//new approach
		$roles = get_post_meta( $item->ID, '_leira-restrict-content', true );
		$id    = isset( $item->ID ) ? $item->ID : false;
		leira_restrict_content()->admin->form( $roles, $id );

		return;


		$wp_roles = wp_roles();
		/**
		 * Pass the menu item to the filter function.
		 * This change is suggested as it allows the use of information from the menu item (and
		 * by extension the target object) to further customize what filters appear during menu
		 * construction.
		 */
		$display_roles = apply_filters( 'leira_restrict_content_available_roles', $wp_roles->role_names, $item );
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
		$roles = get_post_meta( $item->ID, '_leira-restrict-content', true );
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


        <div class="field-leira-restrict-content description description-wide">
            <p style="margin: 0;">
                <span class="description"><?php _e( "Available for:", 'leira-restrict-content' ); ?></span>
            </p>

            <input type="hidden" name="nav-menu-role-nonce"
                   value="<?php echo wp_create_nonce( 'nav-menu-nonce-name' ); ?>"/>

            <input type="hidden" class="nav-menu-id" value="<?php echo $item->ID; ?>"/>

            <input type="radio" class="nav-menu-logged-status"
                   name="nav-menu-logged-status[<?php echo $item->ID; ?>]"
                   id="nav_menu_by_role-for-<?php echo $item->ID; ?>" <?php checked( '', $status ); ?>
                   value=""/>
            <label for="nav_menu_by_role-for-<?php echo $item->ID; ?>">
				<?php _e( 'Everyone', 'leira-restrict-content' ); ?>
            </label>
            <br>

            <input type="radio" class="nav-menu-logged-status"
                   name="nav-menu-logged-status[<?php echo $item->ID; ?>]"
                   id="nav_menu_logged_out-for-<?php echo $item->ID; ?>" <?php checked( 'out', $status ); ?>
                   value="out"/>
            <label for="nav_menu_logged_out-for-<?php echo $item->ID; ?>">
				<?php _e( 'Logged Out Users', 'leira-restrict-content' ); ?>
            </label>
            <br>

            <input type="radio" class="nav-menu-logged-status"
                   name="nav-menu-logged-status[<?php echo $item->ID; ?>]"
                   id="nav_menu_logged_in-for-<?php echo $item->ID; ?>" <?php checked( 'in', $status ); ?>
                   value="in"/>
            <label for="nav_menu_logged_in-for-<?php echo $item->ID; ?>">
				<?php _e( 'Logged In Users', 'leira-restrict-content' ); ?>
            </label>

            <div class="field-leira-restrict-content leira-restrict-content_field description-wide"
                 style="margin: 0 0 0 20px; <?php //echo $hidden;?>">
                <span class="description"><?php _e( "Restrict menu item to a minimum role", 'leira-restrict-content' ); ?></span>
                <br/>

				<?php
				$i = 1;
				/* Loop through each of the available roles. */
				foreach ( $display_roles as $role => $name ) {
					/* If the role has been selected, make sure it's checked. */
					$checked = checked( true, ( is_array( $checked_roles ) && in_array( $role, $checked_roles ) ), false );
					?>
                    <input type="checkbox" name="nav-menu-role[<?php echo $item->ID; ?>][<?php echo $i; ?>]"
                           id="leira-restrict-content-<?php echo $role; ?>-for-<?php echo $item->ID; ?>" <?php echo $checked; ?>
                           value="<?php echo $role; ?>"/>
                    <label for="leira-restrict-content-<?php echo $role; ?>-for-<?php echo $item->ID; ?>">
						<?php echo esc_html( $name ); ?>
						<?php $i ++; ?>
                    </label>
                    <br>

				<?php } ?>

            </div>
        </div>

		<?php
	}

	/**
	 * Save the roles as menu item meta
	 *
	 * @return string
	 * @since 1.0
	 */
	public function update_nav_menu_item( $menu_id, $menu_item_db_id ) {
		$wp_roles      = wp_roles();
		$allowed_roles = apply_filters( 'leira_restrict_content_available_roles', $wp_roles->role_names );
		// Verify this came from our screen and with proper authorization.
		if ( ! isset( $_POST['leira-restrict-content-nonce'] ) || ! wp_verify_nonce( $_POST['leira-restrict-content-nonce'], 'leira-restrict-content-nonce-name' ) ) {
			return;
		}
		$saved_data = false;
		if ( isset( $_POST['leira-restrict-content-status'][ $menu_item_db_id ] ) && $_POST['leira-restrict-content-status'][ $menu_item_db_id ] == 'in' && ! empty ( $_POST['leira-restrict-content-role'][ $menu_item_db_id ] ) ) {
			$custom_roles = array();
			// Only save allowed roles.
			foreach ( (array) $_POST['leira-restrict-content-role'][ $menu_item_db_id ] as $role ) {
				if ( array_key_exists( $role, $allowed_roles ) ) {
					$custom_roles[] = $role;
				}
			}
			if ( ! empty ( $custom_roles ) ) {
				$saved_data = $custom_roles;
			}
		} else if ( isset( $_POST['leira-restrict-content-status'][ $menu_item_db_id ] ) && in_array( $_POST['leira-restrict-content-status'][ $menu_item_db_id ], array(
				'in',
				'out'
			) ) ) {
			$saved_data = $_POST['leira-restrict-content-status'][ $menu_item_db_id ];
		}
		if ( $saved_data ) {
			update_post_meta( $menu_item_db_id, '_leira-restrict-content', $saved_data );
		} else {
			delete_post_meta( $menu_item_db_id, '_leira-restrict-content' );
		}
	}

}
