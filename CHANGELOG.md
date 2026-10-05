## 0.7.3
- Removed the Shop price-range filter section and its related frontend/AJAX state handling.
- Preserved price-based sorting and all other product filters.

## 0.7.1
- Fixed dual price-range interaction and visual selected range.
- Added focused single-product tabs for description, specifications, and reviews with accessible keyboard navigation and hash state.

# Changelog

## 0.8.2
- Mobile header: fixed the logo colliding with/centering into the icon cluster on phones (header-container switched from a 3-column grid with an empty reserved nav track to a 2-item flex, so icons stay left and the logo sits right, with nothing to collide with).
- Mobile hamburger: bigger tap target, line→X open animation, accent active state.
- Mobile nav dropdown: slide+fade+staggered item entrance, rounded corners, dim backdrop (new `nav-active` body state) matching the search/cart/login overlays.
- Homepage category grids stay 2-up on phones instead of collapsing to one stacked column.
- Homepage hero keeps the copy and the watch showcase side by side on phones instead of stacking them.
- Cart drawer and login drawer unified into an identical mobile bottom-sheet (max 82vh, rounded top, slide up) instead of two different full-screen takeovers.

## 0.8.1
- Wired Elementor Pro Theme Builder 'single'/'archive' locations into the actual active `single-product.php` / `archive-product.php` templates.
- Added two more Elementor widgets: contact form (new `velento_contact_form` AJAX handler, wp_mail to admin email) and store map/address card — bringing the total to 22 widgets.
- Clarified in README.md / documentation/README.md that Elementor is optional and lists what it unlocks.
- Added `documentation/elementor-widgets-guide-fa.md`, a Persian guide for store owners covering every widget and both ways to apply a color palette.

## 0.8.0
- Elementor page-builder compatibility: widget category, forced asset loading in editor/preview, Theme Builder header/footer locations, full-width rendering for Elementor-built Pages.
- 20 initial custom Elementor widgets under `inc/elementor/widgets/`, all sharing a "پالت رنگی ولنتو" per-widget color-override section.
- Customizer "پالت رنگی ولنتو" expanded to 5 colors with live postMessage preview, one-way synced into Elementor's own Global Colors.

## 0.7.5
- Hardened theme-controlled inputs and AJAX/REST endpoints with strict length/type validation.
- Added comment/review and WooCommerce order-note server-side validation.
- Added per-identity login/OTP throttling and strict OTP format checks.
- Added safe storefront security response headers.
- Added request-size limits for shop filters and search history.


## 0.7.0 — Product discovery, product detail and account refinement

- Added a modular WooCommerce product-discovery filter system using real product categories, brand taxonomy and registered WooCommerce attribute taxonomies.
- Added AJAX filtering with debounce, request cancellation, nonce verification, sanitization, validation, pagination, sorting and URL state.
- Added price range, stock and sale filters plus active-filter chips and mobile off-canvas filters.
- Added accurate cached discount-percentage sorting without direct database writes.
- Refined Shop product cards with responsive product imagery, controlled whitespace, derived sale/new/bestseller/limited badges and explicit stock information.
- Refined the single-product layout with breadcrumb, balanced gallery sizing, stock quantity/low-stock messaging, WooCommerce attributes/specifications, description and reviews.
- Preserved WooCommerce variation/add-to-cart behavior and related/upsell hooks while removing duplicate default product tabs.
- Refined My Account responsive layout and reduced gold decoration to a restrained accent.
- Added functional 404, generic single-post, comments and index fallbacks instead of empty theme files.
- Connected the existing homepage newsletter form to its existing AJAX endpoint and conditionally loaded its JavaScript module.
- Removed redundant empty WooCommerce archive/single template overrides.
- Kept the existing Search/AJAX Search implementation untouched.


## 0.5.5 — Fix: "برندها" nav item never highlighted (gold underline/text)

- Root cause: the fallback nav menu's "current page" check for Brands was `is_tax('product_brand')` — a taxonomy-archive condition that's never true on this install, since Brands here is a real Page (page-brands.php), not a taxonomy archive. "درباره ما" worked because its check correctly used `is_page()`; Brands never got the equivalent check.
- Added `velento_is_brands_page()` (inc/helpers.php), mirroring the existing `velento_is_blog_page()` pattern, and used it for the fallback menu's Brands item.
- Added `velento_brands_menu_current()` (inc/setup.php), mirroring the existing blog-menu current-item filter, so a *real* admin-created menu assigned to the "primary" location also correctly highlights Brands — not just the fallback menu.
- No visual/markup changes beyond the nav item now correctly receiving `.current-menu-item` (gold underline + gold text, same as every other page) when actually on the Brands page.

## 0.5.4 — Fix: brands/about page CSS & JS not loading via slug-based template

- Root cause: `is_page_template('page-brands.php')` (and the equivalent About check) only matches when a page's Template dropdown is explicitly set. WordPress's own template hierarchy will still load `page-brands.php`/`page-about.php` automatically for a page whose *slug* is `brands`/`about`, with no explicit template selected — which is exactly what the demo-imported pages do. In that case the file rendered correctly but its page-specific CSS/JS never got enqueued, since the meta-based check silently failed.
- Fixed by adding an `|| is_page('brands')` / `|| is_page('about')` fallback to every affected conditional in inc/enqueue.php, matching the pattern already used (correctly) for the blog page.
- No template, markup, or design changes — style/script loading only.

## 0.5.3 — Brands page redesign

- Rebuilt page-brands.php as a curated, editorial Maison showcase (history + "suited for" copy per brand) instead of the generic taxonomy-term grid.
- New self-contained assets/css/pages/brands.css (alternating media/text rows, scroll-reveal) — no longer depends on shop.css.
- New assets/js/pages/brands.js (progressive-enhancement scroll reveal via IntersectionObserver).
- Added cropped/optimized brand logo assets under assets/images/brands/logos/.
- Version bumped specifically so browsers/caches serving the old brands.css under the same file URL are forced to re-fetch it.

## 0.5.2

- (see git history / prior session — not separately logged here)

## 0.5.1 — Production hardening

- Added lightweight IP-based rate limiting to public authentication, OTP, registration and newsletter endpoints.
- Kept existing routes, UI, URLs and data structures unchanged.
- Added basic installation and development documentation.
- Removed no user-facing features.

## 0.5.0

- Previous Velento Shop release.
