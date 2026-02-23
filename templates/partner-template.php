<?php

include_once plugin_dir_path(__FILE__) . 'blank-header.php';

$post_id = get_queried_object_id();
$logo = get_field('logo', $post_id);
$logo_type = get_field('logo_type', $post_id);
$company = get_the_title($post_id);
$discount = get_field('discount_percent', $post_id);
$partner_code = get_field('partner_code', $post_id);
$base = get_field('base_prices', $post_id);
$discounted = get_field('discounted_prices', $post_id);
$portal_url_register = get_field('portal_url_register', $post_id);
$portal_url_login = get_field('portal_url_login', $post_id);
?>
<div class="allowed__sections">
    <!-- Header -->
    <header>
        <div class="logos">
            <div class="logo-gdpr"><img src="<?php echo plugin_dir_url( __FILE__ ); ?>../assets/images/gdprlogowhite.webp" alt="GDPRLocal Logo"></div>
            <div class="logo-divider"></div>
            <div class="logo-partner-group-wrap">
                <div class="logo-gdpr">
                    <img src="<?php echo esc_html ($logo); ?>" alt="<?php echo esc_html ($company); ?> Logo">
                </div>
                <div>
                    <div class="logo-partner"><?php echo esc_html ($company); ?> </div>
                    <div class="partner-badge">Official Partner</div>
                </div>
            </div>
        </div>
        <div class="header-right">
            <a href="<?php echo esc_url($portal_url_login); ?>?partnerCode=<?php echo esc_html($partner_code); ?>" class="btn-outline">Sign In</a>
            <a href="<?php echo esc_url($portal_url_register); ?>?partnerCode=<?php echo esc_html($partner_code); ?>" class="btn-solid">Sign Up</a>
        </div>
    </header>
    <div class="banner">
        All <strong><?php echo esc_html ($company); ?> </strong> clients receive <strong><?php echo esc_html ($discount); ?>% discount</strong>.
    </div>

    <!-- Hero -->
    <section class="hero">
        <h1><em><?php echo esc_html ($company); ?> </em> is an official partner of GDPRLocal</h1>
        <p style="color:var(--muted);font-size:15px;margin-top:6px;font-weight:600;">Choose your data protection representative service below.</p>
        <div class="discount-chip">🎉 Exclusive <?php echo esc_html ($discount); ?>% partner discount applied</div>
    </section>

    <!-- Billing Toggle -->
    <div class="toggle-wrap">
        <span class="toggle-label active" id="lbl-monthly">Monthly</span>
        <label class="toggle-switch">
            <input type="checkbox" id="billingToggle" />
            <span class="slider"></span>
        </label>
        <span class="toggle-label" id="lbl-yearly">Yearly <span class="save-badge">Save <?php echo esc_html ($discount); ?>%</span></span>
    </div>

    <!-- Cards -->
    <div class="cards">

        <!-- Card 1: EU Rep -->
        <div class="card">
            <div class="card-title">Article 27 EU Representative</div>
            <p class="card-desc">Fast setup, instant access to your Written Agreement and our EU GDPR Rep service.</p>

            <div class="price-row">
                <span class="price-orig"
                    id="eu-orig"
                    price-original-monthly="<?php echo esc_attr($base['eu_monthly']); ?>"
                    price-original-yearly="<?php echo esc_attr($base['eu_yearly']); ?>">
                    £<?php echo esc_html($base['eu_monthly']); ?><small>/mo</small>
                </span>

                <span class="price-curr"
                    id="eu-curr"
                    price-discounted-monthly="<?php echo esc_attr($discounted['eu_monthly']); ?>"
                    price-discounted-yearly="<?php echo esc_attr($discounted['eu_yearly']); ?>">
                    <span class="sym">£</span>
                    <span class="price-value"><?php echo esc_html($discounted['eu_monthly']); ?></span>
                </span>

                <span class="price-period">/mo</span>
            </div>

            <div class="cta-group">
                <a href="<?php echo esc_url($portal_url_register); ?>?partnerCode=<?php echo esc_html($partner_code); ?>" class="cta-primary">Sign Up</a>
                <a href="<?php echo esc_url($portal_url_login); ?>?partnerCode=<?php echo esc_html($partner_code); ?>" class="cta-secondary">Sign In</a>
            </div>
        </div>

        <!-- Card 2: UK Rep -->
        <div class="card">
            <div class="card-title">Article 27 UK Representative</div>
            <p class="card-desc">Required if you're outside the UK and handle data or offer services to UK citizens.</p>

            <div class="price-row">
                <span class="price-orig"
                    id="uk-orig"
                    price-original-monthly="<?php echo esc_attr($base['uk_monthly']); ?>"
                    price-original-yearly="<?php echo esc_attr($base['uk_yearly']); ?>">
                    £<?php echo esc_html($base['uk_monthly']); ?><small>/mo</small>
                </span>

                <span class="price-curr"
                    id="uk-curr"
                    price-discounted-monthly="<?php echo esc_attr($discounted['uk_monthly']); ?>"
                    price-discounted-yearly="<?php echo esc_attr($discounted['uk_yearly']); ?>">
                    <span class="sym">£</span>
                    <span class="price-value"><?php echo esc_html($discounted['uk_monthly']); ?></span>
                </span>

                <span class="price-period">/mo</span>
            </div>

            <div class="cta-group">
                <a href="<?php echo esc_url($portal_url_register); ?>?partnerCode=<?php echo esc_html($partner_code); ?>" class="cta-primary">Sign Up</a>
                <a href="<?php echo esc_url($portal_url_login); ?>?partnerCode=<?php echo esc_html($partner_code); ?>" class="cta-secondary">Sign In</a>
            </div>
        </div>

    </div>

    <footer>
        © <?php echo date('Y'); ?> GDPRLocal Ltd · Partner: <?php echo esc_html ($company); ?>  (Code: <?php echo esc_html($partner_code); ?>)
    </footer>
</div>

<?php include_once plugin_dir_path(__FILE__) . 'blank-footer.php'; ?>