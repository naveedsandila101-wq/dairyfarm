<?php
/**
 * Fallback template: blog index, archives and search results.
 *
 * @package Dairyfarm
 */

defined( 'ABSPATH' ) || exit;

get_header();

$dairyfarm_subtitle = '';
if ( is_archive() && get_the_archive_description() ) {
	$dairyfarm_subtitle = wp_strip_all_tags( get_the_archive_description() );
}

get_template_part(
	'template-parts/page-header',
	null,
	array(
		'title'    => dairyfarm_archive_title(),
		'subtitle' => $dairyfarm_subtitle,
	)
);
?>
<section class="df-section df-archive">
	<div class="df-container">
		<?php if ( is_search() ) : ?>
			<div class="df-archive__search"><?php get_search_form(); ?></div>
		<?php endif; ?>

		<?php if ( have_posts() ) : ?>
			<ul class="df-grid df-grid--3" role="list">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content', 'card' );
				endwhile;
				?>
			</ul>

			<?php
			the_posts_pagination(
				array(
					'mid_size'  => 1,
					'prev_text' => dairyfarm_icon( 'chevron-left' ) . '<span class="screen-reader-text">' . esc_html__( 'Previous page', 'dairyfarm' ) . '</span>',
					'next_text' => '<span class="screen-reader-text">' . esc_html__( 'Next page', 'dairyfarm' ) . '</span>' . dairyfarm_icon( 'chevron-right' ),
				)
			);
			?>
		<?php else : ?>
			<div class="df-empty">
				<h2><?php esc_html_e( 'Nothing found', 'dairyfarm' ); ?></h2>
				<p><?php esc_html_e( 'We couldn’t find what you were looking for. Try a different search.', 'dairyfarm' ); ?></p>
				<?php get_search_form(); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
