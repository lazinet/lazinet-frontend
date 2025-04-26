<?php
/**
 * This file contains code related to the product page only.
 *
 * @package RedParts
 * @since 1.0.0
 */

namespace RedParts;

use RedParts\Sputnik\Brands;
use RedParts\Sputnik\Compare;
use RedParts\Sputnik\Garage;
use RedParts\Sputnik\Wishlist;
use RedParts\Sputnik\Featured_Attributes;
use WC_Product;
use WC_Product_Attribute;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'RedParts\Product' ) ) {
	/**
	 * Class Product
	 */
	class Product extends Singleton {
		const DEFAULT_SIDEBAR_POSITION = 'none';

		const VALID_SIDEBAR_POSITIONS = array( 'start', 'end', 'none' );

		/**
		 * Initialization.
		 */
		public function init() {
			add_action( 'init', array( $this, 'deferred_init' ) );
		}

		/**
		 * Deferred initialization.
		 *
		 * @since 1.5.0
		 */
		public function deferred_init() {
			// Remove redundant hooks.
			// Hook: woocommerce_before_single_product_summary.
			remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash' );
			remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
			// Hook: woocommerce_single_product_summary.
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating' );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price' );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
			// Hook: woocommerce_after_single_product_summary.
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

			// Adding hooks.
			// Hook: woocommerce_before_single_product_summary.
			add_action( 'woocommerce_before_single_product_summary', array( $this, 'gallery_template' ), 100 );
			add_action( 'woocommerce_before_single_product_summary', array( $this, 'header_template' ), 200 );
			add_action( 'woocommerce_before_single_product_summary', array( $this, 'intro_template' ), 300 );
			// Hook: woocommerce_single_product_summary.
			add_action( 'woocommerce_single_product_summary', array( $this, 'sale_badge' ), 100 );
			add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 200 );
			add_action( 'woocommerce_single_product_summary', array( $this, 'stock_badge' ), 300 );
			add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 400 );
			add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 500 );
			add_action( 'woocommerce_single_product_summary', array( $this, 'tags' ), 600 );
			add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 700 );
			// Hook: redparts_after_product.
			add_action( 'redparts_after_product', 'woocommerce_upsell_display', 100 );
			add_action( 'redparts_after_product', 'woocommerce_output_related_products', 200 );
			// Hook: redparts_product_gallery.
			add_action( 'redparts_product_gallery', array( $this, 'zoom_button' ), 100 );
			add_action( 'redparts_product_gallery', 'woocommerce_show_product_images', 200 );
			// Hook: redparts_product_header.
			add_action( 'redparts_product_header', 'woocommerce_template_single_title', 100 );
			add_action( 'redparts_product_header', array( $this, 'subtitle_template' ), 200 );
			// Hook: redparts_product_subtitle.
			add_action( 'redparts_product_subtitle', 'woocommerce_template_single_rating', 100 );
			add_action( 'redparts_product_subtitle', array( $this, 'compatibility_badge' ), 200 );
			// Hook: redparts_product_intro.
			add_action( 'redparts_product_intro', 'woocommerce_template_single_excerpt', 100 );
			add_action( 'redparts_product_intro', array( $this, 'features' ), 200 );
			// Hook: woocommerce_after_add_to_cart_button.
			add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'actions' ) );
			// Hook: redparts_product_actions.
			if (
				class_exists( '\RedParts\Sputnik\Wishlist' ) &&
				redparts_sputnik_version_is( '>=', '1.5.0' ) &&
				Wishlist::instance()->is_enabled()
			) {
				add_action( 'redparts_product_actions', array( Wishlist::instance(), 'the_button' ), 100 );
			}
			if (
				class_exists( '\RedParts\Sputnik\Compare' ) &&
				redparts_sputnik_version_is( '>=', '1.5.0' ) &&
				Compare::instance()->is_enabled()
			) {
				add_action( 'redparts_product_actions', array( Compare::instance(), 'the_button' ), 200 );
			}

			// Other hooks and filters.

			// Grouped product.
			add_action( 'woocommerce_grouped_product_columns', array( $this, 'grouped_product_columns' ) );
			add_action( 'woocommerce_grouped_product_list_column_redparts_image', array( $this, 'grouped_product_column_redparts_image' ) );

			// Reset variation link.
			add_filter( 'woocommerce_reset_variations_link', array( $this, 'reset_variation_link' ) );

			// Reviews.
			add_filter( 'woocommerce_product_review_comment_form_args', array( $this, 'review_form_args' ) );
			add_filter( 'woocommerce_reviews_title', array( $this, 'reviews_list_title' ), 10, 2 );

			// Specification tab.
			add_filter( 'woocommerce_product_additional_information_heading', array( $this, 'specification_tab_title' ) );
			add_filter( 'woocommerce_product_additional_information_tab_title', array( $this, 'specification_tab_title' ) );

			// Add to cart quantity.
			add_action( 'woocommerce_before_add_to_cart_quantity', array( $this, 'before_add_to_cart_quantity' ) );
			add_action( 'woocommerce_after_add_to_cart_quantity', array( $this, 'after_add_to_cart_quantity' ) );

			// Pagination.
			add_filter( 'woocommerce_comment_pagination_args', array( $this, 'pagination_args' ) );
		}

		/**
		 * Returns shop sidebar name.
		 *
		 * @return string
		 */
		public function get_sidebar_name(): string {
			return apply_filters( 'redparts_product_sidebar_name', 'redparts-product' );
		}

		/**
		 * Returns the product page layout.
		 *
		 * @return string
		 */
		public function get_layout(): string {
			$result = 'sidebar';

			if ( 'none' === $this->get_sidebar_position() ) {
				$result = 'full';
			}

			return apply_filters( 'redparts_product_get_layout', $result );
		}

		/**
		 * Returns sidebar position.
		 *
		 * @return string
		 */
		public function get_sidebar_position(): string {
			$result   = self::DEFAULT_SIDEBAR_POSITION;
			$settings = Settings::instance()->get();

			if ( ! empty( $settings['product_sidebar_position'] ) && in_array( $settings['product_sidebar_position'], self::VALID_SIDEBAR_POSITIONS, true ) ) {
				$result = $settings['product_sidebar_position'];
			}

			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			if ( isset( $_GET['redparts_product_sidebar_position'] ) ) {
				$get_sidebar_position = sanitize_key( wp_unslash( $_GET['redparts_product_sidebar_position'] ) );

				if ( in_array( $get_sidebar_position, self::VALID_SIDEBAR_POSITIONS, true ) ) {
					$result = $get_sidebar_position;
				}
			}
			// phpcs:enable

			if ( ! is_active_sidebar( $this->get_sidebar_name() ) ) {
				$result = 'none';
			}

			return apply_filters( 'redparts_product_get_sidebar_position', $result );
		}

		/**
		 * Returns an array of columns for table of grouped products.
		 *
		 * @return array
		 */
		public function grouped_product_columns(): array {
			return array(
				'redparts_image',
				'label',
				'quantity',
				'price',
			);
		}

		/**
		 * Returns product image.
		 *
		 * @return string
		 */
		public function grouped_product_column_redparts_image(): string {
			ob_start();

			woocommerce_template_loop_product_thumbnail();

			return ob_get_clean();
		}

		/**
		 * Outputs product featured attributes.
		 */
		public function features() {
			if ( ! class_exists( '\RedParts\Sputnik\Featured_Attributes' ) ) {
				return;
			}

			ob_start();

			Featured_Attributes::instance()->the_featured_attributes();

			$featured_attributes = ob_get_clean();

			if ( empty( $featured_attributes ) ) {
				return;
			}

			?>
			<div class="th-product__features">
				<div class="th-product__features-title">
					<?php esc_html_e( 'Key Features:', 'redparts' ); ?>
				</div>
				<?php echo wp_kses( $featured_attributes, 'redparts_featured_attributes' ); ?>
				<div class="th-product__features-link th-full-specification">
					<!--suppress HtmlUnknownAnchorTarget -->
					<a href="#tab-additional_information">
						<?php esc_html_e( 'See Full Specification', 'redparts' ); ?>
					</a>
				</div>
			</div>
			<?php
		}

		/**
		 * Outputs product tags.
		 */
		public function tags() {
			global $product;

			$separator = ' ';
			$before    = '<div class="th-product__tags th-tags"><span class="sr-only">';
			$before   .= esc_html( _n( 'Tag:', 'Tags:', count( $product->get_tag_ids() ), 'redparts' ) ); // SKIP-ESC.
			$before   .= '</span><div class="th-tags__list">';
			$after     = '</div></div>';

			$tags = wc_get_product_tag_list( $product->get_id(), $separator, $before, $after );

			echo wp_kses( $tags, 'redparts_product_tags' );
		}

		/**
		 * Outputs zoom button.
		 */
		public function zoom_button() {
			?>
			<button type="button" class="th-product-gallery__trigger">
				<?php redparts_the_icon( 'zoom-24' ); ?>
			</button>
			<?php
		}

		/**
		 * Outputs actions.
		 */
		public function actions() {
			/**
			 * Hook: redparts_product_actions.
			 *
			 * @hooked RedParts\Wishlist::the_button - 100
			 * @hooked RedParts\Compare::the_button  - 200
			 */
			do_action( 'redparts_product_actions' );
		}

		/**
		 * Returns reset variation link.
		 *
		 * @param string $link Reset variation link.
		 * @return string
		 */
		public function reset_variation_link( string $link ): string {
			return '<div class="th-reset-variations th-reset-variations--hidden">' . $link . '</div>';
		}

		/**
		 * Returns brand attribute.
		 *
		 * @param WC_Product $product - Product object.
		 *
		 * @return WC_Product_Attribute|null
		 * @noinspection PhpMissingReturnTypeInspection
		 */
		public function get_brand_attribute( WC_Product $product ) {
			if ( ! class_exists( '\RedParts\Sputnik\Brands' ) ) {
				return null;
			}

			$brands = Brands::instance();
			$slug   = $brands->get_attribute_slug();

			if ( empty( $slug ) ) {
				return null;
			}

			/** Attributes array. @var WC_Product_Attribute[] $attributes */
			$attributes = $product->get_attributes();

			if ( isset( $attributes[ $slug ] ) && $attributes[ $slug ]->is_taxonomy() ) {
				return $attributes[ $slug ];
			}

			return null;
		}

		/**
		 * Returns review form args.
		 *
		 * @param array $form - Review form args.
		 * @return array
		 */
		public function review_form_args( array $form ): array {
			$form['title_reply'] = apply_filters( 'redparts_review_form_title', esc_html__( 'Write A Review', 'redparts' ), $form );

			return $form;
		}

		/**
		 * Returns reviews list title.
		 *
		 * @param string $reviews_title - Reviews list title.
		 * @param number $count         - Number of reviews.
		 *
		 * @return string
		 * @noinspection PhpUnusedParameterInspection
		 */
		public function reviews_list_title( string $reviews_title, $count ): string {
			// translators: %s: number of reviews.
			return esc_html( sprintf( _n( '%s Customer Review', '%s Customer Reviews', $count, 'redparts' ), $count ) ); // SKIP-ESC.
		}

		/**
		 * Returns the title of the specification tab.
		 *
		 * @return string
		 */
		public function specification_tab_title(): string {
			return esc_html__( 'Specification', 'redparts' );
		}

		/**
		 * Sets true to the special variable used in the woocommerce/global/quantity-input.php template.
		 */
		public function before_add_to_cart_quantity() {
			global $redparts_in_add_to_cart_quantity;

			$redparts_in_add_to_cart_quantity = true;
		}

		/**
		 * Sets false to the special variable used in the woocommerce/global/quantity-input.php template.
		 */
		public function after_add_to_cart_quantity() {
			global $redparts_in_add_to_cart_quantity;

			$redparts_in_add_to_cart_quantity = false;
		}

		/**
		 * Outputs product header template.
		 */
		public function header_template() {
			echo '<div class="th-product__header">';

			/**
			 * Hook: redparts_product_gallery.
			 *
			 * @hooked woocommerce_template_single_title   - 100
			 * @hooked RedParts\Product::subtitle_template - 200
			 */
			do_action( 'redparts_product_header' );

			echo '</div>';
		}

		/**
		 * Outputs product subtitle template.
		 */
		public function subtitle_template() {
			echo '<div class="th-product__subtitle">';

			/**
			 * Hook: redparts_product_gallery.
			 *
			 * @hooked woocommerce_template_single_rating    - 100
			 * @hooked RedParts\Product::compatibility_badge - 200
			 */
			do_action( 'redparts_product_subtitle' );

			echo '</div>';
		}

		/**
		 * Outputs product intro template.
		 */
		public function intro_template() {
			echo '<div class="th-product__intro">';

			/**
			 * Hook: redparts_product_intro.
			 *
			 * @hooked woocommerce_template_single_excerpt - 100
			 * @hooked RedParts\Product::features          - 200
			 */
			do_action( 'redparts_product_intro' );

			echo '</div>';
		}

		/**
		 * Outputs product gallery template.
		 */
		public function gallery_template() {
			echo '<div class="th-product__gallery th-product-gallery">';
			echo '<div class="th-product-gallery__body">';

			/**
			 * Hook: redparts_product_gallery.
			 *
			 * @hooked RedParts\Product::zoom_button   - 100
			 * @hooked woocommerce_show_product_images - 200
			 */
			do_action( 'redparts_product_gallery' );

			echo '</div>';
			echo '</div>';
		}

		/**
		 * Outputs sale badge.
		 */
		public function sale_badge() {
			ob_start();

			echo '<div class="th-product__badge">';

			ob_start();

			woocommerce_show_product_sale_flash();

			$has_sale_badge = ! empty( ob_get_contents() );

			ob_end_flush();

			echo '</div>';

			if ( $has_sale_badge ) {
				ob_end_flush();
			} else {
				ob_end_clean();
			}
		}

		/**
		 * Outputs stock badge.
		 *
		 * @noinspection DuplicatedCode
		 */
		public function stock_badge() {
			global $product;

			if ( ! $product->is_purchasable() ) {
				return;
			}

			$stock_html = wc_get_stock_html( $product );

			if ( $stock_html ) {
				echo '<div class="th-product__stock">';
				echo wp_kses( $stock_html, 'redparts_stock' );
				echo '</div>';
			}
		}

		/**
		 * Outputs vehicle compatibility badge.
		 */
		public function compatibility_badge() {
			if ( ! class_exists( '\RedParts\Sputnik\Garage' ) ) {
				return;
			}

			global $product;

			$args = array( 'class' => 'th-product__fit' );

			Garage::instance()->the_compatibility_badge( $product->get_id(), $args );
		}

		/**
		 * Modifies pagination arguments.
		 *
		 * @param array $args Arguments.
		 *
		 * @return array
		 */
		public function pagination_args( array $args ): array {
			$prev_icon = redparts_get_icon( 'arrow-rounded-left-7x11' );
			$next_icon = redparts_get_icon( 'arrow-rounded-right-7x11' );

			$args['prev_text'] = '<span class="sr-only">' . esc_html__( 'Previous', 'redparts' ) . '</span>' . $prev_icon;
			$args['next_text'] = '<span class="sr-only">' . esc_html__( 'Next', 'redparts' ) . '</span>' . $next_icon;

			return $args;
		}
	}
}
