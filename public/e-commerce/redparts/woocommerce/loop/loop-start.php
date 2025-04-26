<?php
/**
 * Product Loop Start
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/loop-start.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.3.0
 */

use RedParts\Shop;

defined( 'ABSPATH' ) || exit;

$view_mode = Shop::instance()->get_view_mode();
$grid      = Shop::instance()->get_layout();

global $woocommerce_loop;

$classes = array(
	'th-products-list',
);

if ( empty( $woocommerce_loop['name'] ) ) {
	$classes[] = 'th-products-view__list';

	if ( preg_match( '#^grid-([0-9])-(?:sidebar|full)$#', $grid, $matches ) ) {
		$classes[] = 'th-products-list--grid--' . $matches[1];
	}
}

if ( 'cross-sells' === $woocommerce_loop['name'] ) {
	$view_mode = 'grid';
	$grid      = 'grid-5-full';
}

?>
<div
	class="<?php redparts_the_classes( ...$classes ); ?>"
	data-layout="<?php echo esc_attr( 'grid-with-features' === $view_mode ? 'grid' : $view_mode ); ?>"
	data-with-features="<?php echo esc_attr( 'grid-with-features' === $view_mode ? 'true' : 'false' ); ?>"
>
	<div class="th-products-list__head">
		<div class="th-products-list__column th-products-list__column--image">
			<?php esc_html_e( 'Image', 'redparts' ); ?>
		</div>
		<div class="th-products-list__column th-products-list__column--meta">
			<?php esc_html_e( 'SKU', 'redparts' ); ?>
		</div>
		<div class="th-products-list__column th-products-list__column--product">
			<?php esc_html_e( 'Product', 'redparts' ); ?>
		</div>
		<?php if ( function_exists( 'wc_review_ratings_enabled' ) && wc_review_ratings_enabled() ) : ?>
			<div class="th-products-list__column th-products-list__column--rating">
				<?php esc_html_e( 'Rating', 'redparts' ); ?>
			</div>
		<?php endif; ?>
		<div class="th-products-list__column th-products-list__column--price">
			<?php esc_html_e( 'Price', 'redparts' ); ?>
		</div>
	</div>
	<ul class="th-products-list__content">
