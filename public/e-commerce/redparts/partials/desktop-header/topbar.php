<?php
/**
 * The topbar that will be displayed on desktop devices.
 *
 * @package RedParts
 * @since 1.0.0
 */

use RedParts\Header;
use RedParts\Settings;

defined( 'ABSPATH' ) || exit;

$compare = null;

if (
	class_exists( 'RedParts\Sputnik\Compare' ) &&
	redparts_sputnik_version_is( '>=', '1.5.0' ) &&
	RedParts\Sputnik\Compare::instance()->is_enabled()
) {
	$compare = RedParts\Sputnik\Compare::instance();
}

// phpcs:disable WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
global $WOOCS;

$woocs_installed = isset( $WOOCS );

if ( $woocs_installed ) {
	$currencies           = $WOOCS->get_currencies();
	$current_currency_key = $WOOCS->current_currency;
	$current_currency     = $currencies[ $current_currency_key ];
}
// phpcs:enable

$header_layout        = Header::instance()->get_layout();
$header_show_compare  = Settings::instance()->get_option( 'header_show_compare', 'yes' );
$header_show_currency = Settings::instance()->get_option( 'header_show_currency', 'yes' );

?>

<?php if ( 'classic' === $header_layout ) : ?>
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
	<div class="th-topbar__spring"></div>
<?php endif; ?>

<?php
wp_nav_menu(
	array(
		'theme_location'  => 'redparts-topbar-end',
		'menu_class'      => 'th-topbar th-topbar--end',
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

<?php if ( $compare && 'no' !== $header_show_compare ) : ?>
	<ul class="th-topbar th-topbar--end th-topbar--compare">
		<li class="menu-item">
			<a href="<?php echo esc_url( $compare->get_page_url() ); ?>">
				<?php esc_html_e( 'Compare:', 'redparts' ); ?>
				<span class="menu-item-value"><?php echo esc_html( $compare->count() ); ?></span>
			</a>
		</li>
	</ul>
<?php endif; ?>

<?php if ( $woocs_installed && 1 < count( $currencies ) && 'no' !== $header_show_currency ) : ?>
	<ul class="th-topbar th-topbar--end">
		<li class="menu-item menu-item-has-children">
			<a href="" data-th-prevent-default>
				<?php esc_html_e( 'Currency:', 'redparts' ); ?>
				<span class="menu-item-value"><?php echo esc_html( $current_currency['name'] ); ?></span>
				<?php redparts_the_icon( 'arrow-down-sm-7x5', 'th-menu-item-arrow' ); ?>
			</a>
			<ul class="sub-menu">
				<?php foreach ( $currencies as $currency_code => $currency ) : ?>
					<li class="menu-item">
						<a href="" data-th-currency-code="<?php echo esc_attr( $currency_code ); ?>">
							<?php echo esc_html( $currency['symbol'] ); ?>
							<?php echo esc_html( $currency['description'] ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</li>
	</ul>
<?php endif; ?>
