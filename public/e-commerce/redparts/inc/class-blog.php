<?php
/**
 * This file contains code related to the archive page only.
 *
 * @package RedParts
 * @since 1.0.0
 */

namespace RedParts;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'RedParts\Blog' ) ) {
	/**
	 * Class Blog
	 */
	class Blog extends Singleton {
		const DEFAULT_LAYOUT = 'classic';

		const VALID_LAYOUTS = array( 'classic', 'grid', 'list' );

		const DEFAULT_SIDEBAR_POSITION = 'start';

		const VALID_SIDEBAR_POSITIONS = array( 'start', 'end', 'none' );

		/**
		 * Initialization.
		 */
		public function init() { }

		/**
		 * Returns the product page layout.
		 *
		 * @return string
		 */
		public function get_layout(): string {
			$result   = self::DEFAULT_LAYOUT;
			$settings = Settings::instance()->get();

			if ( ! empty( $settings['blog_layout'] ) && in_array( $settings['blog_layout'], self::VALID_LAYOUTS, true ) ) {
				$result = $settings['blog_layout'];
			}

			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			if ( isset( $_GET['redparts_blog_layout'] ) ) {
				$get_layout = sanitize_key( wp_unslash( $_GET['redparts_blog_layout'] ) );

				if ( in_array( $get_layout, self::VALID_LAYOUTS, true ) ) {
					$result = $get_layout;
				}
			}
			// phpcs:enable

			return apply_filters( 'redparts_blog_get_layout', $result );
		}

		/**
		 * Returns sidebar position.
		 *
		 * @return string
		 */
		public function get_sidebar_position(): string {
			$result   = self::DEFAULT_SIDEBAR_POSITION;
			$settings = Settings::instance()->get();

			if ( ! empty( $settings['blog_sidebar_position'] ) && in_array( $settings['blog_sidebar_position'], self::VALID_SIDEBAR_POSITIONS, true ) ) {
				$result = $settings['blog_sidebar_position'];
			}

			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			if ( isset( $_GET['redparts_blog_sidebar_position'] ) ) {
				$get_sidebar_position = sanitize_key( wp_unslash( $_GET['redparts_blog_sidebar_position'] ) );

				if ( in_array( $get_sidebar_position, self::VALID_SIDEBAR_POSITIONS, true ) ) {
					$result = $get_sidebar_position;
				}
			}
			// phpcs:enable

			return apply_filters( 'redparts_blog_get_sidebar_position', $result );
		}
	}
}
