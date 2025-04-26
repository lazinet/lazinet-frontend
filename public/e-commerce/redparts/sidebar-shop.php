<?php
/**
 * The sidebar containing the shop widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package RedParts
 * @since 1.0.0
 */

use RedParts\Shop;
use RedParts\Product;

defined( 'ABSPATH' ) || exit;

$sidebar_name = '';

if ( is_product() ) {
	$sidebar_name = Product::instance()->get_sidebar_name();
} else {
	$sidebar_name = Shop::instance()->get_sidebar_name();
}

if ( empty( $sidebar_name ) || ! is_active_sidebar( $sidebar_name ) ) {
	return;
}

dynamic_sidebar( $sidebar_name );
