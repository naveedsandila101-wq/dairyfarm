<?php
/**
 * Contact cards, contact form and map.
 *
 * The built-in form posts to admin-post.php (see dairyfarm_contact_submit() in
 * inc/contact-form.php); a form plugin shortcode can replace it.
 *
 * @package Dairyfarm
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$dairyfarm_phone   = dairyfarm_mod( 'phone' );
$dairyfarm_email   = dairyfarm_mod( 'email' );
$dairyfarm_address = dairyfarm_mod( 'address' );
$dairyfarm_hours   = dairyfarm_mod( 'hours' );
$dairyfarm_cards   = array();

if ( $dairyfarm_phone ) {
	$dairyfarm_cards[] = array( 'phone', __( 'Call us', 'dairyfarm' ), '<a href="tel:' . esc_attr( preg_replace( '/[^\d+]/', '', $dairyfarm_phone ) ) . '">' . esc_html( $dairyfarm_phone ) . '</a>' );
}
if ( $dairyfarm_email ) {
	$dairyfarm_cards[] = array( 'mail', __( 'Email us', 'dairyfarm' ), '<a href="mailto:' . esc_attr( antispambot( $dairyfarm_email ) ) . '">' . esc_html( antispambot( $dairyfarm_email ) ) . '</a>' );
}
if ( $dairyfarm_address ) {
	$dairyfarm_cards[] = array( 'map-pin', __( 'Visit the farm', 'dairyfarm' ), nl2br( esc_html( $dairyfarm_address ) ) );
}
if ( $dairyfarm_hours ) {
	$dairyfarm_cards[] = array( 'clock', __( 'Opening hours', 'dairyfarm' ), esc_html( $dairyfarm_hours ) );
}

$dairyfarm_map_query = trim( (string) $attributes['mapAddress'] );
$dairyfarm_map_query = '' !== $dairyfarm_map_query ? $dairyfarm_map_query : str_replace( "\n", ', ', (string) $dairyfarm_address );
$dairyfarm_shortcode = trim( (string) $attributes['formShortcode'] );
$dairyfarm_status    = isset( $_GET['df_contact'] ) ? sanitize_key( $_GET['df_contact'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display only.
?>
<section <?php echo dairyfarm_section_attributes( $attributes, 'contact' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="df-container">
		<?php if ( $attributes['showDetails'] && $dairyfarm_cards ) : ?>
			<ul class="df-contact__cards" role="list">
				<?php foreach ( $dairyfarm_cards as $dairyfarm_card ) : ?>
					<li class="df-card df-contact__card" data-reveal>
						<span class="df-icon-badge"><?php echo dairyfarm_icon( $dairyfarm_card[0] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<span class="df-contact__card-label"><?php echo esc_html( $dairyfarm_card[1] ); ?></span>
						<span class="df-contact__card-value"><?php echo $dairyfarm_card[2]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<div class="df-contact__grid<?php echo $attributes['showMap'] && $dairyfarm_map_query ? '' : ' df-contact__grid--single'; ?>">
			<div class="df-card df-contact__form" data-reveal>
				<?php
				echo dairyfarm_section_heading( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					array(
						'eyebrow' => $attributes['eyebrow'],
						'heading' => $attributes['heading'],
						'intro'   => $attributes['intro'],
					),
					'start'
				);

				if ( $dairyfarm_shortcode ) :
					echo do_shortcode( $dairyfarm_shortcode ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				else :
					if ( 'sent' === $dairyfarm_status ) :
						?>
						<p class="df-form__notice df-form__notice--success" role="status"><?php echo dairyfarm_icon( 'check-circle' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( $attributes['successMessage'] ); ?></p>
					<?php elseif ( $dairyfarm_status ) : ?>
						<p class="df-form__notice df-form__notice--error" role="alert"><?php echo esc_html( dairyfarm_contact_error_message( $dairyfarm_status ) ); ?></p>
					<?php endif; ?>

					<form class="df-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
						<input type="hidden" name="action" value="dairyfarm_contact" />
						<input type="hidden" name="df_page" value="<?php echo esc_attr( (string) get_queried_object_id() ); ?>" />
						<?php wp_nonce_field( 'dairyfarm_contact', 'df_contact_nonce' ); ?>
						<div class="df-form__hp" aria-hidden="true">
							<label><?php esc_html_e( 'Leave this field empty', 'dairyfarm' ); ?><input type="text" name="df_website" tabindex="-1" autocomplete="off" /></label>
						</div>
						<div class="df-form__row">
							<label class="df-form__field">
								<span><?php esc_html_e( 'Your name', 'dairyfarm' ); ?> <abbr title="<?php esc_attr_e( 'required', 'dairyfarm' ); ?>">*</abbr></span>
								<input type="text" name="df_name" required autocomplete="name" maxlength="100" />
							</label>
							<label class="df-form__field">
								<span><?php esc_html_e( 'Email address', 'dairyfarm' ); ?> <abbr title="<?php esc_attr_e( 'required', 'dairyfarm' ); ?>">*</abbr></span>
								<input type="email" name="df_email" required autocomplete="email" maxlength="150" />
							</label>
						</div>
						<div class="df-form__row">
							<label class="df-form__field">
								<span><?php esc_html_e( 'Phone', 'dairyfarm' ); ?></span>
								<input type="tel" name="df_phone" autocomplete="tel" maxlength="40" />
							</label>
							<label class="df-form__field">
								<span><?php esc_html_e( 'Subject', 'dairyfarm' ); ?></span>
								<select name="df_subject">
									<?php foreach ( dairyfarm_contact_subjects() as $dairyfarm_subject ) : ?>
										<option><?php echo esc_html( $dairyfarm_subject ); ?></option>
									<?php endforeach; ?>
								</select>
							</label>
						</div>
						<label class="df-form__field">
							<span><?php esc_html_e( 'Message', 'dairyfarm' ); ?> <abbr title="<?php esc_attr_e( 'required', 'dairyfarm' ); ?>">*</abbr></span>
							<textarea name="df_message" rows="5" required maxlength="5000"></textarea>
						</label>
						<button type="submit" class="df-btn df-btn--primary"><?php echo esc_html( $attributes['submitLabel'] ); ?><?php echo dairyfarm_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
					</form>
				<?php endif; ?>
			</div>

			<?php if ( $attributes['showMap'] && $dairyfarm_map_query ) : ?>
				<div class="df-contact__map" data-reveal>
					<iframe
						title="<?php esc_attr_e( 'Map showing the farm location', 'dairyfarm' ); ?>"
						src="<?php echo esc_url( 'https://maps.google.com/maps?q=' . rawurlencode( $dairyfarm_map_query ) . '&z=14&output=embed' ); ?>"
						loading="lazy"
						referrerpolicy="no-referrer-when-downgrade"
						allowfullscreen></iframe>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
