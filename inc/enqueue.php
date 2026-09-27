<?php
/**
 * Front-end and editor assets.
 *
 * @package Dairyfarm
 */

defined( 'ABSPATH' ) || exit;

/**
 * Web font URL. Return an empty string via the `dairyfarm_font_url` filter
 * to disable Google Fonts (e.g. when self-hosting for GDPR compliance).
 *
 * @return string
 */
function dairyfarm_font_url() {
	return apply_filters(
		'dairyfarm_font_url',
		'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap'
	);
}

/**
 * Asset version: file modification time in development, theme version otherwise.
 *
 * @param string $relative Path relative to the theme root.
 * @return string
 */
function dairyfarm_asset_version( $relative ) {
	$path = DAIRYFARM_DIR . '/' . $relative;
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG && file_exists( $path ) ) {
		return (string) filemtime( $path );
	}
	return DAIRYFARM_VERSION;
}

/**
 * Enqueues front-end assets.
 */
function dairyfarm_enqueue_assets() {
	$font = dairyfarm_font_url();
	if ( $font ) {
		wp_enqueue_style( 'dairyfarm-font', $font, array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	}

	wp_enqueue_style( 'dairyfarm-main', DAIRYFARM_URI . '/assets/css/main.css', array(), dairyfarm_asset_version( 'assets/css/main.css' ) );

	wp_enqueue_script(
		'dairyfarm-main',
		DAIRYFARM_URI . '/assets/js/main.js',
		array(),
		dairyfarm_asset_version( 'assets/js/main.js' ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'dairyfarm_enqueue_assets' );

/**
 * Styles for the block settings sidebar (outside the editor canvas).
 */
function dairyfarm_enqueue_editor_ui() {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || ! $screen->is_block_editor() ) {
		return;
	}
	wp_enqueue_style( 'dairyfarm-editor-ui', DAIRYFARM_URI . '/assets/css/editor-ui.css', array( 'wp-components' ), dairyfarm_asset_version( 'assets/css/editor-ui.css' ) );
}
add_action( 'admin_enqueue_scripts', 'dairyfarm_enqueue_editor_ui' );

/**
 * Preconnect to the font host.
 *
 * @param array  $urls          URLs.
 * @param string $relation_type Relation.
 * @return array
 */
function dairyfarm_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type && dairyfarm_font_url() ) {
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'dairyfarm_resource_hints', 10, 2 );

/**
 * Flags JS availability before paint so reveal animations never hide content for no-JS users.
 */
function dairyfarm_js_class() {
	echo "<script>document.documentElement.classList.add('js');</script>\n";
}
add_action( 'wp_head', 'dairyfarm_js_class', 1 );
