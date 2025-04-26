<?php
/**
 * Related Products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/related.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.9.0
 */

use RedParts\Product;

defined( 'ABSPATH' ) || exit;

$product_layout = Product::instance()->get_layout();
$block_layout   = 'full' === $product_layout ? 'grid-5' : 'grid-4-sidebar';

/**
 * Array of product objects.
 *
 * @var WC_Product[] $related_products
 */

redparts_the_template(
	'partials/block-products-carousel',
	array(
		'title'    => apply_filters( 'woocommerce_product_related_products_heading', esc_html__( 'Related products', 'redparts' ) ),
		'layout'   => $block_layout,
		'products' => $related_products,
		'class'    => 'th-product-view__products-list',
	)
);
