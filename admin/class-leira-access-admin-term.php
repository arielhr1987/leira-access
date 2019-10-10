<?php

/**
 * The admin-specific functionality for taxonomy items.
 *
 * @since      1.0.0
 *
 * @package    Leira_Access
 * @subpackage Leira_Access/admin
 * @author     Ariel <arielhr1987@gmail.com>
 */
class Leira_Access_Admin_Term{

	/**
	 * Constructor.
	 */
	public function __construct() {

	}

	/**
	 * Get the list of available taxonomies
	 *
	 * @return array
	 * @since  1.0.0
	 * @access public
	 */
	public function get_taxonomies() {
		return leira_access()->get_taxonomies();
	}

	/**
	 * Add column header to taxonomy list table
	 *
	 * @param array $columns List of available columns
	 *
	 * @return array
	 * @since  1.0.0
	 * @access public
	 */
	public function custom_column_header( $columns ) {
		$columns['leira-access'] = __( 'Access', 'leira-access' );

		return $columns;
	}

	/**
	 * Add sortable columns to taxonomy list table
	 *
	 * @param array $columns List of available sortable columns
	 *
	 * @return array
	 * @since  1.0.0
	 * @access public
	 */
	public function custom_column_sortable( $columns ) {
		$columns['leira-access'] = 'leira-access';

		return $columns;
	}

	/**
	 * Set content for columns in management page
	 *
	 * @param string $string      Blank string.
	 * @param string $column_name Name of the column.
	 * @param int    $term_id     Term ID.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function custom_column_content( $string, $column_name, $term_id ) {
		if ( 'leira-access' != $column_name ) {
			return;
		}

		$access = get_term_meta( $term_id, Leira_Access::META_KEY, true );
		$output = leira_access()->admin->column_content( $access );

		echo $output;
	}

	/**
	 * THIS METHOD IS NOT USED
	 * Adds "wp_termmeta.meta_value" to the select query statement so the order by can work correctly.
	 *
	 * @param array $selects
	 * @param array $args
	 * @param array $taxonomies
	 *
	 * @return array
	 * @since  1.0.0
	 * @access public
	 */
	public function custom_column_sort_fields( $selects, $args, $taxonomies ) {
		global $pagenow;
		$taxonomy_to_query    = isset( $taxonomies[0] ) ? $taxonomies[0] : false;
		$taxonomy             = isset( $_GET ['taxonomy'] ) ? sanitize_text_field( $_GET ['taxonomy'] ) : false;
		$orderby_to_query     = isset( $args['orderby'] ) ? $args['orderby'] : false;
		$order_by             = isset( $_GET ['orderby'] ) ? sanitize_text_field( $_GET ['orderby'] ) : false;
		$available_taxonomies = $this->get_taxonomies();

		if ( $pagenow == 'edit-tags.php'
		     //&& $order_by == 'leira-access'
		     && $orderby_to_query === '_leira_access'
		     && $taxonomy_to_query === $taxonomy
		     && in_array( $taxonomy, $available_taxonomies ) ) {

			//$selects[] = 'wp_termmeta.meta_value';
			$selects[] = "IF(wp_termmeta.meta_key <> '_leira-access', NULL, wp_termmeta.meta_value) AS _leira_access";
		}

		return $selects;
	}


	/**
	 * Modifies the order by clause in the query so it match the select statement
	 *
	 * @param $clauses
	 * @param $taxonomies
	 * @param $args
	 *
	 * @return mixed
	 * @since  1.0.0
	 * @access public
	 */
	public function custom_column_sort_clauses( $clauses, $taxonomies, $args ) {
		global $pagenow;
		$taxonomy_to_query    = isset( $taxonomies[0] ) ? $taxonomies[0] : false;
		$taxonomy             = isset( $_GET ['taxonomy'] ) ? sanitize_text_field( $_GET ['taxonomy'] ) : false;
		$orderby_to_query     = isset( $args['orderby'] ) ? $args['orderby'] : false;
		$order_by             = isset( $_GET ['orderby'] ) ? sanitize_text_field( $_GET ['orderby'] ) : false;
		$available_taxonomies = $this->get_taxonomies();

		if ( $pagenow == 'edit-tags.php'
		     //&& $order_by == 'leira-access'
		     && $orderby_to_query === '_leira_access'
		     && $taxonomy_to_query === $taxonomy
		     && in_array( $taxonomy, $available_taxonomies ) ) {

			$clauses['orderby'] = 'ORDER BY _leira_access';
		}

		return $clauses;
	}

	/**
	 * Handle sort custom column actions.
	 *
	 * @param WP_Term_Query $query
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function custom_column_sort( $query ) {
		global $pagenow;
		$screen            = get_current_screen();
		$taxonomy_to_query = isset( $query->query_vars['taxonomy'][0] ) ? $query->query_vars['taxonomy'][0] : false;
		$orderby_to_query  = isset( $query->query_vars['orderby'] ) ? $query->query_vars['orderby'] : false;
		$taxonomy          = isset( $_GET ['taxonomy'] ) ? sanitize_text_field( $_GET ['taxonomy'] ) : false;
		$order_by          = isset( $_GET ['orderby'] ) ? sanitize_text_field( $_GET ['orderby'] ) : false;
		$order             = isset( $_GET ['order'] ) ? strtoupper( sanitize_text_field( $_GET ['order'] ) ) : 'DESC';
		if ( ! in_array( $order, array( 'ASC', 'DESC' ) ) ) {
			$order = 'DESC';
		}

		/**
		 * We have a problem with this query, if a term doesn't have set the _leira-access meta key and has an other
		 * meta key set the order statement fails
		 */
		$available_taxonomies = $this->get_taxonomies();
		if ( $pagenow == 'edit-tags.php'
		     //&& $order_by === 'leira-access'
		     && $orderby_to_query === 'leira-access'
		     && $taxonomy_to_query === $taxonomy
		     && in_array( $taxonomy, $available_taxonomies ) ) {

			//Keep on mind that this code is execute in the edit tag page if we are sorting the "Access" column.
			$meta_query = new WP_Meta_Query( array(
					'relation' => 'OR',
					array(
						'key' => Leira_Access::META_KEY,
					),
					array(
						'key'     => Leira_Access::META_KEY,
						'compare' => 'NOT EXISTS'
					)
				)
			);
			//and ordering matches
			$query->meta_query = $meta_query;
			//$query->query_vars['orderby'] = 'meta_value';
			$query->query_vars['orderby'] = '_leira_access';
			$query->query_vars['order']   = $order;

		}
	}

	/**
	 * Add quick edit form to taxonomies list table
	 *
	 * @param string $column_name
	 * @param string $post_type
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function quick_edit_custom_box( $column_name, $post_type ) {
		$screen   = get_current_screen();
		$taxonomy = isset( $screen->taxonomy ) ? $screen->taxonomy : false;
		if ( 'leira-access' != $column_name || ! in_array( $taxonomy, $this->get_taxonomies() ) || 'edit-tags' !== $post_type ) {
			return;
		}

		$id    = false;
		$roles = array();
		?>
        <fieldset class="">
            <div class="inline-edit-col">
				<?php leira_access()->admin->form( array(
					'roles'           => $roles,
					'id'              => $id,
					'input_id_prefix' => 'inline-leira-access'
				) ); ?>
            </div>
        </fieldset>
		<?php
	}

	/**
	 * Enqueue quick edit list table script
	 *
	 * @param $hook
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function admin_enqueue_quick_edit_scripts( $hook ) {
		$pages = array( 'edit-tags.php' );
		if ( ! in_array( $hook, $pages ) ) {
			return;
		}

		$screen   = get_current_screen();
		$taxonomy = isset( $screen->taxonomy ) ? $screen->taxonomy : false;
		if ( ! in_array( $taxonomy, $this->get_taxonomies() ) ) {
			return;
		}

		wp_enqueue_script( 'leira-access-admin-quick-edit-taxonomy-js', plugins_url( '/js/leira-access-admin-quick-edit-taxonomy.js', __FILE__ ), array(
			'jquery',
			'inline-edit-tax',
		) );
	}

	/**
	 * Add access form to term edit screen admin page
	 *
	 * @param $tag
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function edit_form_fields( $tag ) {
		$roles = get_term_meta( $tag->term_id, Leira_Access::META_KEY, true );

		?>
        <tr>
            <th scope="row">
                <label for=""><?php _e( 'Access', 'leira-access' ) ?></label>
            </th>
            <td>
				<?php leira_access()->admin->form( array(
					'roles'      => $roles,
					//'id'         => $tag->term_id,
					'show_label' => false
				) ) ?>
                <p class="description">
                    <!-- An optional description -->
                </p>
            </td>
        </tr>
		<?php
	}

	/**
	 * Add access form to create term screen admin page
	 *
	 * @param $taxonomy
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function add_form_fields( $taxonomy ) {

		?>
        <div class="form-field">
            <label for=""><?php _e( 'Access', 'leira-access' ) ?> </label>
			<?php leira_access()->admin->form( array(
				'show_label' => false
			) ) ?>
            <p class="">
                <!--An optional description-->
            </p>
        </div>
		<?php
	}

	/**
	 * Handle the term edit request, either ajax or regular POST request. The system will handle the information
	 * provided in the "leira-access-*" fields and save it as metadata
	 *
	 * @param $term_id
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function edit( $term_id ) {
		leira_access()->admin->save( $term_id, 'term', false );
	}

	/**
	 * Handle the term add POST request. The system will handle the information provided in the "leira-access-*"
	 * fields and save it as metadata
	 *
	 * @param $term_id
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function save( $term_id ) {
		leira_access()->admin->save( $term_id, 'term', false );
	}

}
