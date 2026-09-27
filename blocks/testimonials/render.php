<?php
/**
 * Customer reviews (queries the df_testimonial post type).
 *
 * @package Dairyfarm
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$dairyfarm_reviews = get_posts(
	array(
		'post_type'        => 'df_testimonial',
		'post_status'      => 'publish',
		'numberposts'      => max( 1, min( 9, (int) $attributes['count'] ) ),
		'orderby'          => array(
			'menu_order' => 'ASC',
			'date'       => 'DESC',
		),
		'suppress_filters' => false,
	)
);
?>
<section <?php echo dairyfarm_section_attributes( $attributes, 'testimonials' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="df-container">
		<div class="df-testimonials__head">
			<?php echo dairyfarm_section_heading( $attributes, 'start' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

			<?php if ( $attributes['showSummary'] && $attributes['ratingValue'] ) : ?>
				<div class="df-rating-summary" data-reveal>
					<strong><?php echo esc_html( $attributes['ratingValue'] ); ?></strong>
					<div>
						<?php echo dairyfarm_stars( (int) round( (float) $attributes['ratingValue'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span><?php echo esc_html( $attributes['ratingText'] ); ?></span>
					</div>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( $dairyfarm_reviews ) : ?>
			<ul class="df-grid df-grid--3" role="list">
				<?php foreach ( $dairyfarm_reviews as $dairyfarm_review ) : ?>
					<?php
					$dairyfarm_rating    = (int) get_post_meta( $dairyfarm_review->ID, '_df_rating', true );
					$dairyfarm_role      = get_post_meta( $dairyfarm_review->ID, '_df_role', true );
					$dairyfarm_highlight = get_post_meta( $dairyfarm_review->ID, '_df_highlight', true );
					$dairyfarm_name      = get_the_title( $dairyfarm_review );
					?>
					<li class="df-card df-review" data-reveal>
						<figure>
							<div class="df-review__top">
								<?php echo dairyfarm_stars( $dairyfarm_rating ? $dairyfarm_rating : 5 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<span class="df-review__quote-mark"><?php echo dairyfarm_icon( 'quote' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							</div>
							<?php if ( $dairyfarm_highlight ) : ?>
								<p class="df-review__title"><?php echo esc_html( $dairyfarm_highlight ); ?></p>
							<?php endif; ?>
							<blockquote class="df-review__text">
								<?php echo wp_kses_post( wpautop( $dairyfarm_review->post_content ) ); ?>
							</blockquote>
							<figcaption class="df-review__author">
								<?php if ( has_post_thumbnail( $dairyfarm_review ) ) : ?>
									<?php echo get_the_post_thumbnail( $dairyfarm_review, 'dairyfarm-avatar', array( 'class' => 'df-avatar', 'alt' => '' ) ); ?>
								<?php else : ?>
									<span class="df-avatar df-avatar--initials" aria-hidden="true"><?php echo esc_html( strtoupper( mb_substr( $dairyfarm_name, 0, 1 ) ) ); ?></span>
								<?php endif; ?>
								<span>
									<cite><?php echo esc_html( $dairyfarm_name ); ?></cite>
									<?php if ( $dairyfarm_role ) : ?>
										<small><?php echo esc_html( $dairyfarm_role ); ?></small>
									<?php endif; ?>
								</span>
							</figcaption>
						</figure>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php elseif ( current_user_can( 'edit_posts' ) ) : ?>
			<p class="df-notice"><?php esc_html_e( 'No reviews yet. Add them under Reviews → Add New in the admin menu.', 'dairyfarm' ); ?></p>
		<?php endif; ?>
	</div>
</section>
