<?php
/**
 * Block & Pattern Categories
 *
 * Registers custom block categories and block pattern categories
 * used throughout this theme.
 *
 * @package MTY
 */

if ( ! function_exists( 'mty_block_categories' ) ) :
	/**
	 * Adds the "Brilliant Blocks" category to the block inserter.
	 *
	 * @param  array                   $block_categories     Existing block categories.
	 * @param  WP_Block_Editor_Context $block_editor_context Current editor context.
	 * @return array
	 */
	function mty_block_categories( $block_categories, $block_editor_context ) {
		return array_merge(
			array(
				array(
					'slug'  => 'mty',
					'title' => __( 'MTY', 'mty' ),
				),
			),
			$block_categories

		);
	}
endif;
add_filter( 'block_categories_all', 'mty_block_categories', 10, 2 );


if ( ! function_exists( 'mty_pattern_categories' ) ) :
	/**
	 * Registers the "MTY" block pattern category.
	 */
	function mty_pattern_categories() {
		register_block_pattern_category(
			'mty',
			array(
				'label'       => __( 'MTY', 'mty' ),
				'description' => __( 'A collection of MTY patterns.', 'mty' ),
			)
		);
	}
endif;
add_action( 'init', 'mty_pattern_categories' );


// ── ID column on the Categories admin screen ──────────────────────────────────

if ( ! function_exists( 'mty_category_columns' ) ) :
	/**
	 * Adds an "ID" column to the Categories list table.
	 *
	 * @param  array $columns Existing column definitions.
	 * @return array
	 */
	function mty_category_columns( $columns ) {
		$columns['mty_id'] = __( 'ID', 'mty' );
		return $columns;
	}
endif;
add_filter( 'manage_edit-category_columns', 'mty_category_columns' );

/**
 * Caps the ID column width on the Categories list table.
 */
function mty_category_column_width() {
	?>
	<style>
		.wp-list-table .column-mty_id {
			width: 100px;
			max-width: 100px;
		}
	</style>
	<?php
}
add_action( 'admin_head-edit-tags.php', 'mty_category_column_width' );


if ( ! function_exists( 'mty_category_column_content' ) ) :
	/**
	 * Outputs the term ID for the custom column.
	 *
	 * @param  string $content      Column content.
	 * @param  string $column_name  Column key.
	 * @param  int    $term_id      Term ID.
	 * @return string
	 */
	function mty_category_column_content( $content, $column_name, $term_id ) {
		if ( 'mty_id' === $column_name ) {
			return (string) $term_id;
		}
		return $content;
	}
endif;
add_filter( 'manage_category_custom_column', 'mty_category_column_content', 10, 3 );
