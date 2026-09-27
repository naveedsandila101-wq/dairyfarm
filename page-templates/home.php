<?php
/**
 * Template Name: Home Page (sections in meta boxes)
 * Template Post Type: page
 *
 * Renders the home sections edited in the "Home Page Sections" meta box.
 * See inc/home-sections.php.
 *
 * @package Dairyfarm
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	echo '<div class="df-landing">';
	echo dairyfarm_render_home_sections( get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block render output.
	echo '</div>';
endwhile;

get_footer();
