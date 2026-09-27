<?php
/**
 * About Us section.
 *
 * @package Dairyfarm
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$dairyfarm_position = 'right' === $attributes['imagePosition'] ? 'right' : 'left';
$dairyfarm_points   = array_filter( wp_list_pluck( (array) $attributes['points'], 'text' ) );
$dairyfarm_has_sub  = ! empty( $attributes['imageSecondary']['id'] ) || ! empty( $attributes['imageSecondary']['url'] );
?>
<section <?php echo dairyfarm_section_attributes( $attributes, 'about', 'image-' . $dairyfarm_position ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="df-container df-split">
		<div class="df-about__media" data-reveal>
			<div class="df-about__frame">
				<?php echo dairyfarm_image( dairyfarm_with_dummy( $attributes['image'], 'barn', __( 'The family barn', 'dairyfarm' ) ), 'large', array( 'sizes' => '(min-width: 1024px) 40vw, 100vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>

			<?php if ( $dairyfarm_has_sub ) : ?>
				<div class="df-about__sub">
					<?php echo dairyfarm_image( $attributes['imageSecondary'], 'dairyfarm-card', array( 'sizes' => '240px' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			<?php endif; ?>

			<?php if ( $attributes['experienceValue'] ) : ?>
				<div class="df-about__badge">
					<strong><?php echo esc_html( $attributes['experienceValue'] ); ?></strong>
					<span><?php echo esc_html( $attributes['experienceLabel'] ); ?></span>
				</div>
			<?php endif; ?>
		</div>

		<div class="df-about__content" data-reveal>
			<?php
			echo dairyfarm_section_heading( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				array(
					'eyebrow' => $attributes['eyebrow'],
					'heading' => $attributes['heading'],
				),
				'start'
			);
			?>

			<div class="df-prose">
				<?php echo wp_kses_post( wpautop( $attributes['text'] ) ); ?>
			</div>

			<?php if ( $dairyfarm_points ) : ?>
				<ul class="df-checklist" role="list">
					<?php foreach ( $dairyfarm_points as $dairyfarm_point ) : ?>
						<li><?php echo dairyfarm_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( $dairyfarm_point ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if ( $attributes['highlightText'] ) : ?>
				<div class="df-highlight">
					<span class="df-highlight__icon"><?php echo dairyfarm_icon( 'sprout' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<div>
						<?php if ( $attributes['highlightTitle'] ) : ?>
							<strong><?php echo esc_html( $attributes['highlightTitle'] ); ?></strong>
						<?php endif; ?>
						<p><?php echo esc_html( $attributes['highlightText'] ); ?></p>
					</div>
				</div>
			<?php endif; ?>

			<div class="df-actions">
				<?php echo dairyfarm_button( $attributes['buttonLabel'], $attributes['buttonUrl'], 'primary', 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
	</div>
</section>
