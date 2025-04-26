<?php
/**
 * RedParts footer.
 *
 * @package RedParts
 * @since 1.0.0
 */

namespace RedParts;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'RedParts\Footer' ) ) {
	/**
	 * Class Footer
	 */
	class Footer extends Singleton {
		/**
		 * Initialization.
		 */
		public function init() {
			add_filter( 'dynamic_sidebar_params', array( $this, 'widget_classes' ) );
		}

		/**
		 * Applies CSS classes to the widget containers in the footer.
		 *
		 * @param array $params Filters the parameters passed to a widget's display callback.
		 *
		 * @return array
		 */
		public function widget_classes( array $params ): array {
			if ( 'redparts-footer' === $params[0]['id'] ) {
				$classes_str = Settings::instance()->get_option( 'footer_widget_classes' );
				$classes     = explode( ',', $classes_str );

				global $redparts_widget_counter;

				$sidebar_id = $params[0]['id'];

				if ( ! isset( $redparts_widget_counter[ $sidebar_id ] ) ) {
					$redparts_widget_counter[ $sidebar_id ] = 0;
				}

				$widget_number = $redparts_widget_counter[ $sidebar_id ]++;
				$before_widget = $params[0]['before_widget'];
				$widget_class  = 'th-col-xl-4 th-col-md-6 th-col-12';

				if ( ! empty( $classes_str ) ) {
					$widget_class = isset( $classes[ $widget_number ] ) ? $classes[ $widget_number ] : $widget_class;
				}

				$before_widget = str_replace(
					'th-site-footer__widget-placeholder',
					esc_attr( $widget_class ),
					$before_widget
				);

				$params[0]['before_widget'] = $before_widget;
			}

			return $params;
		}
	}
}
