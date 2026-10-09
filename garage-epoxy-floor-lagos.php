<?php
/** LANDING - Garage epoxy floor in Lagos. Target: garage and home-owner searches. */

require_once __DIR__ . '/includes/landing.php';

landing_render([
    'slug'        => 'garage-epoxy-floor-lagos',
    'trail'       => 'Garage epoxy floor Lagos',
    'eyebrow'     => 'Garage epoxy floors',
    'title'       => 'Garage Epoxy Floor Lagos | Oil-Resistant, Hard-Wearing Finish',
    'description' => 'Garage epoxy floors in Lagos that resist oil, hot tyres and daily traffic. Diamond-ground prep, flake or solid finish, ' . cfg('warranty_years') . '-year written warranty. Free visit.',
    'keywords'    => 'garage epoxy floor Lagos, garage floor coating Lagos, epoxy garage floor Nigeria, garage epoxy Lekki, flake garage floor Lagos, garage floor paint alternative Nigeria',
    'h1'          => 'A Garage Floor That Handles Cars, Oil and Hot Tyres.',
    'lede'        => 'Epoxy turns a dusty, stained garage into a floor you can hose down. Flake, solid-colour and metallic finishes, installed in about four days.',
    'section_title' => 'What a Garage Epoxy Floor Gives You',
    'benefits'    => [
        'Resists oil, brake fluid, petrol spills and hot tyres.',
        'Seamless: no cracks to trap dirt, and it cleans with a mop.',
        'Flake, solid colour or metallic finish, matched to your home.',
        'A polyaspartic topcoat is available if you need the garage back in 48 hours.',
        cfg('warranty_years') . '-year written warranty on adhesion, delamination and finish.',
    ],
    'steps'       => [
        ['Free visit', 'We check the concrete for moisture and cracks, and measure the garage.'],
        ['Fixed quote', 'Line-itemed within 48 hours: prep, system, finish, timeline and warranty.'],
        ['Day 1 to 3', 'Diamond grinding, crack repair and primer on day one; base and finish on day two; topcoat on day three.'],
        ['Day 4', 'Walkthrough, care instructions and your written warranty. Light use at 24 hours, parking at 7 days.'],
    ],
    'faqs'        => [
        ['When can I park on the new floor?', 'You can walk on it after 24 hours and park after about 7 days. With a polyaspartic topcoat, vehicles can return after roughly 48 hours.'],
        ['Will it peel under hot tyres?', 'Not if the surface is prepared correctly. Peeling almost always comes from skipped preparation, which is why we grind every garage to the correct profile.'],
        ['Can you work around my cars and tools?', 'Yes. We move them, work, and return them. The move is included in the quote.'],
    ],
    'form_title'  => 'Get Your Free Garage Quote',
    'space_options' => ['Single garage', 'Double garage', 'Carport', 'Garage with workshop', 'Other'],
    'cta_label'   => 'Request My Garage Quote',
]);
