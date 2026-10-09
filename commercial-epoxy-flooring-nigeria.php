<?php
/** LANDING - Commercial and industrial epoxy flooring across Nigeria. Target: facility and procurement searches. */

require_once __DIR__ . '/includes/landing.php';

landing_render([
    'slug'        => 'commercial-epoxy-flooring-nigeria',
    'trail'       => 'Commercial epoxy flooring Nigeria',
    'eyebrow'     => 'Commercial and industrial',
    'title'       => 'Commercial Epoxy Flooring Nigeria | Warehouses, Factories & Plants',
    'description' => 'Commercial and industrial epoxy flooring for warehouses, factories, food plants and showrooms across Nigeria. Phased installs, night shifts, documented handover.',
    'keywords'    => 'commercial epoxy flooring Nigeria, industrial epoxy flooring Nigeria, warehouse epoxy Lagos, factory floor epoxy Nigeria, epoxy mortar flooring Nigeria, food plant epoxy Nigeria',
    'h1'          => 'Commercial Epoxy Flooring for Warehouses, Factories and Plants.',
    'lede'        => 'Forklift traffic, chemicals and wash-downs are hard on a floor. We specify the system for your process, install it in phases around your operation, and hand over the documents your auditors ask for.',
    'section_title' => 'Built for the Way Your Facility Runs',
    'benefits'    => [
        'System chosen for your process: chemical, abrasion, thermal and hygiene requirements.',
        'Phased or night-shift installs to protect production and dispatch.',
        'Line marking and safety zones installed under the topcoat, so they do not wear off.',
        'Documentation for audits: product data sheets, application record and warranty.',
        cfg('warranty_years') . '-year written warranty, with a single named project lead.',
    ],
    'steps'       => [
        ['Site survey', 'Slab moisture test, surface profile check and a walk through your process.'],
        ['Specification and phased scope', 'The system, the sequence and the shifts, agreed in writing before work starts.'],
        ['Installation', 'Dust-contained grinding, bay by bay, with the area handed back ready for use.'],
        ['Documented handover', 'Warranty, data sheets and the application record, in one project folder.'],
    ],
    'faqs'        => [
        ['How long does a warehouse floor take?', 'It depends on the area and the number of shifts. A 3,000 m2 warehouse is commonly done over two weekends, bay by bay, so operations keep running.'],
        ['Which system do we need for a food plant?', 'Food and wash-down areas need a seamless, antimicrobial system with coved skirtings. We confirm the exact specification after a site survey.'],
        ['Do you provide documents for ISO or HACCP audits?', 'Yes. Each job includes product data sheets, the application record and the warranty, so the file is ready for your auditor.'],
    ],
    'form_title'  => 'Request a Commercial Site Survey',
    'space_options' => ['Warehouse', 'Factory or workshop', 'Food or beverage plant', 'Showroom or retail', 'Loading bay', 'Other'],
    'space_label' => 'Type of facility',
    'cta_label'   => 'Request a Site Survey',
]);
