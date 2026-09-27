<?php
/**
 * One-click demo setup: home page, blog page, menus, sample products and reviews.
 *
 * Runs only when an administrator clicks the button in the admin notice shown after
 * activation. Nothing is created automatically, and existing content is never overwritten.
 *
 * @package Dairyfarm
 */

defined( 'ABSPATH' ) || exit;

/**
 * Admin notice offering the demo setup.
 */
function dairyfarm_demo_notice() {
	if ( ! current_user_can( 'manage_options' ) || get_option( 'dairyfarm_demo_state' ) ) {
		return;
	}

	$import  = wp_nonce_url( admin_url( 'admin-post.php?action=dairyfarm_demo&do=import' ), 'dairyfarm_demo' );
	$dismiss = wp_nonce_url( admin_url( 'admin-post.php?action=dairyfarm_demo&do=dismiss' ), 'dairyfarm_demo' );
	?>
	<div class="notice notice-info">
		<p><strong><?php esc_html_e( 'Welcome to the Dairyfarm theme!', 'dairyfarm' ); ?></strong></p>
		<p><?php esc_html_e( 'Set up the home page, menus, sample products and reviews in one click. Your existing pages and posts are not changed.', 'dairyfarm' ); ?></p>
		<p>
			<a class="button button-primary" href="<?php echo esc_url( $import ); ?>"><?php esc_html_e( 'Set up demo home page', 'dairyfarm' ); ?></a>
			<a class="button-link" style="margin-left:12px" href="<?php echo esc_url( $dismiss ); ?>"><?php esc_html_e( 'No thanks', 'dairyfarm' ); ?></a>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'dairyfarm_demo_notice' );

/**
 * Handles the notice actions.
 */
function dairyfarm_demo_handle() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to do this.', 'dairyfarm' ) );
	}
	check_admin_referer( 'dairyfarm_demo' );

	$do = isset( $_GET['do'] ) ? sanitize_key( $_GET['do'] ) : '';

	if ( 'import' === $do ) {
		$home_id = dairyfarm_demo_import();
		update_option( 'dairyfarm_demo_state', 'imported', false );
		wp_safe_redirect( get_edit_post_link( $home_id, 'url' ) );
		exit;
	}

	update_option( 'dairyfarm_demo_state', 'dismissed', false );
	wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url() );
	exit;
}
add_action( 'admin_post_dairyfarm_demo', 'dairyfarm_demo_handle' );

/**
 * Creates a page unless one with the same slug already exists.
 *
 * @param string $title   Title.
 * @param string $slug    Slug.
 * @param string $content Content.
 * @return int
 */
function dairyfarm_demo_page( $title, $slug, $content = '' ) {
	$existing = get_page_by_path( $slug );
	if ( $existing ) {
		return (int) $existing->ID;
	}
	return (int) wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => $title,
			'post_name'    => $slug,
			'post_content' => $content,
		)
	);
}

/**
 * Runs the import. Returns the home page ID.
 *
 * @return int
 */
function dairyfarm_demo_import() {
	// Pages.
	$home_id = dairyfarm_demo_page( __( 'Home', 'dairyfarm' ), 'home' );
	$blog_id = dairyfarm_demo_page( __( 'News', 'dairyfarm' ), 'news' );

	// An empty home page gets the meta box template; pages with existing content are left alone.
	if ( '' === trim( (string) get_post_field( 'post_content', $home_id ) ) && ! get_page_template_slug( $home_id ) ) {
		update_post_meta( $home_id, '_wp_page_template', DAIRYFARM_HOME_TEMPLATE );
	}

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $home_id );
	update_option( 'page_for_posts', $blog_id );

	// Product categories.
	$cats = array(
		'milk'   => __( 'Fresh Milk', 'dairyfarm' ),
		'cheese' => __( 'Cheese', 'dairyfarm' ),
		'butter' => __( 'Butter & Cream', 'dairyfarm' ),
		'yogurt' => __( 'Yogurt', 'dairyfarm' ),
	);
	foreach ( $cats as $slug => $name ) {
		if ( ! term_exists( $slug, 'df_product_cat' ) ) {
			wp_insert_term( $name, 'df_product_cat', array( 'slug' => $slug ) );
		}
	}

	// Products.
	if ( ! get_posts( array( 'post_type' => 'df_product', 'posts_per_page' => 1, 'post_status' => 'any', 'fields' => 'ids' ) ) ) {
		$products = array(
			array( __( 'Farm Fresh Whole Milk', 'dairyfarm' ), 'milk', '$3.49', '/ 1 litre', __( 'Bestseller', 'dairyfarm' ), __( 'Creamy, non-homogenised milk from grass-fed cows, bottled within hours of milking.', 'dairyfarm' ) ),
			array( __( 'Low-Fat Toned Milk', 'dairyfarm' ), 'milk', '$3.19', '/ 1 litre', '', __( 'All the goodness and calcium of whole milk with 60% less fat. Perfect for everyday tea and coffee.', 'dairyfarm' ) ),
			array( __( 'Aged Farmhouse Cheddar', 'dairyfarm' ), 'cheese', '$8.99', '/ 250 g', __( 'Award winner', 'dairyfarm' ), __( 'Matured for 12 months in our cellar for a sharp, nutty flavour and crumbly texture.', 'dairyfarm' ) ),
			array( __( 'Fresh Cottage Cheese', 'dairyfarm' ), 'cheese', '$5.49', '/ 400 g', '', __( 'Soft, high-protein curds made fresh every morning with nothing but milk and culture.', 'dairyfarm' ) ),
			array( __( 'Cultured Salted Butter', 'dairyfarm' ), 'butter', '$6.25', '/ 250 g', '', __( 'Slow-churned from ripened cream with a pinch of sea salt. Rich, golden and spreadable.', 'dairyfarm' ) ),
			array( __( 'Greek-Style Yogurt', 'dairyfarm' ), 'yogurt', '$4.75', '/ 500 g', __( 'New', 'dairyfarm' ), __( 'Thick, strained and naturally tangy with live cultures for healthy digestion.', 'dairyfarm' ) ),
		);

		foreach ( $products as $order => $p ) {
			$id = wp_insert_post(
				array(
					'post_type'    => 'df_product',
					'post_status'  => 'publish',
					'post_title'   => $p[0],
					'post_excerpt' => $p[5],
					'post_content' => '<!-- wp:paragraph --><p>' . esc_html( $p[5] ) . '</p><!-- /wp:paragraph -->',
					'menu_order'   => $order,
				)
			);
			if ( $id && ! is_wp_error( $id ) ) {
				wp_set_object_terms( $id, $p[1], 'df_product_cat' );
				update_post_meta( $id, '_df_price', $p[2] );
				update_post_meta( $id, '_df_unit', $p[3] );
				update_post_meta( $id, '_df_badge', $p[4] );
			}
		}
	}

	// Reviews.
	if ( ! get_posts( array( 'post_type' => 'df_testimonial', 'posts_per_page' => 1, 'post_status' => 'any', 'fields' => 'ids' ) ) ) {
		$reviews = array(
			array( 'Sarah Mitchell', __( 'Mother of three, Green Valley', 'dairyfarm' ), __( 'My kids finally love milk', 'dairyfarm' ), __( 'Since switching to their doorstep delivery, my children actually ask for milk. It tastes the way milk should — fresh, creamy and clean.', 'dairyfarm' ) ),
			array( 'David Chen', __( 'Owner, Harvest Table Café', 'dairyfarm' ), __( 'Consistent quality, every week', 'dairyfarm' ), __( 'We use their cream and butter across our whole menu. Deliveries are always on time and our customers notice the difference.', 'dairyfarm' ) ),
			array( 'Priya Raman', __( 'Customer since 2019', 'dairyfarm' ), __( 'The cheddar is outstanding', 'dairyfarm' ), __( 'We visited the farm on an open day and saw how well the cows are cared for. The aged cheddar is now a staple in our house.', 'dairyfarm' ) ),
		);

		foreach ( $reviews as $order => $r ) {
			$id = wp_insert_post(
				array(
					'post_type'    => 'df_testimonial',
					'post_status'  => 'publish',
					'post_title'   => $r[0],
					'post_content' => $r[3],
					'menu_order'   => $order,
				)
			);
			if ( $id && ! is_wp_error( $id ) ) {
				update_post_meta( $id, '_df_rating', 5 );
				update_post_meta( $id, '_df_role', $r[1] );
				update_post_meta( $id, '_df_highlight', $r[2] );
			}
		}
	}

	dairyfarm_demo_menus( $blog_id );

	return $home_id;
}

/**
 * Creates and assigns menus when the locations are empty.
 *
 * @param int $blog_id Blog page ID.
 */
function dairyfarm_demo_menus( $blog_id ) {
	$locations = get_theme_mod( 'nav_menu_locations', array() );

	$menus = array(
		'primary' => array(
			__( 'Main Menu', 'dairyfarm' ),
			array(
				array( __( 'Home', 'dairyfarm' ), home_url( '/' ) ),
				array( __( 'About', 'dairyfarm' ), '#about' ),
				array( __( 'Products', 'dairyfarm' ), '#products' ),
				array( __( 'Our Process', 'dairyfarm' ), '#process' ),
				array( __( 'Gallery', 'dairyfarm' ), '#gallery' ),
				array( __( 'Reviews', 'dairyfarm' ), '#reviews' ),
				array( __( 'News', 'dairyfarm' ), get_permalink( $blog_id ) ),
			),
		),
		'footer'  => array(
			__( 'Footer Quick Links', 'dairyfarm' ),
			array(
				array( __( 'About Us', 'dairyfarm' ), '#about' ),
				array( __( 'How We Farm', 'dairyfarm' ), '#process' ),
				array( __( 'Farm Gallery', 'dairyfarm' ), '#gallery' ),
				array( __( 'FAQs', 'dairyfarm' ), '#faq' ),
				array( __( 'News', 'dairyfarm' ), get_permalink( $blog_id ) ),
			),
		),
		'footer2' => array(
			__( 'Footer Products', 'dairyfarm' ),
			array(),
		),
	);

	foreach ( get_terms( array( 'taxonomy' => 'df_product_cat', 'hide_empty' => false ) ) as $term ) {
		$menus['footer2'][1][] = array( $term->name, get_term_link( $term ) );
	}

	foreach ( $menus as $location => $menu ) {
		if ( ! empty( $locations[ $location ] ) ) {
			continue;
		}

		$existing = wp_get_nav_menu_object( $menu[0] );
		$menu_id  = $existing ? (int) $existing->term_id : wp_create_nav_menu( $menu[0] );
		if ( is_wp_error( $menu_id ) ) {
			continue;
		}

		if ( ! $existing ) {
			foreach ( $menu[1] as $position => $item ) {
				if ( is_wp_error( $item[1] ) ) {
					continue;
				}
				wp_update_nav_menu_item(
					$menu_id,
					0,
					array(
						'menu-item-title'    => $item[0],
						'menu-item-url'      => $item[1],
						'menu-item-type'     => 'custom',
						'menu-item-status'   => 'publish',
						'menu-item-position' => $position + 1,
					)
				);
			}
		}

		$locations[ $location ] = $menu_id;
	}

	set_theme_mod( 'nav_menu_locations', $locations );
}
