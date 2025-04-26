<?php
/**
 * The template for displaying single posts.
 *
 * @package RedParts
 * @since 1.0.0
 */

use RedParts\Sputnik\Share_Buttons;

defined( 'ABSPATH' ) || exit;

?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'th-post-view__card th-post' ); ?>>
	<?php
	$content_classes = array(
		'entry-content',
		'th-post__body',
	);

	if ( '' !== get_post()->post_content ) {
		?>
		<div class="<?php echo esc_attr( implode( ' ', $content_classes ) ); ?>">
			<?php
			the_content(
				sprintf(
					wp_kses(
						/* translators: %s: Name of current post. Only visible to screen readers */
						__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'redparts' ), // SKIP-ESC.
						'redparts_text'
					),
					get_the_title()
				)
			);
			?>
		</div>
		<?php
	} else {
		?>
		<div class="<?php echo esc_attr( implode( ' ', $content_classes ) ); ?>"></div>
		<?php
	}

	$pagination_before  = '<div class="th-post__pagination">';
	$pagination_before .= '<div class="th-post__pagination-title">' . esc_html__( 'Pages:', 'redparts' ) . '</div>';
	$pagination_before .= '<div class="th-post__pagination-list">';

	wp_link_pages(
		array(
			'before' => $pagination_before,
			'after'  => '</div></div>',
		)
	);
	?>

	<?php if ( get_the_tags() || class_exists( '\RedParts\Sputnik\Share_Buttons' ) ) : ?>
		<div class="th-post__footer">
			<?php if ( get_the_tags() ) : ?>
				<div class="th-post__tags th-tags th-tags--sm">
					<span class="screen-reader-text"><?php esc_html_e( 'Tags:', 'redparts' ); ?></span>
					<div class="th-tags__list">
						<?php the_tags( '', '' ); ?>
					</div>
				</div>
			<?php endif; ?>

			<?php
			if ( class_exists( '\RedParts\Sputnik\Share_Buttons' ) ) {
				Share_Buttons::instance()->render( 'th-post__share-links' );
			}
			?>
		</div>
	<?php endif; ?>

	<div class="th-post__author">
		<?php $author_id = get_the_author_meta( 'ID' ); ?>
		<div class="th-post__author-avatar">
			<a href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>">
				<?php echo get_avatar( $author_id ); ?>
			</a>
		</div>
		<div class="th-post__author-info">
			<div class="th-post__author-name">
				<a href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>">
					<?php the_author(); ?>
				</a>
			</div>
			<div class="th-post__author-about">
				<?php echo esc_html( get_the_author_meta( 'user_description' ) ); ?>
			</div>
		</div>
	</div>
</article>
