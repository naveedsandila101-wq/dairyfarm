<?php
/**
 * Default page template.
 *
 * @package Dairyfarm
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	get_template_part(
		'template-parts/page-header',
		null,
		array( 'subtitle' => has_excerpt() ? get_the_excerpt() : '' )
	);
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'df-article' ); ?>>
		<div class="df-container">
			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="df-article__media">
					<?php the_post_thumbnail( 'dairyfarm-hero', array( 'loading' => 'eager', 'fetchpriority' => 'high', 'sizes' => '(min-width: 1200px) 1140px, 100vw' ) ); ?>
				</figure>
			<?php endif; ?>

			<div class="df-entry-content">
				<?php
				the_content();
				wp_link_pages(
					array(
						'before' => '<nav class="df-page-links">' . esc_html__( 'Pages:', 'dairyfarm' ),
						'after'  => '</nav>',
					)
				);
				?>
			</div>

			<?php
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
			?>
		</div>
	</article>
	<?php
endwhile;

get_footer();
