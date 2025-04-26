<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package RedParts
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<?php if ( have_comments() ) : ?>
	<section id="comments" class="th-post-view__card">
		<h4 class="th-post-view__card-title">
			<?php
				// translators: %s number of comments.
				echo esc_html( sprintf( _n( 'Comment (%s)', 'Comments (%s)', get_comments_number(), 'redparts' ), get_comments_number() ) ); // SKIP-ESC.
			?>
		</h4>
		<div class="th-post-view__card-body">
			<ol class="comment-list">
				<?php
				wp_list_comments(
					array(
						'style'       => 'ol',
						'short_ping'  => false,
						'avatar_size' => 38,
					)
				);
				?>
			</ol><!-- .comment-list -->

			<?php

			$prev_icon = redparts_get_icon( 'arrow-rounded-left-7x11' );
			$next_icon = redparts_get_icon( 'arrow-rounded-right-7x11' );

			$prev = '<span class="sr-only">' . esc_html__( 'Previous', 'redparts' ) . '</span>' . $prev_icon;
			$next = '<span class="sr-only">' . esc_html__( 'Next', 'redparts' ) . '</span>' . $next_icon;

			the_comments_pagination(
				array(
					'prev_text' => $prev,
					'next_text' => $next,
				)
			);
			?>
		</div>
	</section>
<?php endif; ?>

<?php

comment_form(
	array(
		'id_form'            => 'comment-form',
		'class_container'    => 'comment-respond th-post-view__card',
		'class_form'         => 'comment-form th-post-view__card-body',
		'title_reply'        => esc_html__( 'Write A Comment', 'redparts' ),
		'title_reply_before' => '<h4 id="reply-title" class="comment-reply-title th-post-view__card-title">',
		'title_reply_after'  => '</h4>',
		'cancel_reply_link'  => esc_html__( 'Cancel', 'redparts' ),
	)
);

if ( have_comments() && ! comments_open() ) {
	?>
	<div class="th-post-view__alert th-alert th-alert--style--info"><?php esc_html_e( 'Comments are closed.', 'redparts' ); ?></div>
	<?php
}

?>
