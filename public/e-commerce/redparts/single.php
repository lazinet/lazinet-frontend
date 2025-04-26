<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package RedParts
 * @since 1.0.0
 */

use RedParts\Post;

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	/** WordPress post object. */
	global $post;

	$sidebar_name     = apply_filters( 'redparts_blog_sidebar_name', 'redparts-blog' );
	$sidebar_position = 'none';

	if ( is_active_sidebar( $sidebar_name ) ) {
		$sidebar_position = Post::instance()->get_sidebar_position();
	}

	$layout_classes = array(
		'th-layout',
		'th-layout--page--post',
	);

	if ( 'none' !== $sidebar_position ) {
		$layout_classes[] = 'th-layout--has-sidebar';
	}

	get_template_part( 'partials/blog/post-header' );

	?>
	<div class="<?php redparts_the_classes( ...$layout_classes ); ?>">
		<div class="th-container">
			<div class="th-layout__body">
				<?php if ( 'start' === $sidebar_position ) : ?>
					<div class="th-layout__item th-layout__item--sidebar">
						<?php get_sidebar( $sidebar_name ); ?>
					</div>
				<?php endif; ?>
				<div class="th-layout__item th-layout__item--content">
					<?php get_template_part( 'partials/blog/post' ); ?>
				</div>
				<?php if ( 'end' === $sidebar_position ) : ?>
					<div class="th-layout__item th-layout__item--sidebar">
						<?php get_sidebar( $sidebar_name ); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<div class="th-block-space th-block-space--layout--before-footer"></div>
	<?php

endwhile; // End of the loop.

get_footer();
