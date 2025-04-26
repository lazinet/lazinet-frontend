<?php
/**
 * The Template for displaying dropcart.
 *
 * @package RedParts
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

?>
<!-- .th-dropcart -->
<div class="th-dropcart">
	<?php

	the_widget(
		'WC_Widget_Cart',
		array(
			'title' => '',
		),
		array(
			'before_widget' => '',
			'after_widget'  => '',
			'before_title'  => '',
			'after_title'   => '',
		)
	);

	?>
</div>
<!-- .th-dropcart / end -->
