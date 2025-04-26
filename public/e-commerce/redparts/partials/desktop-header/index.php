<?php
/**
 * The header that will be displayed on desktop devices.
 *
 * @package RedParts
 * @since 1.0.0
 */

use RedParts\Header;
use RedParts\Settings;

defined( 'ABSPATH' ) || exit;

$wishlist = null;

if (
	class_exists( 'RedParts\Sputnik\Wishlist' ) &&
	redparts_sputnik_version_is( '>=', '1.5.0' ) &&
	RedParts\Sputnik\Wishlist::instance()->is_enabled()
) {
	$wishlist = RedParts\Sputnik\Wishlist::instance();
}

$header_layout           = Header::instance()->get_layout();
$show_departments_menu   = Header::instance()->show_departments_menu();
$show_topbar             = Header::instance()->show_topbar();
$show_wishlist_indicator = 'no' !== Settings::instance()->get_option( 'header_show_wishlist_indicator', 'yes' );
$show_account_indicator  = 'no' !== Settings::instance()->get_option( 'header_show_account_indicator', 'yes' );
$show_cart_indicator     = 'no' !== Settings::instance()->get_option( 'header_show_cart_indicator', 'yes' );

$classes = array( 'th-header' );

if ( ! $show_topbar ) {
	$classes[] = 'th-header--without-topbar';
}

?>
<div class="<?php redparts_the_classes( ...$classes ); ?>">
	<div class="th-header__megamenu-area th-megamenu-area"></div>

	<?php if ( $show_topbar ) : ?>
		<?php if ( 'spaceship' === $header_layout ) : ?>
			<div class="th-header__topbar-start-bg"></div>
			<div class="th-header__topbar-start">
				<?php
				wp_nav_menu(
					array(
						'theme_location'  => 'redparts-topbar-start',
						'menu_class'      => 'th-topbar th-topbar--start',
						'container'       => '',
						'fallback_cb'     => '',
						'depth'           => 2,
						'redparts_arrows' => array(
							'root' => redparts_get_icon( 'arrow-down-sm-7x5', 'th-menu-item-arrow' ),
							'deep' => redparts_get_icon( 'arrow-rounded-right-7x11', 'th-menu-item-arrow' ),
						),
					)
				);
				?>
			</div>
			<div class="th-header__topbar-end-bg"></div>
			<div class="th-header__topbar-end">
				<?php get_template_part( 'partials/desktop-header/topbar' ); ?>
			</div>
		<?php endif; ?>
		<?php if ( 'classic' === $header_layout ) : ?>
			<div class="th-header__topbar-classic-bg"></div>
			<div class="th-header__topbar-classic">
				<?php get_template_part( 'partials/desktop-header/topbar' ); ?>
			</div>
		<?php endif; ?>
	<?php endif; ?>

	<div class="th-header__navbar">
		<?php if ( $show_departments_menu ) : ?>
			<div class="th-header__navbar-departments">
				<?php get_template_part( 'partials/desktop-header/departments' ); ?>
			</div>
		<?php endif; ?>
		<div class="th-header__navbar-menu">
			<?php get_template_part( 'partials/desktop-header/main-menu' ); ?>
		</div>
		<?php if ( 'classic' === $header_layout ) : ?>
			<?php

			$settings          = Settings::instance();
			$contacts_title    = $settings->get_option( 'header_contacts_title' );
			$contacts_subtitle = $settings->get_option( 'header_contacts_subtitle' );
			$contacts_url      = $settings->get_option( 'header_contacts_url' );
			$contacts_show     = 'yes' === $settings->get_option( 'header_contacts_show' );
			$contacts_show     = $contacts_show && ( ! empty( $contacts_title ) || ! empty( $contacts_subtitle ) );

			?>
			<?php if ( $contacts_show ) : ?>
				<div class="th-header__navbar-phone th-phone">
					<?php if ( ! empty( $contacts_url ) ) : ?>
						<a class="th-phone__body" href="<?php echo esc_url( $contacts_url ); ?>">
					<?php else : ?>
						<span class="th-phone__body">
					<?php endif; ?>
						<div class="th-phone__subtitle">
							<?php echo esc_html( $contacts_subtitle ); ?>
						</div>
						<div class="th-phone__title">
							<?php echo esc_html( $contacts_title ); ?>
						</div>
					<?php if ( empty( $contacts_url ) ) : ?>
						</span>
					<?php else : ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		<?php endif; ?>
	</div>
	<div class="th-header__logo">
		<?php get_template_part( 'partials/desktop-header/logo' ); ?>
	</div>
	<div class="th-header__search">
		<?php
		get_search_form(
			array(
				'echo'                => true,
				'redparts_location'   => 'desktop-header',
				'redparts_search_by'  => 'product',
				'redparts_categories' => true,
			)
		);
		?>
	</div>
	<div class="th-header__indicators">
		<?php if ( $wishlist && $show_wishlist_indicator ) : ?>
			<div class="th-indicator th-indicator--wishlist">
				<a href="<?php echo esc_url( $wishlist->get_page_url() ); ?>" class="th-indicator__button">
					<span class="th-indicator__icon">
						<?php redparts_the_icon( 'heart-32' ); ?>
						<span class="th-indicator__counter">
							<?php echo esc_html( $wishlist->get_count() ); ?>
						</span>
					</span>
				</a>
			</div>
		<?php endif; ?>

		<?php if ( class_exists( 'WooCommerce' ) && $show_account_indicator ) : ?>
			<?php

			$indicator_classes = array( 'th-indicator' );

			if ( is_user_logged_in() ) {
				$indicator_classes[] = 'th-indicator--trigger--click';
			}

			?>
			<div class="<?php echo esc_attr( implode( ' ', $indicator_classes ) ); ?>">
				<?php

				global $current_user;

				$account_menu_items   = wc_get_account_menu_items();
				$account_logout_title = $account_menu_items['customer-logout'];

				unset( $account_menu_items['customer-logout'] );

				?>
				<a
					href="<?php echo esc_url( wc_get_account_endpoint_url( 'dashboard' ) ); ?>"
					class="th-indicator__button"
				>
					<span class="th-indicator__icon">
						<?php redparts_the_icon( 'person-32' ); ?>
					</span>
					<span class="th-indicator__title">
						<?php if ( is_user_logged_in() ) : ?>
							<?php echo esc_html( $current_user->user_email ); ?>
						<?php else : ?>
							<?php esc_html_e( 'Hello, Log In', 'redparts' ); ?>
						<?php endif; ?>
					</span>
					<span class="th-indicator__value"><?php esc_html_e( 'My Account', 'redparts' ); ?></span>
				</a>
				<?php if ( is_user_logged_in() ) : ?>
					<div class="th-indicator__content">
						<div class="th-account-menu">
							<a
								href="<?php echo esc_url( wc_get_account_endpoint_url( 'dashboard' ) ); ?>"
								class="th-account-menu__user"
							>
								<div class="th-account-menu__user-avatar">
									<?php echo get_avatar( $current_user->ID, 44 ); ?>
								</div>
								<div class="th-account-menu__user-info">
									<div class="th-account-menu__user-name">
										<?php echo esc_html( $current_user->display_name ); ?>
									</div>
									<div class="th-account-menu__user-email">
										<?php echo esc_html( $current_user->user_email ); ?>
									</div>
								</div>
							</a>

							<?php if ( 0 < count( $account_menu_items ) ) : ?>
								<div class="th-account-menu__divider"></div>
								<ul class="th-account-menu__links">
									<?php foreach ( $account_menu_items as $menu_key => $menu_title ) : ?>
										<li>
											<a href="<?php echo esc_url( wc_get_account_endpoint_url( $menu_key ) ); ?>">
												<?php echo esc_html( $menu_title ); ?>
											</a>
										</li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>

							<?php if ( ! empty( $account_logout_title ) ) : ?>
								<div class="th-account-menu__divider"></div>
								<ul class="th-account-menu__links">
									<li>
										<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'customer-logout' ) ); ?>">
											<?php echo esc_html( $account_logout_title ); ?>
										</a>
									</li>
								</ul>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( class_exists( 'WooCommerce' ) && $show_cart_indicator ) : ?>
			<?php
			$is_hidden = apply_filters( 'woocommerce_widget_cart_is_hidden', is_cart() || is_checkout() );

			$indicator_classes = array(
				'th-indicator',
				'th-indicator--cart',
			);

			if ( ! $is_hidden ) {
				$indicator_classes[] = 'th-indicator--trigger--click';
			}
			?>
			<div class="<?php echo esc_attr( implode( ' ', $indicator_classes ) ); ?>">
				<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="th-indicator__button">
					<span class="th-indicator__icon">
						<?php redparts_the_icon( 'cart-32' ); ?>
						<span class="th-indicator__counter">
							<?php echo esc_html( WC()->cart->get_cart_contents_count() ); ?>
						</span>
					</span>
					<span class="th-indicator__title">
						<?php echo esc_html__( 'Shopping Cart', 'redparts' ); ?>
					</span>
					<span class="th-indicator__value">
						<?php echo wp_kses( WC()->cart->get_cart_total(), 'redparts_cart_total' ); ?>
					</span>
				</a>

				<?php if ( ! $is_hidden ) : ?>
					<div class="th-indicator__content">
						<?php get_template_part( 'partials/desktop-header/dropcart' ); ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</div>
