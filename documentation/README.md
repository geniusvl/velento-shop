# Velento Shop Documentation

## Overview
Velento Shop is a premium RTL WooCommerce WordPress theme designed for luxury product stores.

## Requirements
- WordPress 6.x+
- PHP 8.1+
- WooCommerce latest stable version
- MySQL 5.7+ / MariaDB 10+
- Elementor (free plugin) — optional, only needed for the 22 custom "ولنتو شاپ" page-builder widgets and the color-palette bridge described below

## Installation
1. Upload the theme ZIP from WordPress Dashboard.
2. Activate the theme.
3. Install and activate WooCommerce.
4. Configure your store settings.
5. Import/create products.

## Recommended Setup
### Permalinks
Go to:
Settings → Permalinks → Post name

### WooCommerce Pages
Create:
- Shop
- Cart
- Checkout
- My Account

## Customization
You can customize:
- Logo
- Menus
- Colors
- Products
- Pages
- Widgets

## Product Setup
For each product add:
- Product title
- Description
- Featured image
- Gallery images
- Price
- Categories
- Attributes (for variable products)

## RTL Support
The theme is designed for Persian RTL websites.

## Translation
Use standard WordPress translation tools with the theme text domain.

## Support Checklist
Before contacting support provide:
- WordPress version
- PHP version
- WooCommerce version
- Error message screenshot

## Changelog
See CHANGELOG.md

## Shop Filters

The Shop archive includes AJAX product discovery filters for:
- Price range
- Brand
- Category
- Registered attributes such as strap, movement, movement count, gender, material, color and water resistance (only when the matching WooCommerce attribute exists)
- Stock status
- Sale products

The filter system is progressive: the initial archive is server-rendered and remains usable without JavaScript. With JavaScript enabled, filter changes are debounced, superseded requests are aborted, and the current state is reflected in the URL.

## Elementor Page Builder (optional)

If Elementor is installed and active, the theme registers a "ولنتو شاپ"
widget category with 22 store widgets and syncs its color palette into
Elementor's Global Colors. See `elementor-widgets-guide-fa.md` for the
Persian guide aimed at store owners.

## Commercial QA

Before marketplace submission, test the theme on staging with the target WordPress and WooCommerce versions, including:
- Shop filters and pagination
- Variable products and add-to-cart
- Cart and checkout
- My Account endpoints
- Product reviews
- WooCommerce HPOS
- WooCommerce blocks where used by the store
- Mobile/tablet/desktop layouts
