<?php
/**
 * Search form.
 *
 * @package Dairyfarm
 */

defined( 'ABSPATH' ) || exit;

$dairyfarm_id = wp_unique_id( 'df-search-' );
?>
<form role="search" method="get" class="df-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $dairyfarm_id ); ?>"><?php esc_html_e( 'Search for:', 'dairyfarm' ); ?></label>
	<input type="search" id="<?php echo esc_attr( $dairyfarm_id ); ?>" class="df-search__input" placeholder="<?php esc_attr_e( 'Search…', 'dairyfarm' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" />
	<button type="submit" class="df-search__submit"><?php echo dairyfarm_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span class="screen-reader-text"><?php esc_html_e( 'Search', 'dairyfarm' ); ?></span></button>
</form>
