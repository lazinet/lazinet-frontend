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

if ( ! is_active_sidebar( 'redparts-blog' ) ) {
	return;
}
?>
<aside class="th-sidebar th-sidebar--location--blog widget-area">
	<?php dynamic_sidebar( 'redparts-blog' ); ?>
</aside>
