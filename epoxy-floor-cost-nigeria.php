<?php
/**
 * LANDING - Epoxy floor cost in Nigeria. A pricing guide, which is what most
 * people search for before they search for a contractor. The ranges come from
 * Settings, so this page never disagrees with the quotes you send.
 */

require_once __DIR__ . '/includes/landing.php';

$body = '<p>Epoxy floors in Nigeria are priced per space or per square metre. The ranges below are typical for our work. Your exact price depends on the condition of the floor, the system and the finish, so it is confirmed after a site visit. Prices last checked: ' . e((string) cfg('prices_reviewed_on', 'not yet recorded')) . '.</p>'
      . '<table class="price-table"><thead><tr><th scope="col">Space</th><th scope="col">Typical range</th></tr></thead><tbody>'
      . '<tr><td>Single garage</td><td>' . e(cfg('currency') . cfg('price_garage_1_low')) . ' to ' . e(cfg('currency') . cfg('price_garage_1_high')) . '</td></tr>'
      . '<tr><td>Double garage</td><td>' . e(cfg('currency') . cfg('price_garage_2_low')) . ' to ' . e(cfg('currency') . cfg('price_garage_2_high')) . '</td></tr>'
      . '<tr><td>Basement, per m&sup2;</td><td>' . e(cfg('currency') . cfg('rate_basement')) . '</td></tr>'
      . '<tr><td>Patio or pool deck, per m&sup2;</td><td>' . e(cfg('currency') . cfg('rate_patio')) . '</td></tr>'
      . '<tr><td>Metallic decorative, per m&sup2;</td><td>' . e(cfg('currency') . cfg('rate_metallic')) . '</td></tr>'
      . '<tr><td>Commercial, per m&sup2;</td><td>' . e(cfg('range_commercial')) . '</td></tr>'
      . '</tbody></table>'
      . '<h2>What drives the price</h2>'
      . '<ul><li><strong>Preparation.</strong> Grinding, crack repair and moisture work take the most time. A low quote that leaves them out is the most common reason floors fail.</li>'
      . '<li><strong>The system.</strong> Flake and solid colour cost less than metallic or 3D. A polyaspartic topcoat costs more and cures faster.</li>'
      . '<li><strong>Area and access.</strong> Larger areas cost less per metre. Moving vehicles and furniture, and difficult access, add to the price.</li></ul>'
      . '<h2>What a fair quote includes</h2>'
      . '<ul><li>Preparation, primer, base coat, decorative layer and topcoat, each priced</li>'
      . '<li>Cleaning up and protecting the area</li>'
      . '<li>The written warranty and what it covers</li>'
      . '<li>A timeline, and the cure time before light use</li></ul>'
      . '<p>If one quote is far below the others, ask which of those lines it leaves out.</p>';

landing_render([
    'slug'        => 'epoxy-floor-cost-nigeria',
    'trail'       => 'Epoxy floor cost Nigeria',
    'eyebrow'     => 'Pricing guide',
    'title'       => 'Epoxy Floor Cost in Nigeria | Garage, Warehouse & Decorative Prices',
    'description' => 'How much epoxy flooring costs in Nigeria: garage, basement, warehouse and decorative price ranges, what drives the price, and what a fair quote includes.',
    'keywords'    => 'epoxy floor cost Nigeria, epoxy flooring price Nigeria, how much is epoxy flooring in Nigeria, garage epoxy cost Lagos, epoxy floor price per square metre Nigeria',
    'h1'          => 'Epoxy Floor Cost in Nigeria, Explained.',
    'lede'        => 'Typical ranges for garages, basements, decorative floors and commercial work, what changes the price, and what a fair quote should include.',
    'section_title' => 'Typical Prices and What Sets Them',
    'body'        => $body,
    'benefits'    => [
        'Prices are confirmed after a free site visit, in writing.',
        'Every quote is line-itemed so you can compare like with like.',
        'Deposits and milestone billing are agreed before work starts.',
    ],
    'steps'       => [
        ['Tell us the space', 'Use the form with the type of space and an approximate size, or send a photo on WhatsApp.'],
        ['We visit and measure', 'We test the slab and confirm the system and the finish.'],
        ['Fixed quote in writing', 'Within 48 hours, priced line by line.'],
    ],
    'faqs'        => [
        ['Is epoxy flooring worth the cost?', 'For a garage or a busy floor, a properly prepared epoxy usually lasts many years, where a paint coating often needs redoing within a few years. The cost per year is usually lower.'],
        ['Why are some quotes much cheaper?', 'Almost always because the preparation is cut back. Ask each contractor how they prepare the surface and whether the price includes it.'],
        ['Do you take a deposit?', 'Residential jobs usually have a deposit with the balance on completion. Commercial work is billed in agreed milestones.'],
    ],
    'form_title'  => 'Get a Price for Your Space',
    'space_options' => ['Garage', 'Basement', 'Patio or pool deck', 'Home interior', 'Warehouse or factory', 'Showroom', 'Other'],
    'cta_label'   => 'Get My Price',
]);
