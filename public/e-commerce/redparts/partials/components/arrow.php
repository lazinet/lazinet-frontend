<?php
/**
 * Template for displaying arrow.
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
		'direction' => '',
		'classes'   => '',
	)
);

$direction = 'next' === $args['direction'] ? 'next' : 'prev';

$classes = array(
	'th-arrow',
	'th-arrow--' . $direction,
	$args['classes'],
);

?>
<div class="<?php redparts_the_classes( ...$classes ); ?>">
	<button class="th-arrow__button" type="button">
		<?php if ( 'prev' === $direction ) : ?>
			<?php redparts_the_icon( 'arrow-rounded-left-7x11' ); ?>
		<?php else : ?>
			<?php redparts_the_icon( 'arrow-rounded-right-7x11' ); ?>
		<?php endif; ?>
	</button>
</div>
