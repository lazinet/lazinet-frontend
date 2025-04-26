<?php
/**
 * This file contains the general layout for archive pages.
 *
 * @package RedParts
 * @since 1.0.0
 */

use RedParts\Blog;

defined( 'ABSPATH' ) || exit;

$layout = Blog::instance()->get_layout();

$sidebar_name     = apply_filters( 'redparts_blog_sidebar_name', 'redparts-blog' );
$sidebar_position = 'none';

if ( is_active_sidebar( $sidebar_name ) ) {
	$sidebar_position = Blog::instance()->get_sidebar_position();
}

$post_card_layout_args = array();
$post_card_layout_map  = array(
	'classic' => 'classic',
	'list'    => 'list',
	'grid'    => 'grid-sm',
);

if ( isset( $post_card_layout_map[ $layout ] ) ) {
	$post_card_layout_args['layout'] = $post_card_layout_map[ $layout ];
}

?>
<div class="th-posts-view th-posts-view--layout--<?php echo esc_attr( $layout ); ?>">
	<div class="th-posts-view__list">
		<?php while ( have_posts() ) : ?>
			<div class="th-posts-view__item">
				<?php
				the_post();

				/*
				 * Include the Post-Type-specific template for the content.
				 * If you want to override this in a child theme, then include a file
				 * called content-___.php (where ___ is the Post Type name) and that will be used instead.
				 */
				get_template_part( 'partials/blog/post-card', get_post_type(), $post_card_layout_args );

				?>
			</div>
		<?php endwhile; ?>
	</div>
	<?php
	echo '<div class="th-posts-view__pagination">';

	$prev_icon = redparts_get_icon( 'arrow-rounded-left-7x11' );
	$next_icon = redparts_get_icon( 'arrow-rounded-right-7x11' );

	the_posts_pagination(
		array(
			'prev_text' => '<span class="sr-only">' . esc_html__( 'Previous', 'redparts' ) . '</span>' . $prev_icon,
			'next_text' => '<span class="sr-only">' . esc_html__( 'Next', 'redparts' ) . '</span>' . $next_icon,
		)
	);

	echo '</div>';
	?>
</div>
