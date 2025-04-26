<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package RedParts
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
	<div class="th-block-space th-block-space--layout--spaceship-ledge-height"></div>

	<div class="th-block">
		<div class="th-container">
			<div class="th-not-found">
				<div class="th-not-found__404">
					<?php esc_html_e( 'Oops! Error 404', 'redparts' ); ?>
				</div>

				<div class="th-not-found__content">
					<h1 class="th-not-found__title"><?php esc_html_e( 'Page Not Found', 'redparts' ); ?></h1>

					<p class="th-not-found__text">
						<?php
						echo wp_kses(
							__( 'We can\'t seem to find the page you\'re looking for.<br>Try to use the search.', 'redparts' ), // SKIP-ESC.
							'redparts_text'
						);
						?>
					</p>

					<form class="th-not-found__search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
						<label class="screen-reader-text" for="search-form-404"><?php _x( 'Search for:', 'label', 'redparts' ); ?></label>
						<input
							name="s"
							id="search-form-404"
							value="<?php echo get_search_query(); ?>"
							type="text"
							class="th-not-found__search-input form-control"
							placeholder="<?php echo esc_attr__( 'Search products&hellip;', 'redparts' ); ?>">
						<button
							type="submit"
							class="th-not-found__search-button th-button th-button--size--normal th-button--style--primary"
						>
							<?php echo esc_attr_x( 'Search', 'submit button', 'redparts' ); ?>
						</button>
						<input type="hidden" name="post_type" value="product">
					</form>

					<p class="th-not-found__text">
						<?php esc_html_e( 'Or go to the home page to start over.', 'redparts' ); ?>
					</p>

					<a
						class="th-button th-button--size--small th-button--style--secondary"
						href="<?php echo esc_url( home_url() ); ?>"
					>
						<?php esc_html_e( 'Go To Home Page', 'redparts' ); ?>
					</a>
				</div>
			</div>
		</div>
	</div>

	<div class="th-block-space th-block-space--layout--before-footer"></div>
<?php
get_footer();
