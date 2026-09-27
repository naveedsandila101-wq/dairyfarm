<?php
/**
 * Team / family members grid.
 *
 * @package Dairyfarm
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$dairyfarm_members = array_values(
	array_filter(
		(array) $attributes['members'],
		static function ( $member ) {
			return ! empty( $member['name'] );
		}
	)
);
$dairyfarm_dummies = array( 'farmer-1', 'farmer-2', 'farmer-3', 'farmer-4' );
?>
<section <?php echo dairyfarm_section_attributes( $attributes, 'team' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="df-container">
		<?php echo dairyfarm_section_heading( $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

		<ul class="df-grid df-grid--4 df-team__grid" role="list">
			<?php foreach ( $dairyfarm_members as $dairyfarm_i => $dairyfarm_member ) : ?>
				<?php $dairyfarm_member = wp_parse_args( $dairyfarm_member, array( 'image' => array(), 'name' => '', 'role' => '', 'text' => '' ) ); ?>
				<li class="df-card df-member" data-reveal>
					<div class="df-member__photo">
						<?php echo dairyfarm_image( dairyfarm_with_dummy( $dairyfarm_member['image'], $dairyfarm_dummies[ $dairyfarm_i % count( $dairyfarm_dummies ) ], $dairyfarm_member['name'] ), 'dairyfarm-card', array( 'sizes' => '(min-width: 1024px) 25vw, (min-width: 640px) 50vw, 100vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
					<div class="df-member__body">
						<h3 class="df-member__name"><?php echo esc_html( $dairyfarm_member['name'] ); ?></h3>
						<?php if ( $dairyfarm_member['role'] ) : ?>
							<p class="df-member__role"><?php echo esc_html( $dairyfarm_member['role'] ); ?></p>
						<?php endif; ?>
						<?php if ( $dairyfarm_member['text'] ) : ?>
							<p class="df-member__text"><?php echo esc_html( $dairyfarm_member['text'] ); ?></p>
						<?php endif; ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
