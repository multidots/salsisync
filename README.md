# Salsi Sync Plugin
![banner-772x250](https://github.com/user-attachments/assets/5302a5a6-43e0-4086-9df3-fd2cbd12c864)

WordPress Plugin for [Multidots](https://www.multidots.com/)
Salsi Sync is a powerful plugin that enables WooCommerce site owners to synchronize their products from the Salsify API to WooCommerce effortlessly. This plugin simplifies product updates, image synchronization, custom data mappings, and more, providing a complete integration solution between Salsify and WooCommerce.

## Features

1. **Product Sync from Salsify to WooCommerce:**
   Fetch product data directly from the Salsify API and insert it into WooCommerce as WooCommerce-compatible products.
   
2. **Image Synchronization:**
Syncs both featured images and gallery images for products, ensuring your WooCommerce store displays complete and visually consistent product data.

3. **Custom Data Mapping:**
Customize product data mapping to display unique fields from Salsify API data, ensuring WooCommerce products are enriched with additional, tailored information.

4. **On-Demand Update Checks:**
Manually check for product updates from the Salsify API, allowing you to keep WooCommerce products current with the latest data from Salsify.

5. **Custom WooCommerce Product Templates:**
Display custom product templates on the frontend, tailored specifically for WooCommerce products synced from Salsify.

With Salsi Sync, managing and synchronizing your WooCommerce product data becomes streamlined, efficient, and tailored to meet your store’s unique needs.

### Requirements

`Salsi Sync Pluign` requires the following dependencies:

-   [Node.js](https://nodejs.org/)
-   [NVM](https://wptraining.md10x.com/lessons/install-nvm/)

## Installation

1. Upload the `salsisync` directory to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Go to the **Salsi Sync** settings panel to configure API credentials and other plugin settings.
4. Use the **Bulk Insert** option to add all products from Salsify to WooCommerce or sync updates as needed.

### Frequently Asked Questions

**How do I configure the Salsify API?**
In the plugin settings, you can enter your Salsify API credentials, including API token and organization key, to connect with Salsify.

**How does the custom data mapping work?**
Custom data mapping allows you to map specific fields from the Salsify API data to custom WooCommerce fields, so additional information from Salsify is displayed on your WooCommerce products.

**Will this plugin overwrite existing WooCommerce products?**
No, the plugin is designed to skip existing products if they are already synced unless you choose to update them. Only new or updated products from Salsify will be inserted.


### External services
This plugin connects to the Salsify API to sync product data. It sends product information and API keys when syncing data.
Service: Salsify API
Terms of Service: https://www.salsify.com/legal/terms-of-service
Privacy Policy: https://www.salsify.com/privacy-policy


### Changelog

1.0.0
* Initial release of Salsi Sync.
* Added product sync, image sync, custom data mapping, update checking, and custom templates.

### Upgrade Notice

1.0.0
Initial release.

## Credits
Salsi Sync is developed by Multidots. We appreciate the contributions from the open-source community.

### Support
For support, please visit [your support forum link or website].

### License
This plugin is licensed under the GPLv2 or later license.

## See potential here?
<a href="https://www.multidots.com/contact-us/" rel="nofollow"><img width="1692" height="296" alt="01-GitHub Footer" src="https://github.com/user-attachments/assets/6b9d63e7-3990-472d-acb9-5e4e51b446fc" /></a>
