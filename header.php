<?php
/**
 * Site header.
 *
 * @package Dairyfarm
 */

defined( 'ABSPATH' ) || exit;

$dairyfarm_phone = dairyfarm_mod( 'phone' );
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="df-skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'dairyfarm' ); ?></a>

<?php if ( dairyfarm_mod( 'topbar_enabled' ) ) : ?>
	<div class="df-topbar">
		<div class="df-container df-topbar__inner">
			<p class="df-topbar__text"><?php echo esc_html( dairyfarm_mod( 'topbar_text' ) ); ?></p>
			<ul class="df-topbar__meta" role="list">
				<?php if ( $dairyfarm_phone ) : ?>
					<li><?php echo dairyfarm_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $dairyfarm_phone ) ); ?>"><?php echo esc_html( $dairyfarm_phone ); ?></a></li>
				<?php endif; ?>
				<?php if ( dairyfarm_mod( 'hours' ) ) : ?>
					<li><?php echo dairyfarm_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( dairyfarm_mod( 'hours' ) ); ?></li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
<?php endif; ?>

<header class="df-header" data-header>
	<div class="df-container df-header__inner">
		<div class="df-header__brand">
			<?php dairyfarm_logo(); ?>
		</div>

		<nav class="df-nav" id="df-nav" aria-label="<?php esc_attr_e( 'Primary', 'dairyfarm' ); ?>" data-nav>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'df-nav__list',
					'depth'          => 2,
					'fallback_cb'    => 'dairyfarm_menu_fallback',
				)
			);
			?>
			<div class="df-nav__mobile-cta">
				<?php echo dairyfarm_button( dairyfarm_mod( 'header_cta_label' ), dairyfarm_mod( 'header_cta_url' ), 'primary' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</nav>

		<div class="df-header__actions">
			<?php echo dairyfarm_button( dairyfarm_mod( 'header_cta_label' ), dairyfarm_mod( 'header_cta_url' ), 'primary' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<button class="df-nav-toggle" type="button" aria-controls="df-nav" aria-expanded="false" data-nav-toggle>
				<span class="df-nav-toggle__open"><?php echo dairyfarm_icon( 'menu' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<span class="df-nav-toggle__close"><?php echo dairyfarm_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'dairyfarm' ); ?></span>
			</button>
		</div>
	</div>
</header>

<main id="main" class="df-main" tabindex="-1">
