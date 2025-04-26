<?php
/**
 * The departments that will be displayed on desktop devices.
 *
 * @package RedParts
 * @since 1.0.0
 */

use RedParts\Header;
use RedParts\Settings;

defined( 'ABSPATH' ) || exit;

$header_layout            = Header::instance()->get_layout();
$departments_button_label = Settings::instance()->get_option( 'header_departments_button_label' );

if ( empty( $departments_button_label ) ) {
	$departments_button_label = 'spaceship' === $header_layout
		? esc_html__( 'Menu', 'redparts' )
		: esc_html__( 'Shop By Category', 'redparts' );
}

?>
<div class="th-departments">
	<button class="th-departments__button" type="button">
		<span class="th-departments__button-icon">
			<?php redparts_the_icon( 'menu-16x12' ); ?>
		</span>
		<span class="th-departments__button-title">
			<?php echo esc_html( apply_filters( 'redparts_departments_button_title', $departments_button_label ) ); ?>
		</span>
		<span class="th-departments__button-arrow">
			<?php redparts_the_icon( 'arrow-rounded-down-9x6' ); ?>
		</span>
	</button>
	<div class="th-departments__menu">
		<div class="th-departments__arrow"></div>
		<div class="th-departments__body">
			<?php

			$items_wrap = '
			<ul id="%1$s" class="%2$s">
				<li class="th-menu-item-padding" role="presentation"></li>
				%3$s
				<li class="th-menu-item-padding" role="presentation"></li>
			</ul>
			';

			wp_nav_menu(
				array(
					'theme_location'    => 'redparts-departments',
					'menu_class'        => 'th-departments__list',
					'items_wrap'        => $items_wrap,
					'fallback_cb'       => '',
					'depth'             => 1,
					'redparts_megamenu' => true,
					'redparts_arrows'   => array(
						'root' => redparts_get_icon( 'arrow-rounded-right-7x11', 'th-menu-item-arrow' ),
						'deep' => redparts_get_icon( 'arrow-rounded-right-7x11', 'th-menu-item-arrow' ),
					),
				)
			);
			?>
			<div class="th-departments__menu-container"></div>
		</div>
	</div>
</div>
