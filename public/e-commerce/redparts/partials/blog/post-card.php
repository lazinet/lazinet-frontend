<?php
/**
 * The template part for displaying post cards.
 *
 * @package RedParts
 * @since 1.0.0
 * @noinspection DuplicatedCode
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $args ) ) {
	$args = array();
}

$args = wp_parse_args(
	$args,
	array(
		'layout' => '',
	)
);

if ( in_array( $args['layout'], array( 'classic', 'list', 'grid-sm' ), true ) ) {
	$layout = $args['layout'];
}

$post_card_classes = array( 'th-post-card' );

if ( in_array( $args['layout'], array( 'classic', 'list', 'grid-sm' ), true ) ) {
	$post_card_classes[] = 'th-post-card--layout--' . $args['layout'];
}

$post_card_classes = apply_filters( 'redparts_post_card_classes', $post_card_classes );

?>
<article
	id="post-<?php the_ID(); ?>"
	<?php post_class( implode( ' ', $post_card_classes ) ); ?>
>
	<?php if ( ! post_password_required() && ! is_attachment() && has_post_thumbnail() ) : ?>
		<div class="th-post-card__image">
			<a href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
				<?php
				the_post_thumbnail(
					'post-thumbnail',
					array(
						'alt' => the_title_attribute( array( 'echo' => false ) ),
					)
				);
				?>
			</a>
		</div>
	<?php endif; ?>
	<div class="th-post-card__content">
		<?php
		if ( 'post' === get_post_type() ) {
			/* translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list( esc_html__( ', ', 'redparts' ) );

			if ( $categories_list ) {
				?>
				<div class="th-post-card__category">
					<?php echo wp_kses( $categories_list, 'redparts_categories_list' ); ?>
				</div>
				<?php
			}
		}
		?>
		<div class="th-post-card__title">
			<h2>
				<a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark"><?php the_title(); ?></a>
			</h2>
		</div>
		<div class="th-post-card__meta">
			<div class="th-post-card__meta-list">
				<?php $author_id = get_the_author_meta( 'ID' ); ?>

				<a
					href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>"
					class="th-post-card__meta-item th-post-card__meta-item--author"
				>
					<div class="th-post-card__meta-icon">
						<?php echo get_avatar( $author_id, 18 ); ?>
					</div>
					<div class="th-post-card__meta-value">
						<?php echo esc_html( get_the_author() ); ?>
					</div>
				</a>

				<a
					href="<?php echo esc_url( get_permalink() ); ?>"
					class="th-post-card__meta-item th-post-card__meta-item--date"
				>
					<div class="th-post-card__meta-icon">
						<?php redparts_the_icon( 'calendar-16' ); ?>
					</div>
					<time
						class="th-post-card__meta-value published updated"
						datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"
					>
						<?php echo esc_html( get_the_date() ); ?>
					</time>
				</a>

				<a
					href="<?php echo esc_url( get_comments_link() ); ?>"
					class="th-post-card__meta-item th-post-card__meta-item--comments"
				>
					<div class="th-post-card__meta-icon">
						<?php redparts_the_icon( 'comment-16' ); ?>
					</div>
					<div class="th-post-card__meta-value">
						<?php
						echo esc_html(
							sprintf(
								/* translators: %s Comments count. */
								_n( '%s comment', '%s comments', get_comments_number(), 'redparts' ), // SKIP-ESC.
								get_comments_number()
							)
						);
						?>
					</div>
				</a>
			</div>
		</div>
		<?php if ( '' !== get_post()->post_content ) : ?>
			<div class="th-post-card__excerpt">
				<?php the_excerpt(); ?>
			</div>
		<?php endif; ?>
		<div class="th-post-card__more">
			<a
				href="<?php echo esc_url( get_permalink() ); ?>"
				rel="bookmark"
				class="th-button th-button--style--primary th-button--size--small"
			>
				<?php esc_html_e( 'Read More', 'redparts' ); ?>

				<?php redparts_the_icon( 'arrow-rounded-right-6x9' ); ?>
			</a>
		</div>
	</div>
</article>
