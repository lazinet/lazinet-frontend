<?php
/**
 * This file contains code related to the checkout page only.
 *
 * @package RedParts
 * @since 1.0.0
 */

namespace RedParts;

use WC_Checkout;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'RedParts\Checkout' ) ) {
	/**
	 * Class Checkout
	 */
	class Checkout extends Singleton {
		/**
		 * Initialization.
		 */
		public function init() {
			if ( ! class_exists( 'WooCommerce' ) ) {
				return;
			}

			add_action( 'woocommerce_checkout_before_order_review_heading', array( $this, 'order_review_wrapper_start' ) );
			add_action( 'woocommerce_checkout_after_order_review', array( $this, 'order_review_wrapper_end' ) );

			remove_action( 'woocommerce_checkout_billing', array( WC_Checkout::instance(), 'checkout_form_billing' ) );
			remove_action( 'woocommerce_checkout_shipping', array( WC_Checkout::instance(), 'checkout_form_shipping' ) );

			add_action( 'woocommerce_checkout_billing', array( WC_Checkout::instance(), 'checkout_form_billing' ), 100 );
			add_action( 'woocommerce_checkout_billing', array( WC_Checkout::instance(), 'checkout_form_shipping' ), 200 );
		}

		/**
		 * Outputs start of the order review wrapper.
		 */
		public function order_review_wrapper_start() {
			echo '<div class="woocommerce-checkout-summary">';
		}

		/**
		 * Outputs end of the order review wrapper.
		 */
		public function order_review_wrapper_end() {
			echo '</div>';
		}
	}
}
