<?php
/**
 * RedParts singleton.
 *
 * @package RedParts
 * @since 1.0.0
 */

namespace RedParts;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'RedParts\Singleton' ) ) {
	/**
	 * Class Singleton.
	 */
	abstract class Singleton {
		/**
		 * Singleton instance.
		 *
		 * @return static
		 */
		public static function instance(): Singleton {
			/** Instances array. @var static[] $instances */
			static $instances = array();

			$class = get_called_class();

			if ( ! isset( $instances[ $class ] ) ) {
				$instances[ $class ] = new $class();
			}

			return $instances[ $class ];
		}
	}
}
