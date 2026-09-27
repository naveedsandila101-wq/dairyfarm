<?php
/**
 * Registers the theme's section blocks.
 *
 * Every section is a dynamic block described entirely by its block.json:
 *  - `attributes` hold the content (with sensible defaults, so blocks look finished on insert),
 *  - `dairyfarm.fields` describes the settings UI, which assets/js/blocks-editor.js builds generically,
 *  - `render.php` produces the markup on the server (front end and editor preview share it).
 *
 * Adding a new section = add a folder in /blocks with block.json + render.php. No build step.
 *
 * @package Dairyfarm
 */

defined( 'ABSPATH' ) || exit;

/**
 * Absolute paths of all block folders.
 *
 * @return string[]
 */
function dairyfarm_block_dirs() {
	$dirs = glob( DAIRYFARM_DIR . '/blocks/*', GLOB_ONLYDIR );
	return $dirs ? $dirs : array();
}

/**
 * Injects the attributes every section shares (background tone + anchor ID), so each
 * block.json only declares its own content. Defaults come from `dairyfarm.tone` / `dairyfarm.sectionId`.
 *
 * @param array $metadata Parsed block.json.
 * @return array
 */
function dairyfarm_section_metadata( $metadata ) {
	if ( empty( $metadata['name'] ) || 0 !== strpos( $metadata['name'], 'dairyfarm/' ) ) {
		return $metadata;
	}

	$defaults = isset( $metadata['dairyfarm'] ) ? $metadata['dairyfarm'] : array();

	$metadata['attributes']['tone'] = array(
		'type'    => 'string',
		'default' => isset( $defaults['tone'] ) ? $defaults['tone'] : 'white',
	);
	$metadata['attributes']['sectionId'] = array(
		'type'    => 'string',
		'default' => isset( $defaults['sectionId'] ) ? $defaults['sectionId'] : '',
	);

	// Sections are always edge-to-edge, in the editor canvas as well as on the front end.
	$metadata['supports']['align']   = array( 'full' );
	$metadata['attributes']['align'] = array(
		'type'    => 'string',
		'default' => 'full',
	);

	return $metadata;
}
add_filter( 'block_type_metadata', 'dairyfarm_section_metadata' );

/**
 * Registers the shared editor script and every block.
 */
function dairyfarm_register_blocks() {
	wp_register_script(
		'dairyfarm-blocks-editor',
		DAIRYFARM_URI . '/assets/js/blocks-editor.js',
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-server-side-render', 'wp-data', 'wp-core-data', 'wp-i18n' ),
		dairyfarm_asset_version( 'assets/js/blocks-editor.js' ),
		true
	);

	$client = array();

	foreach ( dairyfarm_block_dirs() as $dir ) {
		$json = $dir . '/block.json';
		if ( ! file_exists( $json ) ) {
			continue;
		}

		$type = register_block_type( $dir );
		if ( ! $type ) {
			continue;
		}

		$meta = json_decode( (string) file_get_contents( $json ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$client[] = array(
			'name'        => $type->name,
			'title'       => $type->title,
			'description' => $type->description,
			'category'    => $type->category,
			'icon'        => $type->icon,
			'keywords'    => $type->keywords,
			'attributes'  => $type->attributes,
			'supports'    => $type->supports,
			'fields'      => isset( $meta['dairyfarm']['fields'] ) ? $meta['dairyfarm']['fields'] : array(),
		);
	}

	wp_add_inline_script(
		'dairyfarm-blocks-editor',
		'window.dairyfarmEditor = ' . wp_json_encode(
			array(
				'blocks' => $client,
				'icons'  => dairyfarm_icon_choices(),
			)
		) . ';',
		'before'
	);

	wp_set_script_translations( 'dairyfarm-blocks-editor', 'dairyfarm', DAIRYFARM_DIR . '/languages' );
}
add_action( 'init', 'dairyfarm_register_blocks' );

/**
 * Adds the "Dairy Farm Sections" inserter category at the top.
 *
 * @param array $categories Categories.
 * @return array
 */
function dairyfarm_block_category( $categories ) {
	array_unshift(
		$categories,
		array(
			'slug'  => 'dairyfarm',
			'title' => __( 'Dairy Farm Sections', 'dairyfarm' ),
			'icon'  => null,
		)
	);
	return $categories;
}
add_filter( 'block_categories_all', 'dairyfarm_block_category' );

/**
 * Ordered block markup for the full home page. Used by the pattern and the demo importer.
 *
 * @return string
 */
function dairyfarm_home_page_markup() {
	$sections = dairyfarm_home_sections();

	return implode(
		"\n\n",
		array_map(
			static function ( $slug ) {
				return '<!-- wp:dairyfarm/' . $slug . ' /-->';
			},
			$sections
		)
	);
}

/**
 * Registers a one-click "full home page" pattern.
 */
function dairyfarm_register_patterns() {
	register_block_pattern_category( 'dairyfarm', array( 'label' => __( 'Dairy Farm', 'dairyfarm' ) ) );

	register_block_pattern(
		'dairyfarm/home-page',
		array(
			'title'       => __( 'Dairy Farm – Complete Home Page', 'dairyfarm' ),
			'description' => __( 'All home page sections in the recommended order.', 'dairyfarm' ),
			'categories'  => array( 'dairyfarm' ),
			'blockTypes'  => array( 'core/post-content' ),
			'postTypes'   => array( 'page' ),
			'content'     => dairyfarm_home_page_markup(),
		)
	);
}
add_action( 'init', 'dairyfarm_register_patterns', 20 );
