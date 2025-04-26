<?php
/**
 * RedParts Post.
 *
 * @package RedParts
 * @since 1.0.0
 */

namespace RedParts;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'RedParts\Post' ) ) {
	/**
	 * Class Post
	 */
	class Post extends Singleton {
		const VALID_SIDEBAR_POSITIONS = array( 'start', 'end', 'none' );

		const DEFAULT_SIDEBAR_POSITION = 'end';

		/**
		 * Returns sidebar position.
		 *
		 * @return string
		 */
		public function get_sidebar_position(): string {
			$result   = self::DEFAULT_SIDEBAR_POSITION;
			$settings = Settings::instance()->get();

			if (
				! empty( $settings['post_sidebar_position'] ) &&
				in_array( $settings['post_sidebar_position'], self::VALID_SIDEBAR_POSITIONS, true )
			) {
				$result = $settings['post_sidebar_position'];
			}

			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			if ( isset( $_GET['redparts_post_sidebar_position'] ) ) {
				$get_post_sidebar_position = sanitize_key( wp_unslash( $_GET['redparts_post_sidebar_position'] ) );

				if ( in_array( $get_post_sidebar_position, self::VALID_SIDEBAR_POSITIONS, true ) ) {
					$result = $get_post_sidebar_position;
				}
			}
			// phpcs:enable

			return apply_filters( 'redparts_get_post_sidebar_position', $result );
		}
	}
}
