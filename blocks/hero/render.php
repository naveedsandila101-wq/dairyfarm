<?php
/**
 * Hero section.
 *
 * @package Dairyfarm
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$dairyfarm_layout = 'cover' === $attributes['layout'] ? 'cover' : 'split';
$dairyfarm_img    = array(
	'loading'       => 'eager',
	'fetchpriority' => 'high',
	'class'         => 'df-hero__img',
	'sizes'         => 'split' === $dairyfarm_layout ? '(min-width: 1024px) 50vw, 100vw' : '100vw',
);
$dairyfarm_stats  = array_filter(
	(array) $attributes['stats'],
	static function ( $stat ) {
		return ! empty( $stat['value'] );
	}
);
?>
<section <?php echo dairyfarm_section_attributes( $attributes, 'hero', $dairyfarm_layout ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( 'cover' === $dairyfarm_layout ) : ?>
		<div class="df-hero__bg">
			<?php echo dairyfarm_image( $attributes['image'], 'dairyfarm-hero', $dairyfarm_img ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span class="df-hero__overlay" style="opacity:<?php echo esc_attr( max( 0, min( 90, (int) $attributes['overlay'] ) ) / 100 ); ?>"></span>
		</div>
	<?php endif; ?>

	<div class="df-container df-hero__grid">
		<div class="df-hero__content" data-reveal>
			<?php echo dairyfarm_eyebrow( $attributes['eyebrow'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

			<?php if ( $attributes['heading'] ) : ?>
				<h1 class="df-hero__title"><?php echo dairyfarm_kses_inline( $attributes['heading'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h1>
			<?php endif; ?>

			<?php if ( $attributes['text'] ) : ?>
				<p class="df-hero__text"><?php echo dairyfarm_kses_inline( $attributes['text'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			<?php endif; ?>

			<div class="df-actions">
				<?php
				echo dairyfarm_button( $attributes['primaryLabel'], $attributes['primaryUrl'], 'cover' === $dairyfarm_layout ? 'light' : 'primary', 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo dairyfarm_button( $attributes['secondaryLabel'], $attributes['secondaryUrl'], 'cover' === $dairyfarm_layout ? 'outline-light' : 'secondary' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			</div>

			<?php if ( $dairyfarm_stats ) : ?>
				<dl class="df-hero__stats">
					<?php foreach ( $dairyfarm_stats as $dairyfarm_stat ) : ?>
						<div class="df-hero__stat">
							<dt><?php echo esc_html( isset( $dairyfarm_stat['label'] ) ? $dairyfarm_stat['label'] : '' ); ?></dt>
							<dd><?php echo esc_html( $dairyfarm_stat['value'] ); ?></dd>
						</div>
					<?php endforeach; ?>
				</dl>
			<?php endif; ?>
		</div>

		<?php if ( 'split' === $dairyfarm_layout ) : ?>
			<div class="df-hero__media" data-reveal>
				<div class="df-hero__frame">
					<?php echo dairyfarm_image( $attributes['image'], 'dairyfarm-hero', $dairyfarm_img ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
				<?php if ( $attributes['badgeTitle'] ) : ?>
					<div class="df-hero__badge">
						<span class="df-hero__badge-icon"><?php echo dairyfarm_icon( 'award' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<span>
							<strong><?php echo esc_html( $attributes['badgeTitle'] ); ?></strong>
							<?php echo esc_html( $attributes['badgeText'] ); ?>
						</span>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
