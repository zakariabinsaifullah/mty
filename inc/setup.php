<?php
/**
 * Theme Setup
 *
 * Handles core theme setup tasks that run on the `after_setup_theme` hook:
 * post format support and editor stylesheet registration.
 *
 * @package MTY
 */

if ( ! function_exists( 'mty_post_format_setup' ) ) :
	/**
	 * Declares support for the post formats used by this theme.
	 */
	function mty_post_format_setup() {
		add_theme_support(
			'post-formats',
			array( 'aside', 'audio', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video' )
		);
	}
endif;
add_action( 'after_setup_theme', 'mty_post_format_setup' );


if ( ! function_exists( 'mty_editor_style' ) ) :
	/**
	 * Loads the editor stylesheet so the editing canvas matches the front end.
	 */
	function mty_editor_style() {
		add_editor_style( 'assets/css/editor-style.css' );
	}
endif;
add_action( 'after_setup_theme', 'mty_editor_style' );


if ( ! function_exists( 'mty_page_excerpts' ) ) :
	/**
	 * Enables excerpts on pages. The attorney bio pages under Our Team use the
	 * excerpt for their role ("Attorney, Mobile, Alabama"), which the About Us
	 * mega menu's team list shows under each name.
	 */
	function mty_page_excerpts() {
		add_post_type_support( 'page', 'excerpt' );
	}
endif;
add_action( 'init', 'mty_page_excerpts' );
