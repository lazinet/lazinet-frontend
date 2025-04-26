<?php
/**
 * This file contains RedParts Sputnik related code that can be used throughout the theme.
 *
 * @package RedParts
 * @since 1.0.0
 */

namespace RedParts;

use RedParts\Sputnik\Compare;
use RedParts\Sputnik\Wishlist;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'RedParts\Sputnik' ) ) {
	/**
	 * Class Sputnik
	 */
	class Sputnik extends Singleton {
		/**
		 * Initialization.
		 */
		public function init() {
			add_action( 'after_setup_theme', array( $this, 'customize_plugin' ) );
		}

		/**
		 * Plugin customization.
		 */
		public function customize_plugin() {
			// Compare button.
			add_filter( 'redparts_sputnik_compare_button_icon', array( $this, 'compare_button_icon' ), 10, 0 );
			add_filter( 'redparts_sputnik_compare_button_added_icon', array( $this, 'compare_button_added_icon' ), 10, 0 );

			// Compare fragments.
			add_filter( 'redparts_sputnik_compare_fragments', array( $this, 'compare_fragments' ) );

			// Wishlist button.
			add_filter( 'redparts_sputnik_wishlist_button_icon', array( $this, 'wishlist_button_icon' ), 10, 0 );
			add_filter( 'redparts_sputnik_wishlist_button_added_icon', array( $this, 'wishlist_button_added_icon' ), 10, 0 );

			// Wishlist remove button.
			add_filter( 'redparts_sputnik_wishlist_remove_button_content', array( $this, 'wishlist_remove_button_content' ), 10, 0 );

			// Wishlist fragments.
			add_filter( 'redparts_sputnik_wishlist_fragments', array( $this, 'wishlist_fragments' ) );

			// Quickview button.
			add_filter( 'redparts_sputnik_quickview_button_icon', array( $this, 'quickview_button_icon' ), 10, 0 );

			// Quickview close button.
			add_filter( 'redparts_sputnik_quickview_close_button_content', array( $this, 'quickview_close_button_content' ), 10, 0 );
			add_filter( 'redparts_sputnik_modal_close_button_content', array( $this, 'quickview_close_button_content' ), 10, 0 );

			// Search placeholder.
			add_filter( 'redparts_sputnik_search_placeholder', array( $this, 'search_placeholder' ) );
			add_filter( 'redparts_sputnik_search_default_placeholder', array( $this, 'search_default_placeholder' ) );
		}

		/**
		 * Returns compare button icon.
		 */
		public function compare_button_icon(): string {
			return redparts_get_icon( 'compare-16' );
		}

		/**
		 * Returns compare button added icon.
		 */
		public function compare_button_added_icon(): string {
			return redparts_get_icon( 'check-16' );
		}

		/**
		 * Returns compare fragments.
		 *
		 * @param array $fragments - Wishlist fragments.
		 *
		 * @return array
		 */
		public function compare_fragments( array $fragments ): array {
			if ( class_exists( '\RedParts\Sputnik\Compare' ) ) {
				$count = Compare::instance()->count();

				$fragments['.th-topbar--compare .menu-item-value'] =
					'<span class="menu-item-value">' . esc_html( $count ) . '</span>';
			}

			return $fragments;
		}

		/**
		 * Returns wishlist button icon.
		 */
		public function wishlist_button_icon(): string {
			return redparts_get_icon( 'wishlist-16' );
		}

		/**
		 * Returns wishlist button added icon.
		 */
		public function wishlist_button_added_icon(): string {
			return redparts_get_icon( 'check-16' );
		}

		/**
		 * Returns wishlist remove button content.
		 */
		public function wishlist_remove_button_content() {
			ob_start();

			?>
			<span class="sr-only"><?php esc_html_e( 'Remove', 'redparts' ); ?></span>
			<?php

			redparts_the_icon( 'cross-12' );

			return ob_get_clean();
		}

		/**
		 * Returns wishlist fragments.
		 *
		 * @param array $fragments - Wishlist fragments.
		 *
		 * @return array
		 */
		public function wishlist_fragments( array $fragments ): array {
			if ( class_exists( '\RedParts\Sputnik\Wishlist' ) ) {
				$count = Wishlist::instance()->count();

				$fragments['.th-indicator--wishlist .th-indicator__counter'] =
					'<span class="th-indicator__counter">' . esc_html( $count ) . '</span>';

				$fragments['.th-mobile-indicator--wishlist .th-mobile-indicator__counter'] =
					'<span class="th-mobile-indicator__counter">' . esc_html( $count ) . '</span>';

				$fragments['.th-mobile-menu__indicator--wishlist .th-mobile-menu__indicator-counter'] =
					'<span class="th-mobile-menu__indicator-counter">' . esc_html( $count ) . '</span>';
			}

			return $fragments;
		}

		/**
		 * Returns quickview button icon.
		 */
		public function quickview_button_icon(): string {
			return redparts_get_icon( 'quickview-16' );
		}

		/**
		 * Returns quickview close button content.
		 */
		public function quickview_close_button_content() {
			ob_start();

			?>
			<span class="sr-only"><?php esc_html_e( 'Close', 'redparts' ); ?></span>
			<?php

			redparts_the_icon( 'cross-20' );

			return ob_get_clean();
		}

		/**
		 * Returns search placeholder.
		 */
		public function search_placeholder() {
			// translators: %s vehicle or category name.
			return Settings::instance()->get_option( 'header_search_placeholder', esc_html__( 'Search for %s', 'redparts' ) );
		}

		/**
		 * Returns default search placeholder.
		 */
		public function search_default_placeholder() {
			return Settings::instance()->get_option( 'header_search_default_placeholder', esc_html__( 'Enter Keyword or Part Number', 'redparts' ) );
		}
	}
}
