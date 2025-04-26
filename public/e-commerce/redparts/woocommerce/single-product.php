<?php
/**
 * The Template for displaying all single products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 1.6.4
 */

use RedParts\Product;

defined( 'ABSPATH' ) || exit;

$layout           = Product::instance()->get_layout();
$sidebar_position = 'none';

if ( 'full' !== $layout ) {
	$sidebar_position = Product::instance()->get_sidebar_position();
}

$layout_classes = array(
	'th-layout',
	'th-layout--page--product',
);

if ( 'none' !== $sidebar_position ) {
	$layout_classes[] = 'th-layout--has-sidebar';
}

get_header( 'shop' );

redparts_the_page_header( apply_filters( 'redparts_product_page_header_args', array( 'show_title' => false ) ) );

do_action( 'woocommerce_before_main_content' );

?>

<div class="<?php redparts_the_classes( ...$layout_classes ); ?>">
	<div class="th-container">
		<div class="th-layout__body">
			<?php if ( 'start' === $sidebar_position ) : ?>
				<div class="th-layout__item th-layout__item--sidebar">
					<?php
					/**
					 * Hook: woocommerce_sidebar.
					 *
					 * @hooked woocommerce_get_sidebar - 10
					 */
					do_action( 'woocommerce_sidebar' );
					?>
				</div>
			<?php endif; ?>
			<div class="th-layout__item th-layout__item--content">
				<?php while ( have_posts() ) : ?>
					<?php
					the_post();

					wc_get_template_part( 'content', 'single-product' );
					?>
				<?php endwhile; // end of the loop. ?>
			</div>
			<?php if ( 'end' === $sidebar_position ) : ?>
				<div class="th-layout__item th-layout__item--sidebar">
					<?php
					/**
					 * Hook: woocommerce_sidebar.
					 *
					 * @hooked woocommerce_get_sidebar - 10
					 */
					do_action( 'woocommerce_sidebar' );
					?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<div class="th-block-space th-block-space--layout--before-footer"></div>

<?php

do_action( 'woocommerce_after_main_content' );

get_footer( 'shop' );

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
