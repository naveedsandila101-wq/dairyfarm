<?php
/**
 * Page banner: inner-page hero with breadcrumbs, title and image.
 *
 * @package Dairyfarm
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$dairyfarm_heading = trim( (string) $attributes['heading'] );
$dairyfarm_heading = '' !== $dairyfarm_heading ? $dairyfarm_heading : esc_html( get_the_title() );
$dairyfarm_image   = dairyfarm_with_dummy( $attributes['image'], $attributes['dummy'] ? $attributes['dummy'] : 'barn', wp_strip_all_tags( $dairyfarm_heading ) );
?>
<section <?php echo dairyfarm_section_attributes( $attributes, 'banner' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="df-container df-banner__grid">
		<div class="df-banner__content" data-reveal>
			<?php
			if ( $attributes['showBreadcrumbs'] ) {
				dairyfarm_breadcrumbs();
			}
			echo dairyfarm_eyebrow( $attributes['eyebrow'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
			<h1 class="df-banner__title"><?php echo dairyfarm_kses_inline( $dairyfarm_heading ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h1>
			<?php if ( $attributes['text'] ) : ?>
				<p class="df-banner__text"><?php echo dairyfarm_kses_inline( $attributes['text'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			<?php endif; ?>
			<?php if ( $attributes['primaryLabel'] && $attributes['primaryUrl'] ) : ?>
				<div class="df-actions">
					<?php echo dairyfarm_button( $attributes['primaryLabel'], $attributes['primaryUrl'], 'primary', 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			<?php endif; ?>
		</div>

		<div class="df-banner__media" data-reveal>
			<div class="df-banner__frame">
				<?php echo dairyfarm_image( $dairyfarm_image, 'dairyfarm-hero', array( 'loading' => 'eager', 'fetchpriority' => 'high', 'sizes' => '(min-width: 1024px) 45vw, 100vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
			<?php if ( $attributes['badgeValue'] ) : ?>
				<div class="df-banner__badge">
					<span class="df-banner__badge-icon"><?php echo dairyfarm_icon( 'award' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<span><strong><?php echo esc_html( $attributes['badgeValue'] ); ?></strong><?php echo esc_html( $attributes['badgeLabel'] ); ?></span>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
