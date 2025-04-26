<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

use RedParts\Product;

defined( 'ABSPATH' ) || exit;

global $product;

$layout = Product::instance()->get_layout();

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked wc_print_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	// Redundant escape.
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo get_the_password_form();
	return;
}

$product_classes = array( 'th-product', 'th-product--layout--' . $layout );

$sidebar_name = apply_filters( 'redparts_product_sidebar_name', 'redparts-product-alt' );

?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'th-product-view', $product ); ?>>
	<div class="<?php redparts_the_classes( ...$product_classes ); ?>">
		<div class="th-product__body">
			<div class="th-product__card th-product__card--one"></div>
			<div class="th-product__card th-product__card--two"></div>

			<?php
			/**
			 * Hook: woocommerce_before_single_product_summary.
			 *
			 * @hooked RedParts\Product::gallery_template - 100
			 * @hooked RedParts\Product::header_template  - 200
			 * @hooked RedParts\Product::intro_template   - 300
			 */
			do_action( 'woocommerce_before_single_product_summary' );
			?>

			<div class="th-product__sidebar">
				<div class="th-product__summary summary entry-summary">
					<?php
					/**
					 * Hook: woocommerce_single_product_summary.
					 *
					 * @hooked WC_Structured_Data::generate_product_data() - 60
					 * @hooked RedParts\Product::sale_badge                - 100
					 * @hooked woocommerce_template_single_price           - 200
					 * @hooked RedParts\Product::stock_badge               - 300
					 * @hooked woocommerce_template_single_meta            - 400
					 * @hooked woocommerce_template_single_add_to_cart     - 500
					 * @hooked RedParts\Product::tags                      - 600
					 * @hooked woocommerce_template_single_sharing         - 700
					 */
					do_action( 'woocommerce_single_product_summary' );
					?>
				</div>

				<?php get_sidebar( $sidebar_name ); ?>
			</div>

			<?php
			/**
			 * Hook: woocommerce_after_single_product_summary.
			 *
			 * @hooked woocommerce_output_product_data_tabs - 10
			 */
			do_action( 'woocommerce_after_single_product_summary' );
			?>
		</div>
	</div>

	<?php
	/**
	 * Hook: redparts_after_product.
	 *
	 * @hooked woocommerce_upsell_display          - 100
	 * @hooked woocommerce_output_related_products - 200
	 */
	do_action( 'redparts_after_product' );
	?>
</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>
