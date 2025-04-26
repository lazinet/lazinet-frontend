<?php
/**
 * This file contains code related to the shop page only.
 *
 * @package RedParts
 * @since 1.0.0
 */

namespace RedParts;

use RedParts\Sputnik\Vehicles;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'RedParts\Shop' ) ) {
	/**
	 * Class Shop
	 */
	class Shop extends Singleton {
		const DEFAULT_LAYOUT = 'grid-4-sidebar';

		const VALID_LAYOUTS = array( 'grid-3-sidebar', 'grid-4-sidebar', 'grid-4-full', 'grid-5-full', 'grid-6-full' );

		const DEFAULT_SIDEBAR_POSITION = 'start';

		const VALID_SIDEBAR_POSITIONS = array( 'start', 'end' );

		const DEFAULT_VIEW_MODE = 'grid';

		const VALID_VIEW_MODES = array( 'grid', 'grid-with-features', 'list', 'table' );

		const DEFAULT_PRODUCTS_PER_PAGE = 12;

		const DEFAULT_PRODUCTS_PER_PAGE_VARIATIONS = array( 9, 12, 18, 24 );

		/**
		 * Initialization.
		 */
		public function init() {
			// Remove actions.
			remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination' );
			remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );

			add_action( 'woocommerce_before_shop_loop', array( $this, 'view_options' ), 15 );
			add_action( 'woocommerce_before_shop_loop', array( $this, 'view_options_legend' ), 20 );
			add_action( 'woocommerce_before_shop_loop', array( $this, 'view_options_spring' ), 25 );
			add_action( 'woocommerce_before_shop_loop', array( $this, 'view_options_per_page' ), 40 );
			add_action( 'woocommerce_before_shop_loop', array( $this, 'view_options_close_tag_before_loop' ), 100 );

			add_action( 'woocommerce_after_shop_loop', array( $this, 'pagination' ), 10 );
			add_action( 'woocommerce_after_shop_loop', array( $this, 'view_options_close_tag_after_loop' ), 20 );

			// Filters.
			add_filter( 'woocommerce_pagination_args', array( $this, 'pagination_args' ), 10 );
			add_filter( 'document_title_parts', array( $this, 'vehicle_document_title' ) );
			add_filter( 'woocommerce_get_breadcrumb', array( $this, 'vehicle_breadcrumb' ) );

			// Remove subcategories from the loop. Because we'll show it before the loop, but not inside.
			// I could just remove "woocommerce_maybe_show_product_subcategories" filter, but:
			//
			// https://help.author.envato.com/hc/en-us/articles/360000480723
			//
			// " Developers must never remove WooCommerce template hooks from modified core templates.
			// " Removing hooks will likely lead to issues with extensions, as well as core WooCommerce functionality.
			add_filter( 'woocommerce_before_output_product_categories', array( $this, 'categories_start_comment' ) );
			add_filter( 'woocommerce_after_output_product_categories', array( $this, 'categories_end_comment' ) );
			add_filter( 'woocommerce_product_loop_start', array( $this, 'remove_subcategories' ), 20 );
		}

		/**
		 * Returns shop sidebar name.
		 *
		 * @return string
		 */
		public function get_sidebar_name(): string {
			return apply_filters( 'redparts_shop_sidebar_name', 'redparts-shop' );
		}

		/**
		 * Returns shop layout.
		 *
		 * @return string
		 */
		public function get_layout(): string {
			$result   = self::DEFAULT_LAYOUT;
			$settings = Settings::instance()->get();

			if ( ! empty( $settings['shop_layout'] ) && in_array( $settings['shop_layout'], self::VALID_LAYOUTS, true ) ) {
				$result = $settings['shop_layout'];
			}

			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			if ( isset( $_GET['redparts_shop_layout'] ) ) {
				$get_layout = sanitize_key( wp_unslash( $_GET['redparts_shop_layout'] ) );

				if ( in_array( $get_layout, self::VALID_LAYOUTS, true ) ) {
					$result = $get_layout;
				}
			}
			// phpcs:enable

			if ( ! is_active_sidebar( $this->get_sidebar_name() ) ) {
				if ( 'grid-3-sidebar' === $result ) {
					$result = 'grid-4-full';
				}
				if ( 'grid-4-sidebar' === $result ) {
					$result = 'grid-5-full';
				}
			}

			return apply_filters( 'redparts_get_shop_layout', $result );
		}

		/**
		 * Returns sidebar position.
		 *
		 * @return string
		 */
		public function get_sidebar_position(): string {
			$result   = self::DEFAULT_SIDEBAR_POSITION;
			$settings = Settings::instance()->get();

			if ( ! empty( $settings['shop_sidebar_position'] ) && in_array( $settings['shop_sidebar_position'], self::VALID_SIDEBAR_POSITIONS, true ) ) {
				$result = $settings['shop_sidebar_position'];
			}

			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			if ( isset( $_GET['redparts_shop_sidebar_position'] ) ) {
				$get_sidebar_position = sanitize_key( wp_unslash( $_GET['redparts_shop_sidebar_position'] ) );

				if ( in_array( $get_sidebar_position, self::VALID_SIDEBAR_POSITIONS, true ) ) {
					$result = $get_sidebar_position;
				}
			}
			// phpcs:enable

			return apply_filters( 'redparts_get_shop_sidebar_position', $result );
		}

		/**
		 * Returns view mode.
		 *
		 * @return string
		 */
		public function get_view_mode(): string {
			$result   = self::DEFAULT_VIEW_MODE;
			$settings = Settings::instance()->get();

			if ( ! empty( $settings['shop_view_mode'] ) && in_array( $settings['shop_view_mode'], self::VALID_VIEW_MODES, true ) ) {
				$result = $settings['shop_view_mode'];
			}

			if ( isset( $_COOKIE['redparts_shop_view_mode'] ) ) {
				$get_view_mode = sanitize_key( $_COOKIE['redparts_shop_view_mode'] );

				if ( in_array( $get_view_mode, self::VALID_VIEW_MODES, true ) ) {
					$result = $get_view_mode;
				}
			}

			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			if ( isset( $_GET['redparts_shop_view_mode'] ) ) {
				$get_view_mode = sanitize_key( wp_unslash( $_GET['redparts_shop_view_mode'] ) );

				if ( in_array( $get_view_mode, self::VALID_VIEW_MODES, true ) ) {
					$result = $get_view_mode;
				}
			}
			// phpcs:enable

			return apply_filters( 'redparts_get_shop_view_mode', $result );
		}

		/**
		 * Returns array of products per page variations.
		 *
		 * @return int[]
		 */
		public function get_products_per_page_variations(): array {
			$settings = Settings::instance()->get();
			$result   = self::DEFAULT_PRODUCTS_PER_PAGE_VARIATIONS;

			if ( ! empty( $settings['products_per_page_variations'] ) ) {
				$result = explode( ',', $settings['products_per_page_variations'] );
				$result = array_filter(
					$result,
					function( $item ) {
						return ! empty( trim( $item ) );
					}
				);
				$result = array_map( 'absint', $result );

				sort( $result );
			}

			return apply_filters( 'redparts_shop_get_products_per_page_variations', $result );
		}

		/**
		 * Returns the number of products per page to display.
		 *
		 * @return int
		 */
		public function get_products_per_page(): int {
			$settings   = Settings::instance()->get();
			$variations = $this->get_products_per_page_variations();
			$result     = self::DEFAULT_PRODUCTS_PER_PAGE;

			if ( ! empty( $settings['products_per_page'] ) ) {
				$result = absint( $settings['products_per_page'] );
			}

			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			if ( isset( $_GET['per_page'] ) ) {
				$per_page = absint( wp_unslash( $_GET['per_page'] ) );

				if ( in_array( $per_page, $variations, true ) ) {
					$result = $per_page;
				}
			}
			// phpcs:enable

			return apply_filters( 'redparts_shop_get_products_per_page', $result );
		}

		/**
		 * Outputs the shop sidebar.
		 *
		 * @param bool $always_offcanvas Set true if the sidebar should always be offcanvas.
		 */
		public function the_sidebar( $always_offcanvas = false ) {
			$classes   = array( 'th-sidebar' );
			$classes[] = $always_offcanvas ? 'th-sidebar--offcanvas--always' : 'th-sidebar--offcanvas--mobile';

			?>
			<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
				<div class="th-sidebar__backdrop"></div>
				<div class="th-sidebar__body">
					<div class="th-sidebar__header">
						<div class="th-sidebar__title"><?php esc_html_e( 'Filters', 'redparts' ); ?></div>
						<button class="th-sidebar__close" type="button">
							<?php redparts_the_icon( 'cross-20' ); ?>
						</button>
					</div>

					<div class="th-sidebar__content">
						<?php
						/**
						 * Hook: woocommerce_sidebar.
						 *
						 * @hooked woocommerce_get_sidebar - 10
						 */
						do_action( 'woocommerce_sidebar' );
						?>
					</div>
				</div>
			</div>
			<?php
		}

		/**
		 * Outputs view options before shop loop.
		 */
		public function view_options() {
			$filters_count = WooCommerce::get_filters_count();
			$layout        = $this->get_layout();
			$view_mode     = $this->get_view_mode();
			$classes       = '-full' === substr( $layout, -5 ) ? 'th-view-options--offcanvas--always' : 'th-view-options--offcanvas--mobile';

			$buttons = array(
				'grid'               => array(
					'title'         => esc_html__( 'Grid', 'redparts' ),
					'layout'        => 'grid',
					'with_features' => 'false',
					'icon'          => 'layout-grid-16',
				),
				'grid-with-features' => array(
					'title'         => esc_html__( 'Grid', 'redparts' ),
					'layout'        => 'grid',
					'with_features' => 'true',
					'icon'          => 'layout-grid-with-details-16',
				),
				'list'               => array(
					'title'         => esc_html__( 'List', 'redparts' ),
					'layout'        => 'list',
					'with_features' => 'false',
					'icon'          => 'layout-list-16',
				),
				'table'              => array(
					'title'         => esc_html__( 'Table', 'redparts' ),
					'layout'        => 'table',
					'with_features' => 'false',
					'icon'          => 'layout-table-16',
				),
			);

			ob_start();

			?>
			<div class="th-products-view">
				<div class="th-products-view__options th-view-options <?php echo esc_attr( $classes ); ?>">
					<div class="th-view-options__body">
						<?php if ( is_active_sidebar( $this->get_sidebar_name() ) ) : ?>
							<button type="button" class="th-view-options__filters-button th-filters-button">
								<span class="th-filters-button__icon"><?php redparts_the_icon( 'filters-16' ); ?></span>
								<span class="th-filters-button__title"><?php esc_html_e( 'Filters', 'redparts' ); ?></span>
								<?php if ( $filters_count ) : ?>
									<span class="th-filters-button__counter"><?php echo esc_html( $filters_count ); ?></span>
								<?php endif; ?>
							</button>
						<?php endif; ?>

						<div class="th-view-options__layout th-layout-switcher">
							<div class="th-layout-switcher__list">
								<?php foreach ( $buttons as $button_key => $button ) : ?>
									<?php
									$button_is_active = $button_key === $view_mode;
									$button_classes   = array( 'th-layout-switcher__button' );

									if ( $button_is_active ) {
										$button_classes[] = 'th-layout-switcher__button--active';
									}
									?>
									<button
										type="button"
										class="<?php redparts_the_classes( ...$button_classes ); ?>"
										title="<?php echo esc_attr( $button['title'] ); ?>"
										data-layout="<?php echo esc_attr( $button['layout'] ); ?>"
										data-with-features="<?php echo esc_attr( $button['with_features'] ); ?>"
									>
										<?php redparts_the_icon( $button['icon'] ); ?>
									</button>
								<?php endforeach; ?>
							</div>
						</div>
			<?php

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo apply_filters( 'redparts_shop_view_options', ob_get_clean() );
		}

		/**
		 * Outputs legend.
		 */
		public function view_options_legend() {
			echo '<div class="th-view-options__legend">';

			woocommerce_result_count();

			echo '</div>';
		}

		/**
		 * Outputs spring.
		 */
		public function view_options_spring() {
			echo '<div class="th-view-options__spring"></div>';
		}

		/**
		 * Outputs the option to select the number of products per page.
		 */
		public function view_options_per_page() {
			$limits   = $this->get_products_per_page_variations();
			$per_page = $this->get_products_per_page();

			if ( ! in_array( $per_page, $limits, true ) ) {
				$limits[] = $per_page;
			}

			sort( $limits );

			if ( 1 < count( $limits ) ) :
				?>
				<form class="th-view-options__select" method="get">
					<label for="th-view-options-per-page"><?php esc_html_e( 'Show', 'redparts' ); ?></label>
					<select id="th-view-options-per-page" class="th-select th-select--size--small" name="per_page">
						<?php foreach ( $limits as $limit ) : ?>
							<option value="<?php echo esc_attr( $limit ); ?>" <?php selected( $limit, $per_page ); ?>>
								<?php echo esc_html( $limit ); ?>
							</option>
						<?php endforeach; ?>
					</select>
					<input type="hidden" name="paged" value="1" />
					<?php wc_query_string_form_fields( null, array( 'per_page', 'submit', 'paged', 'product-page' ) ); ?>
				</form>
				<?php
			endif;
		}

		/**
		 * Outputs close tag for view options before shop loop.
		 */
		public function view_options_close_tag_before_loop() {
			ob_start();

			echo '</div>';

			the_widget(
				'WC_Widget_Layered_Nav_Filters',
				array(
					'title' => esc_html__( 'Active Filters', 'redparts' ),
				),
				array(
					'before_widget' => '<div class="th-view-options__body th-view-options__body--filters %s">',
					'after_widget'  => '</div>',
					'before_title'  => '<div class="th-view-options__label">',
					'after_title'   => '</div>',
				)
			);

			echo '</div>';

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo apply_filters( 'redparts_shop_view_options_close_tag_before_loop', ob_get_clean() );
		}

		/**
		 * Outputs close tag for view options after shop loop.
		 */
		public function view_options_close_tag_after_loop() {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo apply_filters( 'redparts_shop_view_options_close_tag_after_loop', '</div>' );
		}

		/**
		 * Outputs loop pagination.
		 */
		public function pagination() {
			ob_start();

			echo '<div class="th-products-view__pagination">';

			ob_start();

			woocommerce_pagination();

			$has_pagination = ! empty( ob_get_contents() );

			ob_end_flush();

			echo '<div class="th-products-view__pagination-legend">';

			woocommerce_result_count();

			echo '</div>';
			echo '</div>';

			if ( $has_pagination ) {
				ob_end_flush();
			} else {
				ob_end_clean();
			}
		}

		/**
		 * Returns pagination args.
		 *
		 * @param array $args Pagination args.
		 * @return array
		 */
		public function pagination_args( array $args ): array {
			$prev_icon = redparts_get_icon( 'arrow-rounded-left-7x11' );
			$next_icon = redparts_get_icon( 'arrow-rounded-right-7x11' );

			$args['prev_text'] = '<span class="sr-only">' . esc_html__( 'Previous', 'redparts' ) . '</span>' . $prev_icon;
			$args['next_text'] = '<span class="sr-only">' . esc_html__( 'Next', 'redparts' ) . '</span>' . $next_icon;
			$args['mid_size']  = 2;
			$args['end_size']  = 1;

			return $args;
		}

		/**
		 * Returns categories start comment.
		 *
		 * @return string
		 */
		public function categories_start_comment(): string {
			return '<!-- REDPARTS-CATEGORIES-START -->';
		}

		/**
		 * Returns categories end comment.
		 *
		 * @return string
		 */
		public function categories_end_comment(): string {
			return '<!-- REDPARTS-CATEGORIES-END -->';
		}

		/**
		 * Removes subcategories from the loop using special comments and preg_replace.
		 *
		 * @param string $html Loop start HTML.
		 *
		 * @return string
		 */
		public function remove_subcategories( string $html ): string {
			$start_tag = '<!-- REDPARTS-CATEGORIES-START -->';
			$end_tag   = '<!-- REDPARTS-CATEGORIES-END -->';

			$start = strpos( $html, $start_tag );
			$end   = strpos( $html, $end_tag );

			if ( false === $start || false === $end ) {
				return $html;
			}

			$end = $end + strlen( $end_tag );

			return substr_replace( $html, '', $start, $end - $start );
		}

		/**
		 * Replace the documents title on the vehicle page.
		 *
		 * @since 1.4.0
		 *
		 * @param array $title Document title part.
		 *
		 * @return array
		 */
		public function vehicle_document_title( array $title ): array {
			if ( class_exists( 'RedParts\Sputnik\Vehicles' ) && Vehicles::is_vehicle() ) {
				$title['title'] = Vehicles::get_filtered_vehicle_name();
			}

			return $title;
		}

		/**
		 * Replace crumbs on the vehicle page.
		 *
		 * @since 1.4.0
		 *
		 * @param array $crumbs Array of crumbs.
		 *
		 * @return array
		 */
		public function vehicle_breadcrumb( array $crumbs ): array {
			if ( ! class_exists( 'RedParts\Sputnik\Vehicles' ) || ! Vehicles::is_vehicle() ) {
				return $crumbs;
			}

			$filtered_vehicle_name = Vehicles::get_filtered_vehicle_name();

			$preserve_at_end = 0;

			// Preserve the last crumb (for example: Page 1) in the paginated results.
			if ( get_query_var( 'paged' ) && 'subcategories' !== woocommerce_get_loop_display_mode() ) {
				$preserve_at_end = 1;
			}

			// Remove search trail.
			if ( is_search() ) {
				array_splice( $crumbs, count( $crumbs ) - 1 - $preserve_at_end, 1 );
			}

			return array_merge(
				array_slice( $crumbs, 0, count( $crumbs ) - $preserve_at_end ),
				array(
					array(
						wp_strip_all_tags( $filtered_vehicle_name ),
						Vehicles::get_vehicle_link( Vehicles::get_filtered_vehicle() ),
					),
				),
				array_slice( $crumbs, count( $crumbs ) - $preserve_at_end )
			);
		}
	}
}
