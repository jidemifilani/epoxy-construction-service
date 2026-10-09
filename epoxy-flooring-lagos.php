<?php
/** LANDING - Epoxy flooring in Lagos. Target: local search and Lagos-area adverts. */

require_once __DIR__ . '/includes/landing.php';

landing_render([
    'slug'        => 'epoxy-flooring-lagos',
    'trail'       => 'Epoxy flooring Lagos',
    'eyebrow'     => 'Epoxy flooring in Lagos',
    'title'       => 'Epoxy Flooring Lagos | Specialist Installers for Homes & Businesses',
    'description' => 'Epoxy flooring in Lagos by a specialist crew: garages, warehouses, showrooms and decorative floors. Diamond-ground prep, written warranty, free site visit.',
    'keywords'    => 'epoxy flooring Lagos, epoxy floor installation Lagos, epoxy contractor Lagos, epoxy flooring Lekki, epoxy flooring Ikeja, epoxy floor Victoria Island',
    'h1'          => 'Epoxy Flooring in Lagos, Installed by a Specialist Crew.',
    'lede'        => 'We coat garage, warehouse, showroom and home floors across Lagos and the South-West. Every job starts with diamond grinding, ends with a written warranty, and is quoted line by line.',
    'section_title' => 'Why Lagos Floors Choose Us',
    'benefits'    => [
        'Diamond-ground preparation on every job. No acid etching, no shortcuts.',
        'Fixed-price, line-itemed quote within 48 hours of the site visit.',
        cfg('warranty_years') . '-year written warranty on adhesion, delamination and finish.',
        'In-house crews. The team that quotes is the team that installs.',
        'Work scheduled around your business hours, with night shifts for larger sites.',
    ],
    'steps'       => [
        ['Free site visit', 'We measure, check the slab for moisture and cracks, and talk through how the space is used.'],
        ['Written quote', 'Within 48 hours: system, finish, timeline and warranty, priced line by line.'],
        ['Prep and coat', 'Diamond grinding, crack repair, primer, base coat, decorative layer and sealed topcoat.'],
        ['Handover', 'Walkthrough, care instructions and your written warranty.'],
    ],
    'faqs'        => [
        ['How much does epoxy flooring cost in Lagos?', 'It depends on the space, the condition of the floor and the system chosen. The epoxy floor cost guide gives the current ranges, and a site visit gives you the exact price.'],
        ['How long does an epoxy floor take to install?', 'A standard garage takes three to four days, including the cure time before light use. Larger commercial floors are scheduled to suit your operation.'],
        ['Can you coat over an existing floor?', 'Sometimes. We test adhesion and moisture on site before quoting, and we tell you clearly if the existing surface has to come up first.'],
    ],
    'form_title'  => 'Get Your Free Lagos Quote',
    'space_options' => ['Garage', 'Home interior', 'Basement', 'Warehouse or factory', 'Showroom or retail', 'Office', 'Other'],
    'cta_label'   => 'Request My Free Quote',
]);
