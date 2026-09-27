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
				if ( ! has_nav_menu( $dairyfarm_location ) ) {
					continue;
				}
				?>
				<nav class="df-footer__col" aria-label="<?php echo esc_attr( $dairyfarm_heading ); ?>">
					<h2 class="df-footer__heading"><?php echo esc_html( $dairyfarm_heading ); ?></h2>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => $dairyfarm_location,
							'container'      => false,
							'menu_class'     => 'df-footer__links',
							'depth'          => 1,
						)
					);
					?>
				</nav>
			<?php endforeach; ?>

			<div class="df-footer__col">
				<h2 class="df-footer__heading"><?php echo esc_html( dairyfarm_mod( 'footer_col3' ) ); ?></h2>
				<ul class="df-footer__contact" role="list">
					<?php if ( $dairyfarm_address ) : ?>
						<li><?php echo dairyfarm_icon( 'map-pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo nl2br( esc_html( $dairyfarm_address ) ); ?></span></li>
					<?php endif; ?>
					<?php if ( $dairyfarm_phone ) : ?>
						<li><?php echo dairyfarm_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $dairyfarm_phone ) ); ?>"><?php echo esc_html( $dairyfarm_phone ); ?></a></li>
					<?php endif; ?>
					<?php if ( $dairyfarm_email ) : ?>
						<li><?php echo dairyfarm_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><a href="mailto:<?php echo esc_attr( antispambot( $dairyfarm_email ) ); ?>"><?php echo esc_html( antispambot( $dairyfarm_email ) ); ?></a></li>
					<?php endif; ?>
					<?php if ( dairyfarm_mod( 'hours' ) ) : ?>
						<li><?php echo dairyfarm_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo esc_html( dairyfarm_mod( 'hours' ) ); ?></span></li>
					<?php endif; ?>
				</ul>
			</div>
		</div>

		<div class="df-footer__bottom">
			<p><?php echo esc_html( dairyfarm_copyright() ); ?></p>
			<?php
			if ( has_nav_menu( 'legal' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'legal',
						'container'      => 'nav',
						'container_aria_label' => __( 'Legal', 'dairyfarm' ),
						'menu_class'     => 'df-footer__legal',
						'depth'          => 1,
					)
				);
			} elseif ( get_privacy_policy_url() ) {
				the_privacy_policy_link( '<nav class="df-footer__legal">', '</nav>' );
			}
			?>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
