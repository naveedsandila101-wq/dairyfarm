<?php
/**
 * Stats band with count-up numbers.
 *
 * @package Dairyfarm
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;
?>
<section <?php echo dairyfarm_section_attributes( $attributes, 'stats' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="df-container">
		<dl class="df-stats__list">
			<?php foreach ( (array) $attributes['items'] as $dairyfarm_item ) : ?>
				<?php
				$dairyfarm_item    = wp_parse_args( $dairyfarm_item, array( 'value' => '', 'suffix' => '', 'label' => '' ) );
				$dairyfarm_numeric = preg_match( '/^\d+$/', $dairyfarm_item['value'] );
				?>
				<div class="df-stats__item" data-reveal>
					<dt><?php echo esc_html( $dairyfarm_item['label'] ); ?></dt>
					<dd>
						<span <?php echo $dairyfarm_numeric ? 'data-count="' . esc_attr( $dairyfarm_item['value'] ) . '"' : ''; ?>><?php echo esc_html( $dairyfarm_numeric ? number_format_i18n( (int) $dairyfarm_item['value'] ) : $dairyfarm_item['value'] ); ?></span><?php echo esc_html( $dairyfarm_item['suffix'] ); ?>
					</dd>
				</div>
			<?php endforeach; ?>
		</dl>
	</div>
</section>
