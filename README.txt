=== Salsi Sync ===
Contributors: Multidots
Tags: woocommerce, api integration, salsify, sync, products
Requires at least: 6.4
Tested up to: 6.8
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Salsi Sync is a powerful plugin that enables WooCommerce site owners to synchronize their products from the Salsify API to WooCommerce effortlessly.

== Description ==
**Salsi Sync** is a powerful plugin that enables WooCommerce site owners to synchronize their products from the Salsify API to WooCommerce effortlessly. This plugin simplifies product updates, image synchronization, custom data mappings, and more, providing a complete integration solution between Salsify and WooCommerce.

### Key Features:

1. **Product Sync from Salsify to WooCommerce**  
   Fetch product data directly from the Salsify API and insert it into WooCommerce as WooCommerce-compatible products.

2. **Image Synchronization**  
   Syncs both featured images and gallery images for products, ensuring your WooCommerce store displays complete and visually consistent product data.

3. **Custom Data Mapping**  
   Customize product data mapping to display unique fields from Salsify API data, ensuring WooCommerce products are enriched with additional, tailored information.

4. **On-Demand Update Checks**  
   Manually check for product updates from the Salsify API, allowing you to keep WooCommerce products current with the latest data from Salsify.

5. **Custom WooCommerce Product Templates**  
   Display custom product templates on the frontend, tailored specifically for WooCommerce products synced from Salsify.

With **Salsi Sync**, managing and synchronizing your WooCommerce product data becomes streamlined, efficient, and tailored to meet your store's unique needs.


== Installation ==
1. Upload the `salsisync` directory to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Go to the **Salsi Sync** settings panel to configure API credentials and other plugin settings.
4. Use the **Bulk Insert** option to add all products from Salsify to WooCommerce or sync updates as needed.

== Frequently Asked Questions ==

= How do I configure the Salsify API? =
In the plugin settings, you can enter your Salsify API credentials, including API token and organization key, to connect with Salsify.

= How does the custom data mapping work? =
Custom data mapping allows you to map specific fields from the Salsify API data to custom WooCommerce fields, so additional information from Salsify is displayed on your WooCommerce products.

= Will this plugin overwrite existing WooCommerce products? =
No, the plugin is designed to skip existing products if they are already synced unless you choose to update them. Only new or updated products from Salsify will be inserted.

== Screenshots ==

1. **Plugin Options Panel** - Configure your Salsify API settings, mappings, and sync preferences.
2. **Sync Product Settings** - Choose how you want to sync products, images, and custom data.
3. **Custom Product Display** - Frontend display of products synced from Salsify.

== External services ==

This plugin connects to the Salsify API to sync product data. It sends product information and API keys when syncing data.
Service: Salsify API
Terms of Service: https://www.salsify.com/legal/terms-of-service
Privacy Policy: https://www.salsify.com/privacy-policy

== Changelog ==

= 1.1 - 27.05.2025 =
* Maintance release of Salsi Sync.

= 1.0.0 =
* Initial release of Salsi Sync.
* Added product sync, image sync, custom data mapping, update checking, and custom templates.

== Upgrade Notice ==

= 1.0.0 =
Initial release.

== Support ==
For support, please visit [your support forum link or website].

== License ==
This plugin is licensed under the GPLv2 or later license.
