<?php
/**
 * Template Name: Custom Single Product Template 2
 * Template Post Type: product
 *
 * This file is a template for displaying single WooCommerce products with a custom layout.
 * It is used when a single product is viewed and should be loaded from the plugin.
 *
 * @package salsisync
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="salsisync-single-product salsisync-single-product-second">
<?php
get_header( '' );
?>

	<main>
		<div class="salsisync-single-product__header woocommerce-product-header">
			<h1>Template 2</h1>
		</div>
		<div class="container">
			<div class="product-content">
				<?php woocommerce_content(); // WooCommerce default content. ?>
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
			</div>
		</div>
	</main>

<?php get_footer( '' ); ?>
</div>
<?php
