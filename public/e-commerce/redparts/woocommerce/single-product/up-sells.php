<?php
/**
 * Single Product Up-Sells
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/up-sells.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.0.0
 */

use RedParts\Product;

defined( 'ABSPATH' ) || exit;

$product_layout = Product::instance()->get_layout();
$block_layout   = '-full' === substr( $product_layout, -5 ) ? 'grid-5' : 'grid-4-sm';

/**
 * Array of product objects.
 *
 * @var WC_Product[] $upsells
 */

redparts_the_template(
	'partials/block-products-carousel',
	array(
		'title'    => esc_html__( 'You may also like&hellip;', 'redparts' ),
		'layout'   => $block_layout,
		'products' => $upsells,
		'class'    => 'th-product-view__products-list',
	)
);
