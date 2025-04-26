<?php
/**
 * The template for displaying the mobile menu.
 *
 * @package RedParts
 * @since 1.0.0
 */

use RedParts\Settings;
use RedParts\Sputnik\Settings as SputnikSettings;

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
global $WOOCS;

$settings = Settings::instance();

$woocs_installed = isset( $WOOCS );

if ( $woocs_installed ) {
	$currencies           = $WOOCS->get_currencies();
	$current_currency_key = $WOOCS->current_currency;
	$current_currency     = $currencies[ $current_currency_key ];
}
// phpcs:enable

$wishlist = null;

if (
	class_exists( 'RedParts\Sputnik\Wishlist' ) &&
	redparts_sputnik_version_is( '>=', '1.5.0' ) &&
	RedParts\Sputnik\Wishlist::instance()->is_enabled()
) {
	$wishlist = RedParts\Sputnik\Wishlist::instance();
}

$sputnik_settings = null;
$garage           = null;

if ( class_exists( 'RedParts\Sputnik\Garage' ) ) {
	$garage = RedParts\Sputnik\Garage::instance();
}
if ( class_exists( 'RedParts\Sputnik\Settings' ) ) {
	$sputnik_settings = SputnikSettings::instance();
}

$autoparts_features       = $sputnik_settings && 'no' !== $sputnik_settings->get( 'autoparts_features' ) && $garage;
$indicators_show_wishlist = 'no' !== $settings->get_option( 'mobile_menu_indicators_show_wishlist' ) && $wishlist;
$indicators_show_account  = 'no' !== $settings->get_option( 'mobile_menu_indicators_show_account' ) && class_exists( 'WooCommerce' );
$indicators_show_cart     = 'no' !== $settings->get_option( 'mobile_menu_indicators_show_cart' ) && class_exists( 'WooCommerce' );
$indicators_show_garage   = 'no' !== $settings->get_option( 'mobile_menu_indicators_show_garage' ) && $autoparts_features;
$indicators_show          = 'no' !== $settings->get_option( 'mobile_menu_indicators_show' ) && (
	$indicators_show_wishlist ||
	$indicators_show_account ||
	$indicators_show_cart ||
	$indicators_show_garage
);

?>
<div class="th-mobile-menu">
	<div class="th-mobile-menu__links-panel th-mobile-menu__links-panel--template" data-mobile-menu-panel>
		<div class="th-mobile-menu__panel th-mobile-menu__panel--hidden">
			<div class="th-mobile-menu__panel-header">
				<button class="th-mobile-menu__panel-back" type="button">
					<?php redparts_the_icon( 'arrow-rounded-left-7x11' ); ?>
				</button>
				<div class="th-mobile-menu__panel-title"></div>
			</div>
			<div class="th-mobile-menu__panel-body">
				<div class="th-mobile-menu__links"></div>
			</div>
		</div>
	</div>

	<div class="th-mobile-menu__backdrop"></div>
	<div class="th-mobile-menu__body">
		<button class="th-mobile-menu__close" type="button">
			<?php redparts_the_icon( 'cross-12' ); ?>
		</button>

		<div class="th-mobile-menu__panel">
			<div class="th-mobile-menu__panel-header">
				<div class="th-mobile-menu__panel-title">
					<?php echo esc_html__( 'Menu', 'redparts' ); ?>
				</div>
			</div>
			<div class="th-mobile-menu__panel-body">

				<?php if ( $woocs_installed && 1 < count( $currencies ) ) : ?>
					<div class="th-mobile-menu__settings-list">
						<div class="th-mobile-menu__setting" data-mobile-menu-item>
							<button
								class="th-mobile-menu__setting-button"
								title="<?php echo esc_attr__( 'Currency', 'redparts' ); ?>"
								data-mobile-menu-trigger
							>
								<span class="th-mobile-menu__setting-icon th-mobile-menu__setting-icon--currency">
									<?php echo esc_html( $current_currency['symbol'] ); ?>
								</span>
								<span class="th-mobile-menu__setting-title">
									<?php echo esc_html( $current_currency['description'] ); ?>
								</span>
								<span class="th-mobile-menu__setting-arrow">
									<?php redparts_the_icon( 'arrow-rounded-right-6x9' ); ?>
								</span>
							</button>

							<div class="th-mobile-menu__setting-panel" data-mobile-menu-panel>
								<div class="th-mobile-menu__panel th-mobile-menu__panel--hidden">
									<div class="th-mobile-menu__panel-header">
										<button class="th-mobile-menu__panel-back" type="button">
											<?php redparts_the_icon( 'arrow-rounded-left-7x11' ); ?>
										</button>
										<div class="th-mobile-menu__panel-title">
											<?php echo esc_html__( 'Currency', 'redparts' ); ?>
										</div>
									</div>
									<div class="th-mobile-menu__panel-body">
										<div class="th-mobile-menu__links">
											<ul>
												<?php foreach ( $currencies as $currency_code => $currency ) : ?>
													<li>
														<a href="" data-th-currency-code="<?php echo esc_attr( $currency_code ); ?>">
															<?php echo esc_html( $currency['symbol'] ); ?>
															<?php echo esc_html( $currency['description'] ); ?>
														</a>
													</li>
												<?php endforeach; ?>
											</ul>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="th-mobile-menu__divider"></div>
				<?php endif; ?>

				<?php if ( $indicators_show ) : ?>
					<div class="th-mobile-menu__indicators">
						<?php if ( $indicators_show_wishlist ) : ?>
							<a
								class="th-mobile-menu__indicator th-mobile-menu__indicator--wishlist"
								href="<?php echo esc_url( $wishlist->get_page_url() ); ?>"
							>
								<span class="th-mobile-menu__indicator-icon">
									<?php redparts_the_icon( 'heart-20' ); ?>
									<span class="th-mobile-menu__indicator-counter">
										<?php echo esc_html( $wishlist->get_count() ); ?>
									</span>
								</span>
								<span class="th-mobile-menu__indicator-title">
									<?php echo esc_html__( 'Wishlist', 'redparts' ); ?>
								</span>
							</a>
						<?php endif; ?>
						<?php if ( $indicators_show_account ) : ?>
							<a
								class="th-mobile-menu__indicator"
								href="<?php echo esc_url( wc_get_account_endpoint_url( 'dashboard' ) ); ?>"
							>
								<span class="th-mobile-menu__indicator-icon">
									<?php redparts_the_icon( 'person-20' ); ?>
								</span>
								<span class="th-mobile-menu__indicator-title">
									<?php echo esc_html__( 'Account', 'redparts' ); ?>
								</span>
							</a>
						<?php endif; ?>
						<?php if ( $indicators_show_cart ) : ?>
							<a
								class="th-mobile-menu__indicator th-mobile-menu__indicator--cart"
								href="<?php echo esc_url( wc_get_cart_url() ); ?>"
							>
								<span class="th-mobile-menu__indicator-icon">
									<?php redparts_the_icon( 'cart-20' ); ?>
									<span class="th-mobile-menu__indicator-counter">
										<?php echo esc_html( WC()->cart->get_cart_contents_count() ); ?>
									</span>
								</span>
								<span class="th-mobile-menu__indicator-title">
									<?php echo esc_html__( 'Cart', 'redparts' ); ?>
								</span>
							</a>
						<?php endif; ?>
						<?php if ( $indicators_show_garage ) : ?>
							<a
								class="th-mobile-menu__indicator"
								href="<?php echo esc_url( wc_get_account_endpoint_url( 'garage' ) ); ?>"
							>
								<span class="th-mobile-menu__indicator-icon">
									<?php redparts_the_icon( 'car-20' ); ?>
								</span>
								<span class="th-mobile-menu__indicator-title">
									<?php echo esc_html__( 'Garage', 'redparts' ); ?>
								</span>
							</a>
						<?php endif; ?>
					</div>
					<div class="th-mobile-menu__divider"></div>
				<?php endif; ?>

				<?php
				wp_nav_menu(
					array(
						'theme_location'         => 'redparts-mobile-menu',
						'container_class'        => 'th-mobile-menu__links',
						'fallback_cb'            => '',
						'redparts_megamenu'      => true,
						'redparts_arrows'        => array(
							'root' => redparts_get_icon( 'arrow-rounded-right-6x9', 'th-menu-item-arrow' ),
							'deep' => redparts_get_icon( 'arrow-rounded-right-6x9', 'th-menu-item-arrow' ),
						),
						'redparts_megamenu_args' => array(
							'redparts_arrows' => array(
								'root' => redparts_get_icon( 'arrow-rounded-right-6x9', 'th-menu-item-arrow' ),
								'deep' => redparts_get_icon( 'arrow-rounded-right-6x9', 'th-menu-item-arrow' ),
							),
						),
					)
				);
				?>

				<?php

				$contacts_title    = $settings->get_option( 'mobile_menu_contacts_title' );
				$contacts_subtitle = $settings->get_option( 'mobile_menu_contacts_subtitle' );
				$contacts_url      = $settings->get_option( 'mobile_menu_contacts_url' );
				$contacts_show     = 'yes' === $settings->get_option( 'mobile_menu_contacts_show' );
				$contacts_show     = $contacts_show && ( ! empty( $contacts_title ) || ! empty( $contacts_subtitle ) );

				?>
				<?php if ( $contacts_show ) : ?>
					<div class="th-mobile-menu__spring"></div>
					<div class="th-mobile-menu__divider"></div>
					<?php if ( ! empty( $contacts_url ) ) : ?>
						<a class="th-mobile-menu__contacts" href="<?php echo esc_url( $contacts_url ); ?>">
					<?php else : ?>
						<span class="th-mobile-menu__contacts">
					<?php endif; ?>
						<div class="th-mobile-menu__contacts-subtitle">
							<?php echo esc_html( $contacts_subtitle ); ?>
						</div>
						<div class="th-mobile-menu__contacts-title">
							<?php echo esc_html( $contacts_title ); ?>
						</div>
					<?php if ( empty( $contacts_url ) ) : ?>
						</span>
					<?php else : ?>
						</a>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</div>

	</div>
</div>
