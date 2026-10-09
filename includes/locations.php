<?php
/**
 * Location pages: epoxy flooring in named places across Nigeria.
 *
 * Coverage is stated honestly on every page:
 *  - 'standard'    the place is inside the service area set in Settings
 *                  (Lagos and the South-West, within the service radius)
 *  - 'per_project' projects are quoted individually, including travel and site
 *                  logistics. This is the policy already stated on the contact page.
 *
 * No page claims past work in its place. Local uses are described as the kinds of
 * projects we quote there. Edit the text here, or in a future admin section, if
 * your experience of a place differs.
 */

declare(strict_types=1);

const LOCATION_REGIONS = [
    'Lagos'         => 'Lagos',
    'South-West'    => 'South-West',
    'South-South'   => 'South-South',
    'South-East'    => 'South-East',
    'North-West'    => 'North-West',
    'North-Central' => 'North-Central (Abuja)',
];

/** Every location page, in the order the areas hub lists them. */
function location_all(): array
{
    return [
        // --- Lagos -----------------------------------------------------------
        [
            'slug' => 'lekki', 'name' => 'Lekki', 'region' => 'Lagos', 'state' => 'Lagos State',
            'schema' => 'Place', 'coverage' => 'standard',
            'lede' => 'Epoxy floors for homes, showrooms and offices in Lekki, from garages in the estates to shop and office floors on the main roads.',
            'uses' => ['Garages and home floors in the residential estates', 'Showroom and retail floors on the main roads', 'Office and reception floors that need a clean, seamless finish'],
        ],
        [
            'slug' => 'festac-lagos', 'name' => 'Festac, Lagos', 'region' => 'Lagos', 'state' => 'Lagos State',
            'schema' => 'Place', 'coverage' => 'standard',
            'lede' => 'Epoxy flooring in Festac for homes, workshops and shops, with a fixed-price written quote before any work starts.',
            'uses' => ['Home garages and living-room floors', 'Workshop and garage bays that take oil and tools', 'Shop and trading floors that need an easy-to-clean finish'],
        ],
        [
            'slug' => 'amuwo-odofin', 'name' => 'Amuwo-Odofin, Lagos', 'region' => 'Lagos', 'state' => 'Lagos State',
            'schema' => 'Place', 'coverage' => 'standard',
            'lede' => 'Epoxy flooring for homes, workshops and warehouses in Amuwo-Odofin. Every job starts with a site visit and diamond-ground preparation.',
            'uses' => ['Workshops and garages with heavy use', 'Warehouse and storage floors', 'Homes and small commercial spaces'],
        ],
        [
            'slug' => 'ibeju-lekki', 'name' => 'Ibeju-Lekki, Lagos', 'region' => 'Lagos', 'state' => 'Lagos State',
            'schema' => 'Place', 'coverage' => 'standard',
            'lede' => 'Epoxy flooring for warehouses, plants and estates along the Ibeju-Lekki corridor, scheduled around your operation.',
            'uses' => ['Warehouse and distribution floors with forklift traffic', 'Plant and workshop floors that need chemical resistance', 'Estate buildings, staff areas and offices'],
        ],

        // --- South-West ------------------------------------------------------
        [
            'slug' => 'ogun', 'name' => 'Ogun State', 'region' => 'South-West', 'state' => 'Ogun State',
            'schema' => 'State', 'coverage' => 'standard',
            'lede' => 'Epoxy flooring across Ogun State for warehouses, factories, homes and shops, including projects along the Lagos to Ogun corridor.',
            'uses' => ['Warehouse and factory floors', 'Home garages and interior floors', 'Shop and showroom floors'],
        ],
        [
            'slug' => 'ijebu-ode', 'name' => 'Ijebu-Ode, Ogun State', 'region' => 'South-West', 'state' => 'Ogun State',
            'schema' => 'City', 'coverage' => 'standard',
            'lede' => 'Epoxy flooring in Ijebu-Ode for homes, shops and small workshops, with a written quote and a clear programme.',
            'uses' => ['Home garages and living spaces', 'Shop and trading floors', 'Small workshops and storage areas'],
        ],
        [
            'slug' => 'ibadan', 'name' => 'Ibadan, Oyo State', 'region' => 'South-West', 'state' => 'Oyo State',
            'schema' => 'City', 'coverage' => 'standard',
            'lede' => 'Epoxy flooring in Ibadan for homes, shops and warehouses. Each quote is written after a site visit, with the system and warranty stated.',
            'uses' => ['Home garages, basements and living spaces', 'Shop, pharmacy and retail floors', 'Warehouse and storage floors'],
        ],
        [
            'slug' => 'akure', 'name' => 'Akure, Ondo State', 'region' => 'South-West', 'state' => 'Ondo State',
            'schema' => 'City', 'coverage' => 'per_project',
            'lede' => 'Epoxy flooring in Akure for homes and small commercial floors. Projects here are quoted individually, with travel included.',
            'uses' => ['Home garages and interior floors', 'Small shop and office floors', 'Workshops and storage areas'],
        ],

        // --- South-South -----------------------------------------------------
        [
            'slug' => 'port-harcourt', 'name' => 'Port Harcourt, Rivers State', 'region' => 'South-South', 'state' => 'Rivers State',
            'schema' => 'City', 'coverage' => 'per_project',
            'lede' => 'Epoxy flooring in Port Harcourt for workshops, warehouses and homes. Projects here are quoted individually, including travel and site logistics.',
            'uses' => ['Workshop and maintenance-bay floors', 'Warehouse and storage floors', 'Home garages and interior floors'],
        ],
        [
            'slug' => 'benin', 'name' => 'Benin City, Edo State', 'region' => 'South-South', 'state' => 'Edo State',
            'schema' => 'City', 'coverage' => 'per_project',
            'lede' => 'Epoxy flooring in Benin City for workshops, showrooms and homes. Projects here are quoted individually, including travel.',
            'uses' => ['Workshop and garage floors with heavy use', 'Showroom and retail floors', 'Home garages and living spaces'],
        ],
        [
            'slug' => 'delta', 'name' => 'Delta State', 'region' => 'South-South', 'state' => 'Delta State',
            'schema' => 'State', 'coverage' => 'per_project',
            'lede' => 'Epoxy flooring across Delta State for industrial workshops, warehouses and homes. Projects are quoted individually, including travel.',
            'uses' => ['Industrial workshop and plant floors', 'Warehouse and storage floors', 'Home garages and interior floors'],
        ],
        [
            'slug' => 'akwa-ibom', 'name' => 'Akwa Ibom State', 'region' => 'South-South', 'state' => 'Akwa Ibom State',
            'schema' => 'State', 'coverage' => 'per_project',
            'lede' => 'Epoxy flooring in Akwa Ibom for offices, showrooms and homes. Projects are quoted individually, including travel.',
            'uses' => ['Office and reception floors', 'Showroom and retail floors', 'Home garages and interior floors'],
        ],
        [
            'slug' => 'calabar', 'name' => 'Calabar, Cross River State', 'region' => 'South-South', 'state' => 'Cross River State',
            'schema' => 'City', 'coverage' => 'per_project',
            'lede' => 'Epoxy flooring in Calabar for warehouses, shops and homes. Projects are quoted individually, including travel and site logistics.',
            'uses' => ['Warehouse and storage floors', 'Shop and trading floors', 'Home garages and interior floors'],
        ],

        // --- South-East ------------------------------------------------------
        [
            'slug' => 'owerri', 'name' => 'Owerri, Imo State', 'region' => 'South-East', 'state' => 'Imo State',
            'schema' => 'City', 'coverage' => 'per_project',
            'lede' => 'Epoxy flooring in Owerri for homes, showrooms and warehouses. Projects are quoted individually, including travel.',
            'uses' => ['Home garages and living spaces', 'Showroom and retail floors', 'Warehouse and storage floors'],
        ],
        [
            'slug' => 'enugu', 'name' => 'Enugu State', 'region' => 'South-East', 'state' => 'Enugu State',
            'schema' => 'State', 'coverage' => 'per_project',
            'lede' => 'Epoxy flooring in Enugu State for workshops, warehouses and homes. Projects are quoted individually, including travel.',
            'uses' => ['Workshop and garage floors', 'Warehouse and storage floors', 'Home garages and interior floors'],
        ],

        // --- North-West ------------------------------------------------------
        [
            'slug' => 'kano', 'name' => 'Kano State', 'region' => 'North-West', 'state' => 'Kano State',
            'schema' => 'State', 'coverage' => 'per_project',
            'lede' => 'Epoxy flooring in Kano State for warehouses, workshops, shops and homes. Projects are quoted individually, including travel and site logistics.',
            'uses' => ['Warehouse and storage floors', 'Workshop and garage floors', 'Shop and home interior floors'],
        ],

        // --- North-Central --------------------------------------------------
        [
            'slug' => 'abuja', 'name' => 'Abuja, FCT', 'region' => 'North-Central', 'state' => 'Federal Capital Territory',
            'schema' => 'City', 'coverage' => 'per_project',
            'lede' => 'Epoxy flooring in Abuja for offices, showrooms, residential estates and warehouses. Projects are quoted individually, including travel.',
            'uses' => ['Office and reception floors', 'Showroom and retail floors', 'Residential garages and interior floors'],
        ],
    ];
}

/** One location by slug, or null. */
function location_find(string $slug): ?array
{
    foreach (location_all() as $location) {
        if ($location['slug'] === $slug) {
            return $location;
        }
    }
    return null;
}

/** The coverage sentence shown on a location page. Written to match the contact-page policy. */
function location_coverage_line(array $location): string
{
    if ($location['coverage'] === 'standard') {
        return $location['name'] . ' is inside our standard service area. Send us the space and its size, and we will arrange a site visit.';
    }
    return 'Projects in ' . $location['name'] . ' are quoted individually. The quote includes travel and site logistics, so the price is confirmed after we know the job.';
}
