<?php
/**
 * Single Product Meta.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.0.0
 */

use RedParts\Settings;

defined( 'ABSPATH' ) || exit;

global $product;

$product_attributes = $product->get_attributes();

$meta_attributes = explode( ',', Settings::instance()->get_option( 'product_meta_attributes', '__SKU__' ) );
$meta_items      = array();

foreach ( $meta_attributes as $meta_attribute ) {
	$result_item = apply_filters( 'redparts_product_meta_item', array(), $meta_attribute );

	if ( empty( $result_item ) ) {
		if ( '__SKU__' === $meta_attribute ) {
			ob_start();

			if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) :
				$sku = $product->get_sku();
				?>
				<span class="sku"><?php echo esc_html( $sku ? $sku : esc_html__( 'N/A', 'redparts' ) ); ?></span>
				<?php
			endif;

			$value = ob_get_clean();

			if ( $value ) {
				$result_item = array(
					'label' => esc_html__( 'SKU', 'redparts' ),
					'value' => $value,
				);
			}
		} else {
			$slug = wc_attribute_taxonomy_name( sanitize_key( $meta_attribute ) );

			if ( isset( $product_attributes[ $slug ] ) && $product_attributes[ $slug ]->is_taxonomy() ) {
				$attribute          = $product_attributes[ $slug ];
				$attribute_taxonomy = $attribute->get_taxonomy_object();
				$values             = array();

				$attribute_values = wc_get_product_terms( $product->get_id(), $attribute->get_name(), array( 'fields' => 'all' ) );

				foreach ( $attribute_values as $attribute_value ) {
					$value_name = esc_html( $attribute_value->name );

					if ( $attribute_taxonomy->attribute_public ) {
						$url      = esc_url( get_term_link( $attribute_value->term_id, $attribute->get_name() ) );
						$values[] = '<a href="' . $url . '" rel="tag">' . $value_name . '</a>';
					} else {
						$values[] = $value_name;
					}
				}

				$result_item = array(
					'label' => wc_attribute_label( $attribute->get_name() ),
					'value' => wptexturize( implode( ', ', $values ) ),
				);
			}
		}
	}

	$result_item = apply_filters( 'redparts_product_meta_item_after', $result_item, $meta_attribute );

	if ( ! empty( $result_item ) ) {
		$meta_items[] = $result_item;
	}
}

ob_start();
$show_meta = false;

?>
<div class="th-product__meta product_meta">
	<?php
	ob_start();
	do_action( 'woocommerce_product_meta_start' );

	if ( ! empty( ob_get_contents() ) ) {
		$show_meta = true;
	}

	ob_end_flush();

	if ( ! empty( $meta_items ) ) :
		$show_meta = true;
		?>
		<table>
			<tbody>
				<?php foreach ( $meta_items as $meta_item ) : ?>
					<tr>
						<th><?php echo esc_html( $meta_item['label'] ); ?></th>
						<td><?php echo wp_kses( $meta_item['value'], 'redparts_product_meta_value' ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	endif;

	ob_start();
	do_action( 'woocommerce_product_meta_end' );

	if ( ! empty( ob_get_contents() ) ) {
		$show_meta = true;
	}

	ob_end_flush();
	?>
</div>
<?php

if ( $show_meta ) {
	ob_end_flush();
} else {
	ob_end_clean();
}
