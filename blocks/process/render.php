<?php
/**
 * Farm-to-table process steps.
 *
 * @package Dairyfarm
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$dairyfarm_steps = (array) $attributes['steps'];
?>
<section <?php echo dairyfarm_section_attributes( $attributes, 'process' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="df-container">
		<?php echo dairyfarm_section_heading( $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

		<ol class="df-steps" style="--df-steps:<?php echo esc_attr( max( 1, count( $dairyfarm_steps ) ) ); ?>">
			<?php foreach ( $dairyfarm_steps as $dairyfarm_i => $dairyfarm_step ) : ?>
				<?php $dairyfarm_step = wp_parse_args( $dairyfarm_step, array( 'icon' => 'leaf', 'title' => '', 'text' => '' ) ); ?>
				<li class="df-step" data-reveal>
					<div class="df-step__marker">
						<span class="df-step__icon"><?php echo dairyfarm_icon( $dairyfarm_step['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<span class="df-step__num"><?php echo esc_html( str_pad( (string) ( $dairyfarm_i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
					</div>
					<h3 class="df-step__title"><?php echo esc_html( $dairyfarm_step['title'] ); ?></h3>
					<p class="df-step__text"><?php echo esc_html( $dairyfarm_step['text'] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>
