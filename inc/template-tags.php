<?php
/**
 * Template tags used across header, footer and templates.
 *
 * @package Dairyfarm
 */

defined( 'ABSPATH' ) || exit;

/**
 * Makes in-page anchors (#products) work from any page by pointing them at the home page.
 *
 * @param string $url URL or anchor.
 * @return string
 */
function dairyfarm_link( $url ) {
	$url = (string) $url;
	if ( 0 === strpos( $url, '#' ) && ! is_front_page() ) {
		return home_url( '/' ) . $url;
	}
	return $url;
}

/**
 * Applies the same anchor handling to custom menu links such as "#gallery".
 *
 * @param array $atts Link attributes.
 * @return array
 */
function dairyfarm_menu_anchor_links( $atts ) {
	if ( ! empty( $atts['href'] ) ) {
		$atts['href'] = dairyfarm_link( $atts['href'] );
	}
	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'dairyfarm_menu_anchor_links' );

/**
 * Site logo or text wordmark.
 */
function dairyfarm_logo() {
	if ( has_custom_logo() ) {
		the_custom_logo();
		return;
	}
	printf(
		'<a class="df-logo" href="%1$s" rel="home">%2$s<span>%3$s</span></a>',
		esc_url( home_url( '/' ) ),
		dairyfarm_icon( 'milk', 'df-logo__mark' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		esc_html( get_bloginfo( 'name' ) )
	);
}

/**
 * Social profile icon links.
 */
function dairyfarm_social_links() {
	$networks = array(
		'facebook'  => 'Facebook',
		'instagram' => 'Instagram',
		'youtube'   => 'YouTube',
		'x'         => 'X',
		'linkedin'  => 'LinkedIn',
	);

	$items = '';
	foreach ( $networks as $key => $label ) {
		$url = dairyfarm_mod( 'social_' . $key );
		if ( ! $url ) {
			continue;
		}
		$items .= sprintf(
			'<li><a href="%1$s" target="_blank" rel="noopener noreferrer"><span class="screen-reader-text">%2$s</span>%3$s</a></li>',
			esc_url( $url ),
			esc_html( $label ),
			dairyfarm_icon( $key )
		);
	}

	if ( $items ) {
		echo '<ul class="df-social">' . $items . '</ul>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

/**
 * Fallback for the primary menu before one is assigned: lists top-level pages.
 */
function dairyfarm_menu_fallback() {
	echo '<ul class="df-nav__list">';
	wp_list_pages(
		array(
			'title_li' => '',
			'depth'    => 1,
		)
	);
	echo '</ul>';
}

/**
 * Footer copyright line.
 *
 * @return string
 */
function dairyfarm_copyright() {
	return str_replace(
		array( '{year}', '{site}' ),
		array( wp_date( 'Y' ), get_bloginfo( 'name' ) ),
		dairyfarm_mod( 'copyright' )
	);
}

/**
 * Post date, author and reading time.
 */
function dairyfarm_post_meta() {
	$words   = str_word_count( wp_strip_all_tags( get_the_content() ) );
	$minutes = max( 1, (int) ceil( $words / 220 ) );

	printf(
		'<div class="df-post-meta"><span>%1$s<time datetime="%2$s">%3$s</time></span><span>%4$s%5$s</span><span>%6$s%7$s</span></div>',
		dairyfarm_icon( 'calendar' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		esc_attr( get_the_date( DATE_W3C ) ),
		esc_html( get_the_date() ),
		dairyfarm_icon( 'user' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		esc_html( get_the_author() ),
		dairyfarm_icon( 'clock' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		/* translators: %d: minutes. */
		esc_html( sprintf( _n( '%d min read', '%d min read', $minutes, 'dairyfarm' ), $minutes ) )
	);
}

/**
 * Simple breadcrumb trail for page/post headers.
 */
function dairyfarm_breadcrumbs() {
	if ( is_front_page() ) {
		return;
	}

	$crumbs = array(
		array( __( 'Home', 'dairyfarm' ), home_url( '/' ) ),
	);

	if ( is_singular( 'post' ) ) {
		$blog = (int) get_option( 'page_for_posts' );
		if ( $blog ) {
			$crumbs[] = array( get_the_title( $blog ), get_permalink( $blog ) );
		}
	} elseif ( is_singular( 'df_product' ) ) {
		$crumbs[] = array( __( 'Products', 'dairyfarm' ), get_post_type_archive_link( 'df_product' ) );
	} elseif ( is_page() ) {
		foreach ( array_reverse( get_post_ancestors( get_the_ID() ) ) as $ancestor ) {
			$crumbs[] = array( get_the_title( $ancestor ), get_permalink( $ancestor ) );
		}
	}

	echo '<nav class="df-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'dairyfarm' ) . '"><ol>';
	foreach ( $crumbs as $crumb ) {
		printf( '<li><a href="%1$s">%2$s</a></li>', esc_url( $crumb[1] ), esc_html( $crumb[0] ) );
	}
	if ( is_singular() ) {
		printf( '<li aria-current="page">%s</li>', esc_html( get_the_title() ) );
	}
	echo '</ol></nav>';
}

/**
 * Title shown in the page hero for archive-type views.
 *
 * @return string
 */
function dairyfarm_archive_title() {
	if ( is_home() ) {
		$blog = (int) get_option( 'page_for_posts' );
		return $blog ? get_the_title( $blog ) : __( 'Latest News', 'dairyfarm' );
	}
	if ( is_search() ) {
		/* translators: %s: search query. */
		return sprintf( __( 'Search results for “%s”', 'dairyfarm' ), get_search_query() );
	}
	if ( is_post_type_archive( 'df_product' ) ) {
		return __( 'Our Products', 'dairyfarm' );
	}
	return wp_strip_all_tags( get_the_archive_title() );
}
