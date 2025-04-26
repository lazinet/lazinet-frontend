<?php
/**
 * This file contains WooCommerce related code that can be used throughout the theme.
 *
 * @package RedParts
 * @since 1.0.0
 */

namespace RedParts;

use RedParts\Sputnik\Share_Buttons;
use WC_Query;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'RedParts\WooCommerce' ) ) {
	/**
	 * Class WooCommerce
	 */
	class WooCommerce extends Singleton {
		/**
		 * Initialization.
		 */
		public function init() {
			add_action( 'after_setup_theme', array( $this, 'setup' ) );
			add_action( 'woocommerce_share', array( $this, 'share_buttons' ) );
			add_filter( 'woocommerce_format_sale_price', array( $this, 'format_sale_price' ), 10, 3 );
			add_filter( 'woocommerce_get_price_html', array( $this, 'price' ), 100 );

			/**
			 * Disable the default WooCommerce stylesheet.
			 *
			 * @link https://docs.woocommerce.com/document/disable-the-default-stylesheet/
			 */
			add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

			/**
			 * Remove default WooCommerce wrapper.
			 */
			remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper' );
			remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end' );

			/**
			 * Remove WooCommerce breadcrumb.
			 */
			remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

			/**
			 * Fragments.
			 */
			add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'add_to_cart_fragments' ) );

			/**
			 * Options.
			 */
			add_filter( 'woocommerce_review_gravatar_size', array( $this, 'review_gravatar_size' ) );
			add_filter( 'loop_shop_per_page', array( $this, 'products_per_page' ) );

			/**
			 * Login form.
			 */
			add_action( 'woocommerce_before_customer_login_form', array( $this, 'before_customer_login_form' ) );
			add_action( 'woocommerce_after_customer_login_form', array( $this, 'after_customer_login_form' ) );

			/**
			 * Lost password form.
			 */
			add_action( 'woocommerce_before_lost_password_form', array( $this, 'before_lost_password_form' ) );
			add_action( 'woocommerce_after_lost_password_form', array( $this, 'after_lost_password_form' ) );

			/**
			 * Reset password form.
			 */
			add_action( 'woocommerce_before_reset_password_form', array( $this, 'before_reset_password_form' ) );
			add_action( 'woocommerce_after_reset_password_form', array( $this, 'after_reset_password_form' ) );

			/**
			 * Lost password confirmation message.
			 */
			add_action( 'woocommerce_before_lost_password_confirmation_message', array( $this, 'before_lost_password_confirmation_message' ) );
			add_action( 'woocommerce_after_lost_password_confirmation_message', array( $this, 'after_lost_password_confirmation_message' ) );

			/**
			 * Templates wrapper.
			 */
			add_action( 'woocommerce_before_template_part', array( $this, 'before_template_part' ), 10, 1 );
			add_action( 'woocommerce_after_template_part', array( $this, 'after_template_part' ), 10, 1 );

			/**
			 * Remove WooCommerce columns option from customizer.
			 */
			add_filter( 'loop_shop_columns', array( $this, 'shop_columns' ), 10, 1 );

			/**
			 * Changes sale badge template.
			 */
			add_filter( 'woocommerce_sale_flash', array( $this, 'sale_badge_html' ), 10, 0 );

			/**
			 * Adds template-specific classes to the product card.
			 */
			add_filter( 'woocommerce_post_class', array( $this, 'post_classes' ) );
		}

		/**
		 * WooCommerce setup function.
		 */
		public function setup() {
			/**
			 * Declaring WooCommerce support in theme.
			 *
			 * @link https://github.com/woocommerce/woocommerce/wiki/Declaring-WooCommerce-support-in-themes
			 */
			add_theme_support( 'woocommerce' );
			add_theme_support( 'wc-product-gallery-zoom' );
			add_theme_support( 'wc-product-gallery-lightbox' );
			add_theme_support( 'wc-product-gallery-slider' );
		}

		/**
		 * Share buttons for WooCommerce.
		 */
		public function share_buttons() {
			if ( class_exists( '\RedParts\Sputnik\Share_Buttons' ) ) {
				Share_Buttons::instance()->render( 'th-product__share-buttons' );
			}
		}

		/**
		 * Wraps WooCommerce price.
		 *
		 * @param string $price_html Price HTML.
		 *
		 * @return string
		 */
		public function price( string $price_html ): string {
			return '<span class="th-price">' . $price_html . '</span>';
		}

		/**
		 * Defines format for sale price.
		 *
		 * @param float|string $price Early formatted price.
		 * @param float|string $regular_price Regular price.
		 * @param float|string $sale_price Sale price.
		 * @return string
		 */
		public function format_sale_price(
			/** Unused parameter @noinspection PhpUnusedParameterInspection */ $price,
			$regular_price,
			$sale_price
		): string {
			if ( is_numeric( $sale_price ) ) {
				$sale_price = wc_price( $sale_price );
			}
			if ( is_numeric( $regular_price ) ) {
				$regular_price = wc_price( $regular_price );
			}

			return sprintf( '<ins>%s</ins> <del>%s</del>', $sale_price, $regular_price );
		}

		/**
		 * Returns the current number of active filters.
		 * Based on: woocommerce/includes/widgets/class-wc-widget-layered-nav-filters.php
		 *
		 * @return int
		 */
		public static function get_filters_count(): int {
			if ( ! is_shop() && ! is_product_taxonomy() ) {
				return 0;
			}

			$result        = 0;
			$min_price     = 0;
			$max_price     = 0;
			$rating_filter = array();

			// No nonce verification required here.
			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			if ( isset( $_GET['min_price'] ) ) {
				// Sanitized in wc_clean().
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				$min_price = wc_clean( wp_unslash( $_GET['min_price'] ) );
			}

			if ( isset( $_GET['max_price'] ) ) {
				// Sanitized in wc_clean().
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				$max_price = wc_clean( wp_unslash( $_GET['max_price'] ) );
			}

			if ( isset( $_GET['rating_filter'] ) ) {
				// Sanitized in absint().
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				$rating_filter = array_filter( array_map( 'absint', explode( ',', wp_unslash( $_GET['rating_filter'] ) ) ) );
			}
			// phpcs:enable

			$chosen_attributes = WC_Query::get_layered_nav_chosen_attributes();

			if ( ! empty( $chosen_attributes ) ) {
				foreach ( $chosen_attributes as $taxonomy => $data ) {
					foreach ( $data['terms'] as $term_slug ) {
						$term = get_term_by( 'slug', $term_slug, $taxonomy );

						if ( ! $term ) {
							continue;
						}

						$result++;
					}
				}
			}

			if ( $min_price ) {
				$result++;
			}

			if ( $max_price ) {
				$result++;
			}

			if ( ! empty( $rating_filter ) ) {
				$result += count( $rating_filter );
			}

			return apply_filters( 'redparts_woocommerce_get_filters_count', $result );
		}

		/**
		 * Cart fragments.
		 *
		 * @param array $fragments Fragments to refresh via AJAX.
		 *
		 * @return array
		 */
		public function add_to_cart_fragments( array $fragments ): array {
			$cart_count = WC()->cart->get_cart_contents_count();
			$cart_total = WC()->cart->get_cart_total();

			// Desktop header count.
			$fragments['.th-indicator--cart .th-indicator__counter'] =
				'<span class="th-indicator__counter">' . esc_html( $cart_count ) . '</span>';
			// Desktop header total.
			$fragments['.th-indicator--cart .th-indicator__value'] =
				'<span class="th-indicator__value">' . wp_kses( $cart_total, 'redparts_cart_total' ) . '</span>';
			// Mobile header count.
			$fragments['.th-mobile-indicator--cart .th-mobile-indicator__counter'] =
				'<span class="th-mobile-indicator__counter">' . esc_html( $cart_count ) . '</span>';
			// Mobile menu count.
			$fragments['.th-mobile-menu__indicator--cart .th-mobile-menu__indicator-counter'] =
				'<span class="th-mobile-menu__indicator-counter">' . esc_html( $cart_count ) . '</span>';

			return $fragments;
		}

		/**
		 * Returns review gravatar size.
		 *
		 * @return string
		 */
		public function review_gravatar_size(): string {
			return '42';
		}

		/**
		 * Returns number of products per page.
		 *
		 * @return int
		 */
		public function products_per_page(): int {
			return Shop::instance()->get_products_per_page();
		}

		/**
		 * Wraps login form.
		 */
		public function before_customer_login_form() {
			$classes = array( 'th-login-form' );

			if ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) {
				$classes[] = 'th-login-form--columns--two';
			} else {
				$classes[] = 'th-login-form--columns--one';
			}

			echo '<div class="' . esc_attr( redparts_get_classes( ...$classes ) ) . '">';
		}

		/**
		 * Wraps login form.
		 */
		public function after_customer_login_form() {
			echo '</div>';
		}

		/**
		 * Wraps lost password form.
		 */
		public function before_lost_password_form() {
			echo '<div class="th-form-card">';
			echo '<h2>' . esc_html__( 'Reset your password', 'redparts' ) . '</h2>';
		}

		/**
		 * Wraps lost password form.
		 */
		public function after_lost_password_form() {
			echo '</div>';
		}

		/**
		 * Wraps reset password form.
		 */
		public function before_reset_password_form() {
			echo '<div class="th-form-card">';
			echo '<h2>' . esc_html__( 'Change your password', 'redparts' ) . '</h2>';
		}

		/**
		 * Wraps reset password form.
		 */
		public function after_reset_password_form() {
			echo '</div>';
		}

		/**
		 * Wraps lost password confirmation message.
		 */
		public function before_lost_password_confirmation_message() {
			echo '<div class="th-confirmation">';
		}

		/**
		 * Wraps lost password confirmation message.
		 */
		public function after_lost_password_confirmation_message() {
			echo '</div>';
		}

		/**
		 * Outputs the opening tag of the template wrapper.
		 *
		 * @param string $name Template name.
		 */
		public function before_template_part( string $name ) {
			if ( 'order/tracking.php' === $name ) {
				echo '<div class="th-order-tracking">';
			}
		}

		/**
		 * Outputs the closing tag of the template wrapper.
		 *
		 * @param string $name Template name.
		 */
		public function after_template_part( string $name ) {
			if ( 'order/tracking.php' === $name ) {
				echo '</div>';
			}
		}

		/**
		 * Returns HTML of sale badge.
		 *
		 * @return string
		 */
		public function sale_badge_html(): string {
			return '<div class="th-tag-badge th-tag-badge--sale">' . esc_html__( 'Sale', 'redparts' ) . '</div>';
		}

		/**
		 * Adds classes to product card.
		 *
		 * @param string[] $classes Classes array.
		 *
		 * @return string[]
		 */
		public function post_classes( array $classes ): array {
			/** WooCommerce loop options. @var array $woocommerce_loop */
			global $woocommerce_loop;

			$in_loop = ! empty( $woocommerce_loop['name'] );
			$in_loop = $in_loop && in_array( $woocommerce_loop['name'], array( 'up-sells', 'related', 'cross-sells' ), true );

			$additional_classes = array();

			if ( ! is_product() || $in_loop ) {
				$additional_classes[] = 'th-products-list__item';
			}
			if ( ! empty( $woocommerce_loop['redparts_class'] ) ) {
				$additional_classes[] = $woocommerce_loop['redparts_class'];
			}

			return array_merge( $classes, $additional_classes );
		}

		/**
		 * Remove WooCommerce columns option from customizer.
		 */
		public function shop_columns(): int {
			return 1;
		}
	}
}
