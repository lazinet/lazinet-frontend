<?php
/**
 * This file contains code related to the cart page only.
 *
 * @package RedParts
 * @since 1.0.0
 */

namespace RedParts;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'RedParts\Cart' ) ) {
	/**
	 * Class Cart
	 */
	class Cart extends Singleton {
		/**
		 * Initialization.
		 */
		public function init() {
			add_action( 'woocommerce_before_cart', array( $this, 'wrapper_open_tag' ) );
			add_action( 'woocommerce_after_cart', array( $this, 'wrapper_close_tag' ) );

			add_action( 'woocommerce_cart_collaterals', array( $this, 'closes_the_opening_collaterals_tag' ), -1000 );
			add_action( 'woocommerce_cart_collaterals', array( $this, 'opens_the_closing_collaterals_tag' ), 1000 );
		}

		/**
		 * Outputs wrapper open tag.
		 */
		public function wrapper_open_tag() {
			echo '<div class="th-cart">';
		}

		/**
		 * Outputs wrapper close tag.
		 */
		public function wrapper_close_tag() {
			echo '</div>';
		}

		/**
		 * Closes the opening .cart-collaterals tag.
		 */
		public function closes_the_opening_collaterals_tag() {
			echo '</div>';
		}

		/**
		 * Opens the closing .cart-collaterals tag.
		 */
		public function opens_the_closing_collaterals_tag() {
			echo '<div class="cart-collaterals">';
		}
	}
}
