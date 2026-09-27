<?php
/**
 * Site footer.
 *
 * @package Dairyfarm
 */

defined( 'ABSPATH' ) || exit;

$dairyfarm_phone   = dairyfarm_mod( 'phone' );
$dairyfarm_email   = dairyfarm_mod( 'email' );
$dairyfarm_address = dairyfarm_mod( 'address' );
$dairyfarm_hours   = dairyfarm_mod( 'hours' );
?>
</main>

<footer class="df-footer">
	<div class="df-container">
		<div class="df-footer__grid">
			<div class="df-footer__brand">
				<?php dairyfarm_logo(); ?>
				<?php if ( dairyfarm_mod( 'footer_about' ) ) : ?>
					<p><?php echo esc_html( dairyfarm_mod( 'footer_about' ) ); ?></p>
				<?php endif; ?>
				<?php dairyfarm_social_links(); ?>
			</div>

			<?php
			$dairyfarm_columns = array(
				'footer'  => dairyfarm_mod( 'footer_col1' ),
				'footer2' => dairyfarm_mod( 'footer_col2' ),
			);
			foreach ( $dairyfarm_columns as $dairyfarm_location => $dairyfarm_heading ) :
				$dairyfarm_links = has_nav_menu( $dairyfarm_location )
					? wp_nav_menu(
						array(
							'theme_location' => $dairyfarm_location,
							'container'      => false,
							'menu_class'     => 'df-footer__links',
							'depth'          => 1,
							'echo'           => false,
						)
					)
					: dairyfarm_footer_fallback_links( $dairyfarm_location );

				if ( ! $dairyfarm_links ) {
					continue;
				}
				?>
				<nav class="df-footer__col" aria-label="<?php echo esc_attr( $dairyfarm_heading ); ?>">
					<h2 class="df-footer__heading"><?php echo esc_html( $dairyfarm_heading ); ?></h2>
					<?php echo $dairyfarm_links; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- menu markup. ?>
				</nav>
			<?php endforeach; ?>

			<div class="df-footer__col df-footer__visit">
				<h2 class="df-footer__heading"><?php echo esc_html( dairyfarm_mod( 'footer_col3' ) ); ?></h2>
				<ul class="df-footer__contact" role="list">
					<?php if ( $dairyfarm_address ) : ?>
						<li><span class="df-footer__icon"><?php echo dairyfarm_icon( 'map-pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><span><?php echo nl2br( esc_html( $dairyfarm_address ) ); ?></span></li>
					<?php endif; ?>
					<?php if ( $dairyfarm_phone ) : ?>
						<li><span class="df-footer__icon"><?php echo dairyfarm_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $dairyfarm_phone ) ); ?>"><?php echo esc_html( $dairyfarm_phone ); ?></a></li>
					<?php endif; ?>
					<?php if ( $dairyfarm_email ) : ?>
						<li><span class="df-footer__icon"><?php echo dairyfarm_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><a href="mailto:<?php echo esc_attr( antispambot( $dairyfarm_email ) ); ?>"><?php echo esc_html( antispambot( $dairyfarm_email ) ); ?></a></li>
					<?php endif; ?>
				</ul>
				<?php if ( $dairyfarm_hours ) : ?>
					<div class="df-footer__hours">
						<?php echo dairyfarm_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span><small><?php esc_html_e( 'Opening hours', 'dairyfarm' ); ?></small><?php echo esc_html( $dairyfarm_hours ); ?></span>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<svg class="df-footer__hills" viewBox="0 0 1440 120" preserveAspectRatio="none" aria-hidden="true" focusable="false">
		<path d="M0 70C220 20 420 30 640 62S1060 96 1260 52C1340 36 1400 38 1440 46V120H0z" fill="#9fd08f" />
		<path d="M0 92C260 58 520 70 780 90S1200 110 1440 78V120H0z" fill="#6fb35f" />
		<path d="M0 108C300 94 620 100 900 108S1300 112 1440 102V120H0z" fill="#2e8b3e" />
	</svg>

	<div class="df-footer__bottom">
		<div class="df-container df-footer__bottom-inner">
			<p><?php echo esc_html( dairyfarm_copyright() ); ?></p>
			<?php
			if ( has_nav_menu( 'legal' ) ) {
				wp_nav_menu(
					array(
						'theme_location'       => 'legal',
						'container'            => 'nav',
						'container_aria_label' => __( 'Legal', 'dairyfarm' ),
						'menu_class'           => 'df-footer__legal',
						'depth'                => 1,
					)
				);
			} elseif ( get_privacy_policy_url() ) {
				the_privacy_policy_link( '<nav class="df-footer__legal">', '</nav>' );
			}
			?>
			<a class="df-footer__top" href="#main"><?php esc_html_e( 'Back to top', 'dairyfarm' ); ?><?php echo dairyfarm_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
