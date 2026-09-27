<?php
/**
 * Custom post types: Products and Reviews.
 *
 * Kept in the theme for a single-deliverable build. For long-lived sites, move this
 * file into a small site plugin so content survives a theme switch.
 *
 * @package Dairyfarm
 */

defined( 'ABSPATH' ) || exit;

/**
 * Meta fields per post type: key => [ type, sanitize callback, default ].
 *
 * @return array
 */
function dairyfarm_meta_schema() {
	return array(
		'df_product'     => array(
			'_df_price'   => array( 'string', 'sanitize_text_field', '' ),
			'_df_unit'    => array( 'string', 'sanitize_text_field', '' ),
			'_df_badge'   => array( 'string', 'sanitize_text_field', '' ),
			'_df_cta_url' => array( 'string', 'esc_url_raw', '' ),
		),
		'df_testimonial' => array(
			'_df_rating'    => array( 'integer', 'absint', 5 ),
			'_df_role'      => array( 'string', 'sanitize_text_field', '' ),
			'_df_highlight' => array( 'string', 'sanitize_text_field', '' ),
		),
	);
}

/**
 * Registers post types, taxonomy and meta.
 */
function dairyfarm_register_content_types() {
	register_post_type(
		'df_product',
		array(
			'labels'        => array(
				'name'               => __( 'Products', 'dairyfarm' ),
				'singular_name'      => __( 'Product', 'dairyfarm' ),
				'add_new_item'       => __( 'Add New Product', 'dairyfarm' ),
				'edit_item'          => __( 'Edit Product', 'dairyfarm' ),
				'all_items'          => __( 'All Products', 'dairyfarm' ),
				'search_items'       => __( 'Search Products', 'dairyfarm' ),
				'not_found'          => __( 'No products found.', 'dairyfarm' ),
				'featured_image'     => __( 'Product image', 'dairyfarm' ),
				'set_featured_image' => __( 'Set product image', 'dairyfarm' ),
			),
			'public'        => true,
			'has_archive'   => 'products',
			'rewrite'       => array( 'slug' => 'products', 'with_front' => false ),
			'menu_icon'     => 'dashicons-carrot',
			'menu_position' => 20,
			'show_in_rest'  => true,
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'custom-fields' ),
		)
	);

	register_taxonomy(
		'df_product_cat',
		'df_product',
		array(
			'labels'            => array(
				'name'          => __( 'Product Categories', 'dairyfarm' ),
				'singular_name' => __( 'Product Category', 'dairyfarm' ),
			),
			'hierarchical'      => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'product-category', 'with_front' => false ),
		)
	);

	register_post_type(
		'df_testimonial',
		array(
			'labels'              => array(
				'name'               => __( 'Reviews', 'dairyfarm' ),
				'singular_name'      => __( 'Review', 'dairyfarm' ),
				'add_new_item'       => __( 'Add New Review', 'dairyfarm' ),
				'edit_item'          => __( 'Edit Review', 'dairyfarm' ),
				'all_items'          => __( 'All Reviews', 'dairyfarm' ),
				'featured_image'     => __( 'Customer photo', 'dairyfarm' ),
				'set_featured_image' => __( 'Set customer photo', 'dairyfarm' ),
				'enter_title_here'   => __( 'Customer name', 'dairyfarm' ),
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_rest'        => true,
			'exclude_from_search' => true,
			'menu_icon'           => 'dashicons-format-quote',
			'menu_position'       => 21,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'page-attributes', 'custom-fields' ),
		)
	);

	foreach ( dairyfarm_meta_schema() as $post_type => $fields ) {
		foreach ( $fields as $key => $def ) {
			register_post_meta(
				$post_type,
				$key,
				array(
					'type'              => $def[0],
					'single'            => true,
					'default'           => $def[2],
					'sanitize_callback' => $def[1],
					'show_in_rest'      => true,
					'auth_callback'     => static function () {
						return current_user_can( 'edit_posts' );
					},
				)
			);
		}
	}
}
add_action( 'init', 'dairyfarm_register_content_types' );

/**
 * Title placeholder for reviews.
 *
 * @param string  $title Placeholder.
 * @param WP_Post $post  Post.
 * @return string
 */
function dairyfarm_title_placeholder( $title, $post ) {
	if ( 'df_testimonial' === $post->post_type ) {
		return __( 'Customer name', 'dairyfarm' );
	}
	return $title;
}
add_filter( 'enter_title_here', 'dairyfarm_title_placeholder', 10, 2 );

/**
 * Flush rewrite rules once after the theme is activated so /products/ resolves.
 */
function dairyfarm_flush_rewrites() {
	dairyfarm_register_content_types();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'dairyfarm_flush_rewrites' );
