<?php
/**
 * Walker for dropdown list.
 *
 * @package RedParts
 * @since 1.0.0
 */

namespace RedParts;

use Walker;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'RedParts\Walker_Dropdown_List' ) ) {
	/**
	 * Class Checkout
	 */
	class Walker_Dropdown_List extends Walker {
		/**
		 * Database fields to use.
		 *
		 * @var array
		 *
		 * @see Walker::$db_fields
		 */
		public $db_fields = array(
			'parent' => 'parent',
			'id'     => 'term_id',
		);

		/**
		 * Starts the element output.
		 *
		 * @since 2.1.0
		 *
		 * @see Walker::start_el()
		 *
		 * @param string $output            Used to append additional content (passed by reference).
		 * @param object $object            Category data object.
		 * @param int    $depth             Depth of category. Used for padding.
		 * @param array  $args              Uses 'selected', 'show_count', and 'value_field' keys, if they exist.
		 *                                  See wp_dropdown_categories().
		 * @param int    $current_object_id Optional. ID of the current category. Default 0 (unused).
		 *
		 * @noinspection PhpMissingParamTypeInspection
		 */
		public function start_el( &$output, $object, $depth = 0, $args = array(), $current_object_id = 0 ) {
			if ( isset( $args['value_field'] ) && isset( $object->{$args['value_field']} ) ) {
				$value_field = $args['value_field'];
			} else {
				$value_field = 'term_id';
			}

			$item_id_prefix = '';

			if ( isset( $args['item_id_prefix'] ) ) {
				$item_id_prefix = (string) $args['item_id_prefix'];
			}

			$item_id = $item_id_prefix . $object->term_id;

			$output .= '<li';
			$output .= ' id="' . esc_attr( $item_id ) . '"';
			$output .= ' role="option"';
			$output .= ' class="th-dropdown-list__item"';
			$output .= ' data-value="' . esc_attr( $object->{$value_field} ) . '"';
			$output .= '>';

			$output .= str_repeat( '<div class="th-dropdown-list__item-padding"></div>', $depth );
			$output .= '<div class="th-dropdown-list__item-title">' . esc_html( $object->name ) . '</div>';
			$output .= '</li>';
		}
	}
}
