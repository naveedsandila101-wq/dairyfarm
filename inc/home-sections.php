<?php
/**
 * "Home Page" template: every home section is edited in a meta box instead of the block editor.
 *
 * The meta box is generated from the same `dairyfarm.fields` schema the blocks use, and the
 * template renders each section through its block's render.php, so the block and meta box
 * versions of a section always look identical. Values live in one post meta array:
 *
 *   _df_home_sections = [ 'hero' => [ '_enabled' => true, 'heading' => '…', … ], … ]
 *
 * Anything never saved falls back to the block.json defaults, so a new page using the
 * template shows the full demo home page straight away.
 *
 * @package Dairyfarm
 */

defined( 'ABSPATH' ) || exit;

define( 'DAIRYFARM_HOME_TEMPLATE', 'page-templates/home.php' );
define( 'DAIRYFARM_HOME_META', '_df_home_sections' );

/**
 * Home page sections in display order.
 *
 * @return string[]
 */
function dairyfarm_home_sections() {
	return apply_filters( 'dairyfarm_home_sections', array( 'hero', 'features', 'about', 'products', 'process', 'stats', 'gallery', 'testimonials', 'faq', 'cta' ) );
}

/**
 * Title, fields and attribute defaults of one section, read from its registered block.
 *
 * @param string $slug Section slug.
 * @return array|null
 */
function dairyfarm_home_section_schema( $slug ) {
	static $cache = array();

	if ( array_key_exists( $slug, $cache ) ) {
		return $cache[ $slug ];
	}

	$type = WP_Block_Type_Registry::get_instance()->get_registered( 'dairyfarm/' . $slug );
	$json = DAIRYFARM_DIR . '/blocks/' . $slug . '/block.json';

	if ( ! $type || ! file_exists( $json ) ) {
		$cache[ $slug ] = null;
		return null;
	}

	$meta     = json_decode( (string) file_get_contents( $json ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	$defaults = array();
	foreach ( $type->attributes as $key => $attribute ) {
		$defaults[ $key ] = isset( $attribute['default'] ) ? $attribute['default'] : null;
	}

	$cache[ $slug ] = array(
		'title'    => $type->title,
		'fields'   => isset( $meta['dairyfarm']['fields'] ) ? $meta['dairyfarm']['fields'] : array(),
		'defaults' => $defaults,
	);

	return $cache[ $slug ];
}

/**
 * Settings every section shares, shown in its own group at the end of each section.
 *
 * @return array
 */
function dairyfarm_home_shared_fields() {
	$panel = __( 'Section Settings', 'dairyfarm' );

	return array(
		array(
			'key'     => 'tone',
			'type'    => 'select',
			'label'   => __( 'Background', 'dairyfarm' ),
			'panel'   => $panel,
			'options' => array(
				array( 'label' => __( 'White', 'dairyfarm' ), 'value' => 'white' ),
				array( 'label' => __( 'Cream', 'dairyfarm' ), 'value' => 'cream' ),
				array( 'label' => __( 'Mint', 'dairyfarm' ), 'value' => 'mint' ),
				array( 'label' => __( 'Dark', 'dairyfarm' ), 'value' => 'dark' ),
				array( 'label' => __( 'Brand green', 'dairyfarm' ), 'value' => 'brand' ),
			),
		),
		array(
			'key'   => 'sectionId',
			'type'  => 'text',
			'label' => __( 'Anchor ID', 'dairyfarm' ),
			'help'  => __( 'Used by menu links such as #products. Letters, numbers and dashes only.', 'dairyfarm' ),
			'panel' => $panel,
		),
	);
}

/**
 * Whether a page uses the Home Page template.
 *
 * @param int|WP_Post $post Post.
 * @return bool
 */
function dairyfarm_is_home_template( $post ) {
	return DAIRYFARM_HOME_TEMPLATE === get_page_template_slug( $post );
}

/*
 * ---------------------------------------------------------------------------
 * Front end
 * ---------------------------------------------------------------------------
 */

/**
 * Renders all enabled home sections of a page.
 *
 * @param int $post_id Page ID.
 * @return string
 */
function dairyfarm_render_home_sections( $post_id ) {
	$saved = get_post_meta( $post_id, DAIRYFARM_HOME_META, true );
	$saved = is_array( $saved ) ? $saved : array();
	$html  = '';

	foreach ( dairyfarm_home_sections() as $slug ) {
		$schema = dairyfarm_home_section_schema( $slug );
		if ( ! $schema ) {
			continue;
		}

		$values = isset( $saved[ $slug ] ) && is_array( $saved[ $slug ] ) ? $saved[ $slug ] : array();
		if ( isset( $values['_enabled'] ) && ! $values['_enabled'] ) {
			continue;
		}

		$html .= render_block(
			array(
				'blockName'    => 'dairyfarm/' . $slug,
				'attrs'        => array_intersect_key( $values, $schema['defaults'] ),
				'innerBlocks'  => array(),
				'innerHTML'    => '',
				'innerContent' => array(),
			)
		);
	}

	return $html;
}

/*
 * ---------------------------------------------------------------------------
 * Admin: meta box
 * ---------------------------------------------------------------------------
 */

/**
 * Pages using the template get a plain edit screen (title + meta boxes) instead of the block editor.
 */
function dairyfarm_home_remove_editor() {
	$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( $post_id && 'page' === get_post_type( $post_id ) && dairyfarm_is_home_template( $post_id ) ) {
		remove_post_type_support( 'page', 'editor' );
	}
}
add_action( 'load-post.php', 'dairyfarm_home_remove_editor' );

/**
 * Registers the meta box on every page; JS shows it only while the Home Page template is selected.
 */
function dairyfarm_home_add_meta_box() {
	add_meta_box(
		'dairyfarm-home-sections',
		__( 'Home Page Sections', 'dairyfarm' ),
		'dairyfarm_home_render_meta_box',
		'page',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_page', 'dairyfarm_home_add_meta_box' );

/**
 * Hides the box on first paint for pages not (yet) using the template.
 *
 * @param string[] $classes Postbox classes.
 * @return string[]
 */
function dairyfarm_home_postbox_classes( $classes ) {
	global $post;
	if ( ! $post || ! dairyfarm_is_home_template( $post ) ) {
		$classes[] = 'df-home-hidden';
	}
	return $classes;
}
add_filter( 'postbox_classes_page_dairyfarm-home-sections', 'dairyfarm_home_postbox_classes' );

/**
 * Admin assets for the meta box.
 *
 * @param string $hook Admin page.
 */
function dairyfarm_home_admin_assets( $hook ) {
	$screen = get_current_screen();
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) || ! $screen || 'page' !== $screen->post_type ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_style( 'dairyfarm-home-sections', DAIRYFARM_URI . '/assets/css/home-sections.css', array(), dairyfarm_asset_version( 'assets/css/home-sections.css' ) );
	wp_enqueue_script( 'dairyfarm-home-sections', DAIRYFARM_URI . '/assets/js/home-sections.js', array( 'jquery' ), dairyfarm_asset_version( 'assets/js/home-sections.js' ), true );
	wp_localize_script(
		'dairyfarm-home-sections',
		'dairyfarmHome',
		array(
			'template' => DAIRYFARM_HOME_TEMPLATE,
			'i18n'     => array(
				'choose' => __( 'Choose image', 'dairyfarm' ),
				'use'    => __( 'Use this image', 'dairyfarm' ),
				'max'    => __( 'You have reached the maximum number of items.', 'dairyfarm' ),
				'remove' => __( 'Remove this item?', 'dairyfarm' ),
			),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'dairyfarm_home_admin_assets' );

/**
 * Meta box markup: one collapsible panel per section, fields grouped by their `panel`.
 *
 * @param WP_Post $post Page.
 */
function dairyfarm_home_render_meta_box( $post ) {
	wp_nonce_field( 'dairyfarm_home_' . $post->ID, 'dairyfarm_home_nonce' );

	$saved = get_post_meta( $post->ID, DAIRYFARM_HOME_META, true );
	$saved = is_array( $saved ) ? $saved : array();

	echo '<p class="df-home-intro">' . esc_html__( 'Edit each home page section below. Untick "Show this section" to hide it. Images left empty use the theme\'s dummy illustrations.', 'dairyfarm' ) . '</p>';
	echo '<p class="df-home-intro df-home-intro--switch">' . esc_html__( 'Tip: save the page once after choosing the Home Page template. The block editor is then replaced by these fields.', 'dairyfarm' ) . '</p>';

	echo '<div class="df-home">';

	foreach ( dairyfarm_home_sections() as $slug ) {
		$schema = dairyfarm_home_section_schema( $slug );
		if ( ! $schema ) {
			continue;
		}

		$values  = isset( $saved[ $slug ] ) && is_array( $saved[ $slug ] ) ? $saved[ $slug ] : array();
		$values  = array_merge( $schema['defaults'], $values );
		$enabled = ! isset( $values['_enabled'] ) || $values['_enabled'];
		$prefix  = 'df_home[' . $slug . ']';

		// Group fields by panel, keeping the first-seen order.
		$groups = array();
		foreach ( array_merge( $schema['fields'], dairyfarm_home_shared_fields() ) as $field ) {
			$panel              = isset( $field['panel'] ) ? $field['panel'] : __( 'Content', 'dairyfarm' );
			$groups[ $panel ][] = $field;
		}

		printf(
			'<details class="df-home-section%1$s"><summary><span class="df-home-section__title">%2$s</span><span class="df-home-section__off">%3$s</span></summary><div class="df-home-section__body">',
			$enabled ? '' : ' is-disabled',
			esc_html( $schema['title'] ),
			esc_html__( 'Hidden', 'dairyfarm' )
		);

		printf(
			'<p class="df-home-toggle"><input type="hidden" name="%1$s[_enabled]" value="0" /><label><input type="checkbox" class="df-home-enable" name="%1$s[_enabled]" value="1"%2$s /> %3$s</label></p>',
			esc_attr( $prefix ),
			checked( $enabled, true, false ),
			esc_html__( 'Show this section', 'dairyfarm' )
		);

		if ( 'products' === $slug || 'testimonials' === $slug ) {
			printf(
				'<p class="description">%s</p>',
				'products' === $slug
					? esc_html__( 'The product cards come from Products in the admin menu. Set each product\'s image there.', 'dairyfarm' )
					: esc_html__( 'The review cards come from Reviews in the admin menu.', 'dairyfarm' )
			);
		}

		foreach ( $groups as $panel => $fields ) {
			echo '<fieldset class="df-home-group"><legend>' . esc_html( $panel ) . '</legend>';
			foreach ( $fields as $field ) {
				$value = array_key_exists( $field['key'], $values ) ? $values[ $field['key'] ] : null;
				dairyfarm_home_field( $field, $prefix . '[' . $field['key'] . ']', $value );
			}
			echo '</fieldset>';
		}

		echo '</div></details>';
	}

	echo '</div>';
}

/**
 * Outputs one field.
 *
 * @param array  $field Field schema.
 * @param string $name  Input name.
 * @param mixed  $value Current value.
 */
function dairyfarm_home_field( $field, $name, $value ) {
	$type  = isset( $field['type'] ) ? $field['type'] : 'text';
	$label = isset( $field['label'] ) ? $field['label'] : $field['key'];
	$help  = isset( $field['help'] ) ? '<span class="description">' . esc_html( $field['help'] ) . '</span>' : '';

	if ( 'repeater' === $type ) {
		dairyfarm_home_repeater( $field, $name, $value );
		return;
	}

	echo '<div class="df-field df-field--' . esc_attr( $type ) . '">';

	switch ( $type ) {
		case 'textarea':
			printf(
				'<label><span class="df-field__label">%1$s</span><textarea class="widefat" name="%2$s" rows="%3$d">%4$s</textarea></label>',
				esc_html( $label ),
				esc_attr( $name ),
				isset( $field['rows'] ) ? (int) $field['rows'] : 3,
				esc_textarea( (string) $value )
			);
			break;

		case 'number':
			printf(
				'<label><span class="df-field__label">%1$s</span><input type="number" class="small-text" name="%2$s" value="%3$s"%4$s%5$s /></label>',
				esc_html( $label ),
				esc_attr( $name ),
				esc_attr( (string) $value ),
				isset( $field['min'] ) ? ' min="' . esc_attr( $field['min'] ) . '"' : '',
				isset( $field['max'] ) ? ' max="' . esc_attr( $field['max'] ) . '"' : ''
			);
			break;

		case 'toggle':
			printf(
				'<input type="hidden" name="%1$s" value="0" /><label><input type="checkbox" name="%1$s" value="1"%2$s /> %3$s</label>',
				esc_attr( $name ),
				checked( (bool) $value, true, false ),
				esc_html( $label )
			);
			break;

		case 'select':
		case 'icon':
		case 'term':
			$options = dairyfarm_home_field_options( $field );
			printf( '<label><span class="df-field__label">%1$s</span><select name="%2$s">', esc_html( $label ), esc_attr( $name ) );
			foreach ( $options as $option ) {
				printf( '<option value="%1$s"%2$s>%3$s</option>', esc_attr( $option['value'] ), selected( (string) $value, (string) $option['value'], false ), esc_html( $option['label'] ) );
			}
			echo '</select></label>';
			break;

		case 'image':
			$id      = is_array( $value ) && ! empty( $value['id'] ) ? absint( $value['id'] ) : 0;
			$preview = $id ? wp_get_attachment_image_url( $id, 'medium' ) : '';
			printf(
				'<span class="df-field__label">%1$s</span><div class="df-image%2$s"><input type="hidden" name="%3$s[id]" value="%4$s" /><div class="df-image__preview">%5$s<span class="df-image__empty">%6$s</span></div><p><button type="button" class="button df-image__select">%7$s</button> <button type="button" class="button-link df-image__remove">%8$s</button></p></div>',
				esc_html( $label ),
				$id ? ' has-image' : '',
				esc_attr( $name ),
				esc_attr( $id ? (string) $id : '' ),
				$preview ? '<img src="' . esc_url( $preview ) . '" alt="" />' : '<img src="" alt="" hidden />',
				esc_html__( 'No image chosen: a dummy illustration is shown.', 'dairyfarm' ),
				esc_html__( 'Choose image', 'dairyfarm' ),
				esc_html__( 'Remove', 'dairyfarm' )
			);
			break;

		case 'url':
		case 'text':
		default:
			printf(
				'<label><span class="df-field__label">%1$s</span><input type="text" class="widefat" name="%2$s" value="%3$s"%4$s /></label>',
				esc_html( $label ),
				esc_attr( $name ),
				esc_attr( (string) $value ),
				'url' === $type ? ' placeholder="https:// or #section"' : ''
			);
			break;
	}

	echo $help; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
	echo '</div>';
}

/**
 * Choices for select, icon and term fields.
 *
 * @param array $field Field schema.
 * @return array[] { label, value }
 */
function dairyfarm_home_field_options( $field ) {
	if ( 'icon' === $field['type'] ) {
		return dairyfarm_icon_choices();
	}

	if ( 'term' === $field['type'] ) {
		$options = array(
			array(
				'label' => isset( $field['allLabel'] ) ? $field['allLabel'] : __( 'All', 'dairyfarm' ),
				'value' => '',
			),
		);
		$terms   = get_terms(
			array(
				'taxonomy'   => $field['taxonomy'],
				'hide_empty' => false,
			)
		);
		foreach ( is_array( $terms ) ? $terms : array() as $term ) {
			$options[] = array(
				'label' => $term->name,
				'value' => $term->slug,
			);
		}
		return $options;
	}

	return isset( $field['options'] ) ? $field['options'] : array();
}

/**
 * Repeater: collapsible rows plus a <template> the JS clones for new rows.
 *
 * @param array  $field Field schema.
 * @param string $name  Input name.
 * @param mixed  $value Rows.
 */
function dairyfarm_home_repeater( $field, $name, $value ) {
	$rows       = is_array( $value ) ? array_values( $value ) : array();
	$item_label = isset( $field['itemLabel'] ) ? $field['itemLabel'] : __( 'Item', 'dairyfarm' );

	printf(
		'<div class="df-field df-repeater" data-max="%1$d" data-title-key="%2$s"><span class="df-field__label">%3$s</span>%4$s<div class="df-repeater__rows">',
		isset( $field['max'] ) ? (int) $field['max'] : 0,
		esc_attr( isset( $field['titleKey'] ) ? $field['titleKey'] : '' ),
		esc_html( $field['label'] ),
		isset( $field['help'] ) ? '<p class="description">' . esc_html( $field['help'] ) . '</p>' : ''
	);

	foreach ( $rows as $index => $row ) {
		dairyfarm_home_repeater_row( $field, $name . '[' . $index . ']', is_array( $row ) ? $row : array(), $item_label );
	}

	echo '</div><template class="df-repeater__tpl">';
	dairyfarm_home_repeater_row( $field, $name . '[__i__]', array(), $item_label, true );
	echo '</template>';

	printf( '<button type="button" class="button df-repeater__add">%s</button></div>', esc_html( sprintf( /* translators: %s: item label, e.g. "Question". */ __( 'Add %s', 'dairyfarm' ), $item_label ) ) );
}

/**
 * One repeater row.
 *
 * @param array  $field      Repeater schema.
 * @param string $name       Row input name prefix.
 * @param array  $row        Row values.
 * @param string $item_label Row label.
 * @param bool   $open       Start expanded (new rows).
 */
function dairyfarm_home_repeater_row( $field, $name, $row, $item_label, $open = false ) {
	$title_key = isset( $field['titleKey'] ) ? $field['titleKey'] : '';
	$title     = $title_key && ! empty( $row[ $title_key ] ) && is_string( $row[ $title_key ] ) ? $row[ $title_key ] : $item_label;

	printf(
		'<details class="df-repeater__row"%1$s><summary><span class="df-repeater__title">%2$s</span><span class="df-repeater__tools"><button type="button" class="button-link df-repeater__up" aria-label="%3$s">&uarr;</button><button type="button" class="button-link df-repeater__down" aria-label="%4$s">&darr;</button><button type="button" class="button-link df-repeater__remove">%5$s</button></span></summary><div class="df-repeater__fields">',
		$open ? ' open' : '',
		esc_html( wp_strip_all_tags( $title ) ),
		esc_attr__( 'Move up', 'dairyfarm' ),
		esc_attr__( 'Move down', 'dairyfarm' ),
		esc_html__( 'Remove', 'dairyfarm' )
	);

	foreach ( $field['fields'] as $sub ) {
		$default = isset( $sub['default'] ) ? $sub['default'] : null;
		dairyfarm_home_field( $sub, $name . '[' . $sub['key'] . ']', array_key_exists( $sub['key'], $row ) ? $row[ $sub['key'] ] : $default );
	}

	echo '</div></details>';
}

/*
 * ---------------------------------------------------------------------------
 * Admin: saving
 * ---------------------------------------------------------------------------
 */

/**
 * Saves the sections when the page uses the Home Page template.
 *
 * @param int $post_id Page ID.
 */
function dairyfarm_home_save( $post_id ) {
	if ( ! isset( $_POST['dairyfarm_home_nonce'], $_POST['df_home'] ) || ! wp_verify_nonce( sanitize_key( $_POST['dairyfarm_home_nonce'] ), 'dairyfarm_home_' . $post_id ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_page', $post_id ) ) {
		return;
	}
	// The (hidden) box is posted with every page; only store it for pages that use the template.
	if ( ! dairyfarm_is_home_template( $post_id ) ) {
		return;
	}

	$input = wp_unslash( $_POST['df_home'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized per field below.
	if ( ! is_array( $input ) ) {
		return;
	}

	$data = array();

	foreach ( dairyfarm_home_sections() as $slug ) {
		$schema = dairyfarm_home_section_schema( $slug );
		if ( ! $schema || ! isset( $input[ $slug ] ) || ! is_array( $input[ $slug ] ) ) {
			continue;
		}

		$section = array( '_enabled' => ! empty( $input[ $slug ]['_enabled'] ) );

		foreach ( array_merge( $schema['fields'], dairyfarm_home_shared_fields() ) as $field ) {
			$key = $field['key'];
			if ( array_key_exists( $key, $input[ $slug ] ) ) {
				$section[ $key ] = dairyfarm_home_sanitize( $field, $input[ $slug ][ $key ] );
			}
		}

		$data[ $slug ] = $section;
	}

	update_post_meta( $post_id, DAIRYFARM_HOME_META, $data );
}
add_action( 'save_post_page', 'dairyfarm_home_save' );

/**
 * Sanitizes one value according to its field type.
 *
 * @param array $field Field schema.
 * @param mixed $value Raw value.
 * @return mixed
 */
function dairyfarm_home_sanitize( $field, $value ) {
	switch ( isset( $field['type'] ) ? $field['type'] : 'text' ) {
		case 'repeater':
			$rows = array();
			foreach ( is_array( $value ) ? $value : array() as $raw ) {
				if ( ! is_array( $raw ) ) {
					continue;
				}
				$row = array();
				foreach ( $field['fields'] as $sub ) {
					$row[ $sub['key'] ] = dairyfarm_home_sanitize( $sub, isset( $raw[ $sub['key'] ] ) ? $raw[ $sub['key'] ] : '' );
				}
				$rows[] = $row;
			}
			return empty( $field['max'] ) ? $rows : array_slice( $rows, 0, (int) $field['max'] );

		case 'image':
			$id = is_array( $value ) && isset( $value['id'] ) ? absint( $value['id'] ) : 0;
			return $id && wp_attachment_is_image( $id ) ? array( 'id' => $id ) : array();

		case 'number':
			$number = is_numeric( $value ) ? (int) $value : 0;
			if ( isset( $field['min'] ) ) {
				$number = max( (int) $field['min'], $number );
			}
			if ( isset( $field['max'] ) ) {
				$number = min( (int) $field['max'], $number );
			}
			return $number;

		case 'toggle':
			return ! empty( $value );

		case 'select':
		case 'icon':
		case 'term':
			$allowed = wp_list_pluck( dairyfarm_home_field_options( $field ), 'value' );
			$value   = is_string( $value ) ? $value : '';
			return in_array( $value, array_map( 'strval', $allowed ), true ) ? $value : (string) reset( $allowed );

		case 'url':
			return esc_url_raw( trim( (string) $value ) );

		case 'textarea':
		case 'text':
		default:
			if ( 'sectionId' === $field['key'] ) {
				return sanitize_title( (string) $value );
			}
			return wp_kses_post( is_string( $value ) ? $value : '' );
	}
}
