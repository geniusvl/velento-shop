<?php
/**
 * Velento Shop — About Us page.
 * Template Name: درباره ما
 */
if (!defined('ABSPATH')) exit;

get_header();

$home_url  = home_url('/');
$shop_url  = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/');
$contact_phone = '۰۲۱-۸۸۷۷ ۴۶۲۰';
$contact_mobile = '۰۹۱۲-۷۴۰ ۸۳۱۶';
$contact_email = 'hello@velento.ir';
$contact_address = 'تهران، خیابان فرشته، مجتمع پارسیان، طبقه ۳';
?>

<main id="primary" class="site-main velento-about" dir="rtl">

    <section class="about-intro">
        <div class="about-container about-intro-grid">
            <div>
                <span class="about-overline">01 / داستان ما</span>
                <h2>انتخابی برای <em>همیشه.</em></h2>
            </div>
            <div class="about-intro-copy">
                <p class="about-lead">
                    ما ولنتو را با یک ایده ساده شروع کردیم: خرید یک ساعت خوب باید به اندازه خود ساعت،
                    دقیق، مطمئن و لذت‌بخش باشد.
                </p>
                <p>
                    از انتخاب مدل‌ها تا تجربه خرید و پشتیبانی، همه چیز در ولنتو با وسواس طراحی شده است.
                    هدف ما ساختن مجموعه‌ای است که در آن طراحی کلاسیک، جزئیات مدرن و تجربه‌ای متفاوت کنار هم قرار بگیرند.
                </p>
            </div>
        </div>
    </section>

    <section class="about-values" id="why-velento">
        <div class="about-container">
            <div class="about-section-heading">
                <div>
                    <span class="about-overline">02 / چرا ولنتو؟</span>
                    <h2>بیشتر از یک <em>انتخاب.</em></h2>
                </div>
                <p>جزئیات کوچک، تجربه‌ای بزرگ می‌سازند.</p>
            </div>

            <div class="about-value-grid">
                <article class="about-value-card">
                    <span class="about-number">01</span>
                    <div class="about-value-icon">✦</div>
                    <h3>اصالت و اطمینان</h3>
                    <p>هر سفارش با تمرکز روی اصالت، سلامت کالا و اطلاعات شفاف محصول بررسی و آماده ارسال می‌شود.</p>
                </article>
                <article class="about-value-card">
                    <span class="about-number">02</span>
                    <div class="about-value-icon">◌</div>
                    <h3>انتخاب دقیق</h3>
                    <p>کالکشن ولنتو با نگاه به طراحی، کیفیت ساخت و ماندگاری انتخاب می‌شود؛ نه صرفاً بر اساس ترندهای کوتاه‌مدت.</p>
                </article>
                <article class="about-value-card">
                    <span class="about-number">03</span>
                    <div class="about-value-icon">⌁</div>
                    <h3>تجربه شخصی</h3>
                    <p>برای انتخاب مدل مناسب، قبل و بعد از خرید کنار شما هستیم تا تصمیم‌گیری ساده‌تر و مطمئن‌تر باشد.</p>
                </article>
                <article class="about-value-card">
                    <span class="about-number">04</span>
                    <div class="about-value-icon">↗</div>
                    <h3>بسته‌بندی و ارسال</h3>
                    <p>سفارش‌ها با دقت آماده می‌شوند تا تجربه باز کردن بسته، بخشی از حس لوکس خرید شما باشد.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="about-quote">
        <div class="about-container">
            <div class="about-quote-mark">“</div>
            <blockquote>
                بعضی چیزها برای یک لحظه ساخته نمی‌شوند؛
                <span>برای ماندن ساخته می‌شوند.</span>
            </blockquote>
            <p>— فلسفه ولنتو</p>
        </div>
    </section>

    <section class="about-history">
        <div class="about-container">
            <div class="about-section-heading">
                <div>
                    <span class="about-overline">03 / مسیر ما</span>
                    <h2>داستانی که از <em>علاقه</em> شروع شد.</h2>
                </div>
            </div>

            <div class="about-timeline">
                <article class="about-timeline-item">
                    <span class="about-timeline-year">۱۳۹۸</span>
                    <div>
                        <span class="about-timeline-index">A / THE BEGINNING</span>
                        <h3>یک علاقه، یک ایده</h3>
                        <p>ولنتو با هدف ساختن یک تجربه متفاوت برای علاقه‌مندان ساعت شکل گرفت؛ کوچک، دقیق و با وسواس روی جزئیات.</p>
                    </div>
                </article>
                <article class="about-timeline-item">
                    <span class="about-timeline-year">۱۴۰۰</span>
                    <div>
                        <span class="about-timeline-index">B / THE COLLECTION</span>
                        <h3>اولین کالکشن</h3>
                        <p>با گسترش مجموعه، تمرکز ما روی انتخاب مدل‌هایی قرار گرفت که در کنار زیبایی، شخصیت و ماندگاری داشته باشند.</p>
                    </div>
                </article>
                <article class="about-timeline-item">
                    <span class="about-timeline-year">۱۴۰۳</span>
                    <div>
                        <span class="about-timeline-index">C / THE EXPERIENCE</span>
                        <h3>ولنتو، یک تجربه کامل‌تر</h3>
                        <p>فروشگاه آنلاین توسعه پیدا کرد تا جست‌وجو، مقایسه، انتخاب و دریافت ساعت، ساده‌تر و حرفه‌ای‌تر از همیشه باشد.</p>
                    </div>
                </article>
                <article class="about-timeline-item is-current">
                    <span class="about-timeline-year">امروز</span>
                    <div>
                        <span class="about-timeline-index">D / NEXT CHAPTER</span>
                        <h3>ادامه دارد...</h3>
                        <p>ما هنوز در حال ساختن فصل بعدی ولنتو هستیم؛ با کالکشن‌های تازه و تجربه‌ای که هر روز بهتر می‌شود.</p>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section class="about-numbers">
        <div class="about-container about-number-grid">
            <div><strong>+۱۲۰۰</strong><span>ساعت در کالکشن</span></div>
            <div><strong>+۸۵۰</strong><span>مشتری راضی</span></div>
            <div><strong>۹۸٪</strong><span>رضایت از تجربه خرید</span></div>
            <div><strong>۷</strong><span>روز پشتیبانی در هفته</span></div>
        </div>
    </section>

    <section class="about-contact">
        <div class="about-container about-contact-grid">
            <div class="about-contact-copy">
                <span class="about-overline">04 / با ما در ارتباط باشید</span>
                <h2>هنوز سوالی <em>هست؟</em></h2>
                <p>برای انتخاب ساعت، پیگیری سفارش یا دریافت راهنمایی، با ما در ارتباط باشید.</p>
                <a class="about-btn about-btn-gold" href="<?php echo esc_url($shop_url); ?>">ورود به فروشگاه <span>←</span></a>
            </div>

            <div class="about-contact-card">
                <div class="about-contact-row">
                    <span>تلفن</span>
                    <a href="tel:+982188774620"><?php echo esc_html($contact_phone); ?></a>
                </div>
                <div class="about-contact-row">
                    <span>همراه</span>
                    <a href="tel:+989127408316"><?php echo esc_html($contact_mobile); ?></a>
                </div>
                <div class="about-contact-row">
                    <span>ایمیل</span>
                    <a href="mailto:<?php echo esc_attr($contact_email); ?>"><?php echo esc_html($contact_email); ?></a>
                </div>
                <div class="about-contact-row">
                    <span>آدرس</span>
                    <address><?php echo esc_html($contact_address); ?></address>
                </div>
                <div class="about-contact-hours">
                    <span>شنبه تا پنجشنبه</span>
                    <strong>۱۰:۰۰ تا ۲۰:۰۰</strong>
                </div>
            </div>
        </div>
    </section>

    <section class="about-final">
        <div class="about-container">
            <span class="about-kicker"><i></i> VELENTO <i></i></span>
            <h2>لحظه‌ها می‌گذرند.<br><em>سبک شما می‌ماند.</em></h2>
            <a href="<?php echo esc_url($shop_url); ?>" class="about-btn about-btn-outline">کالکشن ولنتو <span>←</span></a>
        </div>
    </section>

</main>

<?php get_footer(); ?>
