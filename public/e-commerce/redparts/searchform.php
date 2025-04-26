<?php
/**
 * Template for displaying search forms in the RedParts theme.
 *
 * @package RedParts
 * @since 1.0.0
 */

use RedParts\Settings;
use RedParts\Sputnik\Vehicles;
use RedParts\Sputnik\Settings as SputnikSettings;

defined( 'ABSPATH' ) || exit;

$sputnik_settings = null;
$garage           = null;

if ( class_exists( 'RedParts\Sputnik\Garage' ) ) {
	$garage = RedParts\Sputnik\Garage::instance();
}
if ( class_exists( 'RedParts\Sputnik\Settings' ) ) {
	$sputnik_settings = SputnikSettings::instance();
}

if ( ! isset( $args ) ) {
	$args = array();
}

/**
 * Array of form arguments.
 *
 * @var string[] $args {
 *     @type string $redparts_location     Specifies where the search form will be located. By default, a string with the value 'undefined'.
 *     @type string $redparts_search_by    Specify the post type to be searched. Default is empty.
 *     @type bool   $redparts_categories   Enables or disables categories selector. Default is false.
 *     @type bool   $redparts_autocomplete Enables or disables default browser autocomplete feature. Default is false.
 *     @type string $redparts_classes      CSS classes to be applied to the root element.
 * }
 */

$location     = ! empty( $args['redparts_location'] ) ? $args['redparts_location'] : 'undefined';
$search_by    = ! empty( $args['redparts_search_by'] ) ? $args['redparts_search_by'] : '';
$categories   = (bool) ( ! empty( $args['redparts_categories'] ) ? $args['redparts_categories'] : false );
$autocomplete = (bool) ( ! empty( $args['redparts_autocomplete'] ) ? $args['redparts_autocomplete'] : false );
$classes      = ! empty( $args['redparts_classes'] ) ? $args['redparts_classes'] : '';

$in_header         = in_array( $location, array( 'desktop-header', 'mobile-header' ), true );
$in_desktop_header = 'desktop-header' === $location;
$in_mobile_header  = 'mobile-header' === $location;

if ( 'product' === $search_by && ! class_exists( 'WooCommerce' ) ) {
	$search_by = 'post';
}

$unique_id   = redparts()->get_unique_id( 'th-search-form-' );
$placeholder = esc_html__( 'Search&hellip;', 'redparts' );
$classes     = redparts_get_classes( 'th-search', 'th-search--location--' . $location, $classes );

// translators: %s search category.
$placeholder_template = esc_html__( 'Search in "%s"', 'redparts' );
$placeholder_default  = esc_html__( 'Search&hellip;', 'redparts' );

if ( $in_desktop_header || $in_mobile_header ) {
	// translators: %s vehicle or category name.
	$placeholder_template = Settings::instance()->get_option( 'header_search_placeholder', esc_html__( 'Search for %s', 'redparts' ) );
	$placeholder_default  = Settings::instance()->get_option( 'header_search_default_placeholder', esc_html__( 'Enter Keyword or Part Number', 'redparts' ) );
}

$placeholder = $placeholder_default;

// Vehicle vars.
$filter_vehicle_disabled = true;
$filter_vehicle_value    = '';
$vehicle                 = null;

// Category vars.
$category                 = null;
$category_taxonomy        = 'category';
$category_input_name      = 'cat';
$category_value_field     = 'term_id';
$category_value           = '';
$category_button_label_id = $unique_id . '-category-button-label';
$category_button_id       = $unique_id . '-category-button';
$default_category_name    = esc_html__( 'All Categories', 'redparts' );

if ( 'product' === $search_by ) {
	$category_taxonomy    = 'product_cat';
	$category_input_name  = 'product_cat';
	$category_value_field = 'slug';
}

$autoparts_features = $sputnik_settings && 'no' !== $sputnik_settings->get( 'autoparts_features' );
$autoparts_features = $autoparts_features && $garage && 'product' === $search_by;

if ( $autoparts_features ) {
	$vehicle = $garage->get_current_vehicle();

	if ( $vehicle ) {
		$filter_vehicle_disabled = null === $vehicle;
		$filter_vehicle_value    = urldecode( $vehicle['slug'] );

		$placeholder = sprintf( $placeholder_template, Vehicles::get_vehicle_name( $vehicle ) );
	}
} elseif ( $in_desktop_header && is_search() ) {
	$get_by         = 'id' === $category_value_field ? 'term_id' : 'slug';
	$category       = get_term_by( $get_by, get_query_var( $category_input_name ), $category_taxonomy );
	$category_value = $category ? $category->{$category_value_field} : '';

	if ( $category ) {
		$placeholder = sprintf( $placeholder_template, $category->name );
	}
}

?>
<div class="<?php echo esc_attr( $classes ); ?>" data-id-prefix="<?php echo esc_attr( $unique_id ); ?>">
	<div class="th-search__wrapper">
		<form class="th-search__body" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php if ( 'product' === $search_by ) : ?>
				<input type="hidden" name="post_type" value="product">
			<?php endif; ?>

			<?php if ( $in_header ) : ?>
				<div class="th-search__shadow"></div>
			<?php endif; ?>

			<input
				class="th-search__input"
				name="s"
				value="<?php echo esc_attr( get_search_query() ); ?>"
				id="<?php echo esc_attr( $unique_id ); ?>"
				data-placeholder-default="<?php echo esc_attr( $placeholder_default ); ?>"
				data-placeholder-template="<?php echo esc_attr( $placeholder_template ); ?>"
				placeholder="<?php echo esc_attr( $placeholder ); ?>"
				type="text"
				aria-label="<?php echo esc_attr_x( 'Search for:', 'label', 'redparts' ); ?>"
				<?php if ( ! $autocomplete ) : ?>
					autocomplete="off"
				<?php endif; ?>

				role="combobox"
				aria-autocomplete="list"
				aria-controls="<?php echo esc_attr( $unique_id . '-suggestions' ); ?>"
				aria-expanded="false"
				aria-activedescendant
			>

			<?php if ( $in_header ) : ?>
				<?php if ( $autoparts_features ) : ?>
					<input
						type="hidden"
						name="<?php echo esc_attr( Vehicles::instance()->get_attribute_filter_name() ); ?>"
						value="<?php echo esc_attr( $filter_vehicle_value ); ?>"
						<?php disabled( $filter_vehicle_disabled ); ?>
					>
					<button class="th-search__button th-search__button--vehicle" type="button">
						<span class="th-search__button-icon" role="img">
							<?php redparts_the_icon( 'car-20' ); ?>
						</span>
						<span class="th-search__button-title">
							<?php

							if ( 'mobile-header' === $location ) {
								echo esc_html__( 'Vehicle', 'redparts' );
							} else {
								echo esc_html__( 'Select Vehicle', 'redparts' );
							}

							?>
						</span>
					</button>
				<?php elseif ( $in_desktop_header ) : ?>
					<div id="<?php echo esc_attr( $category_button_label_id ); ?>" class="sr-only">
						<?php echo esc_attr__( 'Search in:', 'redparts' ); ?>
					</div>

					<input
						class="th-search__category"
						type="hidden"
						name="<?php echo esc_attr( $category_input_name ); ?>"
						value="<?php echo esc_attr( $category_value ); ?>"
						<?php disabled( empty( $category ) ); ?>
					>

					<button
						id="<?php echo esc_attr( $category_button_id ); ?>"
						class="th-search__button th-search__button--category"
						type="button"
						aria-labelledby="<?php echo esc_attr( $category_button_label_id . ' ' . $category_button_id ); ?>"
						data-value="<?php echo esc_attr( $category_value ); ?>"
					>
						<span class="th-search__button-icon" role="img">
							<?php redparts_the_icon( 'category-20' ); ?>
						</span>
						<span class="th-search__button-title">
							<?php echo esc_html( $category ? $category->name : $default_category_name ); ?>
						</span>
					</button>
				<?php endif; ?>
			<?php endif; ?>

			<button class="th-search__button th-search__button--search" type="submit">
				<span class="th-search__button-icon">
					<?php redparts_the_icon( 'search-20' ); ?>
				</span>
				<span class="th-search__button-title">
					<?php echo esc_html_x( 'Search', 'submit button', 'redparts' ); ?>
				</span>
			</button>
			<div class="th-search__box"></div>

			<?php if ( 'desktop-header' === $location ) : ?>
				<div class="th-search__decor">
					<div class="th-search__decor-start"></div>
					<div class="th-search__decor-end"></div>
				</div>
			<?php endif; ?>

			<?php if ( 'mobile-header' === $location ) : ?>
				<button class="th-search__button th-search__button--close" type="button">
					<span class="th-search__button-icon">
						<?php redparts_the_icon( 'cross-20' ); ?>
					</span>
				</button>
			<?php endif; ?>
		</form>

		<?php if ( redparts_sputnik_version_is( '>=', '1.5.0' ) && 'product' === $search_by ) : ?>
			<div class="th-search__dropdown th-search__dropdown--no-animate th-search__dropdown--suggestions">
				<div
					class="th-suggestions"
					tabindex="-1"
					data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>"
					data-nonce="<?php echo esc_attr( wp_create_nonce( 'redparts_sputnik_search_suggestions' ) ); ?>"
					data-taxonomy="<?php echo esc_attr( $autoparts_features ? Vehicles::instance()->get_attribute_slug() : 'product_cat' ); ?>"
					data-taxonomy-value="<?php echo esc_attr( $autoparts_features ? $filter_vehicle_value : $category_value ); ?>"
				>
					<div
						class="th-suggestions__list"
						id="<?php echo esc_attr( $unique_id . '-suggestions' ); ?>"
						role="listbox"
						aria-label="<?php echo esc_html__( 'Products', 'redparts' ); ?>"
					></div>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( $autoparts_features ) : ?>
			<?php if ( 'desktop-header' === $location ) : ?>
				<?php
				$vehicles      = $garage->vehicles();
				$current_panel = 0 !== count( $vehicles ) ? 'list' : 'form';
				?>
				<div class="th-search__dropdown th-search__dropdown--vehicle-picker th-vehicle-picker">
					<div class="th-search__dropdown-arrow"></div>
					<?php
					$classes = array(
						'th-vehicle-picker__panel',
						'th-vehicle-picker__panel--list',
					);

					if ( 'list' === $current_panel ) {
						$classes[] = 'th-vehicle-picker__panel--active';
					}
					?>
					<div class="<?php redparts_the_classes( ...$classes ); ?>" data-panel="list">
						<div class="th-vehicle-picker__panel-body">
							<div class="th-vehicle-picker__text">
								<?php echo esc_html__( 'Select a vehicle to find exact fit parts', 'redparts' ); ?>
							</div>

							<?php $garage->the_current_vehicle_control( array( 'location' => 'header' ) ); ?>

							<div class="th-vehicle-picker__actions">
								<button type="button" class="th-button th-button--style--primary th-button--size--small" data-to-panel="form">
									<?php echo esc_html__( 'Add A Vehicle', 'redparts' ); ?>
								</button>
							</div>
						</div>
					</div>
					<?php
					$classes = array(
						'th-vehicle-picker__panel',
						'th-vehicle-picker__panel--form',
					);

					if ( 'form' === $current_panel ) {
						$classes[] = 'th-vehicle-picker__panel--active';
					}
					?>
					<div class="<?php redparts_the_classes( ...$classes ); ?>" data-panel="form">
						<div class="th-vehicle-picker__panel-body th-garage-add-form">
							<?php echo do_shortcode( '[redparts_sputnik_vehicle_form location="header"]' ); ?>

							<div class="th-vehicle-picker__actions">
								<div class="th-vehicle-picker__link">
									<a href="" data-to-panel="list">
										<?php echo esc_html__( 'Back to vehicles list', 'redparts' ); ?>
									</a>
								</div>

								<?php
								echo do_shortcode(
									'[redparts_sputnik_garage_add_button
											class="th-button th-button--style--primary th-button--size--small"
											loading_class="th-button--loading"
										]'
								);
								?>
							</div>
						</div>
					</div>
				</div>
			<?php endif; ?>
		<?php elseif ( $in_desktop_header ) : ?>
			<div class="th-search__dropdown th-search__dropdown--category-picker">
				<div class="th-search__dropdown-arrow"></div>
				<?php
				redparts_dropdown_list(
					array(
						'taxonomy'         => $category_taxonomy,
						'depth'            => 1,
						'labelled_by'      => $category_button_label_id,
						'item_id_prefix'   => $unique_id . '-category-item-',
						'show_option_none' => $default_category_name,
						'value_field'      => $category_value_field,
					)
				);
				?>
			</div>
		<?php endif; ?>
	</div>
</div>
