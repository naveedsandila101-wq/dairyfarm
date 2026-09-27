<?php
/**
 * Home page. Renders the static front page's blocks edge-to-edge; each section block
 * handles its own container, so editors control the whole page from the block editor.
 *
 * @package Dairyfarm
 */

defined( 'ABSPATH' ) || exit;

// Front page set to "Your latest posts": fall back to the blog index.
if ( 'page' !== get_option( 'show_on_front' ) ) {
	require DAIRYFARM_DIR . '/index.php';
	return;
}

// front-page.php outranks page templates, so hand over to the Home Page template explicitly.
if ( dairyfarm_is_home_template( get_queried_object_id() ) ) {
	require DAIRYFARM_DIR . '/' . DAIRYFARM_HOME_TEMPLATE;
	return;
}

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<div class="df-landing">
		<?php
		if ( '' === trim( get_the_content() ) && current_user_can( 'edit_pages' ) ) {
			printf(
				'<div class="df-container df-empty"><p>%1$s</p><a class="df-btn df-btn--primary" href="%2$s">%3$s</a></div>',
				esc_html__( 'Your home page is empty. Open it in the editor and insert the "Dairy Farm – Complete Home Page" pattern, or add sections one by one from the "Dairy Farm Sections" category.', 'dairyfarm' ),
				esc_url( get_edit_post_link() ),
				esc_html__( 'Edit home page', 'dairyfarm' )
			);
		}
		the_content();
		?>
	</div>
	<?php
endwhile;

get_footer();
