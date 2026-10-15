<?php
/**
 * Testimonials
 *
 * Registers the Testimonials post type. Testimonials are shown through
 * blocks (e.g. a Query Loop) only; they have no single view or archive.
 *
 * Each testimonial has:
 * - Title          → the person's name
 * - Designation    → `mty_designation` post meta
 * - Featured image → the person's avatar
 * - Content        → the testimonial text
 *
 * @package MTY
 */

if ( ! function_exists( 'mty_register_testimonial_post_type' ) ) :
	/**
	 * Registers the `mty_testimonial` post type and its designation meta.
	 *
	 * The type stays publicly queryable so the Query Loop block can list it,
	 * but it has no rewrite rules or archive, and single views return a 404
	 * (see mty_testimonial_disable_single()).
	 */
	function mty_register_testimonial_post_type() {
		register_post_type(
			'mty_testimonial',
			array(
				'labels'              => array(
					'name'                  => __( 'Testimonials', 'mty' ),
					'singular_name'         => __( 'Testimonial', 'mty' ),
					'menu_name'             => __( 'Testimonials', 'mty' ),
					'add_new'               => __( 'Add Testimonial', 'mty' ),
					'add_new_item'          => __( 'Add New Testimonial', 'mty' ),
					'edit_item'             => __( 'Edit Testimonial', 'mty' ),
					'new_item'              => __( 'New Testimonial', 'mty' ),
					'search_items'          => __( 'Search Testimonials', 'mty' ),
					'not_found'             => __( 'No testimonials found.', 'mty' ),
					'not_found_in_trash'    => __( 'No testimonials found in Trash.', 'mty' ),
					'all_items'             => __( 'All Testimonials', 'mty' ),
					'featured_image'        => __( 'Avatar', 'mty' ),
					'set_featured_image'    => __( 'Set avatar', 'mty' ),
					'remove_featured_image' => __( 'Remove avatar', 'mty' ),
					'use_featured_image'    => __( 'Use as avatar', 'mty' ),
				),
				'public'              => false,
				'publicly_queryable'  => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_nav_menus'   => false,
				'show_in_rest'        => true,
				'exclude_from_search' => true,
				'has_archive'         => false,
				'rewrite'             => false,
				'query_var'           => false,
				'menu_position'       => 21,
				'menu_icon'           => 'dashicons-format-quote',
				'supports'            => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
				'template'            => array(
					array(
						'core/paragraph',
						array( 'placeholder' => __( 'Write the testimonial…', 'mty' ) ),
					),
				),
			)
		);

		register_post_meta(
			'mty_testimonial',
			'mty_designation',
			array(
				'type'              => 'string',
				'label'             => __( 'Designation', 'mty' ),
				'description'       => __( 'Job title or company of the person giving the testimonial.', 'mty' ),
				'single'            => true,
				'default'           => '',
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}
endif;
add_action( 'init', 'mty_register_testimonial_post_type' );


if ( ! function_exists( 'mty_testimonial_disable_single' ) ) :
	/**
	 * Returns a 404 for any single testimonial request (e.g. ?p=123 or
	 * ?post_type=mty_testimonial&p=123).
	 */
	function mty_testimonial_disable_single() {
		if ( is_singular( 'mty_testimonial' ) ) {
			global $wp_query;
			$wp_query->set_404();
			status_header( 404 );
			nocache_headers();
		}
	}
endif;
add_action( 'template_redirect', 'mty_testimonial_disable_single' );


if ( ! function_exists( 'mty_testimonial_remove_view_links' ) ) :
	/**
	 * Removes the "View" / "Preview" row action, since there is no single view.
	 *
	 * @param array   $actions Row actions.
	 * @param WP_Post $post    Current post.
	 * @return array
	 */
	function mty_testimonial_remove_view_links( $actions, $post ) {
		if ( 'mty_testimonial' === $post->post_type ) {
			unset( $actions['view'] );
		}
		return $actions;
	}
endif;
add_filter( 'post_row_actions', 'mty_testimonial_remove_view_links', 10, 2 );


if ( ! function_exists( 'mty_testimonial_sitemap' ) ) :
	/**
	 * Keeps testimonials out of the core XML sitemap.
	 *
	 * @param array $post_types Post types included in the sitemap.
	 * @return array
	 */
	function mty_testimonial_sitemap( $post_types ) {
		unset( $post_types['mty_testimonial'] );
		return $post_types;
	}
endif;
add_filter( 'wp_sitemaps_post_types', 'mty_testimonial_sitemap' );


if ( ! function_exists( 'mty_testimonial_title_placeholder' ) ) :
	/**
	 * Uses "Name" as the title placeholder.
	 *
	 * @param string  $text Placeholder text.
	 * @param WP_Post $post Current post.
	 * @return string
	 */
	function mty_testimonial_title_placeholder( $text, $post ) {
		if ( 'mty_testimonial' === $post->post_type ) {
			return __( 'Name', 'mty' );
		}
		return $text;
	}
endif;
add_filter( 'enter_title_here', 'mty_testimonial_title_placeholder', 10, 2 );


// =============================================================================
// Designation meta box
// =============================================================================

if ( ! function_exists( 'mty_testimonial_add_meta_box' ) ) :
	/**
	 * Adds the Designation field to the editor sidebar.
	 */
	function mty_testimonial_add_meta_box() {
		add_meta_box(
			'mty-testimonial-designation',
			__( 'Designation', 'mty' ),
			'mty_testimonial_render_meta_box',
			'mty_testimonial',
			'side',
			'high'
		);
	}
endif;
add_action( 'add_meta_boxes', 'mty_testimonial_add_meta_box' );


if ( ! function_exists( 'mty_testimonial_render_meta_box' ) ) :
	/**
	 * Renders the Designation field.
	 *
	 * @param WP_Post $post Current post.
	 */
	function mty_testimonial_render_meta_box( $post ) {
		wp_nonce_field( 'mty_testimonial_designation', 'mty_testimonial_designation_nonce' );
		?>
		<p>
			<label class="screen-reader-text" for="mty-designation"><?php esc_html_e( 'Designation', 'mty' ); ?></label>
			<input
				type="text"
				id="mty-designation"
				name="mty_designation"
				class="widefat"
				value="<?php echo esc_attr( get_post_meta( $post->ID, 'mty_designation', true ) ); ?>"
				placeholder="<?php esc_attr_e( 'e.g. CEO, Acme Inc.', 'mty' ); ?>"
			/>
		</p>
		<?php
	}
endif;


if ( ! function_exists( 'mty_testimonial_save_meta_box' ) ) :
	/**
	 * Saves the Designation field.
	 *
	 * @param int $post_id Post ID.
	 */
	function mty_testimonial_save_meta_box( $post_id ) {
		if ( ! isset( $_POST['mty_testimonial_designation_nonce'] )
			|| ! wp_verify_nonce( sanitize_key( $_POST['mty_testimonial_designation_nonce'] ), 'mty_testimonial_designation' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) || ! isset( $_POST['mty_designation'] ) ) {
			return;
		}

		update_post_meta( $post_id, 'mty_designation', sanitize_text_field( wp_unslash( $_POST['mty_designation'] ) ) );
	}
endif;
add_action( 'save_post_mty_testimonial', 'mty_testimonial_save_meta_box' );


// =============================================================================
// Admin list columns
// =============================================================================

if ( ! function_exists( 'mty_testimonial_columns' ) ) :
	/**
	 * Adds Avatar and Designation columns to the Testimonials list.
	 *
	 * @param array $columns Existing columns.
	 * @return array
	 */
	function mty_testimonial_columns( $columns ) {
		$new = array();
		foreach ( $columns as $key => $label ) {
			if ( 'title' === $key ) {
				$new['mty_avatar'] = __( 'Avatar', 'mty' );
			}
			$new[ $key ] = $label;
			if ( 'title' === $key ) {
				$new['mty_designation'] = __( 'Designation', 'mty' );
			}
		}
		return $new;
	}
endif;
add_filter( 'manage_mty_testimonial_posts_columns', 'mty_testimonial_columns' );


if ( ! function_exists( 'mty_testimonial_render_column' ) ) :
	/**
	 * Renders the Avatar and Designation columns.
	 *
	 * @param string $column  Column key.
	 * @param int    $post_id Post ID.
	 */
	function mty_testimonial_render_column( $column, $post_id ) {
		if ( 'mty_avatar' === $column ) {
			echo get_the_post_thumbnail( $post_id, array( 40, 40 ), array( 'style' => 'width:40px;height:40px;border-radius:50%;object-fit:cover;' ) );
		} elseif ( 'mty_designation' === $column ) {
			echo esc_html( get_post_meta( $post_id, 'mty_designation', true ) );
		}
	}
endif;
add_action( 'manage_mty_testimonial_posts_custom_column', 'mty_testimonial_render_column', 10, 2 );
