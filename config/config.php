<?php
/**
 * Iris Epoxy Service - configuration.
 *
 * These are the DEFAULTS. Once the site is installed, most of these values
 * can be edited live in the admin dashboard (Settings), which stores overrides
 * in the database. A value set there wins over the one in this file.
 *
 * Only the database connection and the two constants below must be set here,
 * because they are needed before the database can be reached.
 */

// Root-relative path the site is served from. '' when it is the domain root.
define('BASE_URL', '/iris-epoxy-service');

// Canonical origin, used for absolute URLs in schema, sitemap and OG tags.
define('SITE_ORIGIN', 'http://localhost');

return [

    // --- Database (XAMPP defaults; change for the live server) ----------
    'db' => [
        'host'     => '127.0.0.1',
        'port'     => 3306,
        'name'     => 'iris_epoxy_service',
        'user'     => 'root',
        'password' => '',
        'charset'  => 'utf8mb4',
    ],

    // --- Identity ------------------------------------------------------
    'company_name'   => 'Iris Epoxy Service',
    'company_short'  => 'Iris Epoxy',
    'tagline'        => 'Epoxy Flooring & Epoxy Sales',
    'founding_year'  => 2013,
    'founder_name'   => 'Daniel Adeyemi',

    // --- Numbers that appear in the trust bar and headlines ------------
    'years'               => 12,
    'projects_completed'  => 480,
    'warranty_years'      => 10,
    'review_count'        => 210,

    // --- Location ------------------------------------------------------
    'city'           => 'Lagos',
    'state'          => 'Lagos State',
    'service_area'   => 'Lagos and the South-West',
    'service_cities' => ['Lagos Island', 'Lekki & Ajah', 'Ikeja & Mainland', 'Ibadan', 'Abeokuta'],
    'service_radius' => 120, // km
    'office_address' => '14 Admiralty Way, Lekki Phase 1, Lagos',

    // --- Contact -------------------------------------------------------
    'phone'            => '+234 801 234 5678',
    'whatsapp'         => '+234 801 234 5678',
    'email'            => 'hello@irisepoxyservice.com',
    'commercial_email' => 'projects@irisepoxyservice.com',
    'trade_email'      => 'trade@irisepoxyservice.com',
    'hours_weekday'    => 'Monday - Friday: 8:00 AM - 6:00 PM',
    'hours_saturday'   => 'Saturday: 9:00 AM - 2:00 PM',
    'hours_sunday'     => 'Sunday: Closed (emergency enquiries via WhatsApp)',
    'hours_short'      => 'Mon-Sat, 8am-6pm',

    // --- Credentials ---------------------------------------------------
    'cert_1'         => 'ISO 9001',
    'cert_2'         => 'NIBS',
    'manufacturer_1' => 'Sika',
    'manufacturer_2' => 'Mapei',
    'insurance_cover'=> 'N250,000,000',
    'jurisdiction'   => 'Lagos State',

    // --- Pricing (residential table) -----------------------------------
    'currency'            => 'N',
    'price_garage_1_low'  => '480,000',
    'price_garage_1_high' => '720,000',
    'price_garage_2_low'  => '850,000',
    'price_garage_2_high' => '1,350,000',
    'rate_basement'       => '14,500',
    'rate_patio'          => '16,000',
    'rate_metallic'       => '32,000',
    'range_residential'   => 'N480,000 - N1,350,000 per space',
    'range_commercial'    => 'N9,500 - N22,000 per m2',
    'range_decorative'    => 'N28,000 per m2 and up',

    // --- Commercial terms ----------------------------------------------
    'night_shift_threshold' => 800, // m2 above which night shifts carry no premium

    // --- Shop -----------------------------------------------------------
    'delivery_fee'        => 15000,                  // flat delivery charge, naira
    'free_delivery_above' => 500000,                 // orders at or above this ship free
    'bank_name'           => 'First Bank of Nigeria', // placeholder, set in admin
    'bank_account_name'   => 'Iris Epoxy Service Ltd',
    'bank_account_number' => '0000000000',
    'google_review_url'   => '',   // your Google review link, used in the post-delivery message
    'prices_reviewed_on'  => '',   // date the prices were last checked, shown on the cost guide
    'order_terms'         => 'Orders are confirmed by phone or WhatsApp once payment is received. Wholesale drums are dispatched within 3 working days of confirmation.',

    // --- Lead handling --------------------------------------------------
    // Enquiries are stored in the database and shown in the admin dashboard.
    // Set 'notify' to true once a mail transport is configured on the host;
    // on XAMPP mail() has no relay, so it stays off locally.
    'notify'       => false,
    'notify_to'    => 'hello@irisepoxyservice.com',

    // --- WhatsApp alerts (optional) -------------------------------------
    // Sends each new quote request, enquiry and order to the owner's WhatsApp.
    // Needs a Meta WhatsApp Business Cloud API app. Leave these empty until it
    // exists; the owner's number is set in Settings > Enquiry alerts.
    'whatsapp' => [
        'access_token'      => '',          // permanent system-user token from Meta
        'phone_number_id'   => '',          // the business number's ID in WhatsApp Manager
        'graph_version'     => 'v21.0',
        'template'          => 'new_enquiry',
        'template_language' => 'en',
    ],

    // --- Search and advertising -----------------------------------------
    // Coordinates for the office. The default is approximate for Lekki Phase 1,
    // Lagos: confirm the exact point on the Google Business Profile map pin.
    'geo_lat' => '6.4474',
    'geo_lng' => '3.4726',

    // Leave any of these empty until the account exists. Nothing is loaded
    // for an empty value.
    'ga4_measurement_id'          => '',   // e.g. G-XXXXXXXXXX
    'google_ads_id'               => '',   // e.g. AW-XXXXXXXXX
    'google_ads_conversion_label' => '',   // from Google Ads > Conversions
    'meta_pixel_id'               => '',   // Meta Events Manager
    'google_site_verification'    => '',   // Google Search Console token
    'bing_site_verification'      => '',   // Bing Webmaster Tools token

    // --- Social (blank entries are simply not rendered) -----------------
    'social' => [
        'Instagram' => '',
        'Facebook'  => '',
        'LinkedIn'  => '',
    ],
];
