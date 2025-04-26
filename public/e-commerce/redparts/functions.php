<?php
/**
 * This is the typical theme initialization file.
 *
 * @package RedParts
 * @since 1.0.0
 */

use RedParts\Theme;
use RedParts\SVG;
use RedParts\Walker_Dropdown_List;
use RedParts\Sputnik\Vehicles;
use RedParts\Sputnik\Plugin;

defined( 'ABSPATH' ) || exit;

define( 'RED_PARTS_VERSION', wp_get_theme()->get( 'Version' ) );

if ( ! function_exists( 'redparts_autoload' ) ) {
	/**
	 * Automatically loads RedParts classes.
	 *
	 * @param string $classname - Class name.
	 * @noinspection DuplicatedCode
	 */
	function redparts_autoload( string $classname ) {
		$prefix = 'RedParts\\';

		if ( substr( $classname, 0, strlen( $prefix ) ) !== $prefix ) {
			return;
		}

		$classname = substr( $classname, strlen( $prefix ) );
		$classname = strtolower( str_replace( '_', '-', $classname ) );
		$parts     = explode( '\\', $classname );

		if ( 1 > count( $parts ) ) {
			return;
		}

		$parts[ count( $parts ) - 1 ] = 'class-' . $parts[ count( $parts ) - 1 ] . '.php';

		$filename = get_template_directory() . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . implode( DIRECTORY_SEPARATOR, $parts );

		if ( ! file_exists( $filename ) ) {
			return;
		}

		/** Redundant inspection. @noinspection PhpIncludeInspection */
		require_once $filename;
	}

	spl_autoload_register( 'redparts_autoload' );
}

if ( ! function_exists( 'redparts_register_required_plugins' ) ) {
	/** Redundant inspection. @noinspection PhpIncludeInspection */
	require_once get_template_directory() . '/vendor/tgm-plugin-activation/class-tgm-plugin-activation.php';

	/**
	 * Register required plugins.
	 */
	function redparts_register_required_plugins() {
		$plugins = array(
			array(
				'name'               => 'RedParts Sputnik',
				'slug'               => 'redparts-sputnik',
				'source'             => get_template_directory() . '/plugins/redparts-sputnik.zip',
				'required'           => true,
				'version'            => '1.5.1',
				'force_activation'   => false,
				'force_deactivation' => false,
				'external_url'       => '',
				'is_callable'        => '',
			),
			array(
				'name'     => 'Contact Form 7',
				'slug'     => 'contact-form-7',
				'required' => false,
			),
			array(
				'name'     => 'Elementor',
				'slug'     => 'elementor',
				'required' => false,
			),
			array(
				'name'     => 'MC4WP: Mailchimp for WordPress',
				'slug'     => 'mailchimp-for-wp',
				'required' => false,
			),
			array(
				'name'     => 'One Click Demo Import',
				'slug'     => 'one-click-demo-import',
				'required' => false,
			),
			array(
				'name'     => 'Redux Framework',
				'slug'     => 'redux-framework',
				'required' => true,
			),
			array(
				'name'     => 'SVG Support',
				'slug'     => 'svg-support',
				'required' => false,
			),
			array(
				'name'     => 'WooCommerce',
				'slug'     => 'woocommerce',
				'required' => true,
			),
			array(
				'name'     => 'WOOCS - WooCommerce Currency Switcher',
				'slug'     => 'woocommerce-currency-switcher',
				'required' => false,
			),
		);

		$config = array(
			'id'           => 'redparts',               // Unique ID for hashing notices for multiple instances of TGMPA.
			'default_path' => '',                      // Default absolute path to bundled plugins.
			'menu'         => 'tgmpa-install-plugins', // Menu slug.
			'has_notices'  => true,                    // Show admin notices or not.
			'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
			'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
			'is_automatic' => false,                   // Automatically activate plugins after installation or not.
			'message'      => '',                      // Message to output right before the plugins table.
		);

		tgmpa( $plugins, $config );
	}

	add_action( 'tgmpa_register', 'redparts_register_required_plugins' );
}

if ( ! function_exists( 'redparts_get_classes' ) ) {
	/**
	 * Combines CSS classes.
	 *
	 * @noinspection DuplicatedCode
	 *
	 * @param string|array ...$classes Array of CSS classes.
	 * @return string
	 */
	function redparts_get_classes( ...$classes ): string {
		$result = array();

		foreach ( $classes as $class ) {
			if ( is_string( $class ) && trim( $class ) ) {
				$result[] = trim( $class );
			} elseif ( is_array( $class ) ) {
				foreach ( $class as $subclass => $condition ) {
					if ( $condition && trim( $subclass ) ) {
						$result[] = trim( $subclass );
					}
				}
			}
		}

		return implode( ' ', $result );
	}
}

if ( ! function_exists( 'redparts_the_classes' ) ) {
	/**
	 * Combines and outputs CSS classes.
	 *
	 * @param string|array ...$classes Array of CSS classes.
	 */
	function redparts_the_classes( ...$classes ) {
		echo esc_attr( redparts_get_classes( ...$classes ) );
	}
}

if ( ! function_exists( 'redparts_get_template' ) ) {
	/**
	 * Returns the content of the specified template.
	 *
	 * @param string $template Path to the template.
	 * @param array  $args     Template args.
	 *
	 * @return string
	 */
	function redparts_get_template( string $template, $args = array() ): string {
		ob_start();

		redparts_the_template( $template, $args );

		return ob_get_clean();
	}
}

if ( ! function_exists( 'redparts_the_template' ) ) {
	/**
	 * Outputs specified template.
	 *
	 * @noinspection PhpUnusedParameterInspection, PhpIncludeInspection
	 *
	 * @param string $template Path to the template.
	 * @param array  $args     Template args.
	 */
	function redparts_the_template( string $template, $args = array() ) {
		include locate_template( $template . '.php', false, false );
	}
}

if ( ! function_exists( 'redparts_get_icon' ) ) {
	/**
	 * Returns the SVG icon.
	 *
	 * @param string $name Icon name.
	 * @param string $classes CSS classes.
	 * @return string
	 */
	function redparts_get_icon( string $name, $classes = '' ): string {
		return SVG::get( $name, $classes );
	}
}

if ( ! function_exists( 'redparts_the_icon' ) ) {
	/**
	 * Outputs the SVG icon.
	 *
	 * @param string $name Icon name.
	 * @param string $classes CSS classes.
	 */
	function redparts_the_icon( string $name, $classes = '' ) {
		// Escaped in redparts_get_icon().
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo redparts_get_icon( $name, $classes );
	}
}

if ( ! function_exists( 'redparts_the_decor' ) ) {
	/**
	 * Returns the SVG icon.
	 *
	 * @param string $type Icon name.
	 * @param string $classes CSS classes.
	 */
	function redparts_the_decor( $type = 'center', $classes = '' ) {
		$classes = redparts_get_classes( 'th-decor', $classes, 'th-decor--type--' . $type )

		?>
		<div class="<?php echo esc_attr( $classes ); ?>">
			<div class="th-decor__body">
				<div class="th-decor__start"></div>
				<div class="th-decor__end"></div>
				<div class="th-decor__center"></div>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'redparts_has_breadcrumb' ) ) {
	/**
	 * Returns true if current page has breadcrumb.
	 */
	function redparts_has_breadcrumb(): bool {
		static $result = null;

		if ( null !== $result ) {
			return $result;
		}

		ob_start();

		if ( class_exists( 'WooCommerce' ) ) {
			woocommerce_breadcrumb();
		}

		$result = ! empty( ob_get_clean() );

		return $result;
	}
}

if ( ! function_exists( 'redparts_get_the_title' ) ) {
	/**
	 * Returns the page title.
	 *
	 * @return string
	 */
	function redparts_get_the_title(): string {
		$endpoint       = '';
		$endpoint_title = '';

		if ( class_exists( 'WooCommerce' ) ) {
			if ( is_wc_endpoint_url() ) {
				$endpoint = WC()->query->get_current_endpoint();
			}

			if ( $endpoint ) {
				$endpoint_title = WC()->query->get_endpoint_title( $endpoint );
			}
		}

		if ( $endpoint_title ) {
			$result = $endpoint_title;
		} elseif ( class_exists( 'RedParts\Sputnik\Vehicles' ) && Vehicles::is_vehicle() ) {
			$result = Vehicles::get_filtered_vehicle_name();
		} elseif ( is_search() ) {
			// translators: %s search query.
			$result = sprintf( esc_html__( 'Search results: &ldquo;%s&rdquo;', 'redparts' ), get_search_query() );
		} elseif ( is_singular() ) {
			$result = get_the_title();
		} elseif ( class_exists( 'WooCommerce' ) && is_shop() ) {
			$result = get_the_title( wc_get_page_id( 'shop' ) );
		} elseif ( is_home() && ! is_front_page() ) {
			$result = single_post_title( '', false );
		} else {
			$result = single_term_title( '', false );
		}

		if ( empty( $result ) && ! is_front_page() && is_archive() ) {
			$result = get_the_archive_title();
		}

		return empty( $result ) ? '' : $result;
	}
}

if ( ! function_exists( 'redparts_the_title' ) ) {
	/**
	 * Outputs the page title.
	 */
	function redparts_the_title() {
		echo wp_kses_post( redparts_get_the_title() );
	}
}

if ( ! function_exists( 'redparts_the_page_header' ) ) {
	/**
	 * Outputs page header.
	 *
	 * @param array $args Arguments.
	 */
	function redparts_the_page_header( $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'show_title'      => true,
				'show_breadcrumb' => true,
			)
		);

		$args = apply_filters( 'redparts_page_header_args', $args );

		$has_breadcrumb = $args['show_breadcrumb'] && redparts_has_breadcrumb() && ! is_front_page();
		$has_title      = $args['show_title'] && ! empty( redparts_get_the_title() ) && ! is_front_page();

		$header_classes = array( 'th-block-header' );

		if ( $has_breadcrumb ) {
			$header_classes[] = 'th-block-header--has-breadcrumb';
		}

		if ( $has_title ) {
			$header_classes[] = 'th-block-header--has-title';
		}

		if ( ! $has_breadcrumb && ! $has_title ) {
			$header_classes[] = 'th-block-header--empty';
		}

		$header_classes = apply_filters( 'redparts_page_header_classes', $header_classes );

		?>
		<div class="<?php echo esc_attr( implode( ' ', $header_classes ) ); ?>">
			<div class="th-container">
				<div class="th-block-header__body">
					<?php
					if ( $has_breadcrumb ) {
						$breadcrumb_classes = array( 'th-block-header__breadcrumb', 'woocommerce-breadcrumb' );

						if ( $has_title ) {
							$breadcrumb_classes[] = 'woocommerce-breadcrumb--with-title';
						}

						$breadcrumb_classes = implode( ' ', $breadcrumb_classes );

						$args = apply_filters(
							'redparts_breadcrumb_defaults',
							array(
								'delimiter'   => '',
								'wrap_before' => '<nav class="' . esc_attr( $breadcrumb_classes ) . '"><ol>',
								'wrap_after'  => '</ol></nav>',
								'before'      => '<li><span>',
								'after'       => '</span></li>',
							)
						);

						woocommerce_breadcrumb( $args );
					}
					?>
					<?php if ( $has_title ) : ?>
						<h1 class="th-block-header__title page-title">
							<?php redparts_the_title(); ?>
						</h1>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'redparts_dropdown_list' ) ) {
	/**
	 * Outputs dropdown list.
	 *
	 * @param array $args Args to control display of dropdown list.
	 */
	function redparts_dropdown_list( $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'taxonomy'         => 'product_cat',
				'depth'            => 1,
				'item_id_prefix'   => '',
				'show_option_none' => '',
				'value_field'      => 'term_id',
			)
		);

		$terms_args = array(
			'taxonomy'     => $args['taxonomy'],
			'orderby'      => 'name',
			'order'        => 'ASC',
			'hide_empty'   => 1,
			'hierarchical' => 1,
			'pad_counts'   => 1,
		);

		$walker_args = array(
			'item_id_prefix' => $args['item_id_prefix'],
			'value_field'    => $args['value_field'],
		);

		$show_option_none = (string) $args['show_option_none'];

		$categories = get_terms( $terms_args );

		if ( empty( $args['walker'] ) || ! ( $args['walker'] instanceof Walker ) ) {
			$walker = new Walker_Dropdown_List();
		} else {
			$walker = $args['walker'];
		}

		?>
		<ul
			class="th-dropdown-list"
			tabindex="-1"
			role="listbox"
			<?php if ( ! empty( $args['labelled_by'] ) ) : ?>
				aria-labelledby="<?php echo esc_attr( $args['labelled_by'] ); ?>"
			<?php endif; ?>
		>
			<?php if ( ! empty( $show_option_none ) ) : ?>
				<li
					id="<?php echo esc_attr( $args['item_id_prefix'] . 'none' ); ?>"
					role="option"
					class="th-dropdown-list__item"
					data-value=""
				>
					<div class="th-dropdown-list__item-title"><?php echo esc_html( $show_option_none ); ?></div>
				</li>
			<?php endif; ?>
			<?php
			if ( ! ( $categories instanceof WP_Error ) ) {
				echo wp_kses(
					$walker->walk( $categories, $args['depth'], $walker_args ),
					'redparts_dropdown_list'
				);
			}
			?>
		</ul>
		<?php
	}
}

if ( ! function_exists( 'redparts_kses_allowed_html' ) ) {
	/**
	 * Returns allowed html tags.
	 *
	 * @param array  $tags    - Array of allowed tags.
	 * @param string $context - Context.
	 *
	 * @return array
	 */
	function redparts_kses_allowed_html( array $tags, string $context ): array {
		switch ( $context ) {
			case 'redparts_categories_list':
				return array(
					'a' => array(
						'href' => true,
						'rel'  => true,
					),
				);
			case 'redparts_copyright':
			case 'redparts_text':
				return array(
					'br'   => array(),
					'span' => array(
						'class' => array(),
					),
					'a'    => array(
						'href' => array(),
					),
				);
			case 'redparts_star_rating':
				return array(
					'span'   => array(
						'class' => true,
						'style' => true,
					),
					'strong' => array(
						'class' => true,
					),
				);
			case 'redparts_cart_total':
				return array(
					'span' => array(
						'class' => true,
					),
				);
			case 'redparts_featured_attributes':
				return array(
					'ul'   => array(
						'class' => true,
					),
					'li'   => array(
						'class' => true,
					),
					'span' => array(
						'class' => true,
					),
				);
			case 'redparts_product_tags':
				return array(
					'div'  => array(
						'class' => true,
					),
					'span' => array(
						'class' => true,
					),
					'a'    => array(
						'href' => true,
						'rel'  => true,
					),
				);
			case 'redparts_stock':
				return array(
					'div' => array(
						'class' => true,
					),
				);
			case 'redparts_post_author':
				return array(
					'a'    => array(
						'class' => true,
						'href'  => true,
					),
					'span' => array(
						'class' => true,
					),
				);
			case 'redparts_order_status':
				return array(
					'mark' => array(
						'class' => true,
					),
				);
			case 'redparts_product_meta_value':
				return array(
					'a'    => array(
						'href' => true,
						'rel'  => true,
					),
					'span' => array(
						'class' => true,
					),
				);
			case 'redparts_availability_text':
				return array();
			case 'redparts_dropdown_list':
				return array(
					'li'  => array(
						'id'         => true,
						'class'      => true,
						'role'       => true,
						'data-value' => true,
						'data-meta'  => true,
					),
					'div' => array(
						'class' => true,
					),
				);
			default:
				return $tags;
		}
	}

	add_filter( 'wp_kses_allowed_html', 'redparts_kses_allowed_html', 10, 2 );
}

if ( ! function_exists( 'redparts_sputnik_version_is' ) ) {
	/**
	 * Function to compare plugin version.
	 *
	 * @param string $operator Comparison operator.
	 * @param string $version  Version with which to compare.
	 *
	 * @return bool
	 */
	function redparts_sputnik_version_is( string $operator, string $version ): bool {
		$plugin_version = '1.4.0';

		if ( class_exists( 'RedParts\Sputnik\Plugin' ) ) {
			$plugin_version = Plugin::VERSION;
		}

		$a = array_map( 'absint', explode( '.', $version ) );
		$b = array_map( 'absint', explode( '.', $plugin_version ) );

		$is_equal   = $plugin_version === $version;
		$is_greater = $b[0] > $a[0] || ( $b[0] === $a[0] && $b[1] > $a[1] ) || ( $b[0] === $a[0] && $b[1] === $a[1] && $b[2] > $a[2] );
		$is_less    = ! $is_greater && ! $is_equal;

		switch ( $operator ) {
			case '=':
				return $is_equal;
			case '>':
				return $is_greater;
			case '>=':
				return $is_greater || $is_equal;
			case '<':
				return $is_less;
			case '<=':
				return $is_less || $is_equal;
		}

		return false;
	}
}

if ( ! function_exists( 'redparts' ) ) {
	/**
	 * Returns singleton instance of RedParts/Theme.
	 *
	 * @return Theme
	 */
	function redparts(): Theme {
		return Theme::instance();
	}

	redparts()->init();
}
