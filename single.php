<?php
/**
 * Single post template (also used for single products).
 *
 * @package Dairyfarm
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$dairyfarm_is_post = 'post' === get_post_type();
	$dairyfarm_cats    = $dairyfarm_is_post ? get_the_category() : array();

	get_template_part(
		'template-parts/page-header',
		null,
		array(
			'eyebrow'  => $dairyfarm_cats ? $dairyfarm_cats[0]->name : '',
			'subtitle' => has_excerpt() ? get_the_excerpt() : '',
			'meta'     => $dairyfarm_is_post,
		)
	);
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'df-article' ); ?>>
		<div class="df-container">
			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="df-article__media">
					<?php the_post_thumbnail( 'dairyfarm-hero', array( 'loading' => 'eager', 'fetchpriority' => 'high', 'sizes' => '(min-width: 1200px) 1140px, 100vw' ) ); ?>
				</figure>
			<?php endif; ?>

			<?php if ( 'df_product' === get_post_type() && get_post_meta( get_the_ID(), '_df_price', true ) ) : ?>
				<p class="df-entry-price">
					<strong><?php echo esc_html( get_post_meta( get_the_ID(), '_df_price', true ) ); ?></strong>
					<span><?php echo esc_html( get_post_meta( get_the_ID(), '_df_unit', true ) ); ?></span>
				</p>
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

			<?php if ( $dairyfarm_is_post ) : ?>
				<footer class="df-entry-footer">
					<?php the_tags( '<ul class="df-tags" role="list"><li>', '</li><li>', '</li></ul>' ); ?>

					<div class="df-author">
						<?php echo get_avatar( get_the_author_meta( 'ID' ), 64, '', '', array( 'class' => 'df-avatar' ) ); ?>
						<div>
							<p class="df-author__label"><?php esc_html_e( 'Written by', 'dairyfarm' ); ?></p>
							<p class="df-author__name"><?php the_author(); ?></p>
							<?php if ( get_the_author_meta( 'description' ) ) : ?>
								<p class="df-author__bio"><?php echo esc_html( get_the_author_meta( 'description' ) ); ?></p>
							<?php endif; ?>
						</div>
					</div>

					<?php
					the_post_navigation(
						array(
							'prev_text' => '<span class="df-post-nav__label">' . esc_html__( 'Previous', 'dairyfarm' ) . '</span><span class="df-post-nav__title">%title</span>',
							'next_text' => '<span class="df-post-nav__label">' . esc_html__( 'Next', 'dairyfarm' ) . '</span><span class="df-post-nav__title">%title</span>',
						)
					);
					?>
				</footer>
			<?php endif; ?>

			<?php
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
			?>
		</div>
	</article>

	<?php
	if ( $dairyfarm_is_post ) :
		$dairyfarm_related = new WP_Query(
			array(
				'post_type'           => 'post',
				'posts_per_page'      => 3,
				'post__not_in'        => array( get_the_ID() ),
				'category__in'        => wp_list_pluck( $dairyfarm_cats, 'term_id' ),
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			)
		);

		if ( $dairyfarm_related->have_posts() ) :
			?>
			<section class="df-section df-tone--cream df-related" aria-labelledby="df-related-title">
				<div class="df-container">
					<h2 id="df-related-title" class="df-related__title"><?php esc_html_e( 'You might also enjoy', 'dairyfarm' ); ?></h2>
					<ul class="df-grid df-grid--3" role="list">
						<?php
						while ( $dairyfarm_related->have_posts() ) :
							$dairyfarm_related->the_post();
							get_template_part( 'template-parts/content', 'card' );
						endwhile;
						wp_reset_postdata();
						?>
					</ul>
				</div>
			</section>
			<?php
		endif;
	endif;
endwhile;

get_footer();
