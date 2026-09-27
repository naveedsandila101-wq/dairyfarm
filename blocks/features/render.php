<?php
/**
 * Why Choose Us / features grid.
 *
 * @package Dairyfarm
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$dairyfarm_cols = max( 2, min( 4, (int) $attributes['columns'] ) );
?>
<section <?php echo dairyfarm_section_attributes( $attributes, 'features' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="df-container">
		<?php echo dairyfarm_section_heading( $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

		<ul class="df-grid df-grid--<?php echo esc_attr( $dairyfarm_cols ); ?>" role="list">
			<?php foreach ( (array) $attributes['items'] as $dairyfarm_item ) : ?>
				<?php $dairyfarm_item = wp_parse_args( $dairyfarm_item, array( 'icon' => 'leaf', 'title' => '', 'text' => '' ) ); ?>
				<li class="df-card df-feature" data-reveal>
					<span class="df-icon-badge"><?php echo dairyfarm_icon( $dairyfarm_item['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<h3 class="df-feature__title"><?php echo esc_html( $dairyfarm_item['title'] ); ?></h3>
					<p class="df-feature__text"><?php echo esc_html( $dairyfarm_item['text'] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
