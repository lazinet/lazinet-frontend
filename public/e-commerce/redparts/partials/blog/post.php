<?php
/**
 * The template for displaying post content and comments.
 *
 * @package RedParts
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

?>
<div class="th-post-view">
	<div class="th-post-view__body">
		<?php
		get_template_part( 'partials/blog/content-single', get_post_type() );

		// If comments are open or we have at least one comment, load up the comment template.
		if ( comments_open() || get_comments_number() ) :
			comments_template();
		endif;
		?>
	</div>
</div>


