<?php
/**
 * Built-in contact form handler used by the Contact section (blocks/contact).
 *
 * Messages are emailed with wp_mail() to the email address in Customize → Dairyfarm Theme Options → Business & Contact Details
 * (or the site admin email). Spam protection: nonce, honeypot field and a per-IP rate limit.
 * For reliable delivery on live sites, pair with an SMTP plugin.
 *
 * @package Dairyfarm
 */

defined( 'ABSPATH' ) || exit;

/**
 * Subject choices in the form.
 *
 * @return string[]
 */
function dairyfarm_contact_subjects() {
	return apply_filters(
		'dairyfarm_contact_subjects',
		array(
			__( 'General enquiry', 'dairyfarm' ),
			__( 'Deliveries & subscriptions', 'dairyfarm' ),
			__( 'Wholesale & cafés', 'dairyfarm' ),
			__( 'Farm visits & events', 'dairyfarm' ),
		)
	);
}

/**
 * Human-readable message for an error code in the ?df_contact= query arg.
 *
 * @param string $code Error code.
 * @return string
 */
function dairyfarm_contact_error_message( $code ) {
	$messages = array(
		'missing' => __( 'Please fill in your name, a valid email address and a message.', 'dairyfarm' ),
		'expired' => __( 'Your session expired. Please send the form again.', 'dairyfarm' ),
		'limit'   => __( 'You have sent several messages in a short time. Please try again in a few minutes.', 'dairyfarm' ),
		'failed'  => __( 'Sorry, your message could not be sent. Please call or email us directly.', 'dairyfarm' ),
	);
	return isset( $messages[ $code ] ) ? $messages[ $code ] : $messages['failed'];
}

/**
 * Handles a form submission, then redirects back to the page with a status.
 */
function dairyfarm_contact_submit() {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- verified below, after resolving the redirect target.
	$page_id  = isset( $_POST['df_page'] ) ? absint( $_POST['df_page'] ) : 0;
	$back     = $page_id && 'publish' === get_post_status( $page_id ) ? get_permalink( $page_id ) : wp_get_referer();
	$back     = $back ? remove_query_arg( 'df_contact', $back ) : home_url( '/' );
	$redirect = static function ( $status ) use ( $back ) {
		wp_safe_redirect( add_query_arg( 'df_contact', $status, $back ) . '#get-in-touch' );
		exit;
	};

	if ( ! isset( $_POST['df_contact_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['df_contact_nonce'] ), 'dairyfarm_contact' ) ) {
		$redirect( 'expired' );
	}

	// Honeypot: bots fill every field. Pretend success so they learn nothing.
	if ( ! empty( $_POST['df_website'] ) ) {
		$redirect( 'sent' );
	}

	$name    = isset( $_POST['df_name'] ) ? sanitize_text_field( wp_unslash( $_POST['df_name'] ) ) : '';
	$email   = isset( $_POST['df_email'] ) ? sanitize_email( wp_unslash( $_POST['df_email'] ) ) : '';
	$phone   = isset( $_POST['df_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['df_phone'] ) ) : '';
	$subject = isset( $_POST['df_subject'] ) ? sanitize_text_field( wp_unslash( $_POST['df_subject'] ) ) : '';
	$message = isset( $_POST['df_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['df_message'] ) ) : '';
	// phpcs:enable

	if ( '' === $name || ! is_email( $email ) || '' === trim( $message ) ) {
		$redirect( 'missing' );
	}
	if ( ! in_array( $subject, dairyfarm_contact_subjects(), true ) ) {
		$subject = __( 'General enquiry', 'dairyfarm' );
	}

	// At most 5 messages per IP every 10 minutes.
	$ip    = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$key   = 'df_contact_' . md5( $ip );
	$count = (int) get_transient( $key );
	if ( $count >= 5 ) {
		$redirect( 'limit' );
	}
	set_transient( $key, $count + 1, 10 * MINUTE_IN_SECONDS );

	$to = dairyfarm_mod( 'email' );
	$to = is_email( $to ) ? $to : get_option( 'admin_email' );

	$body = implode(
		"\n",
		array(
			/* translators: %s: sender name. */
			sprintf( __( 'Name: %s', 'dairyfarm' ), $name ),
			/* translators: %s: sender email. */
			sprintf( __( 'Email: %s', 'dairyfarm' ), $email ),
			/* translators: %s: sender phone. */
			sprintf( __( 'Phone: %s', 'dairyfarm' ), $phone ? $phone : '–' ),
			/* translators: %s: subject. */
			sprintf( __( 'Subject: %s', 'dairyfarm' ), $subject ),
			'',
			$message,
			'',
			'—',
			/* translators: %s: page URL. */
			sprintf( __( 'Sent from %s', 'dairyfarm' ), $back ),
		)
	);

	$sent = wp_mail(
		$to,
		/* translators: 1: site name, 2: subject. */
		sprintf( __( '[%1$s] New message: %2$s', 'dairyfarm' ), wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ), $subject ),
		$body,
		array( 'Reply-To: ' . str_replace( array( "\r", "\n", '<', '>' ), '', $name ) . ' <' . $email . '>' )
	);

	$redirect( $sent ? 'sent' : 'failed' );
}
add_action( 'admin_post_dairyfarm_contact', 'dairyfarm_contact_submit' );
add_action( 'admin_post_nopriv_dairyfarm_contact', 'dairyfarm_contact_submit' );
