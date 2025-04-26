<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
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

get_header();

if ( is_home() && is_front_page() ) {
	?>
	<div class="th-block-space th-block-space--layout--after-header"></div>
	<?php
}

redparts_the_page_header();

$layout_classes = array(
	'th-layout',
	'th-layout--page--blog',
	'th-layout--variant--blog-' . $layout,
);

if ( 'none' !== $sidebar_position ) {
	$layout_classes[] = 'th-layout--has-sidebar';
}

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
					<?php
					if ( have_posts() ) :
						get_template_part( 'partials/blog/content-archive' );
					else :
						get_template_part( 'partials/content-none' );
					endif;
					?>
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

get_footer();
