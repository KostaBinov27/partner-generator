<?php
/*
Plugin Name: GDPRLocal Partner Landing Pages
Description: Creates custom partner landing pages with unique URLs and discounts. Provides a secure REST API for partner management.
Version: 1.0
Author: Kosta Binov
*/

if (!defined('ABSPATH')) exit;

/* =========================================================
   CONFIGURATION
========================================================= */

// Change this to your secret API key
define('GDPRLOCAL_API_KEY', 'my-secure-api-key-123');

add_action('admin_notices', function () {

    if (!class_exists('ACF')) {
        ?>
        <div class="notice notice-error">
            <p><strong>GDPRLocal:</strong> Advanced Custom Fields (<a target="_blank" href="https://wordpress.org/plugins/advanced-custom-fields/">Free</a> or <a target="_blank" href="https://www.advancedcustomfields.com/pro/">PRO</a>) plugin is required.</p>
        </div>
        <?php
    }
});

if (!class_exists('ACF')) {
    return; // Stop plugin execution early
}

/* =========================================================
   REGISTER CUSTOM POST TYPE (NO SLUG PREFIX)
========================================================= */

add_action('init', function () {

    register_post_type('partner', [
        'label' => 'Partners',
        'public' => true,
        'show_in_rest' => false,
        'rewrite' => false, // remove /partner/
        'supports' => ['title'],
        'menu_icon' => 'dashicons-groups'
    ]);
});


/* =========================================================
   FIX PERMALINK DISPLAY IN DASHBOARD
========================================================= */

add_filter('post_type_link', function ($post_link, $post) {
    if ($post->post_type === 'partner') {
        return home_url('/' . $post->post_name);
    }
    return $post_link;
}, 10, 2);

add_filter('get_sample_permalink', function ($permalink, $post_id, $title, $name, $post) {
    if ($post->post_type === 'partner') {
        $slug = $name ? $name : sanitize_title($title);
        return [home_url('/' . $slug), $slug];
    }
    return $permalink;
}, 10, 5);


/* =========================================================
   REGISTER ACF FIELD GROUPS
========================================================= */

add_action('acf/init', function () {

    acf_add_local_field_group([
        'key' => 'group_partner_fields',
        'title' => 'Partner Information',
        'fields' => [
            [
                'key' => 'field_logo',
                'label' => 'Logo',
                'name' => 'logo',
                'type' => 'image',
                'required' => 1,
                'return_format' => 'url',
                'preview_size' => 'medium',
            ],
            [
                'key' => 'field_logo_type',
                'label' => 'Logo Type',
                'name' => 'logo_type',
                'type' => 'text',
                'required' => 1,
            ],
            [
                'key' => 'field_portal_url_register',
                'label' => 'Portal URL Register',
                'name' => 'portal_url_register',
                'type' => 'text',
                'required' => 1,
            ],
            [
                'key' => 'field_portal_url_login',
                'label' => 'Portal URL Login',
                'name' => 'portal_url_login',
                'type' => 'text',
                'required' => 1,
            ],
            [
                'key' => 'field_landing_page_url',
                'label' => 'Landing Page URL (Slug)',
                'name' => 'landing_page_url',
                'type' => 'text',
                'required' => 1,
                'instructions' => 'The unique URL slug for this partner (auto-generated from API)',
                'readonly' => 1,
            ],
            [
                'key' => 'field_partner_code',
                'label' => 'Partner Code',
                'name' => 'partner_code',
                'type' => 'text',
                'required' => 1,
                'instructions' => 'Unique partner code',
            ],
            [
                'key' => 'field_discount_percent',
                'label' => 'Discount Percent',
                'name' => 'discount_percent',
                'type' => 'number',
                'required' => 1,
                'min' => 0,
                'max' => 100,
                'step' => 1,
                'append' => '%',
            ],
            [
                'key' => 'field_base_prices',
                'label' => 'Base Prices',
                'name' => 'base_prices',
                'type' => 'group',
                'layout' => 'table',
                'sub_fields' => [
                    [
                        'key' => 'field_base_eu_monthly',
                        'label' => 'EU Monthly',
                        'name' => 'eu_monthly',
                        'type' => 'number',
                        'required' => 1,
                        'min' => 0,
                        'step' => 0.01,
                        'prepend' => '€',
                    ],
                    [
                        'key' => 'field_base_eu_yearly',
                        'label' => 'EU Yearly',
                        'name' => 'eu_yearly',
                        'type' => 'number',
                        'required' => 1,
                        'min' => 0,
                        'step' => 0.01,
                        'prepend' => '€',
                    ],
                    [
                        'key' => 'field_base_uk_monthly',
                        'label' => 'UK Monthly',
                        'name' => 'uk_monthly',
                        'type' => 'number',
                        'required' => 1,
                        'min' => 0,
                        'step' => 0.01,
                        'prepend' => '£',
                    ],
                    [
                        'key' => 'field_base_uk_yearly',
                        'label' => 'UK Yearly',
                        'name' => 'uk_yearly',
                        'type' => 'number',
                        'required' => 1,
                        'min' => 0,
                        'step' => 0.01,
                        'prepend' => '£',
                    ],
                ],
            ],
            [
                'key' => 'field_discounted_prices',
                'label' => 'Discounted Prices',
                'name' => 'discounted_prices',
                'type' => 'group',
                'layout' => 'table',
                'sub_fields' => [
                    [
                        'key' => 'field_disc_eu_monthly',
                        'label' => 'EU Monthly',
                        'name' => 'eu_monthly',
                        'type' => 'number',
                        'required' => 1,
                        'min' => 0,
                        'step' => 0.01,
                        'prepend' => '€',
                    ],
                    [
                        'key' => 'field_disc_eu_yearly',
                        'label' => 'EU Yearly',
                        'name' => 'eu_yearly',
                        'type' => 'number',
                        'required' => 1,
                        'min' => 0,
                        'step' => 0.01,
                        'prepend' => '€',
                    ],
                    [
                        'key' => 'field_disc_uk_monthly',
                        'label' => 'UK Monthly',
                        'name' => 'uk_monthly',
                        'type' => 'number',
                        'required' => 1,
                        'min' => 0,
                        'step' => 0.01,
                        'prepend' => '£',
                    ],
                    [
                        'key' => 'field_disc_uk_yearly',
                        'label' => 'UK Yearly',
                        'name' => 'uk_yearly',
                        'type' => 'number',
                        'required' => 1,
                        'min' => 0,
                        'step' => 0.01,
                        'prepend' => '£',
                    ],
                ],
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'partner',
                ],
            ],
        ],
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
    ]);
});


/* =========================================================
   CUSTOM REWRITE RULE ( /slug )
========================================================= */

add_action('init', function () {

    add_rewrite_rule(
        '^([^/]+)/?$',
        'index.php?partner_slug=$matches[1]',
        'top'
    );
});

add_filter('query_vars', function ($vars) {
    $vars[] = 'partner_slug';
    return $vars;
});

add_action('template_redirect', function () {

    $slug = get_query_var('partner_slug');
    if (!$slug) return;

    $post = get_page_by_path($slug, OBJECT, 'partner');
    if ($post) {
        // Set up global post data so get_the_ID() and other template tags work
        global $wp_query;
        $wp_query->query_vars['post_type'] = 'partner';
        $wp_query->is_singular = true;
        $wp_query->is_single = true;
        $wp_query->queried_object = $post;
        $wp_query->queried_object_id = $post->ID;
        $wp_query->post = $post;

        setup_postdata($post);

        include plugin_dir_path(__FILE__) . 'templates/partner-template.php';

        wp_reset_postdata();
        exit;
    }
});


/* =========================================================
   REST API WITH AUTHENTICATION
========================================================= */

add_action('rest_api_init', function () {

    register_rest_route('gdprlocal/v1', '/partner', [
        'methods' => 'POST',
        'callback' => 'gdprlocal_create_partner',
        'permission_callback' => 'gdprlocal_api_auth'
    ]);
});

function gdprlocal_api_auth($request)
{
    $key = $request->get_header('X-GDPRLOCAL-KEY');

    if ($key !== GDPRLOCAL_API_KEY) {
        return new WP_Error('forbidden', 'Invalid API Key', ['status' => 403]);
    }

    return true;
}


/* =========================================================
   CREATE PARTNER
========================================================= */

function gdprlocal_create_partner($request)
{
    $data = $request->get_json_params();

    /* ===============================
       REQUIRED FIELDS
    =============================== */

    $required = ['Logo', 'LogoType', 'LandingPageUrl', 'CompanyName', 'PartnerCode', 'DiscountPercent', 'BasePrices', 'DiscountedPrices', 'PortalUrlRegister', 'PortalUrlLogin'];

    foreach ($required as $field) {
        if (empty($data[$field])) {
            return new WP_Error('missing_field', "$field is required", ['status' => 400]);
        }
    }

    /* ===============================
       VALIDATIONS
    =============================== */

    // Discount
    if (!is_numeric($data['DiscountPercent']) || $data['DiscountPercent'] < 0 || $data['DiscountPercent'] > 100) {
        return new WP_Error('invalid_discount', 'Discount must be between 0–100', ['status' => 400]);
    }

    // LogoType
    if (empty($data['LogoType'])) {
        return new WP_Error('invalid_logo_type', 'LogoType cannot be empty', ['status' => 400]);
    }

    if (empty($data['PortalUrlRegister'])) {
        return new WP_Error('invalid_portal_url_register', 'PortalUrlRegister cannot be empty', ['status' => 400]);
    }

    if (empty($data['PortalUrlLogin'])) {
        return new WP_Error('invalid_portal_url_login', 'PortalUrlLogin cannot be empty', ['status' => 400]);
    }

    // Slug
    $slug = sanitize_title($data['LandingPageUrl']);

    if (get_page_by_path($slug, OBJECT, 'partner')) {
        return new WP_Error('duplicate_slug', 'LandingPageUrl already exists', ['status' => 400]);
    }

    // PartnerCode uniqueness
    $existing = get_posts([
        'post_type' => 'partner',
        'meta_key' => 'partner_code',
        'meta_value' => $data['PartnerCode']
    ]);

    if ($existing) {
        return new WP_Error('duplicate_code', 'PartnerCode already exists', ['status' => 400]);
    }

    // Validate prices
    foreach ($data['BasePrices']['Article27'] as $price) {
        if (!is_numeric($price) || $price < 0) {
            return new WP_Error('invalid_price', 'Base prices must be positive', ['status' => 400]);
        }
    }

    foreach ($data['DiscountedPrices']['Article27'] as $price) {
        if (!is_numeric($price) || $price < 0) {
            return new WP_Error('invalid_price', 'Discounted prices must be positive', ['status' => 400]);
        }
    }

    /* ===============================
       VALIDATE BASE64 IMAGE
    =============================== */

    if (!preg_match('/^data:image\/(png|jpeg);base64,/', $data['Logo'], $matches)) {
        return new WP_Error('invalid_logo', 'Logo must be Base64 PNG or JPG', ['status' => 400]);
    }

    $mime_type = $matches[1] === 'png' ? 'image/png' : 'image/jpeg';
    $extension = $matches[1] === 'png' ? 'png' : 'jpg';

    $base64 = preg_replace('/^data:image\/(png|jpeg);base64,/', '', $data['Logo']);
    $base64 = str_replace(' ', '+', $base64);

    $decoded = base64_decode($base64);

    if (!$decoded) {
        return new WP_Error('invalid_logo', 'Invalid Base64 image data', ['status' => 400]);
    }

    /* ===============================
       CREATE PARTNER POST
    =============================== */

    $post_id = wp_insert_post([
        'post_title' => sanitize_text_field($data['CompanyName']),
        'post_name'  => $slug,
        'post_status' => 'publish',
        'post_type'  => 'partner'
    ]);

    if (is_wp_error($post_id)) {
        return $post_id;
    }

    /* ===============================
       SAVE IMAGE TO MEDIA LIBRARY
    =============================== */

    $upload = wp_upload_bits(
        'partner-logo-' . time() . '.' . $extension,
        null,
        $decoded
    );

    if ($upload['error']) {
        return new WP_Error('upload_error', $upload['error'], ['status' => 500]);
    }

    $attachment = [
        'post_mime_type' => $mime_type,
        'post_title'     => sanitize_text_field($data['CompanyName']) . ' Logo',
        'post_status'    => 'inherit'
    ];

    $attach_id = wp_insert_attachment($attachment, $upload['file'], $post_id);

    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
    wp_update_attachment_metadata($attach_id, $attach_data);

    /* ===============================
       SAVE ACF FIELDS
    =============================== */

    update_field('logo', $attach_id, $post_id);
    update_field('logo_type', $data['LogoType'], $post_id);
    update_field('landing_page_url', $slug, $post_id);
    update_field('partner_code', sanitize_text_field($data['PartnerCode']), $post_id);
    update_field('discount_percent', $data['DiscountPercent'], $post_id);
    update_field('portal_url_register', esc_url($data['PortalUrlRegister']), $post_id);
    update_field('portal_url_login', esc_url($data['PortalUrlLogin']), $post_id);

    update_field('base_prices', [
        'eu_monthly' => $data['BasePrices']['Article27']['EuMonthly'],
        'eu_yearly' => $data['BasePrices']['Article27']['EuYearly'],
        'uk_monthly' => $data['BasePrices']['Article27']['UkMonthly'],
        'uk_yearly' => $data['BasePrices']['Article27']['UkYearly'],
    ], $post_id);

    update_field('discounted_prices', [
        'eu_monthly' => $data['DiscountedPrices']['Article27']['EuMonthly'],
        'eu_yearly' => $data['DiscountedPrices']['Article27']['EuYearly'],
        'uk_monthly' => $data['DiscountedPrices']['Article27']['UkMonthly'],
        'uk_yearly' => $data['DiscountedPrices']['Article27']['UkYearly'],
    ], $post_id);

    /* ===============================
       FLUSH PERMALINKS
    =============================== */

    flush_rewrite_rules();

    return [
        'success' => true,
        'url' => home_url('/' . $slug)
    ];
}

// Enqueue scripts and styles
add_action('wp_enqueue_scripts', function () {
    if (is_singular('partner')) {
        wp_enqueue_style('gdprlocal-partners-style', plugin_dir_url(__FILE__) . 'assets/css/style.css');
        wp_enqueue_script('gdprlocal-partners-script', plugin_dir_url(__FILE__) . 'assets/js/scripts.js', [], false, true);
    }
});
