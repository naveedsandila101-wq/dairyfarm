<?php
/**
 * Customizer: global header, footer, contact and social settings.
 *
 * @package Dairyfarm
 */

defined( 'ABSPATH' ) || exit;

/**
 * Single source of truth for global settings and their defaults.
 *
 * @return array<string,array{section:string,label:string,type:string,default:mixed}>
 */
function dairyfarm_settings_schema() {
	return array(
		// Header.
		'topbar_enabled'   => array( 'section' => 'dairyfarm_header', 'label' => __( 'Show top announcement bar', 'dairyfarm' ), 'type' => 'checkbox', 'default' => true ),
		'topbar_text'      => array( 'section' => 'dairyfarm_header', 'label' => __( 'Announcement text', 'dairyfarm' ), 'type' => 'text', 'default' => __( 'Free doorstep delivery on orders over $30 — milked this morning, on your table by breakfast.', 'dairyfarm' ) ),
		'header_cta_label' => array( 'section' => 'dairyfarm_header', 'label' => __( 'Header button label', 'dairyfarm' ), 'type' => 'text', 'default' => __( 'Order Fresh Milk', 'dairyfarm' ) ),
		'header_cta_url'   => array( 'section' => 'dairyfarm_header', 'label' => __( 'Header button URL', 'dairyfarm' ), 'type' => 'url', 'default' => '#products' ),

		// Business details (also used for structured data).
		'business_name'    => array( 'section' => 'dairyfarm_business', 'label' => __( 'Business name', 'dairyfarm' ), 'type' => 'text', 'default' => '' ),
		'phone'            => array( 'section' => 'dairyfarm_business', 'label' => __( 'Phone', 'dairyfarm' ), 'type' => 'text', 'default' => '+1 (555) 214-7788' ),
		'email'            => array( 'section' => 'dairyfarm_business', 'label' => __( 'Email', 'dairyfarm' ), 'type' => 'email', 'default' => 'hello@example.com' ),
		'address'          => array( 'section' => 'dairyfarm_business', 'label' => __( 'Address', 'dairyfarm' ), 'type' => 'textarea', 'default' => "1280 Meadow Lane\nGreen Valley, WI 53001" ),
		'hours'            => array( 'section' => 'dairyfarm_business', 'label' => __( 'Opening hours', 'dairyfarm' ), 'type' => 'text', 'default' => __( 'Farm shop: Mon–Sat, 7am – 6pm', 'dairyfarm' ) ),

		// Social.
		'social_facebook'  => array( 'section' => 'dairyfarm_social', 'label' => 'Facebook', 'type' => 'url', 'default' => '#' ),
		'social_instagram' => array( 'section' => 'dairyfarm_social', 'label' => 'Instagram', 'type' => 'url', 'default' => '#' ),
		'social_youtube'   => array( 'section' => 'dairyfarm_social', 'label' => 'YouTube', 'type' => 'url', 'default' => '#' ),
		'social_x'         => array( 'section' => 'dairyfarm_social', 'label' => 'X (Twitter)', 'type' => 'url', 'default' => '' ),
		'social_linkedin'  => array( 'section' => 'dairyfarm_social', 'label' => 'LinkedIn', 'type' => 'url', 'default' => '' ),

		// Footer.
		'footer_about'     => array( 'section' => 'dairyfarm_footer', 'label' => __( 'About text', 'dairyfarm' ), 'type' => 'textarea', 'default' => __( 'A family-run, pasture-based dairy producing fresh milk, cheese and butter from happy, grass-fed cows since 1987.', 'dairyfarm' ) ),
		'footer_col1'      => array( 'section' => 'dairyfarm_footer', 'label' => __( 'Column 1 heading (Quick Links menu)', 'dairyfarm' ), 'type' => 'text', 'default' => __( 'Explore', 'dairyfarm' ) ),
		'footer_col2'      => array( 'section' => 'dairyfarm_footer', 'label' => __( 'Column 2 heading (Products menu)', 'dairyfarm' ), 'type' => 'text', 'default' => __( 'Our Products', 'dairyfarm' ) ),
		'footer_col3'      => array( 'section' => 'dairyfarm_footer', 'label' => __( 'Column 3 heading (contact)', 'dairyfarm' ), 'type' => 'text', 'default' => __( 'Visit the Farm', 'dairyfarm' ) ),
		'copyright'        => array( 'section' => 'dairyfarm_footer', 'label' => __( 'Copyright text ({year} and {site} are replaced)', 'dairyfarm' ), 'type' => 'text', 'default' => '© {year} {site}. All rights reserved.' ),
	);
}

/**
 * Reads a global setting with its schema default.
 *
 * @param string $key Setting key without prefix.
 * @return mixed
 */
function dairyfarm_mod( $key ) {
	$schema  = dairyfarm_settings_schema();
	$default = isset( $schema[ $key ] ) ? $schema[ $key ]['default'] : '';
	$value   = get_theme_mod( 'dairyfarm_' . $key, $default );

	if ( 'business_name' === $key && '' === $value ) {
		$value = get_bloginfo( 'name' );
	}

	return $value;
}

/**
 * Registers Customizer panel, sections and controls.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function dairyfarm_customize_register( $wp_customize ) {
	$wp_customize->add_panel(
		'dairyfarm_options',
		array(
			'title'    => __( 'Dairyfarm Theme Options', 'dairyfarm' ),
			'priority' => 30,
		)
	);

	$sections = array(
		'dairyfarm_header'   => __( 'Header', 'dairyfarm' ),
		'dairyfarm_business' => __( 'Business & Contact Details', 'dairyfarm' ),
		'dairyfarm_social'   => __( 'Social Profiles', 'dairyfarm' ),
		'dairyfarm_footer'   => __( 'Footer', 'dairyfarm' ),
	);

	foreach ( $sections as $id => $title ) {
		$wp_customize->add_section( $id, array( 'title' => $title, 'panel' => 'dairyfarm_options' ) );
	}

	$sanitizers = array(
		'checkbox' => 'dairyfarm_sanitize_checkbox',
		'url'      => 'dairyfarm_sanitize_link',
		'email'    => 'sanitize_email',
		'textarea' => 'sanitize_textarea_field',
		'text'     => 'sanitize_text_field',
	);

	foreach ( dairyfarm_settings_schema() as $key => $field ) {
		$setting_id = 'dairyfarm_' . $key;

		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $field['default'],
				'sanitize_callback' => $sanitizers[ $field['type'] ],
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			$setting_id,
			array(
				'label'   => $field['label'],
				'section' => $field['section'],
				'type'    => 'url' === $field['type'] ? 'text' : $field['type'],
			)
		);
	}

	// Live-edit shortcuts in the preview.
	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial(
			'dairyfarm_topbar_text',
			array(
				'selector'        => '.df-topbar__text',
				'render_callback' => static function () {
					return esc_html( dairyfarm_mod( 'topbar_text' ) );
				},
			)
		);
	}
}
add_action( 'customize_register', 'dairyfarm_customize_register' );

/**
 * @param mixed $value Value.
 * @return bool
 */
function dairyfarm_sanitize_checkbox( $value ) {
	return (bool) $value;
}

/**
 * Accepts absolute URLs, root-relative paths and in-page anchors (#products).
 *
 * @param string $value Value.
 * @return string
 */
function dairyfarm_sanitize_link( $value ) {
	$value = trim( (string) $value );
	if ( '' === $value ) {
		return '';
	}
	if ( 0 === strpos( $value, '#' ) ) {
		return '#' . sanitize_title( substr( $value, 1 ) );
	}
	return esc_url_raw( $value );
}
