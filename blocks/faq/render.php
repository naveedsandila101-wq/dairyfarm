<?php
/**
 * FAQ accordion (native <details>, no JS required).
 *
 * @package Dairyfarm
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$dairyfarm_items = array_values(
	array_filter(
		(array) $attributes['items'],
		static function ( $item ) {
			return ! empty( $item['question'] );
		}
	)
);

if ( $attributes['schema'] && $dairyfarm_items && ! wp_is_json_request() ) {
	dairyfarm_schema(
		array(
			'@type'      => 'FAQPage',
			'mainEntity' => array_values(
				array_map(
					static function ( $item ) {
						return array(
							'@type'          => 'Question',
							'name'           => wp_strip_all_tags( $item['question'] ),
							'acceptedAnswer' => array(
								'@type' => 'Answer',
								'text'  => wp_strip_all_tags( isset( $item['answer'] ) ? $item['answer'] : '' ),
							),
						);
					},
					$dairyfarm_items
				)
			),
		)
	);
}

$dairyfarm_phone = dairyfarm_mod( 'phone' );
$dairyfarm_email = dairyfarm_mod( 'email' );
?>
<section <?php echo dairyfarm_section_attributes( $attributes, 'faq' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="df-container df-faq__grid">
		<div class="df-faq__aside">
			<?php echo dairyfarm_section_heading( $attributes, 'start' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

			<?php if ( $attributes['contactTitle'] ) : ?>
				<div class="df-card df-faq__help" data-reveal>
					<span class="df-icon-badge"><?php echo dairyfarm_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<h3><?php echo esc_html( $attributes['contactTitle'] ); ?></h3>
					<p><?php echo esc_html( $attributes['contactText'] ); ?></p>
					<ul class="df-contact-list" role="list">
						<?php if ( $dairyfarm_phone ) : ?>
							<li><a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $dairyfarm_phone ) ); ?>"><?php echo esc_html( $dairyfarm_phone ); ?></a></li>
						<?php endif; ?>
						<?php if ( $dairyfarm_email ) : ?>
							<li><a href="mailto:<?php echo esc_attr( antispambot( $dairyfarm_email ) ); ?>"><?php echo esc_html( antispambot( $dairyfarm_email ) ); ?></a></li>
						<?php endif; ?>
					</ul>
					<?php echo dairyfarm_button( $attributes['contactLabel'], $attributes['contactUrl'], 'primary', 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			<?php endif; ?>
		</div>

		<div class="df-accordion" data-reveal>
			<?php foreach ( $dairyfarm_items as $dairyfarm_i => $dairyfarm_item ) : ?>
				<details class="df-accordion__item"<?php echo 0 === $dairyfarm_i ? ' open' : ''; ?>>
					<summary>
						<span><?php echo esc_html( $dairyfarm_item['question'] ); ?></span>
						<span class="df-accordion__icon"><?php echo dairyfarm_icon( 'plus' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					</summary>
					<div class="df-accordion__body">
						<?php echo wp_kses_post( wpautop( isset( $dairyfarm_item['answer'] ) ? $dairyfarm_item['answer'] : '' ) ); ?>
					</div>
				</details>
			<?php endforeach; ?>
		</div>
	</div>
</section>
