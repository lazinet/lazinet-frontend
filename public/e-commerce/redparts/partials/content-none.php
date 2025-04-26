<?php
/**
 * Template part for displaying a message that posts cannot be found.
 *
 * @package RedParts
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

?>
<div class="th-no-results">
	<div class="th-no-results__body">
		<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>
			<div class="th-no-results__message">
				<?php
				/** Redundant inspection. @noinspection HtmlUnknownTarget */
				/* translators: 1: link to WP admin new post page. */
				$text = esc_html__( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'redparts' );

				printf( wp_kses( $text, 'redparts_text' ), esc_url( admin_url( 'post-new.php' ) ) );
				?>
			</div>
		<?php elseif ( is_search() ) : ?>
			<div class="th-no-results__message">
				<?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'redparts' ); ?>
			</div>
			<div class="th-no-results__actions">
				<?php
				get_search_form(
					array(
						'echo'              => true,
						'redparts_location' => 'empty',
					)
				);
				?>
			</div>
		<?php else : ?>
			<div class="th-no-results__message">
				<?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'redparts' ); ?>
			</div>
			<div class="th-no-results__actions">
				<?php
				get_search_form(
					array(
						'echo'              => true,
						'redparts_location' => 'empty',
					)
				);
				?>
			</div>
		<?php endif; ?>
	</div>
</div>
