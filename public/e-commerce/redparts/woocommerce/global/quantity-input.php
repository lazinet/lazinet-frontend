<?php
/**
 * Product quantity inputs
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/global/quantity-input.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 4.0.0
 */

defined( 'ABSPATH' ) || exit;

global $stroyka_in_add_to_cart_quantity;

/** Maximum quantity. @var integer $max_value */
/** Minimum quantity. @var integer $min_value */
/** Input field id. @var string $input_id */
/** Input field name. @var string $input_name */
/** Input field value. @var integer $input_value */
/** Input field placeholder. @var string $placeholder */
/** Input field mode. @var integer $inputmode */
/** Input field step. @var integer $step */
/** Input field classes. @var string|string[] $classes */

if ( $max_value && $min_value === $max_value ) {
	?>
	<div class="quantity hidden">
		<input type="hidden" id="<?php echo esc_attr( $input_id ); ?>" class="qty" name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $min_value ); ?>" />
	</div>
	<?php
} else {
	/* translators: %s: Quantity. */
	$label = ! empty( $args['product_name'] ) ? sprintf( esc_html__( '%s quantity', 'redparts' ), wp_strip_all_tags( $args['product_name'] ) ) : esc_html__( 'Quantity', 'redparts' );
	?>
	<?php if ( ! empty( $stroyka_in_add_to_cart_quantity ) && $stroyka_in_add_to_cart_quantity ) : ?>
		<label class="th-quantity-label" for="<?php echo esc_attr( $input_id ); ?>">
			<?php esc_html_e( 'Quantity', 'redparts' ); ?>
		</label>
	<?php endif; ?>
	<div class="th-quantity th-input-number quantity">
		<label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php echo esc_attr( $label ); ?></label>
		<input
			type="number"
			id="<?php echo esc_attr( $input_id ); ?>"
			class="th-quantity__input th-input-number__input <?php echo esc_attr( join( ' ', (array) $classes ) ); ?>"
			step="<?php echo esc_attr( $step ); ?>"
			min="<?php echo esc_attr( $min_value ); ?>"
			<?php if ( 0 < $max_value ) : ?>
				max="<?php echo esc_attr( $max_value ); ?>"
			<?php endif; ?>
			name="<?php echo esc_attr( $input_name ); ?>"
			value="<?php echo esc_attr( $input_value ); ?>"
			title="<?php echo esc_attr_x( 'Qty', 'Product quantity input tooltip', 'redparts' ); ?>"
			placeholder="<?php echo esc_attr( $placeholder ); ?>"
			inputmode="<?php echo esc_attr( $inputmode ); ?>">
		<div class="th-input-number__add"></div>
		<div class="th-input-number__sub"></div>
	</div>
	<?php
}
