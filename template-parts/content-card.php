<?php
/**
 * Post card used in archives and related posts.
 *
 * @package Dairyfarm
 */

defined( 'ABSPATH' ) || exit;

$dairyfarm_cats = 'post' === get_post_type() ? get_the_category() : array();
?>
<li <?php post_class( 'df-card df-post-card' ); ?> data-reveal>
	<a class="df-post-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php
		if ( has_post_thumbnail() ) {
			the_post_thumbnail( 'dairyfarm-card', array( 'sizes' => '(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw' ) );
		} else {
			echo dairyfarm_image( dairyfarm_with_dummy( array(), 'pasture' ), 'dairyfarm-card' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		?>
	</a>
	<div class="df-post-card__body">
		<p class="df-post-card__meta">
			<?php if ( $dairyfarm_cats ) : ?>
				<span class="df-post-card__cat"><?php echo esc_html( $dairyfarm_cats[0]->name ); ?></span>
			<?php endif; ?>
			<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
		</p>
		<h3 class="df-post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<p class="df-post-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>
		<a class="df-link-arrow" href="<?php the_permalink(); ?>">
			<?php esc_html_e( 'Read more', 'dairyfarm' ); ?><span class="screen-reader-text"> <?php the_title(); ?></span><?php echo dairyfarm_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</a>
	</div>
</li>
