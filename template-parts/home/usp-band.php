<?php
/**
 * Template Part: Trust / Services Band
 *
 * A short row of the promises luxury watch buyers care most about
 * (authenticity, secure shipping, warranty, support). Sits between the
 * product carousel and the newsletter section on the homepage.
 */

if (!defined('ABSPATH')) {
    exit;
}

$velento_usps = [
    [
        'title' => __('ضمانت اصالت', 'velento-shop'),
        'desc'  => __('هر ساعت پیش از ارسال توسط کارشناسان ما احراز اصالت می‌شود.', 'velento-shop'),
        'icon'  => 'badge',
    ],
    [
        'title' => __('ارسال ایمن', 'velento-shop'),
        'desc'  => __('بسته‌بندی بیمه‌شده و ارسال مطمئن به سراسر کشور.', 'velento-shop'),
        'icon'  => 'box',
    ],
    [
        'title' => __('ضمانت‌نامه معتبر', 'velento-shop'),
        'desc'  => __('گارانتی رسمی روی تمامی محصولات موجود در گالری.', 'velento-shop'),
        'icon'  => 'shield',
    ],
    [
        'title' => __('پشتیبانی اختصاصی', 'velento-shop'),
        'desc'  => __('مشاوره‌ی تخصصی پیش و پس از خرید، همیشه در دسترس شما.', 'velento-shop'),
        'icon'  => 'headset',
    ],
];

$velento_usp_icons = [
    'badge'   => '<svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="9" r="6"></circle><path d="M8.5 14.5 7 21l5-2.4 5 2.4-1.5-6.5"></path></svg>',
    'box'     => '<svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3.5 8 12 3.5 20.5 8 12 12.5 3.5 8Z"></path><path d="M3.5 8v8.5L12 21l8.5-4.5V8"></path><path d="M12 12.5V21"></path></svg>',
    'shield'  => '<svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3.5 19.5 6.5V11.5C19.5 16 16.5 19.7 12 21C7.5 19.7 4.5 16 4.5 11.5V6.5L12 3.5Z"></path><path d="m9 12 2 2 4-4.3"></path></svg>',
    'headset' => '<svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4.5 13.5v-2a7.5 7.5 0 0 1 15 0v2"></path><rect x="3.5" y="13" width="4" height="6" rx="1.4"></rect><rect x="16.5" y="13" width="4" height="6" rx="1.4"></rect><path d="M19.5 19.5v.5a3 3 0 0 1-3 3h-3"></path></svg>',
];
?>

<section class="usp-band" aria-label="<?php echo esc_attr__('خدمات و تضمین‌های ولنتو شاپ', 'velento-shop'); ?>">
    <div class="container usp-grid">
        <?php foreach ($velento_usps as $usp) : ?>
            <div class="usp-item">
                <span class="usp-icon" aria-hidden="true"><?php echo $velento_usp_icons[$usp['icon']]; ?></span>
                <h3 class="usp-title"><?php echo esc_html($usp['title']); ?></h3>
                <p class="usp-desc"><?php echo esc_html($usp['desc']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</section>
