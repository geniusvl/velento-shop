<?php
/**
 * Template Name: برندها
 *
 * Curated brand-story showcase (rebuilt per Phase: brand page redesign).
 * Content is a hand-curated array rather than the velento_brand taxonomy
 * loop the previous version used, since the copy here (history, "who
 * it's for") is editorial writing, not taxonomy data. Each row still
 * links out to that brand's real product archive when a matching term
 * exists in this install, so the page stays connected to the shop
 * instead of being a dead-end gallery.
 */
if (!defined('ABSPATH')) exit;
get_header();

$velento_brand_stories = [
    [
        'slug'     => 'hublot',
        'name'     => 'هابلو',
        'name_en'  => 'HUBLOT',
        'origin'   => 'سوئیس · تأسیس ۱۹۸۰',
        'history'  => 'هابلو با فلسفه‌ی «هنر همجوشی» پا به عرصه گذاشت؛ ترکیب موادی که تا پیش از آن هرگز کنار هم دیده نشده بودند، از طلا و لاستیک تا سرامیک و کربن. اولین ساعت سوئیسی با بند لاستیکی محصول همین برند بود؛ جسارتی که هابلو را به همراه همیشگی ورزش‌های بزرگ جهانی از جمله فوتبال تبدیل کرد.',
        'audience' => 'برای کسانی که از قاعده‌ی «همیشه کلاسیک» عبور کرده‌اند و ساعت را بخشی از هویت پرانرژی و امروزی خود می‌دانند.',
    ],
    [
        'slug'     => 'citizen',
        'name'     => 'سیتیزن',
        'name_en'  => 'CITIZEN',
        'origin'   => 'ژاپن · تأسیس ۱۹۱۸',
        'history'  => 'سیتیزن با فناوری اکو-درایو، ساعتی ساخت که تنها با نور — طبیعی یا مصنوعی — شارژ می‌شود و عملاً نیازی به تعویض باتری ندارد. این نگاه عملگرا به مهندسی، سیتیزن را در طول یک قرن به یکی از قابل‌اعتمادترین نام‌های ساعت‌سازی ژاپن تبدیل کرده است.',
        'audience' => 'برای افرادی با ذهنیت عملگرا که دقت روزمره، دوام، و هوشمندی فنی را به زرق‌وبرق ترجیح می‌دهند.',
    ],
    [
        'slug'     => 'patek-philippe',
        'name'     => 'پاتک فیلیپ',
        'name_en'  => 'PATEK PHILIPPE',
        'origin'   => 'ژنو، سوئیس · تأسیس ۱۸۳۹',
        'history'  => 'قدیمی‌ترین کارخانه ساعت‌سازی مستقلِ خانوادگی جهان، از ۱۸۳۹ تاکنون تنها به دست همان خانواده اداره می‌شود. جمله‌ی معروف این برند همه‌چیز را می‌گوید: «شما هرگز مالک واقعی یک پاتک فیلیپ نیستید؛ فقط آن را برای نسل بعدی نگه می‌دارید.»',
        'audience' => 'برای کلکسیونرها و کسانی که ساعت را نه یک خرید، بلکه سرمایه‌ای برای نسل بعد می‌بینند.',
    ],
    [
        'slug'     => 'audemars-piguet',
        'name'     => 'آدمار پیگه',
        'name_en'  => 'AUDEMARS PIGUET',
        'origin'   => 'لو براسوس، سوئیس · تأسیس ۱۸۷۵',
        'history'  => 'در سال ۱۹۷۲، آدمار پیگه با «رویال اوک» طراحی جرالد جنتا، مفهوم ساعت لوکس ورزشی فولادی را بنیان گذاشت؛ ایده‌ای که در زمان خود جسورانه و بحث‌برانگیز بود و امروز یکی از تأثیرگذارترین طرح‌های تاریخ ساعت‌سازی محسوب می‌شود.',
        'audience' => 'برای صاحبان سبک با سلیقه‌ی متمایز، که پیچیدگی فنی را در قالبی جسورانه و متفاوت می‌خواهند.',
    ],
    [
        'slug'     => 'رولکس',
        'name'     => 'رولکس',
        'name_en'  => 'ROLEX',
        'origin'   => 'ژنو، سوئیس · تأسیس ۱۹۰۵',
        'history'  => 'رولکس در سال ۱۹۲۶ با «اویستر»، اولین ساعت مچی ضدآب جهان را معرفی کرد و از آن پس با مکانیزم خودکار Perpetual، به مترادف دقت و اعتبار پایدار تبدیل شده است؛ نامی که فراتر از ساعت، نماد دستاورد شناخته می‌شود.',
        'audience' => 'برای لحظاتی که باید برای همیشه به یاد بمانند؛ انتخابی همیشگی برای نقاط عطف مهم زندگی.',
    ],
    [
        'slug'     => 'امگا',
        'name'     => 'امگا',
        'name_en'  => 'OMEGA',
        'origin'   => 'بیل، سوئیس · تأسیس ۱۸۴۸',
        'history'  => 'امگا اسپیدمستر در سال ۱۹۶۹ نخستین ساعتی بود که روی کره ماه به مچ بشر بسته شد و از سال ۱۹۳۲ همراه رسمی بازی‌های المپیک است؛ تاریخچه‌ای که امگا را با دقت اثبات‌شده در سخت‌ترین شرایط ممکن گره زده است.',
        'audience' => 'برای علاقه‌مندان به اکتشاف، ورزش، و دستاوردهای بزرگ بشری که دقتی آزموده‌شده می‌خواهند.',
    ],
];

/**
 * Look up a real velento_brand term by name so the CTA can send visitors
 * to an actual filtered product archive instead of a dead link, without
 * hard-failing if the taxonomy or term doesn't exist on this install.
 */
if (!function_exists('velento_brand_story_link')) {
    function velento_brand_story_link($name) {
        if (!taxonomy_exists('velento_brand')) {
            return function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/');
        }
        $term = get_term_by('name', $name, 'velento_brand');
        if ($term && !is_wp_error($term)) {
            // Keep brand discovery inside the real WooCommerce Shop archive.
            // The theme's Shop page already knows how to apply the brand
            // taxonomy filter and progressively enhance it with AJAX. This
            // avoids sending visitors to a taxonomy archive that may not have
            // a matching template in the current installation.
            $shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/');
            return add_query_arg(['brand[]' => $term->slug], $shop_url);
        }
        return function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/');
    }
}
?>
<main id="primary" class="site-main velento-brands">
    <section class="vb-hero">
        <div class="vb-container">
            <span class="vb-eyebrow">VELENTO / MAISONS</span>
            <h1 class="vb-reveal">نام‌هایی که <em>زمان را تعریف کرده‌اند.</em></h1>
            <p class="vb-hero-lead vb-reveal">شش خاندان ساعت‌سازی، شش نگاه متفاوت به دقت، جسارت و میراث — هرکدام برای سلیقه‌ای متفاوت.</p>
        </div>
    </section>

    <section class="vb-list">
        <?php foreach ($velento_brand_stories as $i => $brand) :
            $logo = get_template_directory_uri() . '/assets/images/brands/logos/' . $brand['slug'] . '.webp';
            $num  = sprintf('%02d', $i + 1);
        ?>
        <article class="vb-row vb-reveal" style="--vb-delay: <?php echo esc_attr($i % 3 * 90); ?>ms">
            <div class="vb-container vb-row-grid">
                <div class="vb-media">
                    <span class="vb-num"><?php echo esc_html($num); ?></span>
                    <div class="vb-plate">
                        <img src="<?php echo esc_url($logo); ?>" alt="<?php echo esc_attr($brand['name'] . ' logo'); ?>" loading="lazy" width="720" height="400">
                    </div>
                </div>
                <div class="vb-text">
                    <span class="vb-origin"><?php echo esc_html($brand['origin']); ?></span>
                    <h2><?php echo esc_html($brand['name']); ?> <small><?php echo esc_html($brand['name_en']); ?></small></h2>
                    <p class="vb-history"><?php echo esc_html($brand['history']); ?></p>
                    <p class="vb-audience"><span>مناسب چه کسانی؟</span> <?php echo esc_html($brand['audience']); ?></p>
                    <a class="vb-cta" href="<?php echo esc_url(velento_brand_story_link($brand['name'])); ?>">
                        مشاهده مجموعه <?php echo esc_html($brand['name']); ?>
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                </div>
            </div>
        </article>
        <?php endforeach; ?>
    </section>

    <section class="vb-closing vb-reveal">
        <div class="vb-container">
            <p>هر برند در ولنتو با گارانتی اصالت و مشاوره‌ی تخصصی همراه است.</p>
            <a class="vb-cta vb-cta-outline" href="<?php echo esc_url(function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/')); ?>">مشاهده همه محصولات</a>
        </div>
    </section>
</main>
<?php get_footer(); ?>
