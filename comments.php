<?php
/**
 * Comments template.
 *
 * @package Dairyfarm
 */

defined( 'ABSPATH' ) || exit;

if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="df-comments">
	<?php if ( have_comments() ) : ?>
		<h2 class="df-comments__title">
			<?php
			$dairyfarm_count = get_comments_number();
			/* translators: %s: comment count. */
			echo esc_html( sprintf( _n( '%s comment', '%s comments', $dairyfarm_count, 'dairyfarm' ), number_format_i18n( $dairyfarm_count ) ) );
			?>
		</h2>

		<ol class="df-comments__list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 48,
				)
			);
			?>
		</ol>

		<?php the_comments_navigation(); ?>

		<?php if ( ! comments_open() ) : ?>
			<p class="df-comments__closed"><?php esc_html_e( 'Comments are closed.', 'dairyfarm' ); ?></p>
		<?php endif; ?>
	<?php endif; ?>

	<?php
	comment_form(
		array(
			'class_submit' => 'df-btn df-btn--primary',
		)
	);
	?>
</section>
