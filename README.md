# WP Brand Display

WordPress plugin that displays brand images above the price on WooCommerce single product pages.

## Description

WP Brand Display is a simple and flexible plugin that automatically displays product brand images on single product pages, right above the price. The plugin supports various WooCommerce brand taxonomies and methods of storing brand images.

## Features

- ✅ Automatic display of brand image above price on single product page
- ✅ Support for various brand taxonomies (product_brand, pa_brand, yith_product_brand, pwb-brand)
- ✅ Support for different image storage methods (term meta, ACF fields)
- ✅ Configurable settings (taxonomy, image size, maximum width)
- ✅ Option to link image to brand page
- ✅ Automatic detection of brand taxonomy if not found in settings
- ✅ Responsive design

## Requirements

- WordPress 5.0 or higher
- PHP 7.2 or higher
- WooCommerce plugin (activated)

## Installation

1. Upload the `wp-brand-display.php` file to the `/wp-content/plugins/` directory
2. Activate the plugin through the WordPress admin panel (Plugins → Installed Plugins)
3. Go to Settings → Brand Display to configure settings

## Configuration

### Admin Settings

After activation, go to **Settings → Brand Display** to configure:

1. **Brand Taxonomy**: Select the taxonomy used for product brands
   - Supported taxonomies: product_brand, pa_brand, yith_product_brand, pwb-brand, and others
   - Plugin will automatically try to find brand if not configured

2. **Image Size**: Brand image size
   - Options: Thumbnail, Medium, Large, Full

3. **Max Width (px)**: Maximum image width in pixels
   - Default: 200px

4. **Link to Brand Page**: Enable linking brand image to brand page
   - If enabled, clicking the image will navigate to brand archive page

## Supported Brand Taxonomies

The plugin automatically detects and supports:

- **product_brand** - Standard WooCommerce brand taxonomy
- **pa_brand** - WooCommerce product attribute
- **yith_product_brand** - YITH WooCommerce Brands Add-on
- **pwb-brand** - Perfect WooCommerce Brands
- **Other custom taxonomies** - Any public taxonomy for products

## Supported Image Storage Methods

The plugin attempts to find brand images using the following methods:

1. **Term Meta `thumbnail_id`** - Standard method for WooCommerce taxonomies
2. **Term Meta `brand_image`** - Custom meta key
3. **Term Meta `image`** - Alternative meta key
4. **Term Meta `term_image`** - Alternative meta key
5. **Term Meta `brand_logo`** - Alternative meta key
6. **ACF Field `brand_image`** - Advanced Custom Fields field

## How It Works

1. Plugin automatically activates when WooCommerce is installed
2. On single product page, plugin searches for product brand through configured taxonomy
3. If brand is not found, it tries alternative taxonomies
4. When brand is found, it searches for image through various meta keys
5. Image is displayed above product price (priority 5, before price which is priority 10)

## Styling

The plugin adds basic CSS styles automatically. You can customize styles by adding custom CSS to your theme:

```css
.wp-brand-display {
    margin-bottom: 20px;
}

.wp-brand-display img {
    max-width: 100%;
    height: auto;
}
```

## Troubleshooting

### Brand image is not displaying

1. Check if product is assigned to brand taxonomy
2. Check if brand has an image set
3. Check settings in Settings → Brand Display
4. Check if WooCommerce is activated

### Plugin is not working

- Check if WooCommerce plugin is activated
- Check WordPress error log for errors
- Check if your theme supports WooCommerce hooks

## Support

For issues or questions, check:
- WordPress error log file
- WooCommerce status page
- PHP error log

## License

GPL v2 or later

## Author

Bojan Pavlovic
