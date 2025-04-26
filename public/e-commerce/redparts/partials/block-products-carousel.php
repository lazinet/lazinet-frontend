<?php
/**
 * Template for block products carousel.
 *
 * @package RedParts
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $args ) ) {
	$args = array();
}

$args = wp_parse_args(
	$args,
	array(
		'title'    => '',
		'layout'   => 'grid-5',
		'products' => array(),
		'class'    => '',
	)
);

if ( empty( $args['title'] ) || empty( $args['products'] ) ) {
	return;
}

$classes = array( 'th-block', 'th-block-products-carousel', $args['class'] );

if ( $args['products'] ) :
	?>
	<!-- .th-block-products-carousel -->
	<div class="<?php redparts_the_classes( ...$classes ); ?>" data-layout="<?php echo esc_attr( $args['layout'] ); ?>">
		<div class="th-container">
			<?php
			redparts_the_template(
				'partials/block-header',
				array(
					'title'  => $args['title'],
					'arrows' => true,
				)
			);
			?>

			<div class="th-block-products-carousel__carousel">
				<div class="th-block-products-carousel__carousel-loader"></div>
				<div class="owl-carousel">
					<?php foreach ( $args['products'] as $product ) : ?>
						<div class="th-block-products-carousel__column">
							<ul class="th-block-products-carousel__cell">
								<?php
								global $post;

								// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
								$post = get_post( $product->get_id() );

								setup_postdata( $post );

								wc_set_loop_prop( 'redparts_class', 'th-block-products-carousel__item' );
								wc_set_loop_prop( 'redparts_product_card_class', 'th-product-card--layout--grid' );

								wc_get_template_part( 'content', 'product' );
								?>
							</ul>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
	<!-- .th-block-products-carousel / end -->
	<?php
endif;

wp_reset_postdata();
