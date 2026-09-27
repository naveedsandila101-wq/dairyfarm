<?php
/**
 * Farm gallery.
 *
 * @package Dairyfarm
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$dairyfarm_layout   = 'grid' === $attributes['layout'] ? 'grid' : 'mosaic';
$dairyfarm_btn_url  = $attributes['buttonUrl'] ? $attributes['buttonUrl'] : dairyfarm_mod( 'social_instagram' );
$dairyfarm_lightbox = (bool) $attributes['lightbox'];
$dairyfarm_dummies  = array( 'pasture', 'calves', 'creamery', 'cheese', 'bottles', 'openday' );
?>
<section <?php echo dairyfarm_section_attributes( $attributes, 'gallery' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="df-container">
		<?php echo dairyfarm_section_heading( $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

		<ul class="df-gallery__grid df-gallery__grid--<?php echo esc_attr( $dairyfarm_layout ); ?>" role="list"<?php echo $dairyfarm_lightbox ? ' data-lightbox' : ''; ?>>
			<?php foreach ( array_values( (array) $attributes['images'] ) as $dairyfarm_i => $dairyfarm_item ) : ?>
				<?php
				$dairyfarm_item    = wp_parse_args( $dairyfarm_item, array( 'image' => array(), 'caption' => '' ) );
				$dairyfarm_image   = dairyfarm_with_dummy( $dairyfarm_item['image'], $dairyfarm_dummies[ $dairyfarm_i % count( $dairyfarm_dummies ) ] );
				$dairyfarm_full    = ! empty( $dairyfarm_image['id'] ) ? wp_get_attachment_image_url( $dairyfarm_image['id'], 'full' ) : ( isset( $dairyfarm_image['url'] ) ? $dairyfarm_image['url'] : '' );
				$dairyfarm_img_alt = ! empty( $dairyfarm_image['alt'] ) ? $dairyfarm_image['alt'] : $dairyfarm_item['caption'];
				$dairyfarm_markup  = dairyfarm_image(
					array_merge( $dairyfarm_image, array( 'alt' => $dairyfarm_img_alt ) ),
					'large',
					array( 'sizes' => '(min-width: 1024px) 33vw, 50vw' )
				);
				?>
				<li class="df-gallery__item" data-reveal>
					<figure>
						<?php if ( $dairyfarm_lightbox && $dairyfarm_full ) : ?>
							<a href="<?php echo esc_url( $dairyfarm_full ); ?>" class="df-gallery__link" data-caption="<?php echo esc_attr( $dairyfarm_item['caption'] ); ?>">
								<?php echo $dairyfarm_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<span class="df-gallery__zoom"><?php echo dairyfarm_icon( 'expand' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span class="screen-reader-text"><?php esc_html_e( 'Open image', 'dairyfarm' ); ?></span></span>
							</a>
						<?php else : ?>
							<?php echo $dairyfarm_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php endif; ?>
						<?php if ( $dairyfarm_item['caption'] ) : ?>
							<figcaption><?php echo esc_html( $dairyfarm_item['caption'] ); ?></figcaption>
						<?php endif; ?>
					</figure>
				</li>
			<?php endforeach; ?>
		</ul>

		<?php if ( $attributes['buttonLabel'] && $dairyfarm_btn_url && '#' !== $dairyfarm_btn_url ) : ?>
			<div class="df-actions df-actions--center">
				<?php echo dairyfarm_button( $attributes['buttonLabel'], $dairyfarm_btn_url, 'secondary', 'instagram' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		<?php endif; ?>
	</div>
</section>
