<?php
/**
 * The logo that will be displayed on desktop devices.
 *
 * @package RedParts
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$header      = RedParts\Header::instance();
$has_logo    = $header->has_mobile_logo() || has_custom_logo();
$name        = get_bloginfo( 'name' );
$description = get_bloginfo( 'description' );

$logo_classes = array( 'th-mobile-header__logo', 'th-logo', 'th-logo--mobile' );

if ( $header->has_mobile_logo() ) {
	$logo_classes[] = 'th-logo--has-mobile-image';
}

?>
<div class="<?php redparts_the_classes( ...$logo_classes ); ?>">
	<?php if ( $header->has_mobile_logo() || is_customize_preview() ) : ?>
		<div class="th-logo__image th-logo__image--mobile">
			<?php $header->the_mobile_logo(); ?>
		</div>
	<?php endif; ?>
	<?php if ( has_custom_logo() || is_customize_preview() ) : ?>
		<div class="th-logo__image th-logo__image--desktop site-logo">
			<?php the_custom_logo(); ?>
		</div>
	<?php endif; ?>
	<?php if ( ( ! $has_logo && ! empty( $name ) ) || is_customize_preview() ) : ?>
		<?php if ( is_front_page() && is_home() ) : ?>
			<h1 class="th-logo__title site-title">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
					<?php bloginfo( 'name' ); ?>
				</a>
			</h1>
		<?php else : ?>
			<div class="th-logo__title site-title">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
					<?php bloginfo( 'name' ); ?>
				</a>
			</div>
		<?php endif; ?>
	<?php endif; ?>
</div>
