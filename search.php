<?php
/**
 * Velento Shop — Search results.
 *
 * The header search form and the search overlay's "مشاهده همه نتایج"
 * link both always search products only (post_type=product is forced
 * either way), so this template renders the same shop grid/toolbar/
 * pagination as archive-product.php — via the shared
 * template-parts/shop/product-results.php partial — filtered to only
 * the products WordPress's own search query already matched. No brand
 * tabs here since they don't apply to a filtered result set.
 */
if (!defined('ABSPATH')) exit;
get_header();

$search_term = get_search_query();
?>
<main id="primary" class="site-main velento-shop-page velento-search-page">
    <section class="v-shop-hero">
        <div class="v-shop-wrap v-shop-hero-content">
            <span class="v-shop-overline"><?php esc_html_e('نتایج جستجو', 'velento-shop'); ?></span>
            <h1>
                <?php
                printf(
                    /* translators: %s: the searched term */
                    esc_html__('نتایج جستجو برای «%s»', 'velento-shop'),
                    esc_html($search_term)
                );
                ?>
            </h1>
        </div>
    </section>

    <?php
    get_template_part('template-parts/shop/product-results', null, [
        'show_brand_filter' => false,
        'empty_message'     => sprintf(
            /* translators: %s: the searched term */
            __('برای «%s» نتیجه‌ای پیدا نشد.', 'velento-shop'),
            $search_term
        ),
    ]);
    ?>
</main>
<?php get_footer(); ?>
