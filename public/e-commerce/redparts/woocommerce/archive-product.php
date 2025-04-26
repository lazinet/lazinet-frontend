<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @noinspection DuplicatedCode
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

use RedParts\Shop;

defined( 'ABSPATH' ) || exit;

$layout           = Shop::instance()->get_layout();
$sidebar_position = 'none';

if ( '-full' !== substr( $layout, -5 ) ) {
	$sidebar_position = Shop::instance()->get_sidebar_position();
}

$layout_classes = array(
	'th-layout',
	'th-layout--page--shop',
);

if ( 'none' !== $sidebar_position ) {
	$layout_classes[] = 'th-layout--has-sidebar';
}

get_header( 'shop' );

redparts_the_page_header( apply_filters( 'redparts_shop_page_header_args', array() ) );

/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */
do_action( 'woocommerce_before_main_content' );

?>
<div class="<?php redparts_the_classes( ...$layout_classes ); ?>">
	<div class="th-container">
		<div class="th-layout__body">
			<?php if ( 'start' === $sidebar_position ) : ?>
				<div class="th-layout__item th-layout__item--sidebar">
					<?php Shop::instance()->the_sidebar( false ); ?>
				</div>
			<?php endif; ?>
			<div class="th-layout__item th-layout__item--content">
				<?php

				$display_type = woocommerce_get_loop_display_mode();

				// If displaying categories, append to the loop.
				if ( 'subcategories' === $display_type || 'both' === $display_type ) {
					$product_categories = woocommerce_get_product_subcategories( is_product_category() ? get_queried_object_id() : 0 );

					if ( $product_categories ) {
						?>
						<div class="th-categories-list th-categories-list--layout--columns-5-sidebar">
							<ul class="th-categories-list__body">
								<?php
								foreach ( $product_categories as $category ) {
									?>
									<li <?php wc_product_cat_class( 'th-categories-list__item', $category ); ?>>
										<a
											href="<?php echo esc_url( get_term_link( $category, 'product_cat' ) ); ?>"
											class="th-categories-list__item-body"
										>
											<?php woocommerce_subcategory_thumbnail( $category ); ?>

											<h2 class="th-categories-list__item-title">
												<?php echo esc_html( $category->name ); ?>
											</h2>
											<?php if ( $category->count > 0 ) : ?>
												<div class="th-categories-list__item-count">
													<?php
													$text = sprintf(
														// translators: %s: Products count.
														_n( // SKIP-ESC.
															'<span class="value">%s</span> <span class="label">Product</span>',
															'<span class="value">%s</span> <span class="label">Products</span>',
															$category->count,
															'redparts'
														),
														$category->count
													);

													echo wp_kses( $text, 'redparts_text' );
													?>
												</div>
											<?php endif; ?>
										</a>
									</li>
									<li class="th-categories-list__divider" role="presentation"></li>
									<?php
								}
								?>
							</ul>
						</div>
						<div class="th-mt-3 th-pt-1"></div>
						<?php
					}

					if ( 'subcategories' === $display_type ) {
						wc_set_loop_prop( 'total', 0 );

						// This removes pagination and products from display for themes not using wc_get_loop_prop in their product loops.
						global $wp_query;

						if ( $wp_query->is_main_query() ) {
							$wp_query->post_count    = 0;
							$wp_query->max_num_pages = 0;
						}
					}
				}

				/**
				 * Hook: woocommerce_archive_description.
				 *
				 * @hooked woocommerce_taxonomy_archive_description - 10
				 * @hooked woocommerce_product_archive_description - 10
				 */
				do_action( 'woocommerce_archive_description' );

				?>
				<?php
				if ( woocommerce_product_loop() ) {
					/**
					 * Hook: woocommerce_before_shop_loop.
					 *
					 * @hooked woocommerce_output_all_notices     - 10
					 * @hooked RedParts\Shop::view_options        - 15
					 * @hooked RedParts\Shop::view_options_legend - 20
					 * @hooked RedParts\Shop::view_options_spring - 25
					 * @hooked woocommerce_catalog_ordering - 30
					 * @hooked RedParts\Shop::view_options_per_page - 40
					 * @hooked RedParts\Shop::view_options_close_tag_before_loop - 100
					 */
					do_action( 'woocommerce_before_shop_loop' );

					woocommerce_product_loop_start();

					wc_set_loop_prop( 'redparts_compatibility_badge_scope', 'shop' );

					if ( wc_get_loop_prop( 'total' ) ) {
						while ( have_posts() ) {
							the_post();

							/**
							 * Hook: woocommerce_shop_loop.
							 */
							do_action( 'woocommerce_shop_loop' );

							wc_get_template_part( 'content', 'product' );
						}
					}

					woocommerce_product_loop_end();

					/**
					 * Hook: woocommerce_after_shop_loop.
					 *
					 * @hooked RedParts\Shop::pagination - 10
					 * @hooked RedParts\Shop::view_options_close_tag_after_loop - 20
					 */
					do_action( 'woocommerce_after_shop_loop' );
				} else {
					/**
					 * Hook: woocommerce_no_products_found.
					 *
					 * @hooked wc_no_products_found - 10
					 */
					do_action( 'woocommerce_no_products_found' );
				}
				?>
			</div>
			<?php if ( 'end' === $sidebar_position ) : ?>
				<div class="th-layout__item th-layout__item--sidebar">
					<?php Shop::instance()->the_sidebar( false ); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<div class="th-block-space th-block-space--layout--before-footer"></div>

<?php

do_action( 'woocommerce_after_main_content' );

if ( ! in_array( $sidebar_position, Shop::VALID_SIDEBAR_POSITIONS, true ) ) {
	Shop::instance()->the_sidebar( true );
}

get_footer( 'shop' );
