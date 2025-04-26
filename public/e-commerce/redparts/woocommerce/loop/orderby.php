<?php
/**
 * Show options for ordering
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/orderby.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.6.0
 */

defined( 'ABSPATH' ) || exit;

/** Order by options. @var array $catalog_orderby_options */
/** Current order by value. @var array $orderby */

?>
<form class="th-view-options__select woocommerce-ordering" method="get">
	<label for="th-view-option-sort"><?php esc_attr_e( 'Shop order', 'redparts' ); ?></label>
	<select id="th-view-option-sort" class="th-select th-select--size--small orderby" name="orderby">
		<?php foreach ( $catalog_orderby_options as $option_id => $option_name ) : ?>
			<option value="<?php echo esc_attr( $option_id ); ?>" <?php selected( $orderby, $option_id ); ?>>
				<?php echo esc_html( $option_name ); ?>
			</option>
		<?php endforeach; ?>
	</select>
	<input type="hidden" name="paged" value="1" />
	<?php wc_query_string_form_fields( null, array( 'orderby', 'submit', 'paged', 'product-page' ) ); ?>
</form>
