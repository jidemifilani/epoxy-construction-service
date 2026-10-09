<?php
/**
 * LANDING - Buy epoxy resin in Nigeria. Sends buyers to the shop, and captures
 * bulk and trade enquiries that need a conversation first.
 */

require_once __DIR__ . '/includes/landing.php';

landing_render([
    'slug'        => 'buy-epoxy-resin-nigeria',
    'trail'       => 'Buy epoxy resin Nigeria',
    'eyebrow'     => 'Epoxy for sale',
    'title'       => 'Buy Epoxy Resin in Nigeria | Retail from 1 kg, Wholesale Drums',
    'description' => 'Buy epoxy resin, primer, topcoat and aggregate in Nigeria. Retail packs from 1 kg for DIY and small jobs, wholesale drums for contractors. Order online.',
    'keywords'    => 'buy epoxy resin Nigeria, epoxy resin for sale Nigeria, epoxy resin price Nigeria, epoxy primer Nigeria, epoxy drum wholesale Nigeria, epoxy floor coat Lagos',
    'h1'          => 'Buy Epoxy Resin in Nigeria. From 1 kg, or by the Drum.',
    'lede'        => 'Retail packs for home and small jobs, wholesale drums for contractors and facilities. Order online, and we confirm delivery by phone or WhatsApp.',
    'section_title' => 'Choose How You Buy',
    'body'        => '<p><strong>Retail, from 1 kg.</strong> Tins, pails and bags for garages, workshops and small floors. <strong>Wholesale, by the drum.</strong> Priced per drum for contractors and facilities.</p>'
                   . '<p><a class="btn btn--primary" href="' . e(url('shop')) . '"><span>Browse the epoxy shop</span></a></p>'
                   . '<p>Not sure how much you need? Send us the room size and we will work out the coat, primer and aggregate quantities for you.</p>',
    'benefits'    => [
        'Retail packs from 1 kg. Buy what the job needs.',
        'Wholesale drums for contractors, with trade pricing on request.',
        'Order online with a reference number, then pay by bank transfer.',
        'Delivery across Lagos and the South-West. Wholesale drums dispatch within 3 working days.',
    ],
    'steps'       => [
        ['Choose your packs', 'Pick the product and size in the shop. Add as many as you need.'],
        ['Place the order', 'Enter delivery details and get a reference number straight away.'],
        ['Confirm and pay', 'We confirm the total by phone or WhatsApp. Pay by bank transfer.'],
        ['Delivery', 'Delivered to your address, or collected by arrangement.'],
    ],
    'faqs'        => [
        ['What is the minimum order?', 'Retail is sold from 1 kg. Wholesale is sold by the drum, and the minimum order is one drum of each product.'],
        ['Do you sell the products without installing them?', 'Yes. Many customers buy the materials and a DIY kit. We can advise on quantities, and we are happy to answer product questions before you buy.'],
        ['Can you deliver outside Lagos?', 'We deliver across Lagos and the South-West. For other locations, contact us for a delivery quote.'],
    ],
    'form_title'  => 'Ask About Bulk or Trade Supply',
    'space_options' => ['Retail (1 kg and up)', 'Wholesale (drums)', 'Trade account', 'Not sure, need advice'],
    'space_label' => 'What do you need?',
    'cta_label'   => 'Send My Enquiry',
]);
