<?php

/**
 * The admin-specific functionality for gutenberg blocks.
 *
 * @since      1.0.0
 *
 * @package    Leira_Access
 * @subpackage Leira_Access/admin
 * @author     Ariel <arielhr1987@gmail.com>
 */
class Leira_Access_Admin_Block{
	/**
	 * Constructor.
	 */
	public function __construct() {

	}

	/**
	 * Enqueue gutenberg block editor string
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function enqueue_js() {

		// Enqueue our script
		wp_enqueue_script(
			'leira-access-admin-block-js',
			esc_url( plugins_url( '/js/leira-access-admin-block.js', __FILE__ ) ),
			array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ),
			LEIRA_ACCESS_VERSION,
			true // Enqueue the script in the footer.
		);
	}

	/**
	 * Add roles to admin
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function insert_roles() {
		$roles = leira_access()->admin->get_available_roles();
		?>
        <script>
            if (typeof wp !== 'undefined') {
                wp['_leira-access'] = {
                    roles: <?php echo json_encode( $roles ); ?>
                };
            }
        </script>
		<?php
	}

}
