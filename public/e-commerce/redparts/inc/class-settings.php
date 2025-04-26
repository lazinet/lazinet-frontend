<?php
/**
 * RedParts Settings.
 *
 * @package RedParts
 * @since 1.0.0
 */

namespace RedParts;

use Redux;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'RedParts\Settings' ) ) {
	/**
	 * Class Settings
	 */
	class Settings extends Singleton {
		const REDUX_OPTION_NAME = 'redparts_settings';

		/**
		 * Demo options.
		 *
		 * @var array
		 */
		protected $demo_options = array();

		/**
		 * An array containing a list of valid options and their values.
		 *
		 * @var array
		 */
		protected $valid_demo_option = array(
			'dir'                            => array(
				'ltr',
				'rtl',
			),
			'header_layout'                  => array(
				'spaceship',
				'classic',
			),
			'theme_scheme'                   => array(
				'red',
				'blue',
				'green',
				'orange',
				'violet',
			),
			'desktop_use_predefined_variant' => array(
				'yes',
			),
			'desktop_spaceship_variant'      => array(
				'one',
				'two',
				'three',
			),
			'desktop_classic_variant'        => array(
				'one',
				'two',
				'three',
				'four',
				'five',
			),
			'mobile_use_predefined_variant'  => array(
				'yes',
			),
			'mobile_variant'                 => array(
				'one',
				'two',
				'three',
			),
		);

		/**
		 * Initialization.
		 */
		public function init() {
			add_action( 'after_setup_theme', array( $this, 'redux_init' ), 20 );

			add_action( 'init', array( $this, 'deferred_init' ) );
		}

		/**
		 * Deferred initialization.
		 *
		 * @noinspection PhpUnused
		 */
		public function deferred_init() {
			if (
				! isset( $GLOBALS[ self::REDUX_OPTION_NAME ]['demo_mode'] ) ||
				'yes' !== $GLOBALS[ self::REDUX_OPTION_NAME ]['demo_mode']
			) {
				return;
			}

			if ( ! empty( $_COOKIE['redparts_demo_options'] ) ) {
				$demo_options = sanitize_text_field( wp_unslash( $_COOKIE['redparts_demo_options'] ) );

				$this->demo_options = $this->parse_demo_options( $demo_options );
			}

			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			if ( isset( $_GET['redparts_demo_options'] ) ) {
				$demo_options = sanitize_text_field( wp_unslash( $_GET['redparts_demo_options'] ) );
				// phpcs:enable

				$this->demo_options = $this->parse_demo_options( $demo_options );

				$result = array();

				foreach ( $this->demo_options as $key => $value ) {
					$result[] = "$key:$value";
				}

				setcookie( 'redparts_demo_options', implode( ',', $result ), 0, COOKIEPATH, COOKIE_DOMAIN, false, false );
			}

			if ( ! empty( $this->demo_options['dir'] ) && 'rtl' === $this->demo_options['dir'] && ! is_admin() ) {
				global $wp_locale;

				$wp_locale->text_direction = 'rtl';
			}
		}

		/**
		 * Parses demo options.
		 *
		 * @param string $demo_options Demo options.
		 *
		 * @return array
		 */
		public function parse_demo_options( string $demo_options ): array {
			$result       = array();
			$demo_options = explode( ',', $demo_options );

			foreach ( $demo_options as $option ) {
				$option_data = explode( ':', $option );

				if ( 2 !== count( $option_data ) ) {
					continue;
				}

				$key = sanitize_key( $option_data[0] );

				if ( 'current' === $option_data[1] && isset( $this->demo_options[ $key ] ) ) {
					$option_data[1] = $this->demo_options[ $key ];
				}

				if ( isset( $this->valid_demo_option[ $key ] ) ) {
					if ( 'color' === $this->valid_demo_option[ $key ] ) {
						$result[ $key ] = sanitize_hex_color( $option_data[1] );
					} else {
						$result[ $key ] = sanitize_key( $option_data[1] );
					}
				}
			}

			return $result;
		}

		/**
		 * Redux initialization.
		 */
		public function redux_init() {
			if ( ! class_exists( 'Redux' ) ) {
				return;
			}

			add_filter( 'redux/options/' . self::REDUX_OPTION_NAME . '/sections', array( $this, 'redux_dynamic_sections' ) );

			$theme = wp_get_theme();
			$args  = array(
				// This is where your data is stored in the database and also becomes your global variable name.
				'opt_name'                  => self::REDUX_OPTION_NAME,
				// Name that appears at the top of your panel.
				'display_name'              => $theme->get( 'Name' ),
				// Version that appears at the top of your panel.
				'display_version'           => $theme->get( 'Version' ),
				// Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only).
				'menu_type'                 => 'menu',
				// Show the sections below the admin menu item or not.
				'allow_sub_menu'            => true,
				'menu_title'                => esc_html__( 'RedParts Theme', 'redparts' ),
				'page_title'                => esc_html__( 'RedParts Theme Settings', 'redparts' ),

				// You will need to generate a Google API key to use this feature.
				'google_api_key'            => '',
				// Set it you want google fonts to update weekly. A google_api_key value is required.
				'google_update_weekly'      => false,
				// Must be defined to add google fonts to the typography module
				// Use a asynchronous font on the front end or font string.
				'async_typography'          => false,
				// Disable this in case you want to create your own google fonts loader.
				'disable_google_fonts_link' => true,

				// Show the panel pages on the admin bar.
				'admin_bar'                 => false,
				// Choose an icon for the admin bar menu.
				'admin_bar_icon'            => 'dashicons-portfolio',
				// Choose an priority for the admin bar menu.
				'admin_bar_priority'        => 50,

				// Set a different name for your global variable other than the opt_name.
				'global_variable'           => '',
				// Show the time the page took to load, etc.
				'dev_mode'                  => false,
				// If dev_mode is enabled, will notify developer of updated versions available in the GitHub Repo.
				'update_notice'             => false,

				// Enable basic customizer support.
				'customizer'                => true,

				// Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
				'page_priority'             => null,
				// For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters.
				'page_parent'               => 'themes.php',
				// Permissions needed to access the options panel.
				'page_permissions'          => 'manage_options',
				// Specify a custom URL to an icon.
				'menu_icon'                 => '',
				// Force your panel to always open to a specific tab (by id).
				'last_tab'                  => '',
				// Icon displayed in the admin panel next to your menu_title.
				'page_icon'                 => 'icon-themes',
				// Page slug used to denote the panel, will be based off page title then menu title then opt_name if not provided.
				'page_slug'                 => '',
				// On load save the defaults to DB before user clicks save or not.
				'save_defaults'             => true,
				// If true, shows the default value next to each field that is not the default value.
				'default_show'              => false,
				// What to print by the field's title if the value shown is default. Suggested: *.
				'default_mark'              => '',
				// Shows the Import/Export panel when not used as a field.
				'show_import_export'        => true,

				// CAREFUL -> These options are for advanced use only.
				'transient_time'            => 60 * MINUTE_IN_SECONDS,
				// Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output.
				'output'                    => true,
				// Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head.
				'output_tag'                => true,

				// FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
				// possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
				'database'                  => '',
				// If you prefer not to use the CDN for Select2, Ace Editor, and others, you may download the Redux Vendor Support plugin
				// yourself and run locally or embed it in your code.
				'use_cdn'                   => true,
			);

			Redux::set_args( self::REDUX_OPTION_NAME, $args );

			// Header.
			Redux::set_section(
				self::REDUX_OPTION_NAME,
				array(
					'title'            => esc_html__( 'Header', 'redparts' ),
					'id'               => 'header',
					'customizer_width' => '400px',
					'icon'             => 'el el-website',
					'fields'           => array(
						array(
							'id'      => 'header_layout',
							'type'    => 'select',
							'title'   => esc_html__( 'Layout', 'redparts' ),
							'options' => array(
								'spaceship' => esc_html__( 'Spaceship', 'redparts' ),
								'classic'   => esc_html__( 'Classic', 'redparts' ),
							),
							'default' => 'spaceship',
						),
						array(
							'id'      => 'header_show_topbar',
							'type'    => 'select',
							'title'   => esc_html__( 'Show topbar', 'redparts' ),
							'options' => array(
								'yes' => esc_html__( 'Yes', 'redparts' ),
								'no'  => esc_html__( 'No', 'redparts' ),
							),
							'default' => 'yes',
						),
						array(
							'id'      => 'header_show_compare',
							'type'    => 'select',
							'title'   => esc_html__( 'Show compare in the topbar', 'redparts' ),
							'options' => array(
								'yes' => esc_html__( 'Yes', 'redparts' ),
								'no'  => esc_html__( 'No', 'redparts' ),
							),
							'default' => 'yes',
						),
						array(
							'id'      => 'header_show_currency',
							'type'    => 'select',
							'title'   => esc_html__( 'Show currency switcher in the topbar', 'redparts' ),
							'options' => array(
								'yes' => esc_html__( 'Yes', 'redparts' ),
								'no'  => esc_html__( 'No', 'redparts' ),
							),
							'default' => 'yes',
						),
						array(
							'id'      => 'header_show_wishlist_indicator',
							'type'    => 'select',
							'title'   => esc_html__( 'Show wishlist indicator', 'redparts' ),
							'options' => array(
								'yes' => esc_html__( 'Yes', 'redparts' ),
								'no'  => esc_html__( 'No', 'redparts' ),
							),
							'default' => 'yes',
						),
						array(
							'id'      => 'header_show_account_indicator',
							'type'    => 'select',
							'title'   => esc_html__( 'Show account indicator', 'redparts' ),
							'options' => array(
								'yes' => esc_html__( 'Yes', 'redparts' ),
								'no'  => esc_html__( 'No', 'redparts' ),
							),
							'default' => 'yes',
						),
						array(
							'id'      => 'header_show_cart_indicator',
							'type'    => 'select',
							'title'   => esc_html__( 'Show cart indicator', 'redparts' ),
							'options' => array(
								'yes' => esc_html__( 'Yes', 'redparts' ),
								'no'  => esc_html__( 'No', 'redparts' ),
							),
							'default' => 'yes',
						),
						array(
							'id'      => 'header_show_departments_menu',
							'type'    => 'select',
							'title'   => esc_html__( 'Show departments menu', 'redparts' ),
							'options' => array(
								'yes' => esc_html__( 'Yes', 'redparts' ),
								'no'  => esc_html__( 'No', 'redparts' ),
							),
							'default' => 'yes',
						),
						array(
							'id'          => 'header_departments_button_label',
							'type'        => 'text',
							'title'       => esc_html__( 'Departments button label', 'redparts' ),
							'placeholder' => esc_html__( 'Shop By Category', 'redparts' ),
						),
						array(
							'id'      => 'header_search_default_placeholder',
							'type'    => 'text',
							'title'   => esc_html__( 'Search default placeholder', 'redparts' ),
							'default' => esc_html__( 'Enter Keyword or Part Number', 'redparts' ),
						),
						array(
							'id'      => 'header_search_placeholder',
							'type'    => 'text',
							'title'   => esc_html__( 'Search placeholder when vehicle/category is selected', 'redparts' ),
							// translators: %s vehicle or category name.
							'default' => esc_html__( 'Search for %s', 'redparts' ),
						),
						array(
							'id'       => 'header_contacts_start',
							'type'     => 'section',
							'title'    => esc_html__( 'Navbar contacts', 'redparts' ),
							'indent'   => true,
							'required' => array( 'header_layout', '=', 'classic' ),
						),
						array(
							'id'      => 'header_contacts_show',
							'type'    => 'select',
							'title'   => esc_html__( 'Show', 'redparts' ),
							'options' => array(
								'yes' => esc_html__( 'Yes', 'redparts' ),
								'no'  => esc_html__( 'No', 'redparts' ),
							),
							'default' => 'no',
						),
						array(
							'id'          => 'header_contacts_title',
							'type'        => 'text',
							'title'       => esc_html__( 'Title', 'redparts' ),
							'placeholder' => esc_html__( '800 060-0730', 'redparts' ),
							'default'     => '',
							'required'    => array( 'header_layout', '=', 'classic' ),
						),
						array(
							'id'          => 'header_contacts_subtitle',
							'type'        => 'text',
							'title'       => esc_html__( 'Subtitle', 'redparts' ),
							'placeholder' => esc_html__( 'Call Us', 'redparts' ),
							'default'     => '',
							'required'    => array( 'header_layout', '=', 'classic' ),
						),
						array(
							'id'       => 'header_contacts_url',
							'type'     => 'text',
							'title'    => esc_html__( 'URL', 'redparts' ),
							'default'  => '',
							'required' => array( 'header_layout', '=', 'classic' ),
						),
						array(
							'id'       => 'header_contacts_end',
							'type'     => 'section',
							'indent'   => false,
							'required' => array( 'header_layout', '=', 'classic' ),
						),
					),
				)
			);

			// Footer.
			Redux::set_section(
				self::REDUX_OPTION_NAME,
				array(
					'title'            => esc_html__( 'Footer', 'redparts' ),
					'id'               => 'footer',
					'customizer_width' => '400px',
					'icon'             => 'el el-download-alt',
					'fields'           => array(
						// widget classes.
						array(
							'id'      => 'footer_widget_classes',
							'type'    => 'textarea',
							'title'   => esc_html__( 'Widget Classes', 'redparts' ),
							'desc'    => esc_html__( 'Comma-separated CSS classes to be applied to the footer widget containers.', 'redparts' ),
							'default' => 'th-col-12 th-col-xl-4,th-col-12 th-col-md-6 th-col-xl-4,th-col-12 th-col-md-6 th-col-xl-4',
							'rows'    => 2,
						),
						// copyright.
						array(
							'id'      => 'footer_copyright',
							'type'    => 'textarea',
							'title'   => esc_html__( 'Copyright', 'redparts' ),
							'default' => '',
						),
						// payments.
						array(
							'id'    => 'footer_payments',
							'type'  => 'media',
							'title' => esc_html__( 'Payments Image', 'redparts' ),
						),
					),
				)
			);

			// Mobile.
			Redux::set_section(
				self::REDUX_OPTION_NAME,
				array(
					'title'            => esc_html__( 'Mobile', 'redparts' ),
					'id'               => 'mobile',
					'customizer_width' => '400px',
					'icon'             => 'dashicons-before dashicons-smartphone',
					'fields'           => array(
						array(
							'id'       => 'mobile_header_logo',
							'type'     => 'media',
							'title'    => esc_html__( 'Mobile header logo', 'redparts' ),
							'mode'     => 'image',
							'subtitle' => esc_html__( 'Use this option to specify a different logo for the mobile header.', 'redparts' ),
						),
						array(
							'id'      => 'mobile_header_show_wishlist_indicator',
							'type'    => 'select',
							'title'   => esc_html__( 'Show wishlist indicator', 'redparts' ),
							'options' => array(
								'yes' => esc_html__( 'Yes', 'redparts' ),
								'no'  => esc_html__( 'No', 'redparts' ),
							),
							'default' => 'yes',
						),
						array(
							'id'      => 'mobile_header_show_account_indicator',
							'type'    => 'select',
							'title'   => esc_html__( 'Show account indicator', 'redparts' ),
							'options' => array(
								'yes' => esc_html__( 'Yes', 'redparts' ),
								'no'  => esc_html__( 'No', 'redparts' ),
							),
							'default' => 'yes',
						),
						array(
							'id'      => 'mobile_header_show_cart_indicator',
							'type'    => 'select',
							'title'   => esc_html__( 'Show cart indicator', 'redparts' ),
							'options' => array(
								'yes' => esc_html__( 'Yes', 'redparts' ),
								'no'  => esc_html__( 'No', 'redparts' ),
							),
							'default' => 'yes',
						),
						// Mobile menu indicators.
						array(
							'id'     => 'mobile_menu_indicators_start',
							'type'   => 'section',
							'title'  => esc_html__( 'Mobile menu indicators', 'redparts' ),
							'indent' => true,
						),
						array(
							'id'      => 'mobile_menu_indicators_show',
							'type'    => 'select',
							'title'   => esc_html__( 'Show', 'redparts' ),
							'options' => array(
								'yes' => esc_html__( 'Yes', 'redparts' ),
								'no'  => esc_html__( 'No', 'redparts' ),
							),
							'default' => 'yes',
						),
						array(
							'id'       => 'mobile_menu_indicators_show_wishlist',
							'type'     => 'select',
							'title'    => esc_html__( 'Show wishlist', 'redparts' ),
							'options'  => array(
								'yes' => esc_html__( 'Yes', 'redparts' ),
								'no'  => esc_html__( 'No', 'redparts' ),
							),
							'default'  => 'yes',
							'required' => array( 'mobile_menu_indicators_show', '=', 'yes' ),
						),
						array(
							'id'       => 'mobile_menu_indicators_show_account',
							'type'     => 'select',
							'title'    => esc_html__( 'Show account', 'redparts' ),
							'options'  => array(
								'yes' => esc_html__( 'Yes', 'redparts' ),
								'no'  => esc_html__( 'No', 'redparts' ),
							),
							'default'  => 'yes',
							'required' => array( 'mobile_menu_indicators_show', '=', 'yes' ),
						),
						array(
							'id'       => 'mobile_menu_indicators_show_cart',
							'type'     => 'select',
							'title'    => esc_html__( 'Show cart', 'redparts' ),
							'options'  => array(
								'yes' => esc_html__( 'Yes', 'redparts' ),
								'no'  => esc_html__( 'No', 'redparts' ),
							),
							'default'  => 'yes',
							'required' => array( 'mobile_menu_indicators_show', '=', 'yes' ),
						),
						array(
							'id'       => 'mobile_menu_indicators_show_garage',
							'type'     => 'select',
							'title'    => esc_html__( 'Show garage', 'redparts' ),
							'options'  => array(
								'yes' => esc_html__( 'Yes', 'redparts' ),
								'no'  => esc_html__( 'No', 'redparts' ),
							),
							'default'  => 'yes',
							'required' => array( 'mobile_menu_indicators_show', '=', 'yes' ),
						),
						array(
							'id'     => 'mobile_menu_indicators_end',
							'type'   => 'section',
							'indent' => false,
						),
						// Mobile menu contacts.
						array(
							'id'     => 'mobile_menu_contacts_start',
							'type'   => 'section',
							'title'  => esc_html__( 'Mobile menu contacts', 'redparts' ),
							'indent' => true,
						),
						array(
							'id'      => 'mobile_menu_contacts_show',
							'type'    => 'select',
							'title'   => esc_html__( 'Show', 'redparts' ),
							'options' => array(
								'yes' => esc_html__( 'Yes', 'redparts' ),
								'no'  => esc_html__( 'No', 'redparts' ),
							),
							'default' => 'no',
						),
						array(
							'id'          => 'mobile_menu_contacts_title',
							'type'        => 'text',
							'title'       => esc_html__( 'Title', 'redparts' ),
							'placeholder' => esc_html__( '800 060-0730', 'redparts' ),
							'default'     => '',
						),
						array(
							'id'          => 'mobile_menu_contacts_subtitle',
							'type'        => 'text',
							'title'       => esc_html__( 'Subtitle', 'redparts' ),
							'placeholder' => esc_html__( 'Free call 24/7', 'redparts' ),
							'default'     => '',
						),
						array(
							'id'      => 'mobile_menu_contacts_url',
							'type'    => 'text',
							'title'   => esc_html__( 'URL', 'redparts' ),
							'default' => '',
						),
						array(
							'id'     => 'mobile_menu_contacts_end',
							'type'   => 'section',
							'indent' => false,
						),
					),
				)
			);

			// Blog.
			Redux::set_section(
				self::REDUX_OPTION_NAME,
				array(
					'id'               => 'blog',
					'title'            => esc_html__( 'Blog', 'redparts' ),
					'customizer_width' => '400px',
					'icon'             => 'el el-wordpress',
					'fields'           => array(
						array(
							'id'      => 'blog_layout',
							'type'    => 'radio',
							'title'   => esc_html__( 'Blog layout', 'redparts' ),
							'options' => array(
								'classic' => esc_html__( 'Classic', 'redparts' ),
								'grid'    => esc_html__( 'Grid', 'redparts' ),
								'list'    => esc_html__( 'List', 'redparts' ),
							),
							'default' => 'classic',
						),
						array(
							'id'      => 'blog_sidebar_position',
							'type'    => 'radio',
							'title'   => esc_html__( 'Blog sidebar position', 'redparts' ),
							'options' => array(
								'start' => esc_html__( 'Left', 'redparts' ),
								'end'   => esc_html__( 'Right', 'redparts' ),
								'none'  => esc_html__( 'Full Width', 'redparts' ),
							),
							'default' => 'end',
						),
						array(
							'id'     => 'blog_single_post_section',
							'type'   => 'section',
							'title'  => esc_html__( 'Single Post', 'redparts' ),
							'indent' => true,
						),
						array(
							'id'      => 'post_sidebar_position',
							'type'    => 'radio',
							'title'   => esc_html__( 'Post sidebar position', 'redparts' ),
							'options' => array(
								'start' => esc_html__( 'Left', 'redparts' ),
								'end'   => esc_html__( 'Right', 'redparts' ),
								'none'  => esc_html__( 'Full Width', 'redparts' ),
							),
							'default' => 'end',
						),
						array(
							'id'     => 'blog_single_post_section_end',
							'type'   => 'section',
							'indent' => false,
						),
					),
				)
			);

			// Shop.
			Redux::set_section(
				self::REDUX_OPTION_NAME,
				array(
					'id'               => 'shop',
					'title'            => esc_html__( 'Shop', 'redparts' ),
					'customizer_width' => '400px',
					'icon'             => 'el el-shopping-cart',
					'fields'           => array(
						array(
							'id'      => 'shop_layout',
							'type'    => 'radio',
							'title'   => esc_html__( 'Shop layout', 'redparts' ),
							'options' => array(
								'grid-3-sidebar' => esc_html__( 'Sidebar 3 columns', 'redparts' ),
								'grid-4-sidebar' => esc_html__( 'Sidebar 4 columns', 'redparts' ),
								'grid-4-full'    => esc_html__( 'Full width 4 columns', 'redparts' ),
								'grid-5-full'    => esc_html__( 'Full width 5 columns', 'redparts' ),
								'grid-6-full'    => esc_html__( 'Full width 6 columns', 'redparts' ),
							),
							'default' => 'grid-4-sidebar',
						),
						array(
							'id'      => 'shop_sidebar_position',
							'type'    => 'radio',
							'title'   => esc_html__( 'Sidebar position', 'redparts' ),
							'options' => array(
								'start' => esc_html__( 'Left', 'redparts' ),
								'end'   => esc_html__( 'Right', 'redparts' ),
							),
							'default' => 'start',
						),
						array(
							'id'      => 'shop_view_mode',
							'type'    => 'radio',
							'title'   => esc_html__( 'Default view mode', 'redparts' ),
							'options' => array(
								'grid'               => esc_html__( 'Grid', 'redparts' ),
								'grid-with-features' => esc_html__( 'Grid with features', 'redparts' ),
								'list'               => esc_html__( 'List', 'redparts' ),
								'table'              => esc_html__( 'Table', 'redparts' ),
							),
							'default' => 'grid',
						),
						array(
							'id'       => 'products_per_page',
							'type'     => 'text',
							'title'    => esc_html__( 'Products per page', 'redparts' ),
							'validate' => 'numeric',
							'default'  => 12,
						),
						array(
							'id'       => 'products_per_page_variations',
							'type'     => 'text',
							'title'    => esc_html__( 'Products per page variations', 'redparts' ),
							'desc'     => esc_html__( 'Comma-separated numbers.', 'redparts' ),
							'validate' => 'comma_numeric',
							'default'  => '9, 12, 18, 24',
						),
					),
				)
			);

			// Product.
			Redux::set_section(
				self::REDUX_OPTION_NAME,
				array(
					'id'               => 'product',
					'title'            => esc_html__( 'Product', 'redparts' ),
					'customizer_width' => '400px',
					'icon'             => 'el el-tags',
					'fields'           => array(
						array(
							'id'      => 'product_sidebar_position',
							'type'    => 'radio',
							'title'   => esc_html__( 'Sidebar position', 'redparts' ),
							'options' => array(
								'start' => esc_html__( 'Left', 'redparts' ),
								'end'   => esc_html__( 'Right', 'redparts' ),
								'none'  => esc_html__( 'Full Width', 'redparts' ),
							),
							'default' => 'start',
						),
						array(
							'id'      => 'product_meta_attributes',
							'type'    => 'text',
							'title'   => esc_html__( 'Meta attributes', 'redparts' ),
							'desc'    => esc_html__( 'Comma-separated attribute slugs.', 'redparts' ),
							'default' => '__SKU__',
						),
					),
				)
			);

			// Typography.
			Redux::set_section(
				self::REDUX_OPTION_NAME,
				array(
					'title'  => esc_html__( 'Typography', 'redparts' ),
					'id'     => 'typography',
					'icon'   => 'el el-fontsize',
					'fields' => array(
						array(
							'id'      => 'default_fonts',
							'type'    => 'switch',
							'title'   => esc_html__( 'Use default fonts', 'redparts' ),
							'on'      => esc_html__( 'Yes', 'redparts' ),
							'off'     => esc_html__( 'No', 'redparts' ),
							'default' => true,
						),
						array(
							'id'          => 'primary_font',
							'type'        => 'typography',
							'title'       => esc_html__( 'Primary Font', 'redparts' ),
							'google'      => true,
							'font-backup' => true,
							'units'       => 'px',
							'text-align'  => false,
							'line-height' => false,
							'color'       => false,
							'font-size'   => false,
							'default'     => array(
								'font-backup' => 'Arial, Helvetica, sans-serif',
								'font-weight' => '400',
								'font-family' => 'Roboto',
								'google'      => true,
								'subsets'     => 'latin',
							),
							'required'    => array( 'default_fonts', '=', false ),
						),
						array(
							'id'          => 'headers_font',
							'type'        => 'typography',
							'title'       => esc_html__( 'Headers Font', 'redparts' ),
							'google'      => true,
							'font-backup' => true,
							'units'       => 'px',
							'text-align'  => false,
							'line-height' => false,
							'color'       => false,
							'font-size'   => false,
							'default'     => array(
								'font-backup' => 'Arial, Helvetica, sans-serif',
								'font-weight' => '400',
								'font-family' => 'Roboto',
								'google'      => true,
								'subsets'     => 'latin',
							),
							'required'    => array( 'default_fonts', '=', false ),
						),
					),
				)
			);

			// Colors.
			Redux::set_section(
				self::REDUX_OPTION_NAME,
				array(
					'title' => esc_html__( 'Colors', 'redparts' ),
					'id'    => 'colors',
					'icon'  => 'el el-brush',
				)
			);

			// Colors / General.
			Redux::set_section(
				self::REDUX_OPTION_NAME,
				array(
					'title'      => esc_html__( 'General', 'redparts' ),
					'id'         => 'colors-general',
					'subsection' => true,
					'fields'     => array(
						array(
							'id'       => 'override_elementor_primary_color',
							'type'     => 'switch',
							'title'    => esc_html__( 'Override Elementor Primary Color', 'redparts' ),
							'subtitle' => esc_html__( 'If "On", then the "Elementor Primary Color" will be used instead of the "Theme Scheme Main Color"', 'redparts' ),
							'default'  => true,
						),
						// Theme scheme.
						array(
							'id'     => 'theme_section',
							'type'   => 'section',
							'title'  => esc_html__( 'Theme', 'redparts' ),
							'indent' => true,
						),
						array(
							'id'       => 'theme_scheme',
							'type'     => 'button_set',
							'title'    => esc_html__( 'Theme Scheme', 'redparts' ),
							'subtitle' => esc_html__(
								'Defines the primary color of the theme. Choose one of the proposed ones or define your own by choosing [Custom].',
								'redparts'
							),
							'options'  => array(
								'red'        => esc_html__( 'Red', 'redparts' ),
								'blue'       => esc_html__( 'Blue', 'redparts' ),
								'green'      => esc_html__( 'Green', 'redparts' ),
								'orange'     => esc_html__( 'Orange', 'redparts' ),
								'violet'     => esc_html__( 'Violet', 'redparts' ),
								'__custom__' => esc_html__( '[Custom]', 'redparts' ),
							),
							'default'  => 'red',
						),
						array(
							'id'          => 'theme_scheme_main_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Theme Scheme Main Color', 'redparts' ),
							'subtitle'    => esc_html__( 'Typically background color.', 'redparts' ),
							'default'     => '#e52727',
							'validate'    => 'color',
							'required'    => array( 'theme_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'          => 'theme_scheme_opposite_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Theme Scheme Opposite Color', 'redparts' ),
							'subtitle'    => esc_html__( 'Typically font color.', 'redparts' ),
							'default'     => '#ffffff',
							'validate'    => 'color',
							'required'    => array( 'theme_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'     => 'theme_section_end',
							'type'   => 'section',
							'indent' => false,
						),
						// Theme scheme End.
						// Light scheme.
						array(
							'id'     => 'light_section',
							'type'   => 'section',
							'title'  => esc_html__( 'Light', 'redparts' ),
							'indent' => true,
						),
						array(
							'id'       => 'light_scheme',
							'type'     => 'button_set',
							'title'    => esc_html__( 'Light Scheme', 'redparts' ),
							'subtitle' => esc_html__( 'By default is used as a color for the Header and Topbar.', 'redparts' ),
							'options'  => array(
								'__default__' => esc_html__( '[Default]', 'redparts' ),
								'__custom__'  => esc_html__( '[Custom]', 'redparts' ),
							),
							'default'  => '__default__',
						),
						array(
							'id'          => 'light_scheme_main_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Light Scheme Main Color', 'redparts' ),
							'subtitle'    => esc_html__( 'Typically background color.', 'redparts' ),
							'default'     => '#ffffff',
							'validate'    => 'color',
							'required'    => array( 'light_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'          => 'light_scheme_opposite_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Light Scheme Opposite Color', 'redparts' ),
							'subtitle'    => esc_html__( 'Typically font color.', 'redparts' ),
							'default'     => '#262626',
							'validate'    => 'color',
							'required'    => array( 'light_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'     => 'light_section_end',
							'type'   => 'section',
							'indent' => false,
						),
						// Light scheme End.
						// Dark scheme.
						array(
							'id'     => 'dark_section',
							'type'   => 'section',
							'title'  => esc_html__( 'Dark', 'redparts' ),
							'indent' => true,
						),
						array(
							'id'       => 'dark_scheme',
							'type'     => 'button_set',
							'title'    => esc_html__( 'Dark Scheme', 'redparts' ),
							'subtitle' => esc_html__( 'By default is used as a color for the Departments Menu and Buttons in the hover state.', 'redparts' ),
							'options'  => array(
								'__default__' => esc_html__( '[Default]', 'redparts' ),
								'__custom__'  => esc_html__( '[Custom]', 'redparts' ),
							),
							'default'  => '__default__',
						),
						array(
							'id'          => 'dark_scheme_main_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Dark Scheme Main Color', 'redparts' ),
							'subtitle'    => esc_html__( 'Typically background color.', 'redparts' ),
							'default'     => '#333333',
							'validate'    => 'color',
							'required'    => array( 'dark_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'          => 'dark_scheme_opposite_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Dark Scheme Opposite Color', 'redparts' ),
							'subtitle'    => esc_html__( 'Typically font color.', 'redparts' ),
							'default'     => '#ffffff',
							'validate'    => 'color',
							'required'    => array( 'dark_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'     => 'dark_section_end',
							'type'   => 'section',
							'indent' => false,
						),
						// Dark scheme End.
						// Accent scheme.
						array(
							'id'     => 'accent_section',
							'type'   => 'section',
							'title'  => esc_html__( 'Accent', 'redparts' ),
							'indent' => true,
						),
						array(
							'id'       => 'accent_scheme',
							'type'     => 'button_set',
							'title'    => esc_html__( 'Accent Scheme', 'redparts' ),
							'subtitle' => esc_html__( 'By default is used as a color for the Departments Menu and Buttons in the hover state.', 'redparts' ),
							'options'  => array(
								'__default__' => esc_html__( '[Default]', 'redparts' ),
								'__custom__'  => esc_html__( '[Custom]', 'redparts' ),
							),
							'default'  => '__default__',
						),
						array(
							'id'          => 'accent_scheme_main_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Accent Scheme Main Color', 'redparts' ),
							'subtitle'    => esc_html__( 'Typically background color.', 'redparts' ),
							'default'     => '#ffdf40',
							'validate'    => 'color',
							'required'    => array( 'accent_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'          => 'accent_scheme_opposite_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Accent Scheme Opposite Color', 'redparts' ),
							'subtitle'    => esc_html__( 'Typically font color.', 'redparts' ),
							'default'     => '#262626',
							'validate'    => 'color',
							'required'    => array( 'accent_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'     => 'accent_section_end',
							'type'   => 'section',
							'indent' => false,
						),
						// Accent scheme End.
					),
				)
			);

			// Colors / Desktop Header.
			Redux::set_section(
				self::REDUX_OPTION_NAME,
				array(
					'title'      => esc_html__( 'Desktop Header', 'redparts' ),
					'id'         => 'colors-desktop-header',
					'subsection' => true,
					'fields'     => array(
						array(
							'id'      => 'desktop_use_predefined_variant',
							'type'    => 'button_set',
							'title'   => esc_html__( 'Use Predefined Variant', 'redparts' ),
							'options' => array(
								'yes' => esc_html__( 'Yes', 'redparts' ),
								'no'  => esc_html__( 'No', 'redparts' ),
							),
							'default' => 'yes',
						),
						array(
							'id'       => 'desktop_spaceship_variant',
							'type'     => 'button_set',
							'title'    => esc_html__( 'Variant', 'redparts' ),
							'required' => array(
								array( 'desktop_use_predefined_variant', '=', 'yes' ),
								array( 'header_layout', '=', 'spaceship' ),
							),
							'options'  => array(
								'one'   => esc_html__( 'One', 'redparts' ),
								'two'   => esc_html__( 'Two', 'redparts' ),
								'three' => esc_html__( 'Three', 'redparts' ),
							),
							'default'  => 'one',
						),
						array(
							'id'       => 'desktop_classic_variant',
							'type'     => 'button_set',
							'title'    => esc_html__( 'Variant', 'redparts' ),
							'required' => array(
								array( 'desktop_use_predefined_variant', '=', 'yes' ),
								array( 'header_layout', '=', 'classic' ),
							),
							'options'  => array(
								'one'   => esc_html__( 'One', 'redparts' ),
								'two'   => esc_html__( 'Two', 'redparts' ),
								'three' => esc_html__( 'Three', 'redparts' ),
								'four'  => esc_html__( 'Four', 'redparts' ),
								'five'  => esc_html__( 'Five', 'redparts' ),
							),
							'default'  => 'one',
						),
						// Spaceship Left Topbar.
						array(
							'id'       => 'desktop_spaceship_topbar_start_section',
							'type'     => 'section',
							'title'    => esc_html__( 'Left Topbar', 'redparts' ),
							'indent'   => true,
							'required' => array(
								array( 'desktop_use_predefined_variant', '=', 'no' ),
								array( 'header_layout', '=', 'spaceship' ),
							),
						),
						array(
							'id'      => 'desktop_spaceship_topbar_start_scheme',
							'type'    => 'button_set',
							'title'   => esc_html__( 'Left Topbar Scheme', 'redparts' ),
							'options' => array(
								'theme'      => esc_html__( 'Theme', 'redparts' ),
								'dark'       => esc_html__( 'Dark', 'redparts' ),
								'light'      => esc_html__( 'Light', 'redparts' ),
								'accent'     => esc_html__( 'Accent', 'redparts' ),
								'__custom__' => esc_html__( '[Custom]', 'redparts' ),
							),
							'default' => 'theme',
						),
						array(
							'id'          => 'desktop_spaceship_topbar_start_scheme_main_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Left Topbar Scheme Main Color', 'redparts' ),
							'default'     => '#e52727',
							'validate'    => 'color',
							'required'    => array( 'desktop_spaceship_topbar_start_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'          => 'desktop_spaceship_topbar_start_scheme_opposite_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Left Topbar Scheme Opposite Color', 'redparts' ),
							'default'     => '#ffffff',
							'validate'    => 'color',
							'required'    => array( 'desktop_spaceship_topbar_start_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'     => 'desktop_spaceship_topbar_start_section_end',
							'type'   => 'section',
							'indent' => false,
						),
						// Spaceship Left Topbar End.
						// Spaceship Right Topbar.
						array(
							'id'       => 'desktop_spaceship_topbar_end_section',
							'type'     => 'section',
							'title'    => esc_html__( 'Right Topbar', 'redparts' ),
							'indent'   => true,
							'required' => array(
								array( 'desktop_use_predefined_variant', '=', 'no' ),
								array( 'header_layout', '=', 'spaceship' ),
							),
						),
						array(
							'id'      => 'desktop_spaceship_topbar_end_scheme',
							'type'    => 'button_set',
							'title'   => esc_html__( 'Right Topbar Scheme', 'redparts' ),
							'options' => array(
								'theme'      => esc_html__( 'Theme', 'redparts' ),
								'dark'       => esc_html__( 'Dark', 'redparts' ),
								'light'      => esc_html__( 'Light', 'redparts' ),
								'accent'     => esc_html__( 'Accent', 'redparts' ),
								'__custom__' => esc_html__( '[Custom]', 'redparts' ),
							),
							'default' => 'dark',
						),
						array(
							'id'          => 'desktop_spaceship_topbar_end_scheme_main_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Right Topbar Scheme Main Color', 'redparts' ),
							'default'     => '#333333',
							'validate'    => 'color',
							'required'    => array( 'desktop_spaceship_topbar_end_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'          => 'desktop_spaceship_topbar_end_scheme_opposite_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Right Topbar Scheme Opposite Color', 'redparts' ),
							'default'     => '#ffffff',
							'validate'    => 'color',
							'required'    => array( 'desktop_spaceship_topbar_end_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'     => 'desktop_spaceship_topbar_end_section_end',
							'type'   => 'section',
							'indent' => false,
						),
						// Spaceship Right Topbar End.
						// Classic Topbar.
						array(
							'id'       => 'desktop_classic_topbar_section',
							'type'     => 'section',
							'title'    => esc_html__( 'Topbar', 'redparts' ),
							'indent'   => true,
							'required' => array(
								array( 'desktop_use_predefined_variant', '=', 'no' ),
								array( 'header_layout', '=', 'classic' ),
							),
						),
						array(
							'id'      => 'desktop_classic_topbar_scheme',
							'type'    => 'button_set',
							'title'   => esc_html__( 'Topbar Scheme', 'redparts' ),
							'options' => array(
								'theme'      => esc_html__( 'Theme', 'redparts' ),
								'dark'       => esc_html__( 'Dark', 'redparts' ),
								'light'      => esc_html__( 'Light', 'redparts' ),
								'accent'     => esc_html__( 'Accent', 'redparts' ),
								'__custom__' => esc_html__( '[Custom]', 'redparts' ),
							),
							'default' => 'light',
						),
						array(
							'id'       => 'desktop_classic_topbar_style',
							'type'     => 'radio',
							'title'    => esc_html__( 'Topbar Style', 'redparts' ),
							'subtitle' => esc_html__( 'This option allows to specify how to style the top bar when its background and header background match.', 'redparts' ),
							'options'  => array(
								'background' => esc_html__( 'Mute background', 'redparts' ),
								'border'     => esc_html__( 'Show bottom border', 'redparts' ),
							),
							'default'  => 'background',
						),
						array(
							'id'          => 'desktop_classic_topbar_scheme_main_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Topbar Scheme Main Color', 'redparts' ),
							'default'     => '#ffffff',
							'validate'    => 'color',
							'required'    => array( 'desktop_classic_topbar_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'          => 'desktop_classic_topbar_scheme_opposite_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Topbar Scheme Opposite Color', 'redparts' ),
							'default'     => '#262626',
							'validate'    => 'color',
							'required'    => array( 'desktop_classic_topbar_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'     => 'desktop_classic_topbar_section_end',
							'type'   => 'section',
							'indent' => false,
						),
						// Classic Topbar End.
						// Header.
						array(
							'id'       => 'desktop_header_section',
							'type'     => 'section',
							'title'    => esc_html__( 'Header', 'redparts' ),
							'indent'   => true,
							'required' => array( 'desktop_use_predefined_variant', '=', 'no' ),
						),
						array(
							'id'      => 'desktop_header_scheme',
							'type'    => 'button_set',
							'title'   => esc_html__( 'Header Scheme', 'redparts' ),
							'options' => array(
								'theme'      => esc_html__( 'Theme', 'redparts' ),
								'dark'       => esc_html__( 'Dark', 'redparts' ),
								'light'      => esc_html__( 'Light', 'redparts' ),
								'accent'     => esc_html__( 'Accent', 'redparts' ),
								'__custom__' => esc_html__( '[Custom]', 'redparts' ),
							),
							'default' => 'light',
						),
						array(
							'id'          => 'desktop_header_scheme_main_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Header Scheme Main Color', 'redparts' ),
							'default'     => '#ffffff',
							'validate'    => 'color',
							'required'    => array( 'desktop_header_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'          => 'desktop_header_scheme_opposite_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Header Scheme Opposite Color', 'redparts' ),
							'default'     => '#3d464d',
							'validate'    => 'color',
							'required'    => array( 'desktop_header_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'     => 'desktop_header_section_end',
							'type'   => 'section',
							'indent' => false,
						),
						// Header End.
						// Logo.
						array(
							'id'       => 'desktop_logo_section',
							'type'     => 'section',
							'title'    => esc_html__( 'Logo', 'redparts' ),
							'indent'   => true,
							'required' => array( 'desktop_use_predefined_variant', '=', 'no' ),
						),
						array(
							'id'      => 'desktop_logo_color',
							'type'    => 'button_set',
							'title'   => esc_html__( 'Logo Color', 'redparts' ),
							'options' => array(
								'__default__' => esc_html__( '[Default]', 'redparts' ),
								'__custom__'  => esc_html__( '[Custom]', 'redparts' ),
							),
							'default' => '__default__',
						),
						array(
							'id'          => 'desktop_logo_primary_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Logo Primary Color', 'redparts' ),
							'default'     => '#e52727',
							'validate'    => 'color',
							'required'    => array( 'desktop_logo_color', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'          => 'desktop_logo_secondary_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Logo Secondary Color', 'redparts' ),
							'default'     => '#404040',
							'validate'    => 'color',
							'required'    => array( 'desktop_logo_color', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'     => 'desktop_logo_section_end',
							'type'   => 'section',
							'indent' => false,
						),
						// Logo End.
						// Departments Button.
						array(
							'id'       => 'desktop_departments_button_section',
							'type'     => 'section',
							'title'    => esc_html__( 'Departments Button', 'redparts' ),
							'indent'   => true,
							'required' => array( 'desktop_use_predefined_variant', '=', 'no' ),
						),
						array(
							'id'      => 'desktop_departments_button_normal_scheme',
							'type'    => 'button_set',
							'title'   => esc_html__( 'Departments Button Normal Scheme', 'redparts' ),
							'options' => array(
								'theme'      => esc_html__( 'Theme', 'redparts' ),
								'dark'       => esc_html__( 'Dark', 'redparts' ),
								'light'      => esc_html__( 'Light', 'redparts' ),
								'accent'     => esc_html__( 'Accent', 'redparts' ),
								'__custom__' => esc_html__( '[Custom]', 'redparts' ),
							),
							'default' => 'light',
						),
						array(
							'id'          => 'desktop_departments_button_normal_scheme_main_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Departments Button Normal Scheme Main Color', 'redparts' ),
							'default'     => '#ffffff',
							'validate'    => 'color',
							'required'    => array( 'desktop_departments_button_normal_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'          => 'desktop_departments_button_normal_scheme_opposite_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Departments Button Normal Scheme Opposite Color', 'redparts' ),
							'default'     => '#262626',
							'validate'    => 'color',
							'required'    => array( 'desktop_departments_button_normal_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'      => 'desktop_departments_button_hover_scheme',
							'type'    => 'button_set',
							'title'   => esc_html__( 'Departments Button Hover Scheme', 'redparts' ),
							'options' => array(
								'theme'      => esc_html__( 'Theme', 'redparts' ),
								'dark'       => esc_html__( 'Dark', 'redparts' ),
								'light'      => esc_html__( 'Light', 'redparts' ),
								'accent'     => esc_html__( 'Accent', 'redparts' ),
								'__custom__' => esc_html__( '[Custom]', 'redparts' ),
							),
							'default' => 'theme',
						),
						array(
							'id'          => 'desktop_departments_button_hover_scheme_main_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Departments Button Hover Scheme Main Color', 'redparts' ),
							'default'     => '#e52727',
							'validate'    => 'color',
							'required'    => array( 'desktop_departments_button_hover_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'          => 'desktop_departments_button_hover_scheme_opposite_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Departments Button Hover Scheme Opposite Color', 'redparts' ),
							'default'     => '#ffffff',
							'validate'    => 'color',
							'required'    => array( 'desktop_departments_button_hover_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'     => 'desktop_departments_button_section_end',
							'type'   => 'section',
							'indent' => false,
						),
						// Departments Button End.
						// Indicator Counter.
						array(
							'id'       => 'desktop_indicator_counter_section',
							'type'     => 'section',
							'title'    => esc_html__( 'Indicator Counter', 'redparts' ),
							'indent'   => true,
							'required' => array( 'desktop_use_predefined_variant', '=', 'no' ),
						),
						array(
							'id'      => 'desktop_indicator_counter_scheme',
							'type'    => 'button_set',
							'title'   => esc_html__( 'Indicator Counter Scheme', 'redparts' ),
							'options' => array(
								'theme'      => esc_html__( 'Theme', 'redparts' ),
								'dark'       => esc_html__( 'Dark', 'redparts' ),
								'light'      => esc_html__( 'Light', 'redparts' ),
								'accent'     => esc_html__( 'Accent', 'redparts' ),
								'__custom__' => esc_html__( '[Custom]', 'redparts' ),
							),
							'default' => 'theme',
						),
						array(
							'id'          => 'desktop_indicator_counter_scheme_main_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Indicator Counter Scheme Main Color', 'redparts' ),
							'default'     => '#e52727',
							'validate'    => 'color',
							'required'    => array( 'desktop_indicator_counter_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'          => 'desktop_indicator_counter_scheme_opposite_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Indicator Counter Scheme Opposite Color', 'redparts' ),
							'default'     => '#ffffff',
							'validate'    => 'color',
							'required'    => array( 'desktop_indicator_counter_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'     => 'desktop_indicator_counter_section_end',
							'type'   => 'section',
							'indent' => false,
						),
						// Indicator Counter End.
						// Navbar.
						array(
							'id'       => 'desktop_navbar_section',
							'type'     => 'section',
							'title'    => esc_html__( 'Navbar', 'redparts' ),
							'indent'   => true,
							'required' => array(
								array( 'desktop_use_predefined_variant', '=', 'no' ),
								array( 'header_layout', '=', 'classic' ),
							),
						),
						array(
							'id'      => 'desktop_navbar_scheme',
							'type'    => 'button_set',
							'title'   => esc_html__( 'Navbar Scheme', 'redparts' ),
							'options' => array(
								'theme'      => esc_html__( 'Theme', 'redparts' ),
								'dark'       => esc_html__( 'Dark', 'redparts' ),
								'light'      => esc_html__( 'Light', 'redparts' ),
								'accent'     => esc_html__( 'Accent', 'redparts' ),
								'__custom__' => esc_html__( '[Custom]', 'redparts' ),
							),
							'default' => 'light',
						),
						array(
							'id'          => 'desktop_navbar_scheme_main_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Navbar Scheme Main Color', 'redparts' ),
							'default'     => '#ffffff',
							'validate'    => 'color',
							'required'    => array( 'desktop_navbar_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'          => 'desktop_navbar_scheme_opposite_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Navbar Scheme Opposite Color', 'redparts' ),
							'default'     => '#262626',
							'validate'    => 'color',
							'required'    => array( 'desktop_navbar_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'      => 'desktop_navbar_stretch',
							'type'    => 'radio',
							'title'   => esc_html__( 'Stretch', 'redparts' ),
							'options' => array(
								'no'  => esc_html__( 'No', 'redparts' ),
								'yes' => esc_html__( 'Yes', 'redparts' ),
							),
							'default' => 'no',
						),
						array(
							'id'     => 'desktop_navbar_section_end',
							'type'   => 'section',
							'indent' => false,
						),
						// Navbar End.
						// Vehicle Button.
						array(
							'id'       => 'desktop_vehicle_button_section',
							'type'     => 'section',
							'title'    => esc_html__( 'Vehicle Button', 'redparts' ),
							'indent'   => true,
							'required' => array(
								array( 'desktop_use_predefined_variant', '=', 'no' ),
								array( 'header_layout', '=', 'classic' ),
							),
						),
						array(
							'id'      => 'desktop_vehicle_button_scheme',
							'type'    => 'button_set',
							'title'   => esc_html__( 'Vehicle Button Scheme', 'redparts' ),
							'options' => array(
								'theme'      => esc_html__( 'Theme', 'redparts' ),
								'dark'       => esc_html__( 'Dark', 'redparts' ),
								'light'      => esc_html__( 'Light', 'redparts' ),
								'accent'     => esc_html__( 'Accent', 'redparts' ),
								'__custom__' => esc_html__( '[Custom]', 'redparts' ),
							),
							'default' => 'accent',
						),
						array(
							'id'          => 'desktop_vehicle_button_scheme_main_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Vehicle Button Scheme Main Color', 'redparts' ),
							'default'     => '#ffdf40',
							'validate'    => 'color',
							'required'    => array( 'desktop_vehicle_button_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'          => 'desktop_vehicle_button_scheme_opposite_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Vehicle Button Scheme Opposite Color', 'redparts' ),
							'default'     => '#ffffff',
							'validate'    => 'color',
							'required'    => array( 'desktop_vehicle_button_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'     => 'desktop_vehicle_button_section_end',
							'type'   => 'section',
							'indent' => false,
						),
						// Vehicle Button End.
					),
				)
			);

			// Colors / Mobile Header.
			Redux::set_section(
				self::REDUX_OPTION_NAME,
				array(
					'title'      => esc_html__( 'Mobile Header', 'redparts' ),
					'id'         => 'colors-mobile-header',
					'subsection' => true,
					'fields'     => array(
						array(
							'id'      => 'mobile_use_predefined_variant',
							'type'    => 'button_set',
							'title'   => esc_html__( 'Use Predefined Variant', 'redparts' ),
							'options' => array(
								'yes' => esc_html__( 'Yes', 'redparts' ),
								'no'  => esc_html__( 'No', 'redparts' ),
							),
							'default' => 'yes',
						),
						array(
							'id'       => 'mobile_variant',
							'type'     => 'button_set',
							'title'    => esc_html__( 'Variant', 'redparts' ),
							'required' => array( 'mobile_use_predefined_variant', '=', 'yes' ),
							'options'  => array(
								'one' => esc_html__( 'One', 'redparts' ),
								'two' => esc_html__( 'Two', 'redparts' ),
							),
							'default'  => 'one',
						),
						// Mobile Header.
						array(
							'id'       => 'mobile_header_scheme',
							'type'     => 'button_set',
							'title'    => esc_html__( 'Mobile Header Scheme', 'redparts' ),
							'options'  => array(
								'theme'      => esc_html__( 'Theme', 'redparts' ),
								'dark'       => esc_html__( 'Dark', 'redparts' ),
								'light'      => esc_html__( 'Light', 'redparts' ),
								'accent'     => esc_html__( 'Accent', 'redparts' ),
								'__custom__' => esc_html__( '[Custom]', 'redparts' ),
							),
							'default'  => 'theme',
							'required' => array( 'mobile_use_predefined_variant', '=', 'no' ),
						),
						array(
							'id'          => 'mobile_header_scheme_main_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Mobile Header Scheme Main Color', 'redparts' ),
							'default'     => '#ffffff',
							'validate'    => 'color',
							'required'    => array( 'mobile_header_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'          => 'mobile_header_scheme_opposite_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Mobile Header Scheme Opposite Color', 'redparts' ),
							'default'     => '#262626',
							'validate'    => 'color',
							'required'    => array( 'mobile_header_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						// Mobile Header End.
						// Mobile Logo.
						array(
							'id'       => 'mobile_logo_section',
							'type'     => 'section',
							'title'    => esc_html__( 'Mobile Logo', 'redparts' ),
							'indent'   => true,
							'required' => array( 'mobile_use_predefined_variant', '=', 'no' ),
						),
						array(
							'id'      => 'mobile_logo_color',
							'type'    => 'button_set',
							'title'   => esc_html__( 'Mobile Logo Color', 'redparts' ),
							'options' => array(
								'__default__' => esc_html__( '[Default]', 'redparts' ),
								'__custom__'  => esc_html__( '[Custom]', 'redparts' ),
							),
							'default' => '__default__',
						),
						array(
							'id'          => 'mobile_logo_primary_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Mobile Logo Primary Color', 'redparts' ),
							'default'     => '#e52727',
							'validate'    => 'color',
							'required'    => array( 'mobile_logo_color', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'          => 'mobile_logo_secondary_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Mobile Logo Secondary Color', 'redparts' ),
							'default'     => '#404040',
							'validate'    => 'color',
							'required'    => array( 'mobile_logo_color', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'     => 'mobile_logo_section_end',
							'type'   => 'section',
							'indent' => false,
						),
						// Mobile Logo End.
						// Mobile Vehicle Button.
						array(
							'id'       => 'mobile_vehicle_button_section',
							'type'     => 'section',
							'title'    => esc_html__( 'Mobile Vehicle Button', 'redparts' ),
							'indent'   => true,
							'required' => array( 'mobile_use_predefined_variant', '=', 'no' ),
						),
						array(
							'id'      => 'mobile_vehicle_button_scheme',
							'type'    => 'button_set',
							'title'   => esc_html__( 'Mobile Vehicle Button Scheme', 'redparts' ),
							'options' => array(
								'theme'      => esc_html__( 'Theme', 'redparts' ),
								'dark'       => esc_html__( 'Dark', 'redparts' ),
								'light'      => esc_html__( 'Light', 'redparts' ),
								'accent'     => esc_html__( 'Accent', 'redparts' ),
								'__custom__' => esc_html__( '[Custom]', 'redparts' ),
							),
							'default' => 'accent',
						),
						array(
							'id'          => 'mobile_vehicle_button_scheme_main_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Mobile Vehicle Button Scheme Main Color', 'redparts' ),
							'default'     => '#ffdf40',
							'validate'    => 'color',
							'required'    => array( 'mobile_vehicle_button_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'          => 'mobile_vehicle_button_scheme_opposite_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Mobile Vehicle Button Scheme Opposite Color', 'redparts' ),
							'default'     => '#ffffff',
							'validate'    => 'color',
							'required'    => array( 'mobile_vehicle_button_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'     => 'mobile_vehicle_button_section_end',
							'type'   => 'section',
							'indent' => false,
						),
						// Mobile Vehicle Button End.
						// Mobile Indicator Counter.
						array(
							'id'       => 'mobile_indicator_counter_section',
							'type'     => 'section',
							'title'    => esc_html__( 'Mobile Indicator Counter', 'redparts' ),
							'indent'   => true,
							'required' => array( 'mobile_use_predefined_variant', '=', 'no' ),
						),
						array(
							'id'      => 'mobile_indicator_counter_scheme',
							'type'    => 'button_set',
							'title'   => esc_html__( 'Mobile Indicator Counter Scheme', 'redparts' ),
							'options' => array(
								'theme'      => esc_html__( 'Theme', 'redparts' ),
								'dark'       => esc_html__( 'Dark', 'redparts' ),
								'light'      => esc_html__( 'Light', 'redparts' ),
								'accent'     => esc_html__( 'Accent', 'redparts' ),
								'__custom__' => esc_html__( '[Custom]', 'redparts' ),
							),
							'default' => 'theme',
						),
						array(
							'id'          => 'mobile_indicator_counter_scheme_main_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Mobile Indicator Counter Scheme Main Color', 'redparts' ),
							'default'     => '#e52727',
							'validate'    => 'color',
							'required'    => array( 'mobile_indicator_counter_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'          => 'mobile_indicator_counter_scheme_opposite_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Mobile Indicator Counter Scheme Opposite Color', 'redparts' ),
							'default'     => '#ffffff',
							'validate'    => 'color',
							'required'    => array( 'mobile_indicator_counter_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'     => 'mobile_indicator_counter_section_end',
							'type'   => 'section',
							'indent' => false,
						),
						// Mobile Indicator Counter End.
					),
				)
			);

			// Colors / Footer.
			Redux::set_section(
				self::REDUX_OPTION_NAME,
				array(
					'title'      => esc_html__( 'Footer', 'redparts' ),
					'id'         => 'colors-footer',
					'subsection' => true,
					'fields'     => array(
						// Footer.
						array(
							'id'      => 'footer_scheme',
							'type'    => 'button_set',
							'title'   => esc_html__( 'Footer Scheme', 'redparts' ),
							'options' => array(
								'theme'      => esc_html__( 'Theme', 'redparts' ),
								'dark'       => esc_html__( 'Dark', 'redparts' ),
								'light'      => esc_html__( 'Light', 'redparts' ),
								'accent'     => esc_html__( 'Accent', 'redparts' ),
								'__custom__' => esc_html__( '[Custom]', 'redparts' ),
							),
							'default' => 'dark',
						),
						array(
							'id'          => 'footer_scheme_main_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Footer Scheme Main Color', 'redparts' ),
							'default'     => '#333333',
							'validate'    => 'color',
							'required'    => array( 'footer_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'          => 'footer_scheme_opposite_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Footer Scheme Opposite Color', 'redparts' ),
							'default'     => '#ffffff',
							'validate'    => 'color',
							'required'    => array( 'footer_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						// Footer End.
					),
				)
			);

			// Colors / Buttons.
			Redux::set_section(
				self::REDUX_OPTION_NAME,
				array(
					'title'      => esc_html__( 'Buttons', 'redparts' ),
					'id'         => 'colors-buttons',
					'subsection' => true,
					'fields'     => array(
						// Primary button.
						array(
							'id'     => 'primary_button_section',
							'type'   => 'section',
							'title'  => esc_html__( 'Primary Button', 'redparts' ),
							'indent' => true,
						),
						array(
							'id'      => 'primary_button_scheme',
							'type'    => 'button_set',
							'title'   => esc_html__( 'Primary Button Scheme', 'redparts' ),
							'options' => array(
								'__default__' => esc_html__( '[Default]', 'redparts' ),
								'__custom__'  => esc_html__( '[Custom]', 'redparts' ),
							),
							'default' => '__default__',
						),
						array(
							'id'          => 'primary_button_normal_bg_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Primary Button Background Color', 'redparts' ),
							'default'     => '#e52727',
							'validate'    => 'color',
							'required'    => array( 'primary_button_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'          => 'primary_button_normal_font_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Primary Button Font Color', 'redparts' ),
							'default'     => '#ffffff',
							'validate'    => 'color',
							'required'    => array( 'primary_button_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'          => 'primary_button_hover_bg_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Primary Button Hover Background Color', 'redparts' ),
							'default'     => '#333333',
							'validate'    => 'color',
							'required'    => array( 'primary_button_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'          => 'primary_button_hover_font_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Primary Button Hover Font Color', 'redparts' ),
							'default'     => '#ffffff',
							'validate'    => 'color',
							'required'    => array( 'primary_button_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'     => 'primary_button_section_end',
							'type'   => 'section',
							'indent' => false,
						),
						// Primary button / End.
						// Secondary button.
						array(
							'id'     => 'secondary_button_section',
							'type'   => 'section',
							'title'  => esc_html__( 'Secondary Button', 'redparts' ),
							'indent' => true,
						),
						array(
							'id'      => 'secondary_button_scheme',
							'type'    => 'button_set',
							'title'   => esc_html__( 'Secondary Button Scheme', 'redparts' ),
							'options' => array(
								'__default__' => esc_html__( '[Default]', 'redparts' ),
								'__custom__'  => esc_html__( '[Custom]', 'redparts' ),
							),
							'default' => '__default__',
						),
						array(
							'id'          => 'secondary_button_normal_bg_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Secondary Button Background Color', 'redparts' ),
							'default'     => '#f0f0f0',
							'validate'    => 'color',
							'required'    => array( 'secondary_button_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'          => 'secondary_button_normal_font_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Secondary Button Font Color', 'redparts' ),
							'default'     => '#262626',
							'validate'    => 'color',
							'required'    => array( 'secondary_button_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'          => 'secondary_button_hover_bg_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Secondary Button Hover Background Color', 'redparts' ),
							'default'     => '#333333',
							'validate'    => 'color',
							'required'    => array( 'secondary_button_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'          => 'secondary_button_hover_font_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Secondary Button Hover Font Color', 'redparts' ),
							'default'     => '#ffffff',
							'validate'    => 'color',
							'required'    => array( 'secondary_button_scheme', '=', '__custom__' ),
							'transparent' => false,
						),
						array(
							'id'     => 'secondary_button_section_end',
							'type'   => 'section',
							'indent' => false,
						),
						// Secondary button / End.
					),
				)
			);

			// Colors / Links.
			Redux::set_section(
				self::REDUX_OPTION_NAME,
				array(
					'title'      => esc_html__( 'Links', 'redparts' ),
					'id'         => 'links',
					'subsection' => true,
					'fields'     => array(
						array(
							'id'          => 'link_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Link Color', 'redparts' ),
							'default'     => '#007bff',
							'validate'    => 'color',
							'transparent' => false,
						),
					),
				)
			);

			// Colors / Prices.
			Redux::set_section(
				self::REDUX_OPTION_NAME,
				array(
					'title'      => esc_html__( 'Prices', 'redparts' ),
					'id'         => 'colors-prices',
					'subsection' => true,
					'fields'     => array(
						array(
							'id'          => 'price_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Price color', 'redparts' ),
							'validate'    => 'color',
							'transparent' => false,
							'output'      => array(
								'color' => '.th-price, .widget_products .th-price',
							),
						),
						array(
							'id'          => 'new_price_color',
							'type'        => 'color',
							'title'       => esc_html__( 'New price color', 'redparts' ),
							'validate'    => 'color',
							'transparent' => false,
							'output'      => array(
								'color' => '.th-price ins',
							),
						),
						array(
							'id'          => 'old_price_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Old price color', 'redparts' ),
							'validate'    => 'color',
							'transparent' => false,
							'output'      => array(
								'color' => '.th-price del',
							),
						),
					),
				)
			);

			// Miscellaneous.
			Redux::set_section(
				self::REDUX_OPTION_NAME,
				array(
					'title'  => esc_html__( 'Miscellaneous', 'redparts' ),
					'id'     => 'miscellaneous',
					'icon'   => 'el el-lines',
					'fields' => array(
						array(
							'id'       => 'demo_mode',
							'type'     => 'select',
							'title'    => esc_html__( 'Enable demo mode', 'redparts' ),
							'subtitle' => esc_html__( 'This setting is only necessary to demonstrate the capabilities of the theme. On a real site, it should be disabled. If enabled, the URL parameter redparts_demo_options allows to override some settings.', 'redparts' ),
							'options'  => array(
								'yes' => esc_html__( 'Yes', 'redparts' ),
								'no'  => esc_html__( 'No', 'redparts' ),
							),
							'default'  => 'no',
						),
					),
				)
			);

			// Custom JS.
			Redux::set_section(
				self::REDUX_OPTION_NAME,
				array(
					'title'  => esc_html__( 'Custom JS', 'redparts' ),
					'id'     => 'custom-section',
					'icon'   => 'el el-file',
					'fields' => array(
						array(
							'id'      => 'custom_js',
							'type'    => 'ace_editor',
							'title'   => esc_html__( 'Custom JS', 'redparts' ),
							'mode'    => 'javascript',
							'theme'   => 'monokai',
							'options' => array(
								'minLines' => 20,
							),
						),
					),
				)
			);
		}

		/**
		 * Adds dynamic section the redux plugin.
		 *
		 * @noinspection PhpUnused
		 * @param array $sections Sections array.
		 * @return array
		 */
		public function redux_dynamic_sections( array $sections ): array {
			return $sections;
		}

		/**
		 * Returns array with theme settings.
		 *
		 * @return array
		 */
		public function get(): array {
			global $redparts_settings;
			global $redparts_settings_theme_mod;

			if ( ! isset( $redparts_settings ) ) {
				return array();
			}

			$settings = $redparts_settings;

			// Apply temporary customizer settings.
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			if ( ! empty( $_POST['wp_customize_render_partials'] ) ) {
				if ( ! isset( $redparts_settings_theme_mod ) ) {
					$redparts_settings_theme_mod = get_theme_mod( self::REDUX_OPTION_NAME, array() );
				}

				if ( ! empty( $redparts_settings_theme_mod ) ) {
					$settings = $redparts_settings_theme_mod;
				}
			}

			foreach ( $this->demo_options as $key => $value ) {
				if ( isset( $this->valid_demo_option[ $key ] ) ) {
					$valid_options = $this->valid_demo_option[ $key ];

					if ( 'color' === $valid_options && preg_match( '#\#([A-Fa-f0-9]{3}){1,2}#', $value ) ) {
						$settings[ $key ] = $value;
					} elseif ( is_array( $valid_options ) && in_array( $value, $valid_options, true ) ) {
						$settings[ $key ] = $value;
					}
				}
			}

			$desktop_spaceship_variants = array(
				'one'   => array(
					'desktop_spaceship_topbar_start_scheme' => 'theme',
					'desktop_spaceship_topbar_end_scheme' => 'dark',
					'desktop_header_scheme'               => 'light',
					'desktop_logo_color'                  => '__custom__',
					'desktop_logo_primary_color'          => 'theme',
					'desktop_logo_secondary_color'        => '#404040',
					'desktop_departments_button_normal_scheme' => 'light',
					'desktop_departments_button_hover_scheme' => 'theme',
					'desktop_indicator_counter_scheme'    => 'theme',
				),
				'two'   => array(
					'desktop_spaceship_topbar_start_scheme' => 'dark',
					'desktop_spaceship_topbar_end_scheme' => 'dark',
					'desktop_header_scheme'               => 'theme',
					'desktop_logo_color'                  => '__custom__',
					'desktop_logo_primary_color'          => 'rgba(255, 255, 255, .9)',
					'desktop_logo_secondary_color'        => 'rgba(255, 255, 255, .9)',
					'desktop_departments_button_normal_scheme' => 'theme',
					'desktop_departments_button_hover_scheme' => 'dark',
					'desktop_indicator_counter_scheme'    => 'dark',
				),
				'three' => array(
					'desktop_spaceship_topbar_start_scheme' => 'theme',
					'desktop_spaceship_topbar_end_scheme' => 'theme',
					'desktop_header_scheme'               => 'dark',
					'desktop_logo_color'                  => '__custom__',
					'desktop_logo_primary_color'          => 'rgba(255, 255, 255, .9)',
					'desktop_logo_secondary_color'        => 'rgba(255, 255, 255, .9)',
					'desktop_departments_button_normal_scheme' => 'accent',
					'desktop_departments_button_hover_scheme' => 'theme',
					'desktop_indicator_counter_scheme'    => 'accent',
				),
			);

			$desktop_classic_variants = array(
				'one'   => array(
					'desktop_classic_topbar_scheme'    => 'light',
					'desktop_classic_topbar_style'     => 'border',
					'desktop_header_scheme'            => 'light',
					'desktop_logo_color'               => '__custom__',
					'desktop_logo_primary_color'       => 'theme',
					'desktop_logo_secondary_color'     => '#404040',
					'desktop_departments_button_normal_scheme' => 'light',
					'desktop_departments_button_hover_scheme' => 'theme',
					'desktop_indicator_counter_scheme' => 'theme',
					'desktop_navbar_scheme'            => 'light',
					'desktop_navbar_stretch'           => 'no',
					'desktop_vehicle_button_scheme'    => 'accent',
				),
				'two'   => array(
					'desktop_classic_topbar_scheme'    => 'theme',
					'desktop_classic_topbar_style'     => 'background',
					'desktop_header_scheme'            => 'theme',
					'desktop_logo_color'               => '__custom__',
					'desktop_logo_primary_color'       => 'rgba(255, 255, 255, .9)',
					'desktop_logo_secondary_color'     => 'rgba(255, 255, 255, .6)',
					'desktop_departments_button_normal_scheme' => 'light',
					'desktop_departments_button_hover_scheme' => 'dark',
					'desktop_indicator_counter_scheme' => 'accent',
					'desktop_navbar_scheme'            => 'light',
					'desktop_navbar_stretch'           => 'yes',
					'desktop_vehicle_button_scheme'    => 'accent',
				),
				'three' => array(
					'desktop_classic_topbar_scheme'    => 'dark',
					'desktop_classic_topbar_style'     => 'background',
					'desktop_header_scheme'            => 'light',
					'desktop_logo_color'               => '__custom__',
					'desktop_logo_primary_color'       => 'theme',
					'desktop_logo_secondary_color'     => '#404040',
					'desktop_departments_button_normal_scheme' => 'theme',
					'desktop_departments_button_hover_scheme' => 'dark',
					'desktop_indicator_counter_scheme' => 'theme',
					'desktop_navbar_scheme'            => 'light',
					'desktop_navbar_stretch'           => 'no',
					'desktop_vehicle_button_scheme'    => 'accent',
				),
				'four'  => array(
					'desktop_classic_topbar_scheme'    => 'dark',
					'desktop_classic_topbar_style'     => 'background',
					'desktop_header_scheme'            => 'theme',
					'desktop_logo_color'               => '__custom__',
					'desktop_logo_primary_color'       => 'rgba(255, 255, 255, .9)',
					'desktop_logo_secondary_color'     => 'rgba(255, 255, 255, .6)',
					'desktop_departments_button_normal_scheme' => 'light',
					'desktop_departments_button_hover_scheme' => 'dark',
					'desktop_indicator_counter_scheme' => 'accent',
					'desktop_navbar_scheme'            => 'light',
					'desktop_navbar_stretch'           => 'yes',
					'desktop_vehicle_button_scheme'    => 'accent',
				),
				'five'  => array(
					'desktop_classic_topbar_scheme'    => 'dark',
					'desktop_classic_topbar_style'     => 'background',
					'desktop_header_scheme'            => 'light',
					'desktop_logo_color'               => '__custom__',
					'desktop_logo_primary_color'       => 'theme',
					'desktop_logo_secondary_color'     => '#404040',
					'desktop_departments_button_normal_scheme' => 'dark',
					'desktop_departments_button_hover_scheme' => 'dark',
					'desktop_indicator_counter_scheme' => 'theme',
					'desktop_navbar_scheme'            => 'theme',
					'desktop_navbar_stretch'           => 'yes',
					'desktop_vehicle_button_scheme'    => 'accent',
				),
			);

			$mobile_variants = array(
				'one' => array(
					'mobile_header_scheme'            => 'light',
					'mobile_logo_color'               => '__custom__',
					'mobile_logo_primary_color'       => 'theme',
					'mobile_logo_secondary_color'     => '#404040',
					'mobile_vehicle_button_scheme'    => 'accent',
					'mobile_indicator_counter_scheme' => 'theme',
				),
				'two' => array(
					'mobile_header_scheme'            => 'theme',
					'mobile_logo_color'               => '__custom__',
					'mobile_logo_primary_color'       => 'rgba(255, 255, 255, .9)',
					'mobile_logo_secondary_color'     => 'rgba(255, 255, 255, .6)',
					'mobile_vehicle_button_scheme'    => 'accent',
					'mobile_indicator_counter_scheme' => 'accent',
				),
			);

			if (
				isset( $settings['header_layout'] ) &&
				isset( $settings['desktop_use_predefined_variant'] ) &&
				'yes' === $settings['desktop_use_predefined_variant']
			) {
				if (
					'spaceship' === $settings['header_layout'] &&
					isset( $settings['desktop_spaceship_variant'] ) &&
					isset( $desktop_spaceship_variants[ $settings['desktop_spaceship_variant'] ] )
				) {
					$settings = array_merge(
						$settings,
						$desktop_spaceship_variants[ $settings['desktop_spaceship_variant'] ]
					);
				}

				if (
					'classic' === $settings['header_layout'] &&
					isset( $settings['desktop_classic_variant'] ) &&
					isset( $desktop_classic_variants[ $settings['desktop_classic_variant'] ] )
				) {
					$settings = array_merge(
						$settings,
						$desktop_classic_variants[ $settings['desktop_classic_variant'] ]
					);
				}
			}

			if (
				isset( $settings['mobile_use_predefined_variant'] ) &&
				isset( $settings['mobile_variant'] ) &&
				isset( $mobile_variants[ $settings['mobile_variant'] ] ) &&
				'yes' === $settings['mobile_use_predefined_variant']
			) {
				$settings = array_merge(
					$settings,
					$mobile_variants[ $settings['mobile_variant'] ]
				);
			}

			return $settings;
		}

		/**
		 * Returns value of the specified option.
		 *
		 * @param string $name    - Option name.
		 * @param mixed  $default - Default value.
		 *
		 * @return mixed
		 */
		public function get_option( string $name, $default = '' ) {
			$options = $this->get();

			if ( ! isset( $options[ $name ] ) ) {
				return $default;
			}

			return $options[ $name ];
		}

		/**
		 * Returns true if the specified option is available.
		 *
		 * @param string $name    - Option name.
		 *
		 * @return bool
		 */
		public function has_option( string $name ): bool {
			$options = $this->get();

			return isset( $options[ $name ] );
		}
	}
}
