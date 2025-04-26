<?php
/**
 * This file contains code related to the CSS styles.
 *
 * @package RedParts
 * @since 1.0.0
 */

namespace RedParts;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'RedParts\Style' ) ) {
	/**
	 * Class Style
	 */
	class Style extends Singleton {
		/**
		 * Initialization.
		 */
		public function init() {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		/**
		 * Enqueue scripts.
		 */
		public function enqueue_scripts() {
			$settings = Settings::instance();

			$google_fonts_url = '//fonts.googleapis.com/css?family=Roboto:400,400i,500,500i,700,700i';

			if ( ! $settings->get_option( 'default_fonts', true ) ) {
				$google_fonts_url = $this->get_google_fonts_url();
			}

			wp_enqueue_style(
				'redparts-google-fonts',
				$google_fonts_url,
				array(),
				RED_PARTS_VERSION
			);
		}

		/**
		 * Returns google fonts url.
		 */
		public function get_google_fonts_url(): string {
			$settings = Settings::instance();

			$fonts   = array();
			$subsets = array();
			$options = array(
				$settings->get_option( 'primary_font' ),
				$settings->get_option( 'headers_font' ),
			);

			foreach ( $options as $option ) {
				if (
					! empty( $option ) &&
					! empty( $option['font-family'] ) &&
					! empty( $option['font-weight'] )
				) {
					$family = $option['font-family'];
					$weight = $option['font-weight'];

					if ( ! empty( $option['font-style'] ) && 'italic' === $option['font-style'] ) {
						$weight .= 'i';
					}

					if ( ! isset( $fonts[ $family ] ) ) {
						$fonts[ $family ] = array();
					}

					$fonts[ $family ][] = $weight;

					if ( ! empty( $option['subsets'] ) ) {
						$subset = $option['subsets'];

						if ( ! in_array( $subset, $subsets, true ) ) {
							$subsets[] = $subset;
						}
					}
				}
			}

			$link = '';

			foreach ( $fonts as $family => $styles ) {
				if ( ! empty( $link ) ) {
					$link .= '|';
				}

				$link .= $family . ':' . implode( ',', $styles );
			}

			$link .= '&amp;subset=' . implode( ',', $subsets );
			$link  = '//fonts.googleapis.com/css?family=' . str_replace( '|', '%7C', $link );

			return $link;
		}

		/**
		 * Returns all available preset color schemes.
		 */
		public function get_presets(): array {
			$presets = array(
				'red'    => array(
					'theme' => array(
						'main_color'     => '#e52727',
						'opposite_color' => '#fff',
						'muted_color'    => '#fcc',
						'hover_color'    => 'rgba(0, 0, 0, .12)',
						'active_color'   => 'rgba(0, 0, 0, .2)',
						'divider_color'  => 'rgba(0, 0, 0, .1)',
						'arrow_color'    => 'rgba(0, 0, 0, .25)',
					),
				),
				'blue'   => array(
					'theme' => array(
						'main_color'     => '#1a79ff',
						'opposite_color' => '#fff',
						'muted_color'    => '#cce3ff',
						'hover_color'    => 'rgba(0, 0, 0, .12)',
						'active_color'   => 'rgba(0, 0, 0, .2)',
						'divider_color'  => 'rgba(0, 0, 0, .1)',
						'arrow_color'    => 'rgba(0, 0, 0, .25)',
					),
				),
				'green'  => array(
					'theme' => array(
						'main_color'     => '#006847',
						'opposite_color' => '#fff',
						'muted_color'    => '#c3e5d4',
						'hover_color'    => 'rgba(0, 0, 0, .12)',
						'active_color'   => 'rgba(0, 0, 0, .2)',
						'divider_color'  => 'rgba(0, 0, 0, .1)',
						'arrow_color'    => 'rgba(0, 0, 0, .25)',
					),
				),
				'orange' => array(
					'theme' => array(
						'main_color'     => '#f25900',
						'opposite_color' => '#fff',
						'muted_color'    => '#ffd7bf',
						'hover_color'    => 'rgba(0, 0, 0, .1)',
						'active_color'   => 'rgba(0, 0, 0, .2)',
						'divider_color'  => 'rgba(0, 0, 0, .1)',
						'arrow_color'    => 'rgba(0, 0, 0, .25)',
					),
				),
				'violet' => array(
					'theme' => array(
						'main_color'     => '#a63a70',
						'opposite_color' => '#fff',
						'muted_color'    => '#ffd4e9',
						'hover_color'    => 'rgba(0, 0, 0, .1)',
						'active_color'   => 'rgba(0, 0, 0, .2)',
						'divider_color'  => 'rgba(0, 0, 0, .1)',
						'arrow_color'    => 'rgba(0, 0, 0, .25)',
					),
				),
			);

			return apply_filters( 'redparts_style_presets', $presets );
		}

		/**
		 * Returns available schemes.
		 *
		 * @return Color[][]
		 */
		public function get_schemes(): array {
			static $cache = null;

			if ( null !== $cache ) {
				return $cache;
			}

			$schemes = array(
				'light'  => array(
					'main_color'     => '#fff',
					'opposite_color' => '#262626',
					'muted_color'    => '#767676',
					'hover_color'    => '#f0f0f0',
					'active_color'   => '#ededed',
					'divider_color'  => '#ebebeb',
					'arrow_color'    => '#bfbfbf',
				),
				'dark'   => array(
					'main_color'     => '#333',
					'opposite_color' => '#fff',
					'muted_color'    => '#9e9e9e',
					'hover_color'    => 'rgba(255, 255, 255, .08)',
					'active_color'   => 'rgba(255, 255, 255, .1)',
					'divider_color'  => 'rgba(0, 0, 0, .15)',
					'arrow_color'    => '#808080',
				),
				'accent' => array(
					'main_color'     => '#ffdf40',
					'opposite_color' => '#262626',
					'muted_color'    => '#8c7a23',
					'hover_color'    => '#ffd226', // 'rgba(255, 255, 255, .25)',
					'active_color'   => '#ffca16', // 'rgba(255, 255, 255, .2)',
					'divider_color'  => 'rgba(0, 0, 0, .06)',
					'arrow_color'    => 'rgba(0, 0, 0, .25)',
				),
				'theme'  => array(),
			);

			$presets  = $this->get_presets();
			$settings = Settings::instance();

			$preset_name = $settings->get_option( 'theme_scheme', 'red' );
			$preset      = array();

			if ( isset( $presets[ $preset_name ] ) ) {
				$preset = $presets[ $preset_name ];
			}

			foreach ( $schemes as $scheme_name => $scheme ) {
				if ( isset( $preset[ $scheme_name ] ) ) {
					$scheme = array_merge( $scheme, $preset[ $scheme_name ] );
				}

				if ( '__custom__' === $settings->get_option( $scheme_name . '_scheme' ) ) {
					$scheme = array_merge(
						$scheme,
						$this->make_scheme(
							$settings->get_option( $scheme_name . '_scheme_main_color' ),
							array(
								'opposite_color' => $settings->get_option( $scheme_name . '_scheme_opposite_color' ),
							)
						)
					);
				}

				$schemes[ $scheme_name ] = array_map( 'RedParts\Color::parse', $scheme );
			}

			$cache = apply_filters( 'redparts_style_schemes', $schemes );

			return $cache;
		}

		/**
		 * Returns the default scheme name for the specified element.
		 *
		 * @param string $element - Element name.
		 *
		 * @return string
		 */
		public function get_default_element_scheme_name( string $element ): string {
			$scheme_names = array(
				'desktop_spaceship_topbar_start'    => 'theme',
				'desktop_spaceship_topbar_end'      => 'dark',
				'desktop_classic_topbar'            => 'light',
				'desktop_header'                    => 'light',
				'desktop_departments_button_normal' => 'light',
				'desktop_departments_button_hover'  => 'theme',
				'desktop_indicator_counter'         => 'theme',
				'desktop_navbar'                    => 'light',
				'desktop_vehicle_button'            => 'accent',
				'mobile_header'                     => 'theme',
				'mobile_vehicle_button'             => 'accent',
				'mobile_indicator_counter'          => 'theme',
				'footer'                            => 'dark',
			);

			return $scheme_names[ $element ];
		}

		/**
		 * Returns the scheme of the specified element.
		 *
		 * @param string $element - Element name.
		 *
		 * @return Color[]
		 */
		public function get_element_scheme( string $element ): array {
			static $cache = array();

			if ( isset( $cache[ $element ] ) ) {
				return $cache[ $element ];
			}

			$settings    = Settings::instance();
			$schemes     = $this->get_schemes();
			$scheme_name = $settings->get_option(
				$element . '_scheme',
				$this->get_default_element_scheme_name( $element )
			);

			if ( '__custom__' === $scheme_name && $settings->has_option( $element . '_scheme_main_color' ) ) {
				$main_color = $settings->get_option( $element . '_scheme_main_color' );

				$base_scheme = array();
				$options     = array( 'opposite_color' );

				foreach ( $options as $option ) {
					if ( $settings->has_option( $element . '_scheme_' . $option ) ) {
						$base_scheme[ $option ] = $settings->get_option( $element . '_scheme_' . $option );
					}
				}

				$scheme = $this->make_scheme( $main_color, $base_scheme );
			} else {
				$scheme = $schemes[ $scheme_name ];
				$scheme = array_merge(
					$this->make_scheme( $scheme['main_color'], array() ),
					$scheme
				);
			}

			$cache[ $element ] = array_map( 'RedParts\Color::parse', $scheme );

			return apply_filters( 'redparts_style_element_scheme', $scheme, $element );
		}

		/**
		 * Makes and returns new color scheme based on specified color.
		 *
		 * @param Color|string $main_color  - Main color.
		 * @param array        $base_scheme - Base scheme.
		 *
		 * @return Color[]
		 */
		public function make_scheme( $main_color, $base_scheme = array() ): array {
			$main_color = Color::parse( $main_color );

			if ( isset( $base_scheme['opposite_color'] ) ) {
				$opposite_color = Color::parse( $base_scheme['opposite_color'] );

				unset( $base_scheme['opposite_color'] );
			} else {
				$opposite_color = Color::parse( $main_color->is_dark() ? '#fff' : '#262626' );
			}

			if ( $main_color->is_dark() ) {
				$muted_color   = $main_color->merge( '#fff', 4.72 );
				$hover_color   = $main_color->merge( '#fff', 1.28 );
				$active_color  = $main_color->merge( '#fff', 1.45 );
				$divider_color = $main_color->merge( '#fff', 1.5 );
				$arrow_color   = $main_color->merge( '#fff', 3.2 );
			} else {
				$muted_color   = $main_color->merge( $opposite_color, 3.2 );
				$hover_color   = $main_color->merge( '#000', 1.15 );
				$active_color  = $main_color->merge( '#000', 1.17 );
				$divider_color = $main_color->merge( '#000', 1.2 );
				$arrow_color   = $main_color->merge( '#000', 1.8 );
			}

			return array_merge(
				array(
					'main_color'     => $main_color,
					'opposite_color' => $opposite_color,
					'muted_color'    => $muted_color,
					'hover_color'    => $hover_color,
					'active_color'   => $active_color,
					'divider_color'  => $divider_color,
					'arrow_color'    => $arrow_color,
				),
				$base_scheme
			);
		}

		/**
		 * Makes CSS rule.
		 *
		 * @param array|string $selectors  - Array of CSS selectors.
		 * @param array|string $properties - Array of CSS properties.
		 *
		 * @return string
		 */
		public function make_rule( $selectors, $properties ): string {
			$selectors  = (array) $selectors;
			$properties = (array) $properties;

			$properties = array_map(
				function( $key, $value ) {
					return is_string( $key ) ? "$key: $value;" : $value;
				},
				array_keys( $properties ),
				array_values( $properties )
			);

			return implode( ', ', $selectors ) . '{' . implode( '', $properties ) . '}';
		}

		/**
		 * Makes CSS rule for font.
		 *
		 * @param array|string $selectors - Array of CSS selectors.
		 * @param array        $font      - Array of font options.
		 *
		 * @return string
		 */
		public function make_font_rule( $selectors, array $font ): string {
			$properties = array();

			if ( ! empty( $font['font-family'] ) ) {
				$family = "'" . $font['font-family'] . "'";

				if ( ! empty( $font['font-backup'] ) ) {
					$family .= ', ' . $font['font-backup'];
				}

				$properties['font-family'] = $family;
			}

			if ( ! empty( $font['font-weight'] ) ) {
				$properties['font-weight'] = $font['font-weight'];
			}

			$properties['font-style'] = ! empty( $font['font-style'] ) ? $font['font-style'] : 'normal';

			return empty( $properties ) ? '' : $this->make_rule( $selectors, $properties );
		}

		/**
		 * Returns common CSS.
		 */
		public function get_common_css(): string {
			$css      = '';
			$settings = Settings::instance();

			if ( ! $settings->get_option( 'default_fonts', true ) ) {
				$css .= $this->make_font_rule(
					array(
						'body',
						'.woocommerce-loop-product__title',
						'.th-post-card__title h2',
						'.th-categories-list__item-title',
						'.woocommerce-checkout .woocommerce-form__label-for-checkbox',
					),
					$settings->get_option( 'primary_font', array() )
				);

				$css .= $this->make_font_rule(
					array(
						'h1',
						'h2',
						'h3',
						'h4',
						'h5',
						'h6',
						'.th-block-finder__title',
						'.th-block-sale__title',
						'.th-category-card--layout--overlay .th-category-card__name',
						'.th-block-banners__item-title',
						'.th-block-products-columns__title',
						'.th-block-categories__title',
						'.th-vehicle-picker-modal__header',
						'.th-post-card--layout--classic .th-post-card__title h2',
						'.edit-account legend',
						'.woocommerce-address-fields legend',
						'.th-block-about__card-title',
						'.th-block-teammates__title',
						'.th-block-reviews__title',
					),
					$settings->get_option( 'headers_font', array() )
				);
			}

			if ( $settings->get_option( 'override_elementor_primary_color' ) ) {
				$schemes       = $this->get_schemes();
				$primary_color = $schemes['theme']['main_color'];

				$css .= $this->make_rule(
					'body.elementor-page',
					array( '--e-global-color-primary' => $primary_color )
				);
			}

			return $css;
		}

		/**
		 * Returns CSS related to the header.
		 */
		public function get_desktop_header_css(): string {
			$css  = '';
			$css .= $this->get_desktop_logo_css();
			$css .= $this->get_desktop_indicator_counter_css();

			$settings = Settings::instance();

			if ( 'spaceship' === $settings->get_option( 'header_layout', 'spaceship' ) ) {
				// Spaceship.
				$css .= $this->get_desktop_spaceship_topbar_css( 'start' );
				$css .= $this->get_desktop_spaceship_topbar_css( 'end' );
				$css .= $this->get_desktop_spaceship_header_css();
				$css .= $this->get_desktop_spaceship_departments_button_css();
				$css .= $this->get_desktop_spaceship_search_css();
			} else {
				// Classic.
				$css .= $this->get_desktop_classic_topbar_css();
				$css .= $this->get_desktop_classic_header_css();
				$css .= $this->get_desktop_classic_departments_button_css();
				$css .= $this->get_desktop_classic_navbar_css();
				$css .= $this->get_desktop_classic_vehicle_button_css();
			}

			// #c5dfe2;

			return $css;
		}

		/**
		 * Returns CSS related to the desktop logo.
		 */
		public function get_desktop_logo_css(): string {
			$css = '';

			$settings        = Settings::instance();
			$scheme          = $this->get_element_scheme( 'desktop_header' );
			$font_color      = $scheme['opposite_color'];
			$primary_color   = $font_color;
			$secondary_color = $font_color;

			if ( '__custom__' === $settings->get_option( 'desktop_logo_color' ) ) {
				$primary_color = $settings->get_option( 'desktop_logo_primary_color', $primary_color );

				if ( 'theme' === $primary_color ) {
					$schemes       = $this->get_schemes();
					$primary_color = $schemes['theme']['main_color'];
				}

				$primary_color   = Color::parse( $primary_color );
				$secondary_color = Color::parse( $settings->get_option( 'desktop_logo_secondary_color', $secondary_color ) );
			}

			$css .= ".th-logo--desktop .th-part-primary { color: $primary_color; }";
			$css .= ".th-logo--desktop .th-part-secondary { color: $secondary_color }";

			return $css;
		}

		/**
		 * Returns CSS related to the spaceship indicator counter.
		 */
		public function get_desktop_indicator_counter_css(): string {
			$css = '';

			$scheme     = $this->get_element_scheme( 'desktop_indicator_counter' );
			$bg_color   = $scheme['main_color'];
			$font_color = $scheme['opposite_color'];

			$css .= ".th-indicator__counter:before { background: $bg_color; }";
			$css .= ".th-indicator__counter { color: $font_color; }";

			if ( $font_color->is_dark() ) {
				$css .= '@media (-webkit-max-device-pixel-ratio: 1), (max-resolution: 1dppx) {';
				$css .= '.th-indicator__counter { font-weight: 500; }';
				$css .= '}';
			}

			return $css;
		}

		/**
		 * Returns CSS related to the spaceship topbar.
		 *
		 * @param string $side Topbar side: "start" or "end".
		 * @return string
		 */
		public function get_desktop_spaceship_topbar_css( string $side ): string {
			$css = '';

			$scheme      = $this->get_element_scheme( 'desktop_spaceship_topbar_' . $side );
			$bg_color    = $scheme['main_color'];
			$font_color  = $scheme['opposite_color'];
			$muted_color = $scheme['muted_color'];
			$hover_color = $scheme['hover_color'];
			$arrow_color = $scheme['arrow_color'];

			$css .= $this->make_rule(
				array(
					".th-header__topbar-$side-bg:before",
					".th-topbar--$side",
				),
				"background: $bg_color;"
			);
			$css .= ".th-topbar--$side > .menu-item > a { color: $muted_color; }";
			$css .= $this->make_rule(
				array(
					".th-topbar--$side > .menu-item:hover > a",
					".th-topbar--$side > .menu-item > a .menu-item-value",
					".th-topbar--$side > .menu-item > a:not([href])",
				),
				"color: $font_color;"
			);
			$css .= ".th-topbar--$side > .menu-item-has-children:hover > a { background: $hover_color; }";
			$css .= ".th-topbar--$side > .menu-item > a svg { fill: $arrow_color; }";

			if ( $font_color->contrast( '#000' ) <= 2 ) {
				$css .= '@media (-webkit-max-device-pixel-ratio: 1), (max-resolution: 1dppx) {';
				$css .= $this->make_rule(
					array(
						".th-topbar--$side > .menu-item > a .menu-item-value",
						".th-topbar--$side > .menu-item > a:not([href])",
					),
					'font-weight: 500;'
				);
				$css .= '}';
			}

			return $css;
		}

		/**
		 * Returns CSS related to the spaceship header.
		 *
		 * @return string
		 */
		public function get_desktop_spaceship_header_css(): string {
			$css = '';

			$scheme      = $this->get_element_scheme( 'desktop_header' );
			$bg_color    = $scheme['main_color'];
			$font_color  = $scheme['opposite_color'];
			$muted_color = $scheme['muted_color'];
			$hover_color = $scheme['hover_color'];
			$arrow_color = $scheme['arrow_color'];

			$site_bg_color = Color::parse( '#fafafa' );

			if ( $site_bg_color->contrast( '#fff' ) < $bg_color->contrast( '#fff' ) ) {
				$css .= $this->make_rule(
					array(
						'.th-header',
						'.th-search--location--desktop-header .th-search__decor-end:before',
						'.th-search--location--desktop-header .th-search__decor-start:before',
					),
					'box-shadow: none;'
				);
			}

			$css .= ".th-header { color: $font_color; }";
			$css .= $this->make_rule(
				array(
					'.th-header',
					'.th-search--location--desktop-header .th-search__decor-start:before',
					'.th-search--location--desktop-header .th-search__decor-end:before',
				),
				"background: $bg_color;"
			);

			$css .= $this->make_rule(
				array(
					'.th-logo--desktop .th-logo__slogan',
					'.th-indicator__title',
				),
				"color: $muted_color;"
			);

			$css .= $this->make_rule(
				array(
					'.th-main-menu__list > .menu-item:hover > a',
					'.th-indicator--open .th-indicator__button',
					'.th-indicator:hover .th-indicator__button',
				),
				"background: $hover_color;"
			);

			$css .= ".th-main-menu__list > .menu-item-has-children > a svg { color: $arrow_color; }";

			if ( $font_color->is_white() ) {
				$css .= '@media (-webkit-max-device-pixel-ratio: 1), (max-resolution: 1dppx) {';
				$css .= '.th-main-menu__list > .menu-item > a { font-weight: 400; }';
				$css .= '.th-indicator__value { font-weight: 400; }';
				$css .= '}';
			}

			return $css;
		}

		/**
		 * Returns CSS related to the spaceship departments button.
		 *
		 * @return string
		 */
		public function get_desktop_spaceship_departments_button_css(): string {
			$css = '';

			$header_scheme   = $this->get_element_scheme( 'desktop_header' );
			$header_bg_color = $header_scheme['main_color'];

			$normal_scheme     = $this->get_element_scheme( 'desktop_departments_button_normal' );
			$normal_bg_color   = $normal_scheme['main_color'];
			$normal_font_color = $normal_scheme['opposite_color'];

			if ( ! $normal_bg_color->compare( $header_bg_color ) ) {
				$inline_end = is_rtl() ? 'left' : 'right';

				$css .= ".th-header__navbar-departments { margin-$inline_end: 8px; }";
			}

			$css .= ".th-departments__button { background: $normal_bg_color; }";
			$css .= ".th-departments__button { color: $normal_font_color; }";
			$css .= ".th-departments__button .th-departments__button-icon { color: $normal_font_color; }";

			if ( $normal_font_color->is_dark() ) {
				$css .= '@media (-webkit-max-device-pixel-ratio: 1), (max-resolution: 1dppx) {';
				$css .= '.th-departments__button { font-weight: 500; }';
				$css .= '}';
			}

			$hover_scheme     = $this->get_element_scheme( 'desktop_departments_button_hover' );
			$hover_bg_color   = $hover_scheme['main_color'];
			$hover_font_color = $hover_scheme['opposite_color'];

			$css .= $this->make_rule(
				array(
					'.th-departments--open .th-departments__button',
					'.th-departments__button:hover',
				),
				"background: $hover_bg_color; color: $hover_font_color;"
			);
			$css .= $this->make_rule(
				array(
					'.th-departments--open .th-departments__button .th-departments__button-icon',
					'.th-departments__button:hover .th-departments__button-icon',
				),
				"color: $hover_font_color;"
			);

			return $css;
		}

		/**
		 * Returns CSS related to the spaceship search.
		 *
		 * @return string
		 */
		public function get_desktop_spaceship_search_css(): string {
			$css = '';

			$scheme      = $this->get_element_scheme( 'desktop_header' );
			$bg_color    = $scheme['main_color'];
			$font_color  = $scheme['opposite_color'];
			$muted_color = $scheme['muted_color'];

			if ( $bg_color->is_dark() ) {
				$icon_color      = $bg_color->merge( '#fff', 3 );
				$hover_bg_color  = $bg_color->merge( '#fff', 1.4 );
				$active_bg_color = $bg_color->merge( '#fff', 1.9 );
			} else {
				$icon_color      = $bg_color->merge( '#000', 2.1 );
				$hover_bg_color  = $bg_color->merge( '#000', 1.15 );
				$active_bg_color = $bg_color->merge( '#000', 1.25 );
			}

			// Divider.
			$css .= $this->make_rule(
				array(
					'.th-search--location--desktop-header .th-search__button:before',
					'.th-search--location--desktop-header .th-search__button:before',
				),
				"background: $hover_bg_color;"
			);
			// Button.
			$css .= ".th-search--location--desktop-header .th-search__button { color: $icon_color; }";
			$css .= $this->make_rule(
				array(
					'.th-search--location--desktop-header .th-search__button--hover:after',
					'.th-search--location--desktop-header .th-search__button:hover:after',
				),
				"background: $hover_bg_color;"
			);
			$css .= $this->make_rule(
				array(
					'.th-search--location--desktop-header .th-search__button--hover:active:after',
					'.th-search--location--desktop-header .th-search__button:active:after',
				),
				"background: $active_bg_color;"
			);
			// Input.
			$css .= $this->make_rule(
				array(
					'.th-search--location--desktop-header .th-search__box',
					'.th-search--location--desktop-header .th-search__input:hover ~ .th-search__box',
					'.th-search--location--desktop-header .th-search__input:focus ~ .th-search__box',
				),
				"color: $hover_bg_color;"
			);
			$css .= ".th-search--location--desktop-header .th-search__input { color: $font_color; }";
			$css .= ".th-search--location--desktop-header .th-search__input::placeholder { color: $muted_color; }";

			return $css;
		}

		/**
		 * Returns CSS related to the classic topbar.
		 *
		 * @return string
		 */
		public function get_desktop_classic_topbar_css(): string {
			$css = '';

			$settings = Settings::instance();

			$header_scheme = $this->get_element_scheme( 'desktop_header' );

			$scheme        = $this->get_element_scheme( 'desktop_classic_topbar' );
			$bg_color      = $scheme['main_color'];
			$font_color    = $scheme['opposite_color'];
			$muted_color   = $scheme['muted_color'];
			$hover_color   = $scheme['hover_color'];
			$arrow_color   = $scheme['arrow_color'];
			$divider_color = $scheme['divider_color'];

			$is_equal            = $header_scheme['main_color']->compare( $bg_color );
			$is_background_style = 'background' === $settings->get_option( 'desktop_classic_topbar_style' );
			$is_border_style     = ! $is_background_style;

			if ( $is_equal && $is_border_style ) {
				$css .= '.th-header { --th-topbar-height: 36px; }';
				$css .= ".th-header__topbar-classic-bg { border-bottom: 1px solid $divider_color; }";
				$css .= '.th-header__topbar-classic { padding-bottom: 1px; }';
			} else {
				$css .= '.th-header { --th-topbar-height: 34px; }';
				$css .= '.th-header__topbar-classic-bg { border-bottom: none; }';
				$css .= '.th-header__topbar-classic { padding-bottom: 0; }';

				if ( $is_equal ) {
					if ( $bg_color->contrast( '#fff' ) > 3 ) {
						$bg_color = $bg_color->merge( '#000', 1.2 );
					} else {
						$bg_color = $bg_color->merge( '#000', 1.11 );
					}
				}
			}

			$css .= $this->make_rule(
				array(
					'.th-header__topbar-classic-bg',
					'.th-topbar',
				),
				"background: $bg_color;"
			);
			$css .= ".th-topbar > .menu-item > a { color: $muted_color; }";
			$css .= $this->make_rule(
				array(
					'.th-topbar > .menu-item:hover > a',
					'.th-topbar > .menu-item > a .menu-item-value',
					'.th-topbar > .menu-item > a:not([href])',
				),
				"color: $font_color;"
			);
			$css .= ".th-topbar > .menu-item-has-children:hover > a { background: $hover_color; }";
			$css .= ".th-topbar > .menu-item > a svg { fill: $arrow_color; }";

			if ( $font_color->is_white() ) {
				$css .= '@media (-webkit-max-device-pixel-ratio: 1), (max-resolution: 1dppx) {';
				$css .= $this->make_rule(
					array(
						'.th-topbar > .menu-item > a .menu-item-value',
						'.th-topbar > .menu-item > a:not([href])',
					),
					'font-weight: 400;'
				);
				$css .= '}';
			}

			return $css;
		}

		/**
		 * Returns CSS related to the classic header.
		 *
		 * @return string
		 */
		public function get_desktop_classic_header_css(): string {
			$css = '';

			$navbar_scheme = $this->get_element_scheme( 'desktop_navbar' );

			$scheme      = $this->get_element_scheme( 'desktop_header' );
			$bg_color    = $scheme['main_color'];
			$font_color  = $scheme['opposite_color'];
			$muted_color = $scheme['muted_color'];
			$hover_color = $scheme['hover_color'];

			$site_bg_color = Color::parse( '#fafafa' );

			if ( $site_bg_color->contrast( '#fff' ) < $navbar_scheme['main_color']->contrast( '#fff' ) ) {
				$css .= '.th-header { box-shadow: none; }';
			}

			$css .= ".th-header { background: $bg_color; }";
			$css .= ".th-header { color: $font_color; }";
			$css .= ".th-indicator__title { color: $muted_color; }";
			$css .= $this->make_rule(
				array(
					'.th-indicator--open .th-indicator__button',
					'.th-indicator:hover .th-indicator__button',
				),
				"background: $hover_color;"
			);

			if ( $font_color->is_white() ) {
				$css .= '@media (-webkit-max-device-pixel-ratio: 1), (max-resolution: 1dppx) {';
				$css .= '.th-indicator__value { font-weight: 400; }';
				$css .= '}';
			}

			return $css;
		}

		/**
		 * Returns CSS related to the classic departments button.
		 *
		 * @return string
		 */
		public function get_desktop_classic_departments_button_css(): string {
			$css = '';

			// Normal.
			$normal_scheme      = $this->get_element_scheme( 'desktop_departments_button_normal' );
			$normal_bg_color    = $normal_scheme['main_color'];
			$normal_font_color  = $normal_scheme['opposite_color'];
			$normal_arrow_color = $normal_scheme['arrow_color'];

			$css .= ".th-departments__button { background: $normal_bg_color; }";
			$css .= ".th-departments__button { color: $normal_font_color; }";
			$css .= ".th-departments__button .th-departments__button-icon { color: $normal_font_color; }";
			$css .= ".th-departments__button .th-departments__button-arrow { color: $normal_arrow_color; }";

			if ( $normal_font_color->is_dark() ) {
				$css .= '@media (-webkit-max-device-pixel-ratio: 1), (max-resolution: 1dppx) {';
				$css .= '.th-departments__button { font-weight: 500; }';
				$css .= '}';
			}

			// Hover.
			$hover_scheme      = $this->get_element_scheme( 'desktop_departments_button_hover' );
			$hover_bg_color    = $hover_scheme['main_color'];
			$hover_font_color  = $hover_scheme['opposite_color'];
			$hover_arrow_color = $hover_scheme['arrow_color'];

			$css .= $this->make_rule(
				array(
					'.th-departments--open .th-departments__button',
					'.th-departments__button:hover',
				),
				"background: $hover_bg_color; color: $hover_font_color;"
			);
			$css .= $this->make_rule(
				array(
					'.th-departments--open .th-departments__button .th-departments__button-icon',
					'.th-departments__button:hover .th-departments__button-icon',
				),
				"color: $hover_font_color;"
			);
			$css .= $this->make_rule(
				array(
					'.th-departments--open .th-departments__button .th-departments__button-arrow',
					'.th-departments__button:hover .th-departments__button-arrow',
				),
				"color: $hover_arrow_color;"
			);

			$settings = Settings::instance();

			if ( 'yes' === $settings->get_option( 'desktop_navbar_stretch' ) ) {
				$css .= '.th-departments__button { border-radius: 0; }';
			}

			return $css;
		}

		/**
		 * Returns CSS related to the classic navbar.
		 *
		 * @return string
		 */
		public function get_desktop_classic_navbar_css(): string {
			$css = '';

			$settings = Settings::instance();

			$header_scheme      = $this->get_element_scheme( 'desktop_header' );
			$departments_scheme = $this->get_element_scheme( 'desktop_departments_button_normal' );

			$scheme        = $this->get_element_scheme( 'desktop_navbar' );
			$bg_color      = $scheme['main_color'];
			$font_color    = $scheme['opposite_color'];
			$muted_color   = $scheme['muted_color'];
			$hover_color   = $scheme['hover_color'];
			$divider_color = $scheme['divider_color'];
			$arrow_color   = $scheme['arrow_color'];

			$css .= '.th-header__navbar { height: 52px; }';
			$css .= ".th-header__navbar { background: $bg_color; color: $font_color; }";
			$css .= ".th-phone__subtitle { color: $muted_color; }";

			if ( $bg_color->compare( $header_scheme['main_color'] ) ) {
				$css .= ".th-header__navbar { border-top: 1px solid $divider_color; }";
			} else {
				$css .= '.th-header__navbar { border-top: none; }';
			}

			if ( $bg_color->compare( $header_scheme['main_color'] ) ) {
				if ( 'yes' === $settings->get_option( 'desktop_navbar_stretch' ) ) {
					$css .= '.th-header__navbar { height: 47px; padding-top: 0; padding-bottom: 0; }';
				} else {
					$css .= '.th-header__navbar { height: 53px; }';
				}
			} elseif ( 'yes' === $settings->get_option( 'desktop_navbar_stretch' ) ) {
				$css .= '.th-header__navbar { height: 46px; padding-top: 0; padding-bottom: 0; }';
			}

			if ( 'yes' === $settings->get_option( 'desktop_navbar_stretch' ) ) {
				$css .= $this->make_rule(
					array(
						'.th-main-menu__list > .menu-item > a',
						'.th-phone__body',
					),
					'border-radius: 0;'
				);
			}

			if ( $bg_color->compare( $departments_scheme['main_color'] ) ) {
				$css .= ".th-header__navbar-departments:after { background: $divider_color; }";
			} else {
				$css .= '.th-header__navbar-departments:after { background: transparent; }';
			}

			$css .= ".th-main-menu__list > .menu-item-has-children > a svg { color: $arrow_color; }";
			$css .= $this->make_rule(
				array(
					'.th-main-menu__list > .menu-item:hover > a',
					'.th-phone__body:hover',
				),
				"background: $hover_color;"
			);

			if ( $font_color->is_white() ) {
				$css .= '@media (-webkit-max-device-pixel-ratio: 1), (max-resolution: 1dppx) {';
				$css .= $this->make_rule(
					array(
						'.th-main-menu__list > .menu-item > a',
						'.th-phone__title',
					),
					'font-weight: 400;'
				);
				$css .= '}';
			}

			return $css;
		}

		/**
		 * Returns CSS related to the classic vehicle button.
		 *
		 * @return string
		 */
		public function get_desktop_classic_vehicle_button_css(): string {
			$css = '';

			$scheme      = $this->get_element_scheme( 'desktop_vehicle_button' );
			$bg_color    = $scheme['main_color'];
			$font_color  = $scheme['opposite_color'];
			$hover_color = $bg_color->merge( $scheme['hover_color'] );
			$arrow_color = $scheme['arrow_color'];
			$icon_color  = clone $font_color;

			$icon_color->alpha = .9;

			$svg = "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 5 10'><path fill='$arrow_color' d='M4.503,4 L0.497,4 C0.094,4 -0.142,3.492 0.089,3.122 L2.095,0.233 C2.293,-0.084 2.712,-0.084 2.911,0.233 L4.911,3.122 C5.142,3.492 4.906,4 4.503,4 ZM0.497,6 L4.503,6 C4.906,6 5.142,6.504 4.911,6.871 L2.905,9.737 C2.707,10.052 2.288,10.052 2.089,9.737 L0.089,6.871 C-0.142,6.504 0.094,6 0.497,6 Z'></path></svg>";
			$svg = rawurlencode( $svg );

			$css .= ".th-search--location--desktop-header .th-search__button--vehicle { background-color: $bg_color; }";
			$css .= ".th-search--location--desktop-header .th-search__button--vehicle { color: $font_color; }";
			$css .= ".th-search--location--desktop-header .th-search__button--vehicle .th-search__button-icon { fill: $icon_color; }";
			$css .= ".th-search--location--desktop-header .th-search__button--vehicle { background-image: url('data:image/svg+xml,$svg'); }";

			$css .= ".th-search--location--desktop-header .th-search__button--vehicle:hover { background-color: $hover_color; }";
			$css .= ".th-search--location--desktop-header .th-search__button--vehicle:active { background-color: $bg_color; }";

			return $css;
		}

		/**
		 * Returns CSS related to the mobile header.
		 *
		 * @return string
		 */
		public function get_mobile_header_css(): string {
			$css = '';

			$settings     = Settings::instance();
			$scheme       = $this->get_element_scheme( 'mobile_header' );
			$bg_color     = $scheme['main_color'];
			$font_color   = $scheme['opposite_color'];
			$hover_color  = $scheme['hover_color'];
			$active_color = $scheme['active_color'];

			$site_bg_color = Color::parse( '#fafafa' );

			if ( $site_bg_color->contrast( '#fff' ) < $bg_color->contrast( '#fff' ) ) {
				$css .= '.th-mobile-header { box-shadow: none; }';
			}

			$css .= $this->make_rule(
				array(
					'.th-mobile-header',
					'.th-mobile-header__menu-button',
				),
				"background: $bg_color"
			);

			$css .= $this->make_rule(
				array(
					'.th-mobile-header__menu-button:hover',
					'.th-mobile-indicator:hover .th-mobile-indicator__button',
				),
				"background: $hover_color"
			);

			$css .= $this->make_rule(
				'.th-mobile-header__menu-button:active',
				"background: $active_color"
			);

			$css .= $this->make_rule(
				array(
					'.th-mobile-indicator__icon',
					'.th-mobile-header__menu-button',
				),
				"fill: $font_color"
			);

			// Logo.
			$scheme          = $this->get_element_scheme( 'mobile_header' );
			$font_color      = $scheme['opposite_color'];
			$primary_color   = $font_color;
			$secondary_color = $font_color;

			if ( '__custom__' === $settings->get_option( 'mobile_logo_color' ) ) {
				$primary_color = $settings->get_option( 'mobile_logo_primary_color', $primary_color );

				if ( 'theme' === $primary_color ) {
					$schemes       = $this->get_schemes();
					$primary_color = $schemes['theme']['main_color'];
				}

				$primary_color   = Color::parse( $primary_color );
				$secondary_color = Color::parse( $settings->get_option( 'mobile_logo_secondary_color', $secondary_color ) );
			}

			$css .= ".th-logo--mobile .th-part-primary { color: $primary_color; }";
			$css .= ".th-logo--mobile .th-part-secondary { color: $secondary_color }";

			// Vehicle Button.
			$scheme      = $this->get_element_scheme( 'mobile_vehicle_button' );
			$bg_color    = $scheme['main_color'];
			$font_color  = $scheme['opposite_color'];
			$hover_color = $bg_color->merge( $scheme['hover_color'] );
			$icon_color  = clone $font_color;

			$icon_color->alpha = .9;

			$css .= ".th-search--location--mobile-header .th-search__button--vehicle { background-color: $bg_color; }";
			$css .= ".th-search--location--mobile-header .th-search__button--vehicle { color: $font_color; }";
			$css .= ".th-search--location--mobile-header .th-search__button--vehicle .th-search__button-icon { fill: $icon_color; }";

			$css .= ".th-search--location--mobile-header .th-search__button--vehicle:hover { background-color: $hover_color; }";
			$css .= ".th-search--location--mobile-header .th-search__button--vehicle:active { background-color: $bg_color; }";

			// Indicator Counter.
			$scheme     = $this->get_element_scheme( 'mobile_indicator_counter' );
			$bg_color   = $scheme['main_color'];
			$font_color = $scheme['opposite_color'];

			$css .= ".th-mobile-indicator__counter:before { background: $bg_color; }";
			$css .= ".th-mobile-indicator__counter { color: $font_color; }";

			if ( $font_color->is_dark() ) {
				$css .= '@media (-webkit-max-device-pixel-ratio: 1), (max-resolution: 1dppx) {';
				$css .= '.th-mobile-indicator__counter { font-weight: 500; }';
				$css .= '}';
			}

			return $css;
		}

		/**
		 * Returns CSS related to the footer.
		 *
		 * @return string
		 */
		public function get_footer_css(): string {
			$css = '';

			$scheme        = $this->get_element_scheme( 'footer' );
			$bg_color      = $scheme['main_color'];
			$font_color    = $scheme['opposite_color'];
			$muted_color   = $scheme['muted_color'];
			$site_bg_color = Color::parse( '#fafafa' );

			if ( $site_bg_color->contrast( '#fff' ) >= $bg_color->contrast( '#fff' ) ) {
				$css .= $this->make_rule(
					array(
						'.th-site-footer',
						'.th-site-footer__decor .th-decor__center',
						'.th-site-footer__decor .th-decor__end',
						'.th-site-footer__decor .th-decor__start',
					),
					'box-shadow: 0 -1px 3px rgba(0, 0, 0, .08)'
				);
				$css .= '.th-mobile-header { box-shadow: none; }';
			}

			$css .= $this->make_rule(
				array(
					'.th-site-footer',
					'.th-site-footer__decor .th-decor__center',
					'.th-site-footer__decor .th-decor__end',
					'.th-site-footer__decor .th-decor__start',
				),
				array(
					'background' => $bg_color,
				)
			);
			$css .= $this->make_rule(
				array(
					'.th-site-footer',
					'.th-site-footer__bottom',
				),
				array(
					'color' => $muted_color,
				)
			);
			$css .= $this->make_rule(
				array(
					'.th-site-footer__widget-title',
					'.th-footer-links__title',
					'.th-footer-contacts__contacts dd',
					'.th-footer-links__list a:hover',
					'.th-site-footer__bottom a',
				),
				array(
					'color' => $font_color,
				)
			);

			if ( $bg_color->is_dark() ) {
				$control_bg_one = $bg_color->merge( '#fff', 1.47 );
				$control_bg_two = $bg_color->merge( '#fff', 1.67 );
			} else {
				$control_bg_one = $bg_color->merge( '#000', 1.2 );
				$control_bg_two = $bg_color->merge( '#000', 1.3 );
			}

			// Input.
			$css .= $this->make_rule(
				array(
					'.th-footer-newsletter .th-subscribe-form__field::placeholder',
					'.th-footer-newsletter .th-subscribe-form__field:focus::placeholder',
				),
				array(
					'color' => $muted_color,
				)
			);
			$css .= $this->make_rule(
				'.th-footer-newsletter .th-subscribe-form__field',
				array(
					'color'            => $font_color,
					'background-color' => $control_bg_one,
					'border-color'     => $control_bg_one,
				)
			);
			$css .= $this->make_rule(
				'.th-footer-newsletter .th-subscribe-form__field:hover',
				array(
					'background-color' => $control_bg_two,
					'border-color'     => $control_bg_two,
				)
			);
			$css .= $this->make_rule(
				'.th-footer-newsletter .th-subscribe-form__field:focus',
				array(
					'background-color' => 'transparent',
					'border-color'     => $control_bg_two,
				)
			);

			// Button.
			$css .= $this->make_rule(
				'.th-site-footer',
				array(
					'--th-btn-context-hover-bg-color'    => $control_bg_two,
					'--th-btn-context-hover-font-color'  => $font_color,
					'--th-btn-context-active-bg-color'   => $control_bg_one,
					'--th-btn-context-active-font-color' => $font_color,
				)
			);

			// Bottom.
			if ( $bg_color->contrast( '#fff' ) > 3 ) {
				$bg_color = $bg_color->merge( '#000', 1.12 );
			} else {
				$bg_color = $bg_color->merge( '#000', 1.06 );
			}

			$css .= $this->make_rule(
				array(
					'.th-site-footer__bottom',
				),
				array(
					'background' => $bg_color,
				)
			);

			return $css;
		}

		/**
		 * Returns CSS related to the newsletter widget.
		 *
		 * @return string
		 */
		public function get_newsletter_widget_css(): string {
			$css = '';

			$scheme      = $this->get_element_scheme( 'footer' );
			$bg_color    = $scheme['main_color'];
			$font_color  = $scheme['opposite_color'];
			$muted_color = $scheme['muted_color'];

			$css .= $this->make_rule(
				array(
					'.widget_mc4wp_form_widget',
				),
				array(
					'background' => $bg_color,
					'color'      => $font_color,
				)
			);
			$css .= $this->make_rule(
				'.widget_mc4wp_form_widget .th-subscribe-form__text',
				array(
					'color' => $muted_color,
				)
			);

			$divider_color        = clone $font_color;
			$divider_color->alpha = .13;

			$css .= $this->make_rule(
				'.widget_mc4wp_form_widget .th-widget__title:after',
				array(
					'background' => $divider_color,
				)
			);

			if ( $bg_color->is_dark() ) {
				$control_bg_one = $bg_color->merge( '#fff', 1.47 );
				$control_bg_two = $bg_color->merge( '#fff', 1.67 );
			} else {
				$control_bg_one = $bg_color->merge( '#000', 1.2 );
				$control_bg_two = $bg_color->merge( '#000', 1.3 );
			}

			// Input.
			$css .= $this->make_rule(
				array(
					'.widget_mc4wp_form_widget .th-subscribe-form__field::placeholder',
					'.widget_mc4wp_form_widget .th-subscribe-form__field:focus::placeholder',
				),
				array(
					'color' => $muted_color,
				)
			);
			$css .= $this->make_rule(
				'.widget_mc4wp_form_widget .th-subscribe-form__field',
				array(
					'color'            => $font_color,
					'background-color' => $control_bg_one,
					'border-color'     => $control_bg_one,
				)
			);
			$css .= $this->make_rule(
				'.widget_mc4wp_form_widget .th-subscribe-form__field:hover',
				array(
					'background-color' => $control_bg_two,
					'border-color'     => $control_bg_two,
				)
			);
			$css .= $this->make_rule(
				'.widget_mc4wp_form_widget .th-subscribe-form__field:focus',
				array(
					'background-color' => 'transparent',
					'border-color'     => $control_bg_two,
				)
			);

			// Button.
			$css .= $this->make_rule(
				'.widget_mc4wp_form_widget',
				array(
					'--th-btn-context-hover-bg-color'    => $control_bg_two,
					'--th-btn-context-hover-font-color'  => $font_color,
					'--th-btn-context-active-bg-color'   => $control_bg_one,
					'--th-btn-context-active-font-color' => $font_color,
				)
			);

			return $css;
		}

		/**
		 * Returns CSS related to the buttons.
		 *
		 * @return string
		 */
		public function get_buttons_css(): string {
			$settings = Settings::instance();
			$css      = '';

			$theme_scheme = $this->get_schemes()['theme'];
			$dark_scheme  = $this->get_schemes()['dark'];

			$btn_primary_normal_scheme = $theme_scheme;
			$btn_primary_hover_scheme  = $dark_scheme;

			if ( '__custom__' === $settings->get_option( 'primary_button_scheme' ) ) {
				$btn_primary_normal_scheme = $this->make_scheme(
					$settings->get_option( 'primary_button_normal_bg_color' ),
					array(
						'opposite_color' => $settings->get_option( 'primary_button_normal_font_color' ),
					)
				);
				$btn_primary_hover_scheme  = $this->make_scheme(
					$settings->get_option( 'primary_button_hover_bg_color' ),
					array(
						'opposite_color' => $settings->get_option( 'primary_button_hover_font_color' ),
					)
				);
			}

			$primary_normal_bg_color   = $btn_primary_normal_scheme['main_color'];
			$primary_normal_font_color = $btn_primary_normal_scheme['opposite_color'];
			$primary_hover_bg_color    = $btn_primary_hover_scheme['main_color'];
			$primary_hover_font_color  = $btn_primary_hover_scheme['opposite_color'];
			$primary_active_bg_color   = $primary_hover_bg_color->merge( $btn_primary_hover_scheme['hover_color'] );
			$primary_active_font_color = $primary_hover_font_color;

			$css .= $this->make_rule(
				':root',
				array(
					'--th-btn-primary-normal-bg-color'   => $primary_normal_bg_color,
					'--th-btn-primary-normal-font-color' => $primary_normal_font_color,
					'--th-btn-primary-hover-bg-color'    => $primary_hover_bg_color,
					'--th-btn-primary-hover-font-color'  => $primary_hover_font_color,
					'--th-btn-primary-active-bg-color'   => $primary_active_bg_color,
					'--th-btn-primary-active-font-color' => $primary_active_font_color,
				)
			);

			if ( '__custom__' === $settings->get_option( 'secondary_button_scheme' ) ) {
				$btn_secondary_normal_scheme = $this->make_scheme(
					$settings->get_option( 'secondary_button_normal_bg_color' ),
					array(
						'opposite_color' => $settings->get_option( 'secondary_button_normal_font_color' ),
					)
				);
				$btn_secondary_hover_scheme  = $this->make_scheme(
					$settings->get_option( 'secondary_button_hover_bg_color' ),
					array(
						'opposite_color' => $settings->get_option( 'secondary_button_hover_font_color' ),
					)
				);

				$secondary_normal_bg_color   = $btn_secondary_normal_scheme['main_color'];
				$secondary_normal_font_color = $btn_secondary_normal_scheme['opposite_color'];
				$secondary_hover_bg_color    = $btn_secondary_hover_scheme['main_color'];
				$secondary_hover_font_color  = $btn_secondary_hover_scheme['opposite_color'];
				$secondary_active_bg_color   = $secondary_hover_bg_color->merge( $btn_secondary_hover_scheme['hover_color'] );
				$secondary_active_font_color = $secondary_hover_font_color;

				$css .= $this->make_rule(
					':root',
					array(
						'--th-btn-secondary-normal-bg-color'   => $secondary_normal_bg_color,
						'--th-btn-secondary-normal-font-color' => $secondary_normal_font_color,
						'--th-btn-secondary-hover-bg-color'    => $secondary_hover_bg_color,
						'--th-btn-secondary-hover-font-color'  => $secondary_hover_font_color,
						'--th-btn-secondary-active-bg-color'   => $secondary_active_bg_color,
						'--th-btn-secondary-active-font-color' => $secondary_active_font_color,
					)
				);
			}

			return $css;
		}

		/**
		 * Returns the remaining CSS.
		 *
		 * @return string
		 */
		public function get_other_css(): string {
			$settings = Settings::instance();
			$css      = '';

			$scheme                = $this->get_schemes()['theme'];
			$dark_scheme           = $this->get_schemes()['dark'];
			$accent_scheme         = $this->get_schemes()['accent'];
			$main_color            = $scheme['main_color'];
			$opposite_color        = $scheme['opposite_color'];
			$dark_main_color       = $dark_scheme['main_color'];
			$dark_opposite_color   = $dark_scheme['opposite_color'];
			$dark_muted_color      = $dark_scheme['muted_color'];
			$accent_main_color     = $accent_scheme['main_color'];
			$accent_opposite_color = $accent_scheme['opposite_color'];
			$accent_muted_color    = $accent_scheme['muted_color'];

			$css .= $this->make_rule(
				':root',
				array(
					"--th-theme-scheme-main: $main_color;",
					"--th-theme-scheme-opposite: $opposite_color;",
					"--th-dark-scheme-main: $dark_main_color;",
					"--th-dark-scheme-opposite: $dark_opposite_color;",
					"--th-dark-scheme-muted: $dark_muted_color;",
					"--th-accent-scheme-main: $accent_main_color;",
					"--th-accent-scheme-opposite: $accent_opposite_color;",
					"--th-accent-scheme-muted: $accent_muted_color;",
				)
			);

			// Radio button.
			$image = "<svg xmlns='http://www.w3.org/2000/svg' width='6px' height='6px'><circle fill='$opposite_color' cx='3' cy='3' r='3' /></svg>";
			$image = rawurlencode( $image );

			$css .= $this->make_rule(
				'input[type="radio"]:checked',
				array(
					'background-color' => $main_color,
					'background-image' => "url('data:image/svg+xml,$image')",
				)
			);

			// Checkbox.
			$image = "<svg xmlns='http://www.w3.org/2000/svg' width='9px' height='7px'><path fill='$opposite_color' d='M9,1.395L3.46,7L0,3.5L1.383,2.095L3.46,4.2L7.617,0L9,1.395Z' /></svg>";
			$image = rawurlencode( $image );

			$css .= $this->make_rule(
				array(
					'input[type="checkbox"]:checked',
					'.widget_layered_nav .woocommerce-widget-layered-nav-list__item--chosen a:before',
					'.widget_rating_filter .chosen a:before',
				),
				array(
					'background-color' => $main_color,
					'background-image' => "url('data:image/svg+xml,$image')",
				)
			);

			// Links.
			$link_color = $settings->get_option( 'link_color' );

			if ( ! empty( $link_color ) ) {
				$css .= $this->make_rule(
					':root',
					array(
						'--th-link-color' => $link_color,
					)
				);

				$color        = Color::parse( $settings->get_option( 'link_color' ) );
				$color->alpha = .8;

				$css .= $this->make_rule(
					'.th-widget-comments__author a:hover',
					array(
						'border-color' => $color,
					)
				);
			}

			return $css;
		}

		/**
		 * Returns theme CSS.
		 */
		public function css(): string {
			$css  = '';
			$css .= $this->get_common_css();
			$css .= $this->get_desktop_header_css();
			$css .= $this->get_mobile_header_css();
			$css .= $this->get_footer_css();
			$css .= $this->get_newsletter_widget_css();
			$css .= $this->get_buttons_css();
			$css .= $this->get_other_css();

			return $css;
		}
	}
}
