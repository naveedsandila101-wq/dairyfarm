<?php
/**
 * Call to action / newsletter band.
 *
 * @package Dairyfarm
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$dairyfarm_phone     = dairyfarm_mod( 'phone' );
$dairyfarm_email     = dairyfarm_mod( 'email' );
$dairyfarm_address   = dairyfarm_mod( 'address' );
$dairyfarm_tel       = $dairyfarm_phone ? 'tel:' . preg_replace( '/[^\d+]/', '', $dairyfarm_phone ) : '';
$dairyfarm_secondary = $attributes['secondaryUrl'] ? $attributes['secondaryUrl'] : $dairyfarm_tel;
$dairyfarm_has_image = ! empty( $attributes['image']['id'] ) || ! empty( $attributes['image']['url'] );
$dairyfarm_shortcode = trim( (string) $attributes['formShortcode'] );
?>
<section <?php echo dairyfarm_section_attributes( $attributes, 'cta', $dairyfarm_has_image ? 'has-image' : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="df-container">
		<div class="df-cta__panel" data-reveal>
			<div class="df-cta__content">
				<?php echo dairyfarm_eyebrow( $attributes['eyebrow'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php if ( $attributes['heading'] ) : ?>
					<h2 class="df-cta__title"><?php echo dairyfarm_kses_inline( $attributes['heading'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
				<?php endif; ?>
				<?php if ( $attributes['text'] ) : ?>
					<p class="df-cta__text"><?php echo dairyfarm_kses_inline( $attributes['text'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				<?php endif; ?>

				<?php if ( $dairyfarm_shortcode ) : ?>
					<div class="df-cta__form"><?php echo do_shortcode( $dairyfarm_shortcode ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
				<?php else : ?>
					<div class="df-actions">
						<?php
						echo dairyfarm_button( $attributes['primaryLabel'], $attributes['primaryUrl'], 'light', 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo dairyfarm_button( $attributes['secondaryLabel'], $dairyfarm_secondary, 'outline-light', 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					</div>
				<?php endif; ?>

				<?php if ( $attributes['note'] ) : ?>
					<p class="df-cta__note"><?php echo dairyfarm_icon( 'check-circle' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( $attributes['note'] ); ?></p>
				<?php endif; ?>
			</div>

			<?php if ( $attributes['showContact'] || $dairyfarm_has_image ) : ?>
				<div class="df-cta__side">
					<?php if ( $dairyfarm_has_image ) : ?>
						<div class="df-cta__image"><?php echo dairyfarm_image( $attributes['image'], 'dairyfarm-card', array( 'sizes' => '(min-width: 1024px) 40vw, 100vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
					<?php endif; ?>
					<?php if ( $attributes['showContact'] ) : ?>
						<ul class="df-cta__contact" role="list">
							<?php if ( $dairyfarm_phone ) : ?>
								<li><?php echo dairyfarm_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><small><?php esc_html_e( 'Call us', 'dairyfarm' ); ?></small><a href="<?php echo esc_attr( $dairyfarm_tel ); ?>"><?php echo esc_html( $dairyfarm_phone ); ?></a></span></li>
							<?php endif; ?>
							<?php if ( $dairyfarm_email ) : ?>
								<li><?php echo dairyfarm_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><small><?php esc_html_e( 'Email', 'dairyfarm' ); ?></small><a href="mailto:<?php echo esc_attr( antispambot( $dairyfarm_email ) ); ?>"><?php echo esc_html( antispambot( $dairyfarm_email ) ); ?></a></span></li>
							<?php endif; ?>
							<?php if ( $dairyfarm_address ) : ?>
								<li><?php echo dairyfarm_icon( 'map-pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><small><?php esc_html_e( 'Farm shop', 'dairyfarm' ); ?></small><?php echo nl2br( esc_html( $dairyfarm_address ) ); ?></span></li>
							<?php endif; ?>
						</ul>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
