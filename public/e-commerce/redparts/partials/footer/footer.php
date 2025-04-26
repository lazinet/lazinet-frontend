<?php
/**
 * The Template for displaying site footer.
 *
 * @package RedParts
 * @since 1.0.0
 */

use RedParts\Settings;

defined( 'ABSPATH' ) || exit;

$sidebar_name = apply_filters( 'redparts_footer_sidebar_name', 'redparts-footer' );

$settings  = Settings::instance();
$copyright = $settings->get_option( 'footer_copyright' );
$payments  = $settings->get_option( 'footer_payments' );

?>
<div class="th-site-footer">
	<?php redparts_the_decor( 'bottom', 'th-site-footer__decor' ); ?>

	<?php if ( is_active_sidebar( $sidebar_name ) ) : ?>
		<div class="th-site-footer__widgets">
			<div class="th-container">
				<div class="th-row">
					<?php dynamic_sidebar( $sidebar_name ); ?>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $copyright ) || ! empty( $payments['id'] ) ) : ?>
		<div class="th-site-footer__bottom">
			<div class="th-container">
				<div class="th-site-footer__bottom-row">
					<?php if ( ! empty( $copyright ) ) : ?>
						<div class="th-site-footer__copyright">
							<?php echo wp_kses( $copyright, 'redparts_copyright' ); ?>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( $payments['id'] ) ) : ?>
						<div class="th-site-footer__payments">
							<?php echo wp_get_attachment_image( $payments['id'], 'full' ); ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	<?php endif; ?>
</div>
