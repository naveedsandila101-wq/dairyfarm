<?php
/**
 * Template Name: Landing Page (full width, no title)
 * Template Post Type: page
 *
 * For campaign or inner pages built entirely from Dairy Farm section blocks.
 *
 * @package Dairyfarm
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	echo '<div class="df-landing">';
	the_content();
	echo '</div>';
endwhile;

get_footer();
