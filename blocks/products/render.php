<?php
/**
 * Products grid (queries the df_product post type).
 *
 * @package Dairyfarm
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$dairyfarm_orderby = in_array( $attributes['orderby'], array( 'menu_order', 'date', 'title' ), true ) ? $attributes['orderby'] : 'menu_order';
$dairyfarm_args    = array(
	'post_type'           => 'df_product',
	'post_status'         => 'publish',
	'posts_per_page'      => max( 1, min( 12, (int) $attributes['count'] ) ),
	'orderby'             => array(
		$dairyfarm_orderby => 'date' === $dairyfarm_orderby ? 'DESC' : 'ASC',
		'date'             => 'DESC',
	),
	'no_found_rows'       => true,
	'ignore_sticky_posts' => true,
);

if ( $attributes['category'] ) {
	$dairyfarm_args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		array(
			'taxonomy' => 'df_product_cat',
			'field'    => 'slug',
			'terms'    => sanitize_title( $attributes['category'] ),
		),
	);
}

$dairyfarm_query = new WP_Query( $dairyfarm_args );

// Collect the categories actually present in the result, for the filter tabs.
$dairyfarm_terms = array();
foreach ( $dairyfarm_query->posts as $dairyfarm_post ) {
	foreach ( (array) get_the_terms( $dairyfarm_post, 'df_product_cat' ) as $dairyfarm_term ) {
		if ( $dairyfarm_term instanceof WP_Term ) {
			$dairyfarm_terms[ $dairyfarm_term->slug ] = $dairyfarm_term->name;
		}
	}
}
$dairyfarm_filters = $attributes['showFilters'] && ! $attributes['category'] && count( $dairyfarm_terms ) > 1;
?>
<section <?php echo dairyfarm_section_attributes( $attributes, 'products' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="df-container" data-filter-scope>
		<?php echo dairyfarm_section_heading( $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

		<?php if ( $dairyfarm_filters ) : ?>
			<div class="df-filters" role="group" aria-label="<?php esc_attr_e( 'Filter products by category', 'dairyfarm' ); ?>">
				<button type="button" class="df-filters__btn is-active" data-filter="*" aria-pressed="true"><?php esc_html_e( 'All', 'dairyfarm' ); ?></button>
				<?php foreach ( $dairyfarm_terms as $dairyfarm_slug => $dairyfarm_name ) : ?>
					<button type="button" class="df-filters__btn" data-filter="<?php echo esc_attr( $dairyfarm_slug ); ?>" aria-pressed="false"><?php echo esc_html( $dairyfarm_name ); ?></button>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( $dairyfarm_query->have_posts() ) : ?>
			<ul class="df-grid df-grid--3 df-products__grid" role="list">
				<?php
				while ( $dairyfarm_query->have_posts() ) :
					$dairyfarm_query->the_post();
					$dairyfarm_id    = get_the_ID();
					$dairyfarm_cats  = get_the_terms( $dairyfarm_id, 'df_product_cat' );
					$dairyfarm_cats  = is_array( $dairyfarm_cats ) ? $dairyfarm_cats : array();
					$dairyfarm_price = get_post_meta( $dairyfarm_id, '_df_price', true );
					$dairyfarm_unit  = get_post_meta( $dairyfarm_id, '_df_unit', true );
					$dairyfarm_badge = get_post_meta( $dairyfarm_id, '_df_badge', true );
					$dairyfarm_link  = get_post_meta( $dairyfarm_id, '_df_cta_url', true );
					$dairyfarm_link  = $dairyfarm_link ? $dairyfarm_link : get_permalink();
					?>
					<li class="df-card df-product" data-reveal data-category="<?php echo esc_attr( implode( ' ', wp_list_pluck( $dairyfarm_cats, 'slug' ) ) ); ?>">
						<a class="df-product__media" href="<?php echo esc_url( get_permalink() ); ?>" tabindex="-1" aria-hidden="true">
							<?php
							if ( has_post_thumbnail() ) {
								the_post_thumbnail( 'dairyfarm-card', array( 'sizes' => '(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw' ) );
							} else {
								echo dairyfarm_image( array(), 'dairyfarm-card' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}
							?>
							<?php if ( $dairyfarm_badge ) : ?>
								<span class="df-product__badge"><?php echo esc_html( $dairyfarm_badge ); ?></span>
							<?php endif; ?>
						</a>
						<div class="df-product__body">
							<?php if ( $dairyfarm_cats ) : ?>
								<p class="df-product__cat"><?php echo esc_html( $dairyfarm_cats[0]->name ); ?></p>
							<?php endif; ?>
							<h3 class="df-product__title"><a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_title(); ?></a></h3>
							<p class="df-product__text"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18 ) ); ?></p>
							<div class="df-product__footer">
								<?php if ( $dairyfarm_price ) : ?>
									<p class="df-product__price"><strong><?php echo esc_html( $dairyfarm_price ); ?></strong> <span><?php echo esc_html( $dairyfarm_unit ); ?></span></p>
								<?php endif; ?>
								<?php if ( $attributes['buttonLabel'] ) : ?>
									<a class="df-btn df-btn--small df-btn--secondary" href="<?php echo esc_url( $dairyfarm_link ); ?>">
										<?php echo esc_html( $attributes['buttonLabel'] ); ?>
										<span class="screen-reader-text"><?php the_title(); ?></span>
									</a>
								<?php endif; ?>
							</div>
						</div>
					</li>
				<?php endwhile; ?>
			</ul>
			<?php wp_reset_postdata(); ?>
		<?php elseif ( current_user_can( 'edit_posts' ) ) : ?>
			<p class="df-notice"><?php esc_html_e( 'No products yet. Add them under Products → Add New in the admin menu and they will appear here automatically.', 'dairyfarm' ); ?></p>
		<?php endif; ?>

		<?php if ( $attributes['footerLabel'] ) : ?>
			<div class="df-actions df-actions--center">
				<?php echo dairyfarm_button( $attributes['footerLabel'], $attributes['footerUrl'] ? $attributes['footerUrl'] : get_post_type_archive_link( 'df_product' ), 'primary', 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		<?php endif; ?>
	</div>
</section>
