<?php
/**
 * Template Name: Custom Single Product Template 1
 * Template Post Type: product
 *
 * This file is a template for displaying single WooCommerce products with a custom layout.
 * It is used when a single product is viewed and should be loaded from the plugin.
 *
 * @package salsisync
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Load header and footer for the custom single product template.
 * @codingStandardsIgnoreStart
 * echo do_blocks( file_get_contents( get_theme_file_path( '/parts/header.html' ) ) );
 * get_header('shop');
 * @codingStandardsIgnoreEnd
 */
get_header();
?>
<div class="salsisync-single-product salsisync-single-product-first">
	<?php
		do_action( 'woocommerce_before_main_content' );
	?>

	<main id="site-content" class="woocommerce-single-product-page" role="main">

			<div class="woocommerce-product-wrapper">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'single-product-content', get_the_ID() ); ?>>
						<div class="salsisync-single-product__header woocommerce-product-header">
							<h1 class="woocommerce-product-title"><?php the_title(); ?></h1>
						</div>
					<div class="container">
						<div class="woocommerce-product-details">
							<!-- WooCommerce Product Images -->
							<div class="product-gallery">
								<?php woocommerce_show_product_images(); ?>
							</div>

							<!-- Product Information -->
							<div class="product-info">
								<?php woocommerce_template_single_title(); ?>
								<?php woocommerce_template_single_rating(); ?>
								<?php woocommerce_template_single_price(); ?>
								<?php woocommerce_template_single_excerpt(); ?>
								<?php woocommerce_template_single_add_to_cart(); ?>
								<?php woocommerce_template_single_meta(); ?>
								<?php //phpcs:ignore wc_get_template_part( 'content', 'single-product' ); ?>								

							</div>
							<div class="salsisync-custom-meta-data custom-meta-data">
								<?php
								/**
								 * Show last inserted custom data mapping fields.
								 * Old key
								 * $get_custom_keys = get_option( 'salsisync__custom_data_mapping_fiels', array() );
								 */
								$get_custom_keys = get_option( 'salsisync__last_inserted_custom_data_mapping_fields', array() );
								foreach ( $get_custom_keys as $key => $value ) {
									$custom_field              = get_post_meta( get_the_ID(), $value['key'], true );
									$unserialized_custom_field = maybe_unserialize( $custom_field );
									$custom_field_value        = is_array( $unserialized_custom_field['value'] ) ? $unserialized_custom_field['value'][0] : $unserialized_custom_field['value'];
									$custom_field_value        = empty( $custom_field_value ) ? 'No value found' : $custom_field_value;
									if ( $custom_field ) {
										echo '<div class="custom-meta-data__item">';
										echo '<span class="custom-meta-data__label">' . esc_html( $unserialized_custom_field['label'] ) . '</span>';
										echo '<span class="custom-meta-data__value">' . esc_html( $custom_field_value ) . '</span>';
										echo '</div>';
									}
								}
								?>
							</div>
						</div>

						<div class="woocommerce-product-description">
							<!-- WooCommerce Product Description -->
							<?php woocommerce_output_product_data_tabs(); ?>
						</div>

						<div class="woocommerce-related-products">
							<!-- WooCommerce Related Products -->
							<?php woocommerce_output_related_products(); ?>
						</div>
					</div>
				<?php endwhile; ?>
			</div>
		</div>
	</main>

	<?php
	do_action( 'woocommerce_after_main_content' );
	?>
</div>
<?php
/**
 * Load footer for the custom single product template.
 * @codingStandardsIgnoreStart
 * echo do_blocks( file_get_contents( get_theme_file_path( '/parts/footer.html' ) ) );
 * get_footer('shop');
 * @codingStandardsIgnoreEnd
 */
get_footer();
