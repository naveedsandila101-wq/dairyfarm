<?php
/**
 * Dairyfarm theme bootstrap.
 *
 * @package Dairyfarm
 */

defined( 'ABSPATH' ) || exit;

define( 'DAIRYFARM_VERSION', '1.0.0' );
define( 'DAIRYFARM_DIR', get_template_directory() );
define( 'DAIRYFARM_URI', get_template_directory_uri() );

$dairyfarm_includes = array(
	'inc/helpers.php',
	'inc/icons.php',
	'inc/setup.php',
	'inc/enqueue.php',
	'inc/customizer.php',
	'inc/post-types.php',
	'inc/meta-boxes.php',
	'inc/blocks.php',
	'inc/template-tags.php',
	'inc/schema.php',
	'inc/demo-import.php',
);

foreach ( $dairyfarm_includes as $dairyfarm_file ) {
	require_once DAIRYFARM_DIR . '/' . $dairyfarm_file;
}
