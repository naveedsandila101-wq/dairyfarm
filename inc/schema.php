<?php
/**
 * Structured data (JSON-LD). Disable with: add_filter( 'dairyfarm_schema_enabled', '__return_false' );
 *
 * @package Dairyfarm
 */

defined( 'ABSPATH' ) || exit;

/**
 * Collects schema nodes during render; printed once in the footer.
 *
 * @param array|null $node Node to add, or null to read.
 * @return array
 */
function dairyfarm_schema( $node = null ) {
	static $nodes = array();
	if ( is_array( $node ) ) {
		$nodes[] = $node;
	}
	return $nodes;
}

/**
 * Adds LocalBusiness data on the home page.
 */
function dairyfarm_schema_business() {
	if ( ! is_front_page() ) {
		return;
	}

	$node = array(
		'@type'     => 'LocalBusiness',
		'name'      => dairyfarm_mod( 'business_name' ),
		'url'       => home_url( '/' ),
		'telephone' => dairyfarm_mod( 'phone' ),
		'email'     => dairyfarm_mod( 'email' ),
		'address'   => preg_replace( '/\s*\n\s*/', ', ', trim( (string) dairyfarm_mod( 'address' ) ) ),
	);

	$logo = get_theme_mod( 'custom_logo' );
	if ( $logo ) {
		$node['logo'] = wp_get_attachment_image_url( $logo, 'full' );
	}

	$same_as = array();
	foreach ( array( 'facebook', 'instagram', 'youtube', 'x', 'linkedin' ) as $network ) {
		$url = dairyfarm_mod( 'social_' . $network );
		if ( $url && '#' !== $url ) {
			$same_as[] = $url;
		}
	}
	if ( $same_as ) {
		$node['sameAs'] = $same_as;
	}

	dairyfarm_schema( array_filter( $node ) );
}
add_action( 'wp_head', 'dairyfarm_schema_business', 5 );

/**
 * Prints collected JSON-LD.
 */
function dairyfarm_schema_print() {
	if ( ! apply_filters( 'dairyfarm_schema_enabled', true ) ) {
		return;
	}

	$nodes = dairyfarm_schema();
	if ( ! $nodes ) {
		return;
	}

	$graph = array(
		'@context' => 'https://schema.org',
		'@graph'   => $nodes,
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $graph, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG ) . "</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'wp_footer', 'dairyfarm_schema_print', 50 );
