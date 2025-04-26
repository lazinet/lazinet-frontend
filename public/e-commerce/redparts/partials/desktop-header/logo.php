<?php
/**
 * The logo that will be displayed on desktop devices.
 *
 * @package RedParts
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$name        = get_bloginfo( 'name' );
$description = get_bloginfo( 'description' );

?>
<div class="th-logo th-logo--desktop">
	<?php if ( ! empty( $description ) || is_customize_preview() ) : ?>
		<div class="th-logo__slogan site-description">
			<?php bloginfo( 'description' ); ?>
		</div>
	<?php endif; ?>
	<?php if ( has_custom_logo() || is_customize_preview() ) : ?>
		<div class="th-logo__image site-logo">
			<?php the_custom_logo(); ?>
		</div>
	<?php endif; ?>
	<?php if ( ( ! has_custom_logo() && ! empty( $name ) ) || is_customize_preview() ) : ?>
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
