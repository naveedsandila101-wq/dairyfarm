<?php
/**
 * Theme supports, menus and image sizes.
 *
 * @package Dairyfarm
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registers theme features.
 */
function dairyfarm_setup() {
	load_theme_textdomain( 'dairyfarm', DAIRYFARM_DIR . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 96,
			'width'       => 320,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	// Editor gets the same stylesheet as the front end so server-rendered previews match.
	add_editor_style( array( dairyfarm_font_url(), 'assets/css/main.css', 'assets/css/editor.css' ) );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'dairyfarm' ),
			'footer'  => __( 'Footer – Quick Links', 'dairyfarm' ),
			'footer2' => __( 'Footer – Products', 'dairyfarm' ),
			'legal'   => __( 'Footer – Legal', 'dairyfarm' ),
		)
	);

	add_image_size( 'dairyfarm-card', 640, 480, true );
	add_image_size( 'dairyfarm-hero', 1600, 1200, false );
	add_image_size( 'dairyfarm-avatar', 120, 120, true );
}
add_action( 'after_setup_theme', 'dairyfarm_setup' );

/**
 * Content width for embeds.
 */
function dairyfarm_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'dairyfarm_content_width', 760 );
}
add_action( 'after_setup_theme', 'dairyfarm_content_width', 0 );

/**
 * Adds useful body classes.
 *
 * @param string[] $classes Classes.
 * @return string[]
 */
function dairyfarm_body_classes( $classes ) {
	if ( get_theme_mod( 'dairyfarm_topbar_enabled', true ) ) {
		$classes[] = 'has-topbar';
	}
	if ( is_singular() && has_post_thumbnail() ) {
		$classes[] = 'has-featured-image';
	}
	return $classes;
}
add_filter( 'body_class', 'dairyfarm_body_classes' );

/**
 * Shorter, cleaner excerpts.
 */
add_filter(
	'excerpt_length',
	static function () {
		return 24;
	}
);
add_filter(
	'excerpt_more',
	static function () {
		return '&hellip;';
	}
);
