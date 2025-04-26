<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Stroyka
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! is_active_sidebar( 'redparts-product-alt' ) ) {
	return;
}
?>
<aside class="th-product__widgets th-sidebar th-sidebar--location--product widget-area">
	<?php dynamic_sidebar( 'redparts-product-alt' ); ?>
</aside>
