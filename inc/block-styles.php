<?php
/**
 * Core Block Styles
 *
 * Registers custom style variations for core (and third-party) blocks.
 *
 * @package MTY
 */

if ( ! function_exists( 'mty_block_styles' ) ) :
	/**
	 * Registers all custom block style variations for the theme.
	 */
	function mty_block_styles() {
		register_block_style(
			'core/group',
			array(
				'name'  => 'wrap-mobile',
				'label' => __( 'Wrap Mobile', 'mty' ),
			)
		);

		register_block_style(
			'core/group',
			array(
				'name'  => 'plain-links',
				'label' => __( 'Plain Links', 'mty' ),
			)
		);


		register_block_style(
			'core/list',
			array(
				'name'  => 'mega-links',
				'label' => __( 'Mega Links', 'mty' ),
			)
		);

		register_block_style(
			'core/list',
			array(
				'name'  => 'mega-links-large',
				'label' => __( 'Mega Links: Large', 'mty' ),
			)
		);

		register_block_style(
			'core/button',
			array(
				'name'  => 'alternative',
				'label' => __( 'Alternative', 'mty' ),
			)
		);

		register_block_style(
			'core/button',
			array(
				'name'  => 'link',
				'label' => __( 'Link', 'mty' ),
			)
		);

		register_block_style(
			'core/accordion',
			array(
				'name'  => 'numbered',
				'label' => __( 'Numbered', 'mty' ),
			)
		);
	}
endif;
add_action( 'init', 'mty_block_styles' );
