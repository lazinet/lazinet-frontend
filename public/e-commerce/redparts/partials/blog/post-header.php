<?php
/**
 * The template for displaying post header.
 *
 * @package RedParts
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$classes   = array( 'th-post-header' );
$has_image = ! post_password_required() && ! is_attachment() && has_post_thumbnail();

if ( $has_image ) {
	$classes[] = 'th-post-header--has-image';
}

?>
<div class="<?php redparts_the_classes( ...$classes ); ?>">
	<?php if ( $has_image ) : ?>
		<div class="th-post-header__image">
			<?php the_post_thumbnail(); ?>
		</div>
	<?php endif; ?>

	<div class="th-post-header__body entry-header">
		<div class="th-post-header__categories">
			<div class="th-post-header__categories-list">
				<span class="screen-reader-text"><?php esc_html_e( 'Posted in:', 'redparts' ); ?></span>
				<?php the_category( ' ' ); ?>
			</div>
		</div>
		<h1 class="th-post-header__title">
			<?php the_title(); ?>
		</h1>
		<div class="th-post-header__meta">
			<ul class="th-post-header__meta-list">
				<li class="th-post-header__meta-item">
					<?php
					$author_url = get_author_posts_url( get_the_author_meta( 'ID' ) );
					$byline     = '<a class="url fn n" href="' . esc_url( $author_url ) . '">' . esc_html( get_the_author() ) . '</a>';
					$byline     = sprintf(
						/* translators: %s: post author. */
						esc_html_x( 'By %s', 'post author', 'redparts' ),
						'<span class="author vcard">' . $byline . '</span>'
					);

					echo wp_kses( $byline, 'redparts_post_author' );
					?>
				</li>
				<li class="th-post-header__meta-item">
					<a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark">
						<time class="entry-date published" datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>">
							<?php echo esc_html( get_the_date() ); ?>
						</time>
					</a>
				</li>
				<?php

				if ( 0 < get_comments_number() ) {
					echo '<li class="th-post-header__meta-item"><a href="#comments">';
					/* translators: %s: number of comments. */
					echo esc_html( sprintf( _n( '%s Comment', '%s Comments', get_comments_number(), 'redparts' ), get_comments_number() ) ); // SKIP-ESC.
					echo '</a></li>';
				} elseif ( comments_open() ) {
					echo '<li class="th-post-header__meta-item"><a href="#comment-form">';
					echo esc_html__( 'Write A Comment', 'redparts' );
					echo '</a></li>';
				}

				?>
			</ul>
		</div>
	</div>

	<?php redparts_the_decor( 'bottom', 'th-post-header__decor' ); ?>
</div>
