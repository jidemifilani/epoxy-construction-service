<?php
/**
 * LOCATION - epoxy flooring in a named place. Reached as /epoxy-flooring/<slug>
 * through the rewrite in .htaccess. Places are defined in includes/locations.php.
 */

require_once __DIR__ . '/includes/landing.php';
require_once __DIR__ . '/includes/locations.php';

$slug = isset($_GET['slug']) && is_string($_GET['slug']) ? trim($_GET['slug'], '/') : '';
$location = $slug === '' ? null : location_find($slug);

if ($location === null) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    exit;
}

$name      = $location['name'];
$uses      = $location['uses'];
$threshold = (int) cfg('night_shift_threshold', 800);

$usesHtml = '<ul>';
foreach ($uses as $use) {
    $usesHtml .= '<li>' . e($use) . '</li>';
}
$usesHtml .= '</ul>';

$body = '<p class="note">' . e(location_coverage_line($location)) . '</p>'
      . '<h2>Common projects in ' . e($name) . '</h2>' . $usesHtml
      . '<p>Every project starts with the same preparation we use everywhere: diamond grinding, a moisture check and crack repair. The system is then chosen for how the space is used.</p>';

$faqs = [
    ['Do you work in ' . $name . '?', location_coverage_line($location)],
    ['How do I get a price for ' . $name . '?', 'Send the type of space and an approximate size using the form on this page. We reply within one business day, and we confirm the next step, whether that is a visit or a quote from the photos.'],
    ['Can you work outside normal hours?', 'Night and weekend work is available on larger projects, above ' . number_format($threshold) . ' m&sup2;, at no premium, subject to access on site.'],
];

$typeWord = match ($location['schema']) {
    'State' => 'state',
    'City'  => 'city',
    default => 'area',
};

landing_render([
    'slug'          => 'epoxy-flooring/' . $location['slug'],
    'trail'         => 'Epoxy flooring in ' . $name,
    'eyebrow'       => 'Epoxy flooring in ' . $name,
    'title'         => 'Epoxy Flooring in ' . $name . ' | Iris Epoxy',
    'description'   => 'Epoxy flooring in ' . $name . ' for ' . strtolower(implode(', ', array_slice($uses, 0, 2))) . '. Diamond-ground prep, a written quote and a free site visit.',
    'keywords'      => 'epoxy flooring ' . $name . ', epoxy floor installation ' . $name . ', epoxy contractor ' . $name . ', epoxy resin ' . $name,
    'h1'            => 'Epoxy Flooring in ' . $name . '.',
    'lede'          => $location['lede'],
    'section_title' => 'What We Quote in ' . $name,
    'body'          => $body,
    'benefits'      => [
        'Diamond-ground preparation on every job, not acid etching.',
        'A fixed, line-itemed quote in writing before work starts.',
        cfg('warranty_years') . '-year written warranty on adhesion, delamination and finish.',
        'In-house crews. The team that quotes is the team that installs.',
    ],
    'steps'         => [
        ['Tell us the space', 'Use the form with the type of space and an approximate size, or send photos on WhatsApp.'],
        ['Site visit', 'We measure, check the slab for moisture and cracks, and agree the system and finish.'],
        ['Written quote', 'A line-itemed quote within 48 hours of the visit, with the timeline and warranty.'],
    ],
    'faqs'          => $faqs,
    'form_title'    => 'Get a Quote for ' . $name,
    'space_options' => ['Garage', 'Home interior', 'Basement', 'Warehouse or factory', 'Showroom or retail', 'Office', 'Other'],
    'cta_label'     => 'Request a Quote',
    'schema'        => [[
        '@type'      => 'Service',
        '@id'        => absolute_url('epoxy-flooring/' . $location['slug']) . '#service',
        'name'       => 'Epoxy flooring in ' . $name,
        'serviceType' => 'Epoxy flooring installation',
        'description' => $location['lede'],
        'provider'   => ['@id' => absolute_url() . '#business'],
        'areaServed' => ['@type' => $location['schema'], 'name' => $name],
        'url'        => absolute_url('epoxy-flooring/' . $location['slug']),
    ]],
    'canonical'     => $location['canonical'] ?? null,
]);
