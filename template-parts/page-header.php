<?php
/**
 * Page / post / archive title banner.
 *
 * @package Dairyfarm
 * @var array $args { title, subtitle, meta (bool), eyebrow }
 */

defined( 'ABSPATH' ) || exit;

$dairyfarm_args = wp_parse_args(
	isset( $args ) ? $args : array(),
	array(
		'title'    => get_the_title(),
		'subtitle' => '',
		'eyebrow'  => '',
		'meta'     => false,
	)
);
?>
<header class="df-page-header">
	<div class="df-container df-page-header__inner">
		<?php dairyfarm_breadcrumbs(); ?>
		<?php echo dairyfarm_eyebrow( $dairyfarm_args['eyebrow'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<h1 class="df-page-header__title"><?php echo esc_html( $dairyfarm_args['title'] ); ?></h1>
		<?php if ( $dairyfarm_args['subtitle'] ) : ?>
			<p class="df-page-header__subtitle"><?php echo esc_html( $dairyfarm_args['subtitle'] ); ?></p>
		<?php endif; ?>
		<?php
		if ( $dairyfarm_args['meta'] ) {
			dairyfarm_post_meta();
		}
		?>
	</div>
</header>
