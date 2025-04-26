<?php
/**
 * The template for displaying content of all pages.
 *
 * @package RedParts
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$show_header = true;

if ( class_exists( 'WooCommerce' ) && is_account_page() && ! is_user_logged_in() ) {
	$show_header = false;
}

?>
<?php if ( $show_header ) : ?>
	<?php redparts_the_page_header(); ?>
<?php else : ?>
	<div class="th-block-space th-block-space--layout--after-header"></div>
<?php endif; ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if ( ! post_password_required() && ! is_attachment() && has_post_thumbnail() ) : ?>
		<div class="post-thumbnail">
			<?php the_post_thumbnail(); ?>
		</div>
	<?php endif; ?>

	<div class="entry-content th-container">
		<div class="th-page-content">
			<?php the_content(); ?>
		</div>
		<?php

		$pagination_before  = '<div class="th-page-pagination">';
		$pagination_before .= '<div class="th-page-pagination__title">' . esc_html__( 'Pages:', 'redparts' ) . '</div>';
		$pagination_before .= '<div class="th-page-pagination__list">';

		wp_link_pages(
			array(
				'before' => $pagination_before,
				'after'  => '</div></div>',
			)
		);
		?>
	</div><!-- .entry-content -->
</article>

<?php // If comments are open or we have at least one comment, load up the comment template. ?>
<?php if ( comments_open() || get_comments_number() ) : ?>
	<div class="th-container th-page-comments">
		<?php comments_template(); ?>
	</div>
<?php endif; ?>

<div class="th-block-space th-block-space--layout--before-footer"></div>
