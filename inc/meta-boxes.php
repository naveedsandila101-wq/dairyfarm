<?php
/**
 * Meta boxes for product and review details (work in both block and classic editors).
 *
 * @package Dairyfarm
 */

defined( 'ABSPATH' ) || exit;

/**
 * Field definitions rendered in the meta boxes.
 *
 * @return array
 */
function dairyfarm_meta_box_fields() {
	return array(
		'df_product'     => array(
			'title'  => __( 'Product Details', 'dairyfarm' ),
			'fields' => array(
				'_df_price'   => array( 'label' => __( 'Price', 'dairyfarm' ), 'type' => 'text', 'placeholder' => '$4.50' ),
				'_df_unit'    => array( 'label' => __( 'Unit', 'dairyfarm' ), 'type' => 'text', 'placeholder' => '/ 1 litre' ),
				'_df_badge'   => array( 'label' => __( 'Badge (optional)', 'dairyfarm' ), 'type' => 'text', 'placeholder' => __( 'Bestseller', 'dairyfarm' ) ),
				'_df_cta_url' => array( 'label' => __( 'Order link (defaults to product page)', 'dairyfarm' ), 'type' => 'url', 'placeholder' => 'https://' ),
			),
		),
		'df_testimonial' => array(
			'title'  => __( 'Review Details', 'dairyfarm' ),
			'fields' => array(
				'_df_rating'    => array( 'label' => __( 'Rating (1–5)', 'dairyfarm' ), 'type' => 'number', 'placeholder' => '5' ),
				'_df_role'      => array( 'label' => __( 'Role / location', 'dairyfarm' ), 'type' => 'text', 'placeholder' => __( 'Customer since 2019', 'dairyfarm' ) ),
				'_df_highlight' => array( 'label' => __( 'Headline', 'dairyfarm' ), 'type' => 'text', 'placeholder' => __( 'Best milk in town', 'dairyfarm' ) ),
			),
		),
	);
}

/**
 * Registers meta boxes.
 */
function dairyfarm_add_meta_boxes() {
	foreach ( dairyfarm_meta_box_fields() as $post_type => $box ) {
		add_meta_box( 'dairyfarm-' . $post_type, $box['title'], 'dairyfarm_render_meta_box', $post_type, 'side', 'high', array( 'fields' => $box['fields'] ) );
	}
}
add_action( 'add_meta_boxes', 'dairyfarm_add_meta_boxes' );

/**
 * Meta box markup.
 *
 * @param WP_Post $post Post.
 * @param array   $box  Box args.
 */
function dairyfarm_render_meta_box( $post, $box ) {
	wp_nonce_field( 'dairyfarm_meta_' . $post->ID, 'dairyfarm_meta_nonce' );

	foreach ( $box['args']['fields'] as $key => $field ) {
		$value = get_post_meta( $post->ID, $key, true );
		$extra = 'number' === $field['type'] ? ' min="1" max="5" step="1"' : '';
		printf(
			'<p><label for="%1$s"><strong>%2$s</strong></label><br /><input class="widefat" type="%3$s" id="%1$s" name="%1$s" value="%4$s" placeholder="%5$s"%6$s /></p>',
			esc_attr( $key ),
			esc_html( $field['label'] ),
			esc_attr( $field['type'] ),
			esc_attr( $value ),
			esc_attr( $field['placeholder'] ),
			$extra // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static string.
		);
	}
}

/**
 * Saves meta box values.
 *
 * @param int $post_id Post ID.
 */
function dairyfarm_save_meta_boxes( $post_id ) {
	if ( ! isset( $_POST['dairyfarm_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['dairyfarm_meta_nonce'] ), 'dairyfarm_meta_' . $post_id ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$post_type = get_post_type( $post_id );
	$schema    = dairyfarm_meta_schema();

	if ( ! isset( $schema[ $post_type ] ) ) {
		return;
	}

	foreach ( $schema[ $post_type ] as $key => $def ) {
		if ( ! isset( $_POST[ $key ] ) ) {
			continue;
		}
		$value = call_user_func( $def[1], wp_unslash( $_POST[ $key ] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( '_df_rating' === $key ) {
			$value = max( 1, min( 5, (int) $value ) );
		}
		update_post_meta( $post_id, $key, $value );
	}
}
add_action( 'save_post', 'dairyfarm_save_meta_boxes' );
