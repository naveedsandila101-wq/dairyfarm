<?php
/**
 * Section-based page templates (Home, About Us, Contact Us): every section of the page is
 * edited in a "Page Sections" meta box instead of the block editor.
 *
 * The meta box is generated from the same `dairyfarm.fields` schema the blocks use, and the
 * templates render each section through its block's render.php, so the block and meta box
 * versions of a section always look identical. Values live in one post meta array:
 *
 *   _df_sections = [ 'hero' => [ '_enabled' => true, 'heading' => '…', … ], … ]
 *
 * Anything never saved falls back to the template's defaults (block.json defaults plus the
 * per-template overrides below), so a new page shows a complete design straight away.
 *
 * @package Dairyfarm
 */

defined( 'ABSPATH' ) || exit;

define( 'DAIRYFARM_HOME_TEMPLATE', 'page-templates/home.php' );
define( 'DAIRYFARM_SECTIONS_META', '_df_sections' );

/**
 * Section templates: key => [ file, label, sections => [ slug => default overrides ] ].
 * Sections render in the order listed. `_enabled => false` hides a section by default.
 *
 * @return array
 */
function dairyfarm_section_templates() {
	$templates = array(
		'home'    => array(
			'file'     => DAIRYFARM_HOME_TEMPLATE,
			'label'    => __( 'Home Page Sections', 'dairyfarm' ),
			'sections' => array(
				'hero'         => array(),
				'features'     => array(),
				'about'        => array(),
				'products'     => array(),
				'process'      => array(),
				'stats'        => array(),
				'gallery'      => array(),
				'testimonials' => array(),
				'faq'          => array(),
				'cta'          => array(),
			),
		),
		'about'   => array(
			'file'     => 'page-templates/about.php',
			'label'    => __( 'About Us Page Sections', 'dairyfarm' ),
			'sections' => array(
				'page-banner'  => array(
					'eyebrow' => __( 'About Us', 'dairyfarm' ),
					'heading' => __( 'Rooted in family, <span>raised on pasture</span>', 'dairyfarm' ),
					'text'    => __( 'For three generations our family has farmed this valley with one simple belief: happy, healthy cows on healthy land give the best milk you have ever tasted.', 'dairyfarm' ),
					'dummy'   => 'calves',
				),
				'about'        => array(
					'tone'      => 'white',
					'sectionId' => 'story',
					'eyebrow'   => __( 'Our Story', 'dairyfarm' ),
				),
				'stats'        => array(),
				'features'     => array(
					'eyebrow' => __( 'Our Values', 'dairyfarm' ),
					'heading' => __( 'What we stand for', 'dairyfarm' ),
					'intro'   => __( 'The principles that guide every decision on our farm, from the pasture to your doorstep.', 'dairyfarm' ),
					'items'   => array(
						array( 'icon' => 'sprout', 'title' => __( 'Care for the Land', 'dairyfarm' ), 'text' => __( 'Regenerative grazing, no synthetic pesticides and hedgerows full of wildlife.', 'dairyfarm' ) ),
						array( 'icon' => 'heart', 'title' => __( 'Animal Welfare', 'dairyfarm' ), 'text' => __( 'Our cows live outdoors most of the year, with vet care and plenty of space.', 'dairyfarm' ) ),
						array( 'icon' => 'users', 'title' => __( 'Community First', 'dairyfarm' ), 'text' => __( 'We hire locally, host school visits and support the valley food bank.', 'dairyfarm' ) ),
						array( 'icon' => 'shield', 'title' => __( 'Honest Quality', 'dairyfarm' ), 'text' => __( 'Every batch is lab-tested and fully traceable back to our own herd.', 'dairyfarm' ) ),
					),
				),
				'process'      => array( 'tone' => 'cream' ),
				'team'         => array(),
				'testimonials' => array( 'tone' => 'mint' ),
				'cta'          => array(
					'eyebrow'      => __( 'Visit Us', 'dairyfarm' ),
					'heading'      => __( 'Come and meet the herd', 'dairyfarm' ),
					'text'         => __( 'Our farm shop is open six days a week, and every month we host a family open day with tractor rides and milking demos.', 'dairyfarm' ),
					'primaryLabel' => __( 'Get in Touch', 'dairyfarm' ),
					'primaryUrl'   => home_url( '/contact/' ),
					'note'         => __( 'Free entry · Families welcome', 'dairyfarm' ),
				),
			),
		),
		'contact' => array(
			'file'     => 'page-templates/contact.php',
			'label'    => __( 'Contact Us Page Sections', 'dairyfarm' ),
			'sections' => array(
				'page-banner' => array(
					'eyebrow' => __( 'Contact Us', 'dairyfarm' ),
					'heading' => __( 'We would love to <span>hear from you</span>', 'dairyfarm' ),
					'text'    => __( 'Questions about deliveries, a wholesale enquiry or planning a farm visit? Send us a message and our family team will reply within one working day.', 'dairyfarm' ),
					'dummy'   => 'openday',
				),
				'contact'     => array(),
				'faq'         => array( 'tone' => 'cream' ),
				'cta'         => array( '_enabled' => false ),
			),
		),
	);

	return apply_filters( 'dairyfarm_section_templates', $templates );
}

/**
 * Home page sections in display order (used by the block pattern and demo importer).
 *
 * @return string[]
 */
function dairyfarm_home_sections() {
	$templates = dairyfarm_section_templates();
	return array_keys( $templates['home']['sections'] );
}

/**
 * The section template a page uses, or null.
 *
 * @param int|WP_Post $post Page.
 * @return array|null Template config plus its `key`.
 */
function dairyfarm_page_section_template( $post ) {
	$file = get_page_template_slug( $post );
	if ( ! $file ) {
		return null;
	}
	foreach ( dairyfarm_section_templates() as $key => $template ) {
		if ( $template['file'] === $file ) {
			return array_merge( $template, array( 'key' => $key ) );
		}
	}
	return null;
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

/**
 * Title, fields and attribute defaults of one section, read from its registered block.
 *
 * @param string $slug Section slug.
 * @return array|null
 */
function dairyfarm_section_schema( $slug ) {
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
 * Settings every section shares, shown in their own group at the end of each section.
 *
 * @return array
 */
function dairyfarm_section_shared_fields() {
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
 * Saved section values of a page. Falls back to the pre-1.2 meta key used by the Home template.
 *
 * @param int $post_id Page ID.
 * @return array
 */
function dairyfarm_get_page_sections( $post_id ) {
	$saved = get_post_meta( $post_id, DAIRYFARM_SECTIONS_META, true );
	if ( ! is_array( $saved ) || ! $saved ) {
		$saved = get_post_meta( $post_id, '_df_home_sections', true );
	}
	return is_array( $saved ) ? $saved : array();
}

/**
 * Values of one section: block defaults < template overrides < saved values.
 *
 * @param string $slug      Section slug.
 * @param array  $overrides Template overrides.
 * @param array  $saved     Saved values for the section.
 * @return array
 */
function dairyfarm_section_values( $slug, $overrides, $saved ) {
	$schema = dairyfarm_section_schema( $slug );
	return array_merge( $schema ? $schema['defaults'] : array(), $overrides, $saved );
}

/*
 * ---------------------------------------------------------------------------
 * Front end
 * ---------------------------------------------------------------------------
 */

/**
 * Renders all enabled sections of a page that uses a section template.
 *
 * @param int $post_id Page ID.
 * @return string
 */
function dairyfarm_render_page_sections( $post_id ) {
	$template = dairyfarm_page_section_template( $post_id );
	if ( ! $template ) {
		return '';
	}

	$saved = dairyfarm_get_page_sections( $post_id );
	$html  = '';

	foreach ( $template['sections'] as $slug => $overrides ) {
		$schema = dairyfarm_section_schema( $slug );
		if ( ! $schema ) {
			continue;
		}

		$values = dairyfarm_section_values( $slug, $overrides, isset( $saved[ $slug ] ) && is_array( $saved[ $slug ] ) ? $saved[ $slug ] : array() );
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

/**
 * Shared body of the section page templates.
 */
function dairyfarm_section_template_body() {
	get_header();

	while ( have_posts() ) :
		the_post();
		echo '<div class="df-landing">';
		echo dairyfarm_render_page_sections( get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block render output.
		echo '</div>';
	endwhile;

	get_footer();
}

/*
 * ---------------------------------------------------------------------------
 * Admin: meta box
 * ---------------------------------------------------------------------------
 */

/**
 * Pages using a section template get a plain edit screen (title + meta boxes) instead of the block editor.
 */
function dairyfarm_sections_remove_editor() {
	$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( $post_id && 'page' === get_post_type( $post_id ) && dairyfarm_page_section_template( $post_id ) ) {
		remove_post_type_support( 'page', 'editor' );
	}
}
add_action( 'load-post.php', 'dairyfarm_sections_remove_editor' );

/**
 * Registers the meta box on every page; JS shows it only while a section template is selected.
 *
 * @param WP_Post $post Page.
 */
function dairyfarm_sections_add_meta_box( $post ) {
	$template = dairyfarm_page_section_template( $post );

	add_meta_box(
		'dairyfarm-page-sections',
		$template ? $template['label'] : __( 'Page Sections', 'dairyfarm' ),
		'dairyfarm_sections_render_meta_box',
		'page',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_page', 'dairyfarm_sections_add_meta_box' );

/**
 * Hides the box on first paint for pages not (yet) using a section template.
 *
 * @param string[] $classes Postbox classes.
 * @return string[]
 */
function dairyfarm_sections_postbox_classes( $classes ) {
	global $post;
	if ( ! $post || ! dairyfarm_page_section_template( $post ) ) {
		$classes[] = 'df-sections-hidden';
	}
	return $classes;
}
add_filter( 'postbox_classes_page_dairyfarm-page-sections', 'dairyfarm_sections_postbox_classes' );

/**
 * Admin assets for the meta box.
 *
 * @param string $hook Admin page.
 */
function dairyfarm_sections_admin_assets( $hook ) {
	$screen = get_current_screen();
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) || ! $screen || 'page' !== $screen->post_type ) {
		return;
	}

	$templates = array();
	foreach ( dairyfarm_section_templates() as $key => $template ) {
		$templates[ $template['file'] ] = array(
			'key'   => $key,
			'label' => $template['label'],
		);
	}

	wp_enqueue_media();
	wp_enqueue_style( 'dairyfarm-page-sections', DAIRYFARM_URI . '/assets/css/page-sections.css', array(), dairyfarm_asset_version( 'assets/css/page-sections.css' ) );
	wp_enqueue_script( 'dairyfarm-page-sections', DAIRYFARM_URI . '/assets/js/page-sections.js', array( 'jquery' ), dairyfarm_asset_version( 'assets/js/page-sections.js' ), true );
	wp_localize_script(
		'dairyfarm-page-sections',
		'dairyfarmSections',
		array(
			'templates' => $templates,
			'i18n'      => array(
				'choose' => __( 'Choose image', 'dairyfarm' ),
				'use'    => __( 'Use this image', 'dairyfarm' ),
				'max'    => __( 'You have reached the maximum number of items.', 'dairyfarm' ),
				'remove' => __( 'Remove this item?', 'dairyfarm' ),
			),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'dairyfarm_sections_admin_assets' );

/**
 * Meta box markup. A page already on a section template gets just that template's fields;
 * any other page gets every template's fields (hidden), so choosing a template in the
 * block editor shows the right set straight away.
 *
 * @param WP_Post $post Page.
 */
function dairyfarm_sections_render_meta_box( $post ) {
	wp_nonce_field( 'dairyfarm_sections_' . $post->ID, 'dairyfarm_sections_nonce' );

	$current   = dairyfarm_page_section_template( $post );
	$templates = dairyfarm_section_templates();
	if ( $current ) {
		$templates = array( $current['key'] => $templates[ $current['key'] ] );
	}

	$saved = dairyfarm_get_page_sections( $post->ID );

	echo '<p class="df-sections-intro">' . esc_html__( 'Edit each section below. Untick "Show this section" to hide it. Images left empty use the theme\'s dummy illustrations.', 'dairyfarm' ) . '</p>';
	echo '<p class="df-sections-intro df-sections-intro--switch">' . esc_html__( 'Tip: save the page once after choosing the template. The block editor is then replaced by these fields.', 'dairyfarm' ) . '</p>';
	echo '<p class="notice notice-info inline df-sections-reload" hidden>' . esc_html__( 'Update the page to load the fields for the newly selected template.', 'dairyfarm' ) . '</p>';

	foreach ( $templates as $key => $template ) {
		printf( '<div class="df-sections" data-template="%s">', esc_attr( $template['file'] ) );

		foreach ( $template['sections'] as $slug => $overrides ) {
			$schema = dairyfarm_section_schema( $slug );
			if ( ! $schema ) {
				continue;
			}

			$values  = dairyfarm_section_values( $slug, $overrides, $current && isset( $saved[ $slug ] ) && is_array( $saved[ $slug ] ) ? $saved[ $slug ] : array() );
			$enabled = ! isset( $values['_enabled'] ) || $values['_enabled'];
			$prefix  = 'df_sections[' . $key . '][' . $slug . ']';

			// Group fields by panel, keeping the first-seen order.
			$groups = array();
			foreach ( array_merge( $schema['fields'], dairyfarm_section_shared_fields() ) as $field ) {
				$panel              = isset( $field['panel'] ) ? $field['panel'] : __( 'Content', 'dairyfarm' );
				$groups[ $panel ][] = $field;
			}

			printf(
				'<details class="df-sections__item%1$s"><summary><span class="df-sections__title">%2$s</span><span class="df-sections__off">%3$s</span></summary><div class="df-sections__body">',
				$enabled ? '' : ' is-disabled',
				esc_html( $schema['title'] ),
				esc_html__( 'Hidden', 'dairyfarm' )
			);

			printf(
				'<p class="df-sections__toggle"><input type="hidden" name="%1$s[_enabled]" value="0" /><label><input type="checkbox" class="df-sections__enable" name="%1$s[_enabled]" value="1"%2$s /> %3$s</label></p>',
				esc_attr( $prefix ),
				checked( $enabled, true, false ),
				esc_html__( 'Show this section', 'dairyfarm' )
			);

			$notes = array(
				'products'     => __( 'The product cards come from Products in the admin menu. Set each product\'s image there.', 'dairyfarm' ),
				'testimonials' => __( 'The review cards come from Reviews in the admin menu.', 'dairyfarm' ),
				'contact'      => __( 'Phone, email, address and opening hours come from Appearance → Customize → Dairyfarm Theme Options → Business & Contact Details. Messages from the built-in form are emailed to that address.', 'dairyfarm' ),
			);
			if ( isset( $notes[ $slug ] ) ) {
				printf( '<p class="description">%s</p>', esc_html( $notes[ $slug ] ) );
			}

			foreach ( $groups as $panel => $fields ) {
				echo '<fieldset class="df-sections__group"><legend>' . esc_html( $panel ) . '</legend>';
				foreach ( $fields as $field ) {
					$value = array_key_exists( $field['key'], $values ) ? $values[ $field['key'] ] : null;
					dairyfarm_section_field( $field, $prefix . '[' . $field['key'] . ']', $value );
				}
				echo '</fieldset>';
			}

			echo '</div></details>';
		}

		echo '</div>';
	}
}

/**
 * Outputs one field.
 *
 * @param array  $field Field schema.
 * @param string $name  Input name.
 * @param mixed  $value Current value.
 */
function dairyfarm_section_field( $field, $name, $value ) {
	$type  = isset( $field['type'] ) ? $field['type'] : 'text';
	$label = isset( $field['label'] ) ? $field['label'] : $field['key'];
	$help  = isset( $field['help'] ) ? '<span class="description">' . esc_html( $field['help'] ) . '</span>' : '';

	if ( 'repeater' === $type ) {
		dairyfarm_section_repeater( $field, $name, $value );
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
			$options = dairyfarm_section_field_options( $field );
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
function dairyfarm_section_field_options( $field ) {
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
function dairyfarm_section_repeater( $field, $name, $value ) {
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
		dairyfarm_section_repeater_row( $field, $name . '[' . $index . ']', is_array( $row ) ? $row : array(), $item_label );
	}

	echo '</div><template class="df-repeater__tpl">';
	dairyfarm_section_repeater_row( $field, $name . '[__i__]', array(), $item_label, true );
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
function dairyfarm_section_repeater_row( $field, $name, $row, $item_label, $open = false ) {
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
		dairyfarm_section_field( $sub, $name . '[' . $sub['key'] . ']', array_key_exists( $sub['key'], $row ) ? $row[ $sub['key'] ] : $default );
	}

	echo '</div></details>';
}

/*
 * ---------------------------------------------------------------------------
 * Admin: saving
 * ---------------------------------------------------------------------------
 */

/**
 * Saves the sections of the template the page uses.
 *
 * @param int $post_id Page ID.
 */
function dairyfarm_sections_save( $post_id ) {
	if ( ! isset( $_POST['dairyfarm_sections_nonce'], $_POST['df_sections'] ) || ! wp_verify_nonce( sanitize_key( $_POST['dairyfarm_sections_nonce'] ), 'dairyfarm_sections_' . $post_id ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_page', $post_id ) ) {
		return;
	}

	// The box is posted with every page; only store the set matching the page's template.
	$template = dairyfarm_page_section_template( $post_id );
	$input    = wp_unslash( $_POST['df_sections'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized per field below.
	if ( ! $template || ! is_array( $input ) || ! isset( $input[ $template['key'] ] ) || ! is_array( $input[ $template['key'] ] ) ) {
		return;
	}
	$input = $input[ $template['key'] ];

	$data = array();

	foreach ( array_keys( $template['sections'] ) as $slug ) {
		$schema = dairyfarm_section_schema( $slug );
		if ( ! $schema || ! isset( $input[ $slug ] ) || ! is_array( $input[ $slug ] ) ) {
			continue;
		}

		$section = array( '_enabled' => ! empty( $input[ $slug ]['_enabled'] ) );

		foreach ( array_merge( $schema['fields'], dairyfarm_section_shared_fields() ) as $field ) {
			$key = $field['key'];
			if ( array_key_exists( $key, $input[ $slug ] ) ) {
				$section[ $key ] = dairyfarm_section_sanitize( $field, $input[ $slug ][ $key ] );
			}
		}

		$data[ $slug ] = $section;
	}

	update_post_meta( $post_id, DAIRYFARM_SECTIONS_META, $data );
	delete_post_meta( $post_id, '_df_home_sections' );
}
add_action( 'save_post_page', 'dairyfarm_sections_save' );

/**
 * Sanitizes one value according to its field type.
 *
 * @param array $field Field schema.
 * @param mixed $value Raw value.
 * @return mixed
 */
function dairyfarm_section_sanitize( $field, $value ) {
	switch ( isset( $field['type'] ) ? $field['type'] : 'text' ) {
		case 'repeater':
			$rows = array();
			foreach ( is_array( $value ) ? $value : array() as $raw ) {
				if ( ! is_array( $raw ) ) {
					continue;
				}
				$row = array();
				foreach ( $field['fields'] as $sub ) {
					$row[ $sub['key'] ] = dairyfarm_section_sanitize( $sub, isset( $raw[ $sub['key'] ] ) ? $raw[ $sub['key'] ] : '' );
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
			$allowed = wp_list_pluck( dairyfarm_section_field_options( $field ), 'value' );
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
