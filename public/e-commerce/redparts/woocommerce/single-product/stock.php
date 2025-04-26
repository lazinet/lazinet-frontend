<?php
/**
 * Single Product stock.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/stock.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Used global variables in the template.
 *
 * @global string $class        - Stock class.
 * @global string $availability - Stock label.
 */

$badge_classes = array( 'th-status-badge', 'th-status-badge--has-text' );

if ( 'in-stock' === $class ) {
	$badge_classes[] = 'th-status-badge--style--success';
}
if ( 'out-of-stock' === $class ) {
	$badge_classes[] = 'th-status-badge--style--failure';
}

?>
<div class="<?php redparts_the_classes( ...$badge_classes ); ?>">
	<div class="th-status-badge__body">
		<div class="th-status-badge__text">
			<?php echo wp_kses( $availability, 'redparts_availability_text' ); ?>
		</div>
	</div>
</div>
