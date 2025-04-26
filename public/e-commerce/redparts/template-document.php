<?php
/**
 * Template Name: Document
 * Template Post Type: post, page
 *
 * @package Stroyka
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

get_header();

redparts_the_page_header(
	array(
		'show_title'      => false,
		'show_breadcrumb' => false,
	)
);

?>
	<div class="th-block-space th-block-space--layout--spaceship-ledge-height"></div>

	<div class="th-block">
		<div class="th-container">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'th-document' ); ?>>
					<div class="th-document__header">
						<h1 class="th-document__title"><?php the_title(); ?></h1>
						<?php
						$subtitle = get_post_meta( get_post()->ID, 'redparts_subtitle', true );

						if ( $subtitle ) :
							?>
							<div class="th-document__subtitle"><?php echo esc_html( $subtitle ); ?></div>
							<?php
						endif;
						?>
					</div>
					<div class="th-document__content">
						<div class="th-typography">
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
					</div>
				</article>
				<?php
			endwhile;
			?>
		</div>
	</div>

	<div class="th-block-space th-block-space--layout--before-footer"></div>
<?php
get_footer();
