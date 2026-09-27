<?php
/**
 * 404 template.
 *
 * @package Dairyfarm
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="df-section df-404">
	<div class="df-container df-empty">
		<p class="df-404__code">404</p>
		<h1><?php esc_html_e( 'This page wandered off the pasture', 'dairyfarm' ); ?></h1>
		<p><?php esc_html_e( 'The page you’re looking for doesn’t exist or has moved.', 'dairyfarm' ); ?></p>
		<?php get_search_form(); ?>
		<div class="df-actions df-actions--center">
			<?php echo dairyfarm_button( __( 'Back to home', 'dairyfarm' ), home_url( '/' ), 'primary', 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</div>
</section>
<?php
get_footer();
