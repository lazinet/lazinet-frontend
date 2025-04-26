<?php
/**
 * Template for block header.
 *
 * @package RedParts
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $args ) ) {
	$args = array();
}

$args = wp_parse_args(
	$args,
	array(
		'title'   => '',
		'classes' => '',
		'arrows'  => false,
		'groups'  => array(),
	)
);

if ( empty( $args['title'] ) ) {
	return;
}

?>
<div class="<?php redparts_the_classes( 'th-section-header', $args['classes'] ); ?>">
	<div class="th-section-header__body">
		<h2 class="th-section-header__title">
			<?php echo esc_html( $args['title'] ); ?>
		</h2>

		<div class="th-section-header__spring"></div>

		<?php if ( $args['arrows'] ) : ?>
			<div class="th-section-header__arrows">
				<?php
				redparts_the_template(
					'partials/components/arrow',
					array(
						'direction' => 'prev',
						'classes'   => 'th-section-header__arrow th-section-header__arrow--prev',
					)
				);
				?>
				<?php
				redparts_the_template(
					'partials/components/arrow',
					array(
						'direction' => 'next',
						'classes'   => 'th-section-header__arrow th-section-header__arrow--next',
					)
				);
				?>
			</div>
		<?php endif; ?>

		<div class="th-section-header__divider"></div>
	</div>
</div>
