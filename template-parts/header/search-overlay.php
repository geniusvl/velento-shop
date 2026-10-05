<?php if (!defined('ABSPATH')) exit; ?>

<div class="search-overlay" id="velento-search-overlay" role="search" aria-hidden="true">
    <form class="search-overlay-form" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
        <?php if (function_exists('is_shop')) : ?>
            <input type="hidden" name="post_type" value="product">
        <?php endif; ?>
        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" class="search-overlay-icon">
            <circle cx="10.5" cy="10.5" r="6.5"></circle>
            <line x1="20" y1="20" x2="15.3" y2="15.3"></line>
        </svg>
        <input
            type="search"
            class="search-overlay-input"
            name="s"
            placeholder="<?php echo esc_attr__('جستجوی محصولات، ساعت‌ها…', 'velento-shop'); ?>"
            autocomplete="off"
        >
        <button type="button" class="search-overlay-close" aria-label="<?php echo esc_attr__('بستن جستجو', 'velento-shop'); ?>">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                <line x1="5" y1="5" x2="19" y2="19"></line>
                <line x1="19" y1="5" x2="5" y2="19"></line>
            </svg>
        </button>
    </form>

    <div class="search-overlay-body" data-search-body>

        <ul class="search-results" data-search-results hidden></ul>

        <template data-search-view-all-template>
            <a class="search-view-all">
                <?php esc_html_e('مشاهده همه نتایج', 'velento-shop'); ?>
                <span aria-hidden="true">←</span>
            </a>
        </template>

        <div class="search-panel" data-search-recent-panel hidden>
            <div class="search-panel-head">
                <h3><?php esc_html_e('جستجوهای اخیر', 'velento-shop'); ?></h3>
                <button type="button" class="search-panel-clear" data-search-clear-recent>
                    <?php esc_html_e('پاک کردن', 'velento-shop'); ?>
                </button>
            </div>
            <ul class="search-recent-list" data-search-recent-list></ul>
        </div>

        <div class="search-panel" data-search-viewed-panel hidden>
            <div class="search-panel-head">
                <h3><?php esc_html_e('بازدیدهای اخیر', 'velento-shop'); ?></h3>
                <button type="button" class="search-panel-clear" data-search-clear-viewed>
                    <?php esc_html_e('پاک کردن', 'velento-shop'); ?>
                </button>
            </div>
            <ul class="search-viewed-list" data-search-viewed-list></ul>
        </div>

    </div>
</div>
