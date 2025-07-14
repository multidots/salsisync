<?php
/**
 * Template Name: Custom Single Product Template
 * Template Post Type: product
 *
 * Custom Single Product Template for WooCommerce compatible with FSE themes.
 *
 * This file is a template for displaying single WooCommerce products with a custom layout.
 * It is used when a single product is viewed and should be loaded from the plugin.
 *
 * @package YourPluginName
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.
get_header(); // Use the WooCommerce shop header.
?>
	<div id="salsify-woo-template-1">
		<div id="product-header-for-fse">
			<?php
				do_action( 'woocommerce_before_main_content' );
			?>
		</div>
		<div class="salsisync-single-product salsisync-single-product-first">
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
									</div>
								</div>

								<div class="salsisync-custom-meta-data custom-meta-data">
									<?php
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
		</div>
		<div id="product-footer-for-fse">
			<?php
				do_action( 'woocommerce_after_main_content' );
				echo wp_kses_post( do_blocks( '<!-- wp:template-part {"slug":"footer"} /-->' ) );
			?>
		</div>
	</div>
<?php
get_footer();
