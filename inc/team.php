<?php
/**
 * Team
 *
 * Registers the Team post type for the attorney bio pages. Each member's page
 * is built freely in the block editor (all sections); the details that repeat
 * across the site (the Our Team cards, the About Us mega menu, the bio hero
 * and fact list) come from the fields below, so they are typed once.
 *
 * Each team member has:
 * - Title          → full name ("James T. Laura, Jr.")
 * - Excerpt        → short bio, shown on the Our Team cards
 * - Featured image → headshot
 * - Order          → position in team lists (Page Attributes → Order)
 * - Content        → the bio page sections
 * - Team details   → the `mty_team_*` meta below (editor sidebar panel)
 *
 * Blocks show the details through the "Team member" block bindings source
 * (`mty/team`), which also works inside a Query Loop of team members.
 *
 * URLs are /team/<slug>/, under the Our Team page (/team/). It has no archive:
 * the Our Team page lists the members.
 *
 * @package MTY
 */

if ( ! function_exists( 'mty_team_meta_fields' ) ) :
	/**
	 * The team member meta fields: key => label, description.
	 *
	 * @return array
	 */
	function mty_team_meta_fields() {
		return array(
			'mty_team_short_name'       => array(
				'label'       => __( 'Short name', 'mty' ),
				'description' => __( 'First name used in headings and buttons, e.g. "James" ("Meet James.", "Read James\'s bio").', 'mty' ),
			),
			'mty_team_name_highlight'   => array(
				'label'       => __( 'Highlighted name', 'mty' ),
				'description' => __( 'The end of the name shown in green, e.g. "Laura, Jr." Leave empty to use the last word.', 'mty' ),
			),
			'mty_team_position'         => array(
				'label'       => __( 'Position', 'mty' ),
				'description' => __( 'e.g. "Founder" or "Attorney".', 'mty' ),
			),
			'mty_team_office'           => array(
				'label'       => __( 'Office', 'mty' ),
				'description' => __( 'e.g. "Fairhope, Alabama".', 'mty' ),
			),
			'mty_team_role'             => array(
				'label'       => __( 'Role', 'mty' ),
				'description' => __( 'Fact list, e.g. "Founder and owner".', 'mty' ),
			),
			'mty_team_licensed_in'      => array(
				'label'       => __( 'Licensed in', 'mty' ),
				'description' => __( 'e.g. "Alabama (2012), Florida (2014), Mississippi, Georgia".', 'mty' ),
			),
			'mty_team_cases'            => array(
				'label'       => __( 'Cases', 'mty' ),
				'description' => __( 'e.g. "Car, truck, and motorcycle accidents. Wrongful death".', 'mty' ),
			),
			'mty_team_practicing_since' => array(
				'label'       => __( 'Practicing since', 'mty' ),
				'description' => __( 'e.g. "2012".', 'mty' ),
			),
		);
	}
endif;


if ( ! function_exists( 'mty_register_team_post_type' ) ) :
	/**
	 * Registers the `mty_team` post type and its meta.
	 */
	function mty_register_team_post_type() {
		register_post_type(
			'mty_team',
			array(
				'labels'              => array(
					'name'                  => __( 'Team', 'mty' ),
					'singular_name'         => __( 'Team Member', 'mty' ),
					'menu_name'             => __( 'Team', 'mty' ),
					'add_new'               => __( 'Add Team Member', 'mty' ),
					'add_new_item'          => __( 'Add New Team Member', 'mty' ),
					'edit_item'             => __( 'Edit Team Member', 'mty' ),
					'new_item'              => __( 'New Team Member', 'mty' ),
					'view_item'             => __( 'View Team Member', 'mty' ),
					'search_items'          => __( 'Search Team', 'mty' ),
					'not_found'             => __( 'No team members found.', 'mty' ),
					'not_found_in_trash'    => __( 'No team members found in Trash.', 'mty' ),
					'all_items'             => __( 'All Team Members', 'mty' ),
					'featured_image'        => __( 'Headshot', 'mty' ),
					'set_featured_image'    => __( 'Set headshot', 'mty' ),
					'remove_featured_image' => __( 'Remove headshot', 'mty' ),
					'use_featured_image'    => __( 'Use as headshot', 'mty' ),
					'item_published'        => __( 'Team member published.', 'mty' ),
					'item_updated'          => __( 'Team member updated.', 'mty' ),
				),
				'public'              => true,
				'show_in_rest'        => true,
				'show_in_nav_menus'   => true,
				'has_archive'         => false,
				'hierarchical'        => false,
				'rewrite'             => array(
					'slug'       => 'team',
					'with_front' => false,
				),
				'menu_position'       => 20,
				'menu_icon'           => 'dashicons-groups',
				'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'revisions', 'custom-fields' ),
			)
		);

		foreach ( mty_team_meta_fields() as $key => $field ) {
			register_post_meta(
				'mty_team',
				$key,
				array(
					'type'              => 'string',
					'label'             => $field['label'],
					'description'       => $field['description'],
					'single'            => true,
					'default'           => '',
					'show_in_rest'      => true,
					'sanitize_callback' => 'sanitize_text_field',
					'auth_callback'     => function ( $allowed, $meta_key, $post_id ) {
						return current_user_can( 'edit_post', $post_id );
					},
				)
			);
		}
	}
endif;
add_action( 'init', 'mty_register_team_post_type' );


if ( ! function_exists( 'mty_team_flush_rewrite_rules' ) ) :
	/**
	 * Flushes the rewrite rules once after the Team post type is added or its
	 * URL rules change (the theme is deployed, not activated, so there is no
	 * activation hook). Bump the version when the rewrite settings change.
	 */
	function mty_team_flush_rewrite_rules() {
		$version = '1';
		if ( get_option( 'mty_team_rewrite_version' ) !== $version ) {
			flush_rewrite_rules( false );
			update_option( 'mty_team_rewrite_version', $version );
		}
	}
endif;
add_action( 'init', 'mty_team_flush_rewrite_rules', 99 );


if ( ! function_exists( 'mty_team_page_fallback' ) ) :
	/**
	 * Lets pages under the Our Team page keep working. Team URLs (/team/<slug>/)
	 * are matched before pages, so a page at the same address (e.g. an attorney
	 * page not yet moved to Team) would 404. When no team member has that slug
	 * but a page at that path exists, the request goes to the page instead.
	 *
	 * @param array $query_vars Parsed query vars.
	 * @return array
	 */
	function mty_team_page_fallback( $query_vars ) {
		if ( empty( $query_vars['mty_team'] ) || ( ! empty( $query_vars['post_type'] ) && 'mty_team' !== $query_vars['post_type'] ) ) {
			return $query_vars;
		}

		$slug = $query_vars['mty_team'];
		if ( get_page_by_path( $slug, OBJECT, 'mty_team' ) ) {
			return $query_vars;
		}

		$path = 'team/' . $slug;
		if ( get_page_by_path( $path ) ) {
			return array( 'pagename' => $path );
		}

		return $query_vars;
	}
endif;
add_filter( 'request', 'mty_team_page_fallback' );


if ( ! function_exists( 'mty_team_title_placeholder' ) ) :
	/**
	 * Uses "Full name" as the title placeholder.
	 *
	 * @param string  $text Placeholder text.
	 * @param WP_Post $post Current post.
	 * @return string
	 */
	function mty_team_title_placeholder( $text, $post ) {
		if ( 'mty_team' === $post->post_type ) {
			return __( 'Full name', 'mty' );
		}
		return $text;
	}
endif;
add_filter( 'enter_title_here', 'mty_team_title_placeholder', 10, 2 );


// =============================================================================
// Block bindings: "Team member" source
// =============================================================================

if ( ! function_exists( 'mty_team_name_html' ) ) :
	/**
	 * Returns the member's name with the highlighted part wrapped in the
	 * Highlight format (`<mark class="mty-highlight">`), e.g.
	 * "James T. <mark>Laura, Jr.</mark>".
	 *
	 * @param int $post_id Team member ID.
	 * @return string
	 */
	function mty_team_name_html( $post_id ) {
		$name      = get_the_title( $post_id );
		$highlight = trim( (string) get_post_meta( $post_id, 'mty_team_name_highlight', true ) );

		if ( '' === $highlight ) {
			$parts     = preg_split( '/\s+/', trim( wp_strip_all_tags( $name ) ) );
			$highlight = count( $parts ) > 1 ? end( $parts ) : '';
		}

		$position = '' === $highlight ? false : strrpos( $name, $highlight );
		if ( false === $position ) {
			return esc_html( $name );
		}

		return esc_html( substr( $name, 0, $position ) )
			. '<mark class="mty-highlight">' . esc_html( $highlight ) . '</mark>'
			. esc_html( substr( $name, $position + strlen( $highlight ) ) );
	}
endif;


if ( ! function_exists( 'mty_team_binding_value' ) ) :
	/**
	 * Returns one team member value for the `mty/team` binding source.
	 *
	 * Keys:
	 * - name     → full name with the highlighted part ("James T. Laura, Jr.")
	 * - eyebrow  → "Founder, Fairhope, Alabama" (position, office)
	 * - tagline  → "Founder, More 2 You Law. Fairhope, Alabama."
	 * - bio_link → "Read James's bio"
	 * - any field name without the `mty_team_` prefix (short_name, position,
	 *   office, role, licensed_in, cases, practicing_since)
	 *
	 * @param int    $post_id Team member ID.
	 * @param string $key     Field key.
	 * @return string|null
	 */
	function mty_team_binding_value( $post_id, $key ) {
		$meta = function ( $field ) use ( $post_id ) {
			return trim( (string) get_post_meta( $post_id, 'mty_team_' . $field, true ) );
		};

		$short_name = $meta( 'short_name' );
		if ( '' === $short_name ) {
			$short_name = strtok( wp_strip_all_tags( get_the_title( $post_id ) ), ' ' );
		}

		switch ( $key ) {
			case 'name':
				return mty_team_name_html( $post_id );

			case 'eyebrow':
				return esc_html( implode( ', ', array_filter( array( $meta( 'position' ), $meta( 'office' ) ) ) ) );

			case 'tagline':
				$parts = array_filter(
					array(
						$meta( 'position' ) ? $meta( 'position' ) . ', ' . __( 'More 2 You Law', 'mty' ) . '.' : '',
						$meta( 'office' ) ? $meta( 'office' ) . '.' : '',
					)
				);
				return esc_html( implode( ' ', $parts ) );

			case 'bio_link':
				/* translators: %s: team member's short name, e.g. "James". */
				return esc_html( sprintf( __( 'Read %s\'s bio', 'mty' ), $short_name ) );

			case 'short_name':
				return esc_html( $short_name );
		}

		if ( array_key_exists( 'mty_team_' . $key, mty_team_meta_fields() ) ) {
			return esc_html( $meta( $key ) );
		}

		return null;
	}
endif;


if ( ! function_exists( 'mty_register_team_binding_source' ) ) :
	/**
	 * Registers the "Team member" block bindings source. Bound blocks read the
	 * current team member: the post in a Query Loop, or the page being viewed.
	 */
	function mty_register_team_binding_source() {
		register_block_bindings_source(
			'mty/team',
			array(
				'label'              => __( 'Team member', 'mty' ),
				'uses_context'       => array( 'postId', 'postType' ),
				'get_value_callback' => function ( $source_args, $block_instance ) {
					$post_id = $block_instance->context['postId'] ?? get_the_ID();
					$key     = $source_args['key'] ?? '';

					if ( ! $post_id || '' === $key || 'mty_team' !== get_post_type( $post_id ) ) {
						return null;
					}

					$post = get_post( $post_id );
					if ( ( ! is_post_publicly_viewable( $post ) && ! current_user_can( 'read_post', $post_id ) ) || post_password_required( $post ) ) {
						return null;
					}

					return mty_team_binding_value( $post_id, $key );
				},
			)
		);
	}
endif;
add_action( 'init', 'mty_register_team_binding_source' );


// =============================================================================
// Editor: Team details panel + binding source (build/extensions/team)
// =============================================================================

if ( ! function_exists( 'mty_enqueue_team_editor_assets' ) ) :
	/**
	 * Enqueues the Team editor script: the "Team details" sidebar panel on team
	 * members, and the editor side of the `mty/team` binding source (used in
	 * any editor, e.g. a Query Loop of team members on a page).
	 */
	function mty_enqueue_team_editor_assets() {
		$asset_file = get_theme_file_path( 'build/extensions/team/index.asset.php' );

		if ( ! file_exists( $asset_file ) ) {
			return;
		}

		$assets = require $asset_file;

		wp_enqueue_script(
			'mty-team-extension',
			get_theme_file_uri( 'build/extensions/team/index.js' ),
			$assets['dependencies'],
			wp_get_theme()->get( 'Version' ),
			true
		);

		$fields = array();
		foreach ( mty_team_meta_fields() as $key => $field ) {
			$fields[] = array(
				'key'         => $key,
				'label'       => $field['label'],
				'description' => $field['description'],
			);
		}

		wp_localize_script(
			'mty-team-extension',
			'mtyTeam',
			array(
				'fields'   => $fields,
				'firmName' => __( 'More 2 You Law', 'mty' ),
			)
		);
	}
endif;
add_action( 'enqueue_block_editor_assets', 'mty_enqueue_team_editor_assets' );


// =============================================================================
// Admin list columns
// =============================================================================

if ( ! function_exists( 'mty_team_columns' ) ) :
	/**
	 * Adds Headshot, Position/Office and Order columns to the Team list.
	 *
	 * @param array $columns Existing columns.
	 * @return array
	 */
	function mty_team_columns( $columns ) {
		$new = array();
		foreach ( $columns as $key => $label ) {
			if ( 'title' === $key ) {
				$new['mty_headshot'] = __( 'Headshot', 'mty' );
			}
			$new[ $key ] = $label;
			if ( 'title' === $key ) {
				$new['mty_eyebrow'] = __( 'Position, Office', 'mty' );
				$new['mty_order']   = __( 'Order', 'mty' );
			}
		}
		return $new;
	}
endif;
add_filter( 'manage_mty_team_posts_columns', 'mty_team_columns' );


if ( ! function_exists( 'mty_team_render_column' ) ) :
	/**
	 * Renders the Team list columns.
	 *
	 * @param string $column  Column key.
	 * @param int    $post_id Post ID.
	 */
	function mty_team_render_column( $column, $post_id ) {
		if ( 'mty_headshot' === $column ) {
			echo get_the_post_thumbnail( $post_id, array( 40, 40 ), array( 'style' => 'width:40px;height:40px;object-fit:cover;' ) );
		} elseif ( 'mty_eyebrow' === $column ) {
			echo mty_team_binding_value( $post_id, 'eyebrow' ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in mty_team_binding_value().
		} elseif ( 'mty_order' === $column ) {
			echo (int) get_post_field( 'menu_order', $post_id );
		}
	}
endif;
add_action( 'manage_mty_team_posts_custom_column', 'mty_team_render_column', 10, 2 );


if ( ! function_exists( 'mty_team_admin_order' ) ) :
	/**
	 * Sorts the Team list by Order, then name, unless another sort is chosen.
	 *
	 * @param WP_Query $query Admin list query.
	 */
	function mty_team_admin_order( $query ) {
		if ( is_admin() && $query->is_main_query() && 'mty_team' === $query->get( 'post_type' ) && ! $query->get( 'orderby' ) ) {
			$query->set( 'orderby', array( 'menu_order' => 'ASC', 'title' => 'ASC' ) );
		}
	}
endif;
add_action( 'pre_get_posts', 'mty_team_admin_order' );
