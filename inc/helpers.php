<?php
/**
 * Shared rendering helpers used by templates and blocks.
 *
 * @package Dairyfarm
 */

defined( 'ABSPATH' ) || exit;

/**
 * Allowed inline markup for short, editor-provided copy (headings, intros).
 *
 * @param string $text Raw text.
 * @return string
 */
function dairyfarm_kses_inline( $text ) {
	return wp_kses(
		(string) $text,
		array(
			'strong' => array(),
			'em'     => array(),
			'br'     => array(),
			'span'   => array( 'class' => true ),
			'a'      => array(
				'href'   => true,
				'target' => true,
				'rel'    => true,
			),
		)
	);
}

/**
 * Renders an image stored as a block attribute ({ id, url, alt }).
 * Falls back to a branded placeholder when no image has been chosen yet.
 *
 * @param array|null $image Image attribute.
 * @param string     $size  Registered image size.
 * @param array      $attr  Extra <img> attributes.
 * @return string
 */
function dairyfarm_image( $image, $size = 'large', $attr = array() ) {
	$image = is_array( $image ) ? $image : array();
	$id    = isset( $image['id'] ) ? absint( $image['id'] ) : 0;
	$url   = isset( $image['url'] ) ? $image['url'] : '';
	$alt   = isset( $image['alt'] ) ? $image['alt'] : '';

	$attr = wp_parse_args(
		$attr,
		array(
			'loading'  => 'lazy',
			'decoding' => 'async',
		)
	);

	if ( $id && wp_attachment_is_image( $id ) ) {
		if ( '' !== $alt ) {
			$attr['alt'] = $alt;
		}
		return wp_get_attachment_image( $id, $size, false, $attr );
	}

	if ( $url ) {
		$html = sprintf( '<img src="%s" alt="%s"', esc_url( $url ), esc_attr( $alt ) );
		foreach ( $attr as $name => $value ) {
			$html .= sprintf( ' %s="%s"', esc_attr( $name ), esc_attr( $value ) );
		}
		return $html . ' />';
	}

	$class = isset( $attr['class'] ) ? ' ' . $attr['class'] : '';

	return sprintf(
		'<div class="df-placeholder%1$s" role="img" aria-label="%2$s">%3$s<span>%4$s</span></div>',
		esc_attr( $class ),
		esc_attr__( 'Image placeholder', 'dairyfarm' ),
		dairyfarm_icon( 'image' ),
		esc_html__( 'Choose an image in the block settings', 'dairyfarm' )
	);
}

/**
 * Returns the image attribute unchanged when one has been chosen, otherwise a bundled
 * dummy illustration from assets/images/dummy/ so the page never looks unfinished.
 *
 * @param array|null $image Image attribute ({ id, url, alt }).
 * @param string     $name  Dummy file name without extension, e.g. "pasture".
 * @param string     $alt   Alt text for the dummy image.
 * @return array
 */
function dairyfarm_with_dummy( $image, $name, $alt = '' ) {
	if ( is_array( $image ) && ( ! empty( $image['id'] ) || ! empty( $image['url'] ) ) ) {
		return $image;
	}

	$name = sanitize_file_name( $name );
	if ( ! file_exists( DAIRYFARM_DIR . '/assets/images/dummy/' . $name . '.svg' ) ) {
		$name = 'pasture';
	}

	return array(
		'url' => DAIRYFARM_URI . '/assets/images/dummy/' . $name . '.svg',
		'alt' => $alt,
	);
}

/**
 * Dummy illustration name for a product, based on its first category.
 *
 * @param int $post_id Product ID.
 * @return string
 */
function dairyfarm_product_dummy( $post_id ) {
	$map   = array(
		'milk'   => 'bottles',
		'cheese' => 'cheese',
		'butter' => 'butter',
		'yogurt' => 'yogurt',
	);
	$terms = get_the_terms( $post_id, 'df_product_cat' );
	$slug  = is_array( $terms ) && $terms ? $terms[0]->slug : '';

	return isset( $map[ $slug ] ) ? $map[ $slug ] : 'bottles';
}

/**
 * Renders a link styled as a button. Returns an empty string when label or URL is missing.
 *
 * @param string $label   Button label.
 * @param string $url     Destination.
 * @param string $variant primary|secondary|ghost|light|outline-light.
 * @param string $icon    Optional trailing icon name.
 * @return string
 */
function dairyfarm_button( $label, $url, $variant = 'primary', $icon = '' ) {
	if ( '' === trim( (string) $label ) || '' === trim( (string) $url ) ) {
		return '';
	}

	return sprintf(
		'<a class="df-btn df-btn--%1$s" href="%2$s">%3$s%4$s</a>',
		esc_attr( $variant ),
		esc_url( dairyfarm_link( $url ) ),
		esc_html( $label ),
		$icon ? dairyfarm_icon( $icon ) : ''
	);
}

/**
 * Small pill label shown above section headings.
 *
 * @param string $text Label text.
 * @return string
 */
function dairyfarm_eyebrow( $text ) {
	if ( '' === trim( (string) $text ) ) {
		return '';
	}

	return sprintf(
		'<p class="df-eyebrow">%1$s<span>%2$s</span></p>',
		dairyfarm_icon( 'leaf' ),
		esc_html( $text )
	);
}

/**
 * Standard centred section heading (eyebrow + title + intro).
 *
 * @param array  $attributes Block attributes.
 * @param string $align      center|start.
 * @return string
 */
function dairyfarm_section_heading( $attributes, $align = 'center' ) {
	$eyebrow = isset( $attributes['eyebrow'] ) ? $attributes['eyebrow'] : '';
	$heading = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
	$intro   = isset( $attributes['intro'] ) ? $attributes['intro'] : '';

	if ( ! $eyebrow && ! $heading && ! $intro ) {
		return '';
	}

	$html  = '<header class="df-section-head df-section-head--' . esc_attr( $align ) . '" data-reveal>';
	$html .= dairyfarm_eyebrow( $eyebrow );
	$html .= $heading ? '<h2 class="df-section-head__title">' . dairyfarm_kses_inline( $heading ) . '</h2>' : '';
	$html .= $intro ? '<p class="df-section-head__intro">' . dairyfarm_kses_inline( $intro ) . '</p>' : '';
	$html .= '</header>';

	return $html;
}

/**
 * Wrapper attributes shared by every section block: anchor id, tone class and block supports.
 *
 * @param array  $attributes Block attributes.
 * @param string $slug       Section slug, e.g. "hero".
 * @param string $modifier   Optional modifier, e.g. "cover" → df-hero--cover.
 * @return string
 */
function dairyfarm_section_attributes( $attributes, $slug, $modifier = '' ) {
	$tones = array( 'white', 'cream', 'mint', 'dark', 'brand' );
	$tone  = isset( $attributes['tone'] ) && in_array( $attributes['tone'], $tones, true ) ? $attributes['tone'] : 'white';
	$slug  = sanitize_html_class( $slug );

	$class = sprintf( 'df-section df-%1$s df-tone--%2$s', $slug, $tone );
	if ( $modifier ) {
		$class .= sprintf( ' df-%1$s--%2$s', $slug, sanitize_html_class( $modifier ) );
	}

	$extra = array( 'class' => $class );

	if ( ! empty( $attributes['sectionId'] ) ) {
		$extra['id'] = sanitize_title( $attributes['sectionId'] );
	}

	return get_block_wrapper_attributes( $extra );
}

/**
 * Star rating markup.
 *
 * @param int $rating 1–5.
 * @return string
 */
function dairyfarm_stars( $rating ) {
	$rating = max( 0, min( 5, (int) $rating ) );
	$html   = '<div class="df-stars" role="img" aria-label="' . esc_attr(
		/* translators: %d: star rating out of five. */
		sprintf( __( 'Rated %d out of 5', 'dairyfarm' ), $rating )
	) . '">';

	for ( $i = 1; $i <= 5; $i++ ) {
		$html .= '<span class="df-stars__star' . ( $i <= $rating ? ' is-filled' : '' ) . '">' . dairyfarm_icon( 'star' ) . '</span>';
	}

	return $html . '</div>';
}
