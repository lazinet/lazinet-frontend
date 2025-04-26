<?php
/**
 * Main RedParts theme class.
 *
 * @package RedParts
 * @since 1.0.0
 */

namespace RedParts;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'RedParts\Theme' ) ) {
	/**
	 * Class Theme.
	 */
	class Theme extends Singleton {
		/**
		 * Array of theme modules.
		 *
		 * @var array
		 */
		public $modules = array(
			'RedParts\Settings',
			'RedParts\Customizer',
			'RedParts\Checkout',
			'RedParts\Header',
			'RedParts\Product',
			'RedParts\Product_Card',
			'RedParts\Shop',
			'RedParts\WooCommerce',
			'RedParts\Import',
			'RedParts\Style',
			'RedParts\Menu',
			'RedParts\Footer',
			'RedParts\Sputnik',
			'RedParts\Cart',
		);

		/**
		 * Initialization.
		 */
		public function init() {
			add_action( 'after_setup_theme', array( $this, 'disable_woocs_ads' ) );
			add_action( 'after_setup_theme', array( $this, 'init_content_width' ), 0 );
			add_action( 'after_setup_theme', array( $this, 'init_theme' ) );
			add_action( 'after_setup_theme', array( $this, 'init_gutenberg' ) );
			add_action( 'after_setup_theme', array( $this, 'init_menus' ) );
			add_action( 'widgets_init', array( $this, 'init_sidebars' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'wp_head', array( $this, 'pingback_link' ) );

			foreach ( $this->modules as $module ) {
				$module::instance()->init();
			}
		}

		/**
		 * Disables woocs ads.
		 *
		 * @noinspection PhpUnused
		 */
		public function disable_woocs_ads() {
			// phpcs:disable WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
			global $WOOCS;

			if ( ! empty( $WOOCS ) ) {
				remove_action( 'init', array( $WOOCS, 'init_marketig_woocs' ) );
			}
			// phpcs:enable
		}

		/**
		 * Set the content width in pixels, based on the theme's design and stylesheet.
		 *
		 * Priority 0 to make it available to lower priority callbacks.
		 *
		 * @noinspection PhpUnused
		 *
		 * @global int $content_width
		 */
		public function init_content_width() {
			global $content_width;

			$content_width = 1350;
		}

		/**
		 * Sets up theme defaults and registers support for various WordPress features.
		 *
		 * @noinspection PhpUnused
		 */
		public function init_theme() {
			load_theme_textdomain( 'redparts', get_template_directory() . '/languages' );

			add_theme_support( 'automatic-feed-links' );
			add_theme_support( 'title-tag' );
			add_theme_support( 'post-thumbnails' );
			add_theme_support( 'customize-selective-refresh-widgets' );
			add_theme_support(
				'html5',
				array(
					'search-form',
					'comment-form',
					'comment-list',
					'gallery',
					'caption',
				)
			);
			add_theme_support(
				'custom-background',
				apply_filters(
					'redparts_custom_background_args',
					array(
						'default-color' => 'ffffff',
						'default-image' => '',
					)
				)
			);
			add_theme_support(
				'custom-logo',
				array(
					'height'      => 250,
					'width'       => 250,
					'flex-width'  => true,
					'flex-height' => true,
				)
			);
		}

		/**
		 * Initializes Gutenberg.
		 *
		 * @noinspection PhpUnused
		 */
		public function init_gutenberg() {
			$direction = is_rtl() ? '-rtl' : '-ltr';
			$font_url  = 'https://fonts.googleapis.com/css?family=Roboto:400,400i,500,500i,700,700i';
			$font_url  = str_replace( ',', '%2C', $font_url );

			add_theme_support( 'editor-styles' );
			add_theme_support( 'align-wide' );
			add_theme_support( 'responsive-embeds' );

			add_editor_style( 'assets/css/editor' . $direction . '.min.css' );
			add_editor_style( $font_url );
		}

		/**
		 * Registers navigation menus.
		 *
		 * @noinspection PhpUnused
		 */
		public function init_menus() {
			register_nav_menu( 'redparts-main', esc_html_x( 'Main', 'Admin', 'redparts' ) );
			register_nav_menu( 'redparts-topbar-start', esc_html_x( 'Topbar Start', 'Admin', 'redparts' ) );
			register_nav_menu( 'redparts-topbar-end', esc_html_x( 'Topbar End', 'Admin', 'redparts' ) );
			register_nav_menu( 'redparts-departments', esc_html_x( 'Departments', 'Admin', 'redparts' ) );
			register_nav_menu( 'redparts-mobile-menu', esc_html_x( 'Mobile Menu', 'Admin', 'redparts' ) );
		}

		/**
		 * Registers sidebars.
		 *
		 * @noinspection PhpUnused
		 */
		public function init_sidebars() {
			// Footer Widgets.
			register_sidebar(
				array(
					'name'          => esc_html_x( 'Footer Widgets', 'Admin', 'redparts' ),
					'id'            => 'redparts-footer',
					'description'   => esc_html_x( 'Add three widgets to the site footer.', 'Admin', 'redparts' ),
					'before_widget' => '<div id="%1$s" class="th-site-footer__widget th-site-footer__widget-placeholder %2$s">',
					'after_widget'  => '</div>',
					'before_title'  => '<h5 class="th-site-footer__widget-title">',
					'after_title'   => '</h5>',
				)
			);

			// Blog Sidebar.
			register_sidebar(
				array(
					'name'          => esc_html_x( 'Blog Sidebar', 'Admin', 'redparts' ),
					'id'            => 'redparts-blog',
					'description'   => esc_html_x( 'Widgets in this area will be shown in the blog pages.', 'Admin', 'redparts' ),
					'before_widget' => '<section id="%1$s" class="th-sidebar__widget th-widget %2$s">',
					'after_widget'  => '</section>',
					'before_title'  => '<h5 class="th-sidebar__widget-title th-widget__title">',
					'after_title'   => '</h5>',
				)
			);

			// Shop Filters.
			register_sidebar(
				array(
					'name'          => esc_html_x( 'Shop Filters', 'Admin', 'redparts' ),
					'id'            => 'redparts-filters',
					'description'   => esc_html_x( 'Add product filters widgets here.', 'Admin', 'redparts' ),
					'before_widget' => '<div id="%1$s" class="th-widget-filters__item %2$s"><div class="th-filter th-filter--open" data-collapse-item>',
					'after_widget'  => '</div></div></div></div>',
					'before_title'  => '<button type="button" class="th-filter__title" data-collapse-trigger>',
					'after_title'   => '<span class="th-filter__arrow">' . redparts_get_icon( 'arrow-rounded-down-12x7' ) . '</span></button><div class="th-filter__body" data-collapse-content><div class="th-filter__container">',
				)
			);

			// Shop Sidebar.
			register_sidebar(
				array(
					'name'          => esc_html_x( 'Shop Sidebar', 'Admin', 'redparts' ),
					'id'            => 'redparts-shop',
					'description'   => esc_html_x( 'Widgets in this area will be shown in the shop pages.', 'Admin', 'redparts' ),
					'before_widget' => '<section id="%1$s" class="th-sidebar__widget th-widget %2$s">',
					'after_widget'  => '</section>',
					'before_title'  => '<h5 class="th-sidebar__widget-title th-widget__title">',
					'after_title'   => '</h5>',
				)
			);

			// Product Sidebar.
			register_sidebar(
				array(
					'name'          => esc_html_x( 'Product Sidebar', 'Admin', 'redparts' ),
					'id'            => 'redparts-product',
					'description'   => esc_html_x( 'Widgets in this area will be shown in the product pages.', 'Admin', 'redparts' ),
					'before_widget' => '<section id="%1$s" class="th-sidebar__widget th-widget %2$s">',
					'after_widget'  => '</section>',
					'before_title'  => '<h5 class="th-sidebar__widget-title th-widget__title">',
					'after_title'   => '</h5>',
				)
			);

			// Product Alternative Sidebar.
			register_sidebar(
				array(
					'name'          => esc_html_x( 'Product Alternative Sidebar', 'Admin', 'redparts' ),
					'id'            => 'redparts-product-alt',
					'description'   => esc_html_x( 'Widgets in this area will be shown in the product pages.', 'Admin', 'redparts' ),
					'before_widget' => '<section id="%1$s" class="th-sidebar__widget th-widget %2$s">',
					'after_widget'  => '</section>',
					'before_title'  => '<h5 class="th-sidebar__widget-title th-widget__title">',
					'after_title'   => '</h5>',
				)
			);
		}

		/**
		 * Enqueue frontend scripts.
		 */
		public function enqueue_scripts() {
			$direction     = is_rtl() ? '-rtl' : '-ltr';
			$header_layout = Header::instance()->get_layout();

			// Register scripts.
			wp_register_script(
				'owl-carousel',
				get_template_directory_uri() . '/assets/vendor/owl-carousel/owl.carousel.min.js',
				array( 'jquery' ),
				'2.3.4',
				true
			);
			wp_register_script(
				'popperjs',
				get_template_directory_uri() . '/assets/vendor/popperjs/umd/popper.min.js',
				array(),
				'2.5.4',
				true
			);
			wp_register_script(
				'tippy.js',
				get_template_directory_uri() . '/assets/vendor/tippy.js/tippy-bundle.umd.min.js',
				array( 'popperjs' ),
				'6.2.7',
				true
			);
			wp_register_script(
				'redparts-number',
				get_template_directory_uri() . '/assets/js/number.js',
				array( 'jquery' ),
				RED_PARTS_VERSION,
				true
			);
			wp_register_script(
				'redparts-block-products-carousel',
				get_template_directory_uri() . '/assets/js/block-products-carousel.js',
				array( 'jquery', 'owl-carousel' ),
				RED_PARTS_VERSION,
				true
			);
			wp_register_script(
				'redparts-main',
				get_template_directory_uri() . '/assets/js/main.js',
				array( 'jquery', 'owl-carousel', 'tippy.js', 'redparts-number' ),
				RED_PARTS_VERSION,
				true
			);
			wp_localize_script(
				'redparts-main',
				'redPartsVars',
				array(
					'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				)
			);

			// Enqueue scripts.
			if ( ! class_exists( 'RedParts\Sputnik\Plugin' ) ) {
				wp_enqueue_script( 'redparts-block-products-carousel' );
			}

			wp_enqueue_script( 'redparts-main' );

			if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
				wp_enqueue_script( 'comment-reply' );
			}

			// Register styles.
			wp_register_style(
				'owl-carousel',
				get_template_directory_uri() . '/assets/vendor/owl-carousel/assets/owl.carousel.min.css',
				array(),
				'2.3.4'
			);
			wp_register_style(
				'redparts-header-desktop',
				get_template_directory_uri() . '/assets/css/header-desktop-' . $header_layout . $direction . '.min.css',
				array(),
				RED_PARTS_VERSION,
				'(min-width: 1200px)'
			);
			wp_register_style(
				'redparts-header-mobile',
				get_template_directory_uri() . '/assets/css/header-mobile' . $direction . '.min.css',
				array(),
				RED_PARTS_VERSION,
				'(max-width: 1199px)'
			);
			wp_register_style(
				'redparts-main',
				get_template_directory_uri() . '/assets/css/style' . $direction . '.min.css',
				array( 'redparts-header-desktop', 'redparts-header-mobile', 'owl-carousel' ),
				RED_PARTS_VERSION
			);
			wp_register_style(
				'redparts-style-css',
				get_template_directory_uri() . '/style.css',
				array( 'redparts-main' ),
				RED_PARTS_VERSION
			);
			wp_register_style(
				'redparts-child-style-css',
				get_stylesheet_uri(),
				array( 'redparts-style-css' ),
				RED_PARTS_VERSION
			);

			// Enqueue styles.
			wp_enqueue_style( 'redparts-main' );
			wp_add_inline_style( 'redparts-main', Style::instance()->css() );
			wp_enqueue_style( 'redparts-style-css' );

			if ( is_child_theme() ) {
				wp_enqueue_style( 'redparts-child-style-css' );
			}
		}

		/**
		 * Returns an array of theme settings.
		 *
		 * @return array
		 */
		public function settings(): array {
			global $redparts_settings;

			if ( empty( $redparts_settings ) ) {
				return array();
			}

			return $redparts_settings;
		}

		/**
		 * Returns unique ID.
		 *
		 * @param string $prefix ID prefix.
		 * @return string
		 */
		public function get_unique_id( $prefix = '' ): string {
			static $id_counter = 0;

			if ( function_exists( 'wp_unique_id' ) ) {
				return wp_unique_id( $prefix );
			}

			return $prefix . (string) ++$id_counter;
		}

		/**
		 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
		 *
		 * @noinspection PhpUnused
		 */
		public function pingback_link() {
			if ( is_singular() && pings_open() ) {
				?>
				<link rel="pingback" href="<?php echo esc_url( get_bloginfo( 'pingback_url' ) ); ?>">
				<?php
			}
		}
	}
}
