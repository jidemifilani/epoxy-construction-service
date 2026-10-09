<?php
/**
 * Site content that repeats across pages: projects, reviews, FAQs, the system
 * comparison table and the blog plan. All of it comes from the website script.
 *
 * Kept as plain arrays so a page renders a filtered slice without a database.
 */

declare(strict_types=1);

/** Portfolio entries. 'tag' drives the portfolio filter buttons. */
function default_projects(): array
{
    $city = cfg('city');
    return [
        [
            'slug'    => 'reflections-house',
            'name'    => 'The Reflections House',
            'location'=> $city,
            'tag'     => 'residential',
            'space'   => 'Living, dining and kitchen',
            'size'    => '180 m2',
            'system'  => 'Metallic pearl epoxy',
            'finish'  => 'Pearl white with silver flow',
            'timeline'=> '4 days',
            'art'     => 'metallic',
            'summary' => 'Private residence. 180 m2 metallic pearl epoxy flowing through living, dining and kitchen with no transition strips.',
        ],
        [
            'slug'    => 'distribution-hub',
            'name'    => 'Logistics Distribution Hub',
            'location'=> $city,
            'tag'     => 'commercial',
            'space'   => 'Warehouse and loading bays',
            'size'    => '3,200 m2',
            'system'  => 'Solvent-free industrial epoxy',
            'finish'  => 'Mid-grey with embedded line marking',
            'timeline'=> '2 weekends',
            'art'     => 'warehouse',
            'summary' => '3,200 m2 solvent-free industrial epoxy with embedded line marking. Installed over two weekends to protect operations.',
        ],
        [
            'slug'    => 'flagship-showroom',
            'name'    => 'Automotive Flagship Showroom',
            'location'=> $city,
            'tag'     => 'decorative',
            'space'   => 'Vehicle display floor',
            'size'    => '420 m2',
            'system'  => '3D-effect decorative epoxy',
            'finish'  => 'High-gloss with embedded brand logo',
            'timeline'=> '5 nights',
            'art'     => 'showroom',
            'summary' => '3D-effect decorative floor with the brand logo embedded under a clear topcoat. Night-shift install over five nights.',
        ],
        [
            'slug'    => 'blue-reef-cafe',
            'name'    => 'The Blue Reef Cafe',
            'location'=> $city,
            'tag'     => 'commercial',
            'space'   => 'Commercial kitchen',
            'size'    => '90 m2',
            'system'  => 'Food-grade epoxy with coved skirtings',
            'finish'  => 'Satin, anti-slip aggregate',
            'timeline'=> '3 days',
            'art'     => 'kitchen',
            'summary' => 'Food-grade epoxy with coved skirtings in a 90 m2 commercial kitchen. HACCP audit passed within one week of install.',
        ],
        [
            'slug'    => 'private-residence',
            'name'    => 'Lekki Private Residence',
            'location'=> 'Lekki, ' . $city,
            'tag'     => 'residential',
            'space'   => 'Entry and living room',
            'size'    => '240 m2',
            'system'  => 'Metallic epoxy',
            'finish'  => 'Copper and bronze flow',
            'timeline'=> '5 days',
            'art'     => 'entry',
            'summary' => '240 m2 metallic epoxy across the entry and living room, hand-manipulated into a single continuous flow pattern.',
        ],
        [
            'slug'    => 'two-car-garage',
            'name'    => 'Ikoyi Two-Car Garage',
            'location'=> 'Ikoyi, ' . $city,
            'tag'     => 'residential',
            'space'   => 'Garage',
            'size'    => '58 m2',
            'system'  => 'Flake broadcast with polyaspartic topcoat',
            'finish'  => 'Slate blue flake blend',
            'timeline'=> '3 days',
            'art'     => 'garage',
            'summary' => 'Full flake broadcast with a polyaspartic topcoat. Vehicle-ready in 48 hours instead of the usual seven days.',
        ],
    ];
}

/** Reviews. 'group' drives the testimonials filter. */
function default_testimonials(): array
{
    return [
        [
            'quote' => 'They were the only company that explained what grinding was and why it mattered. Three years in, the floor still looks like day one.',
            'name'  => 'Adaeze N.',
            'role'  => 'Homeowner, ' . cfg('city'),
            'group' => 'homeowners',
            'project'=> 'Garage, 32 m2, flake system',
        ],
        [
            'quote' => 'Six warehouses, one contractor. ' . cfg('company_name') . ' has coated all of them. Not one callback.',
            'name'  => 'Tunde O.',
            'role'  => 'Operations Director, regional logistics group',
            'group' => 'businesses',
            'project'=> 'Warehouses, 14,000 m2 total, solvent-free epoxy',
        ],
        [
            'quote' => 'I spec their systems on every mid-to-high-end residential project. Finish quality is consistent.',
            'name'  => 'Ifeoma E.',
            'role'  => 'Principal Architect',
            'group' => 'trade',
            'project'=> 'Multiple projects, metallic and self-levelling systems',
        ],
        [
            'quote' => 'The wash-down zone was replaced across two weekends with no stop in production. We passed the HACCP audit on the first inspection.',
            'name'  => 'Samuel A.',
            'role'  => 'Plant Manager, food processing',
            'group' => 'businesses',
            'project'=> 'Wash-down zone, 640 m2, 4mm epoxy mortar',
        ],
        [
            'quote' => 'The quote was line-itemed down to the primer. The final invoice matched it exactly.',
            'name'  => 'Grace M.',
            'role'  => 'Homeowner, Lekki',
            'group' => 'homeowners',
            'project'=> 'Basement, 74 m2, moisture-tolerant system',
        ],
        [
            'quote' => 'They joined our design meetings before the floor spec was locked and flagged a moisture problem the main contractor had missed.',
            'name'  => 'Chidi U.',
            'role'  => 'Project Manager, main contractor',
            'group' => 'trade',
            'project'=> 'Mixed-use development, 1,900 m2',
        ],
    ];
}

/** The full FAQ, grouped as the script groups it. */
function default_faq_groups(): array
{
    return [
        'Pricing' => [
            ['How much does epoxy flooring cost?', 'Residential: ' . cfg('range_residential') . '. Commercial: ' . cfg('range_commercial') . '. Decorative: ' . cfg('range_decorative') . '. Final price depends on prep and system.'],
            ['Why is one quote cheaper than another?', 'Almost always because prep is being skipped. Ask for a line-itemed quote.'],
            ['Do you take deposits?', 'Yes - typically 50% deposit, 50% on completion for residential. Milestone billing on commercial.'],
        ],
        'Durability' => [
            ['How long will it last?', 'Residential: 10-20 years. Commercial: 7-15 years depending on traffic.'],
            ['Will it scratch?', 'Our topcoats are highly scratch-resistant, but not scratch-proof. Daily wear is invisible; a dragged steel chair is not.'],
            ['Does it crack if the slab moves?', 'Minor cracks are managed during prep. Structural slab movement is a separate issue.'],
        ],
        'Maintenance' => [
            ['How do I clean it?', 'Soft broom and a damp mop with pH-neutral cleaner. No abrasives.'],
            ['Can I use bleach?', 'For spot sanitisation, yes. Daily use, no - it dulls the finish over time.'],
            ['How often does it need recoating?', 'Typically every 7-15 years depending on wear.'],
        ],
        'Installation &amp; Timeline' => [
            ['How long does it take?', 'Residential garage: 2-4 days. Commercial is scoped per project.'],
            ['Do I need to move everything out?', 'Ideally yes. We can quote to move and return it.'],
            ['Can we live in the house during install?', 'Yes if other areas are habitable. The floor area needs to be isolated during cure.'],
        ],
        'Technical' => [
            ['What is the difference between epoxy and polyaspartic?', 'Epoxy is the base system; polyaspartic is a fast-cure, UV-stable topcoat. Many of our systems combine both.'],
            ['Can epoxy go over tile?', 'Sometimes - it depends on bond and condition. We test on-site.'],
            ['Is it slippery?', 'Smooth gloss can be. We add anti-slip aggregates in wet or outdoor areas as standard.'],
        ],
    ];
}

/** The four-question FAQ snippet shown on the home page. */
function default_faq_snippet(): array
{
    return [
        ['How long does an epoxy floor last?', '10-25 years depending on system and traffic.'],
        ['How long does installation take?', 'Residential: 2-4 days. Commercial: scoped per project.'],
        ['Is it slippery when wet?', 'Not with our anti-slip aggregates - standard in wet-area installs.'],
        ['Can you install over existing tile or paint?', 'Sometimes. We test on-site before quoting.'],
    ];
}

/** Commercial system comparison table. */
function systems_table(): array
{
    return [
        ['Solvent-free epoxy',        'General commercial, light industrial', '300-500 microns', '24-48 hrs'],
        ['Self-levelling epoxy',      'High-end showrooms, offices',          '1-3 mm',          '48-72 hrs'],
        ['Epoxy mortar / screed',     'Heavy industry, loading bays',         '4-9 mm',          '48-72 hrs'],
        ['Polyaspartic topcoat',      '24-hour cure, UV-stable',              '150-300 microns', '2-4 hrs'],
        ['ESD / anti-static epoxy',   'Electronics, pharma',                  '2-3 mm',          '48-72 hrs'],
        ['MMA (methyl methacrylate)', 'Freezers, 1-day turnarounds',          '3-6 mm',          '1-2 hrs'],
    ];
}

/** The seven process steps. */
function process_steps(): array
{
    return [
        ['Free On-Site Inspection', 'We visit the space, measure accurately, test slab moisture, check for cracks or coatings, and discuss how the space will actually be used. No obligation.'],
        ['Fixed-Price Written Quote', 'Within 48 hours you get a line-itemed quote: prep, primer, base coat, decorative layer, topcoat, cleanup, warranty. One number. No surprises.'],
        ['Diamond Grinding (Surface Preparation)', 'Industrial grinders fitted with diamond-segment discs mechanically profile the concrete to the correct Concrete Surface Profile for your chosen system. This is the step that determines whether your floor lasts 2 years or 20.'],
        ['Crack &amp; Joint Repair', 'Cracks are chased, V-cut and filled with a flexible polymer. Control joints are honoured or filled depending on the system.'],
        ['Primer &amp; Base Coat Application', 'A penetrating primer locks into the profile. The base coat delivers colour or sets up the decorative layer.'],
        ['Decorative or Functional Layer', 'Metallic manipulation, flake broadcast, 3D application, line marking or aggregate - depending on your project.'],
        ['Sealed Topcoat, Cure &amp; Walkthrough', 'A clear topcoat (standard, UV-stable, anti-slip or antimicrobial) protects everything underneath. We walk you through the floor, hand over care instructions and give you your written warranty.'],
    ];
}

/** Blog plan from the script. Each entry gets its own page via /blog/<slug>. */
function default_articles(): array
{
    $city = cfg('city');
    $year = (int) date('Y');
    return [
        ['how-much-does-epoxy-flooring-cost', 'How Much Does Epoxy Flooring Cost in ' . $city . ' in ' . $year . '?', 'Pricing', 'Captures price-intent searches with real ranges instead of "contact us for a quote".',
            ['What drives the price: prep, system, area', 'Residential ranges by space type', 'Commercial rates per m2', 'Why the cheapest quote is usually the most expensive', 'How to read a line-itemed quote']],
        ['epoxy-vs-polished-concrete-vs-tile', 'Epoxy vs. Polished Concrete vs. Tile: An Honest Comparison', 'Comparison', 'Targets the comparison keyword with a side-by-side that admits where epoxy loses.',
            ['Install cost and timeline compared', 'Lifespan under real traffic', 'Maintenance burden', 'Where each one wins', 'Where epoxy is the wrong choice']],
        ['why-epoxy-floors-peel', 'Why Epoxy Floors Peel - And How to Make Sure Yours Does Not', 'Authority', 'Trust content. Explains the failure mode most competitors avoid discussing.',
            ['Adhesion failure explained simply', 'Acid-etching vs. diamond grinding', 'Moisture: the silent killer', 'Nine questions that expose a shortcut', 'What a real warranty covers']],
        ['guide-to-metallic-epoxy-floors', 'The Full Guide to Metallic Epoxy Floors (With Real Photos)', 'Decorative', 'Lead magnet for the decorative audience.',
            ['How metallic pigment actually moves', 'Colour blends and what they look like installed', 'Gloss levels', 'Living with a metallic floor', 'Cost compared with stone']],
        ['how-to-choose-an-epoxy-contractor', 'How to Choose an Epoxy Contractor: 9 Questions to Ask', 'Bottom-funnel', 'Captures buyers who are already comparing contractors.',
            ['Ask about surface preparation first', 'Ask who is actually on site', 'Ask for the warranty in writing', 'Ask for the product data sheet', 'Ask what happens if it fails']],
        ['food-grade-epoxy-and-haccp', 'Food-Grade Epoxy Flooring: What HACCP Actually Requires', 'Commercial', 'Targets food industry decision-makers.',
            ['The hygiene requirements in plain language', 'Coved skirtings and why they matter', 'Antimicrobial topcoats', 'Documentation auditors ask for', 'Phasing an install around production']],
        ['warehouse-epoxy-minimising-downtime', 'Warehouse Epoxy Flooring: Minimising Downtime During Install', 'Commercial', 'Targets facility managers whose main objection is shutdown.',
            ['Bay-by-bay phasing', 'Fast-cure systems and what they cost', 'Night and weekend shifts', 'Dust containment in a live facility', 'A realistic 3,000 m2 timeline']],
        ['best-garage-floor-coatings', 'The Best Garage Floor Coatings: Epoxy vs. Polyaspartic vs. Paint', 'Residential', 'High-volume residential keyword.',
            ['What garage-floor paint really is', 'Epoxy build and durability', 'Polyaspartic cure speed and UV stability', 'Hot-tyre pickup explained', 'What we would put in our own garage']],
        ['how-to-maintain-an-epoxy-floor', 'How to Maintain an Epoxy Floor (So It Lasts 20 Years)', 'Maintenance', 'Reduces post-install support load.',
            ['The weekly routine, in two lines', 'Cleaners to avoid', 'Dealing with spills', 'When to recoat', 'Repairing a scratch']],
        ['decorative-epoxy-trends', '5 Decorative Epoxy Trends in ' . $year, 'Decorative', 'Social-share and designer-audience content.',
            ['Muted metallics replacing high-chrome', 'Terrazzo-style aggregate', 'Large-format marble veining', 'Zoned colour in open-plan spaces', 'Matte finishes in residential']],
        ['epoxy-over-existing-tile', 'Can You Install Epoxy Over Existing Tile? Here Is When It Works.', 'Technical', 'Answers a question that drives a lot of search traffic.',
            ['The bond test we run on site', 'When tile must come up', 'Hollow tiles and why they fail', 'Levelling over grout lines', 'Cost compared with removal']],
        ['true-lifespan-of-epoxy-flooring', 'The True Lifespan of Epoxy Flooring: What Actually Determines It', 'Authority', 'Sets realistic expectations and positions prep as the variable.',
            ['Prep quality', 'System thickness', 'Traffic type', 'UV exposure', 'Maintenance habits']],
    ];
}

/*
 * ---------------------------------------------------------------------------
 *  Site content, read from the database.
 *
 *  The default_* functions above are the starter content. database/install.php
 *  copies them into the tables once; from then on the admin dashboard edits
 *  the tables and these functions read them back. Each returns the same shape
 *  the page templates have always used, so no template needed to change.
 *
 *  Everything stored is PLAIN TEXT. Templates escape it on output.
 * ---------------------------------------------------------------------------
 */

/** Published portfolio projects, in the order the admin set. */
function projects(): array
{
    return db_rows('SELECT id, slug, name, location, tag, space, size, `system`, finish, timeline, art, summary
                    FROM projects WHERE published = 1 ORDER BY sort, id');
}

/** Published reviews. The DB column is group_key; templates read it as 'group'. */
function testimonials(): array
{
    $rows = db_rows('SELECT quote, name, role, group_key, project
                     FROM testimonials WHERE published = 1 ORDER BY sort, id');
    return array_map(static function (array $row): array {
        $row['group'] = $row['group_key'];
        unset($row['group_key']);
        return $row;
    }, $rows);
}

/** Published FAQs, grouped under their group name, in the admin's order. */
function faq_groups(): array
{
    $groups = [];
    $rows = db_rows('SELECT group_name, question, answer FROM faqs
                     WHERE published = 1 ORDER BY group_sort, group_name, sort, id');
    foreach ($rows as $row) {
        $groups[e($row['group_name'])][] = [e($row['question']), e($row['answer'])];
    }
    return $groups;
}

/** The FAQs flagged for the home page. */
function faq_snippet(): array
{
    $rows = db_rows('SELECT question, answer FROM faqs
                     WHERE published = 1 AND show_on_home = 1 ORDER BY sort, id');
    return array_map(static fn (array $row): array => [e($row['question']), e($row['answer'])], $rows);
}

/** Published blog articles, as [slug, title, category, purpose, outline[]]. */
function articles(): array
{
    $rows = db_rows('SELECT slug, title, category, purpose, outline, body, created_at
                     FROM articles WHERE published = 1 ORDER BY sort, id');
    return array_map('article_tuple', $rows);
}

function find_article(string $slug): ?array
{
    $row = db_row('SELECT slug, title, category, purpose, outline, body, created_at
                   FROM articles WHERE slug = ? AND published = 1', [$slug]);
    return $row === null ? null : article_tuple($row);
}

/** The outline is stored one section per line; the templates expect a list. */
function article_tuple(array $row): array
{
    $outline = array_values(array_filter(array_map('trim', explode("\n", (string) $row['outline'])), 'strlen'));
    return [$row['slug'], $row['title'], $row['category'], (string) $row['purpose'], $outline,
            (string) ($row['body'] ?? ''), (string) ($row['created_at'] ?? '')];
}

/** Published shop products for one channel ('retail' or 'wholesale'). */
function shop_products(string $channel): array
{
    return db_rows('SELECT id, name, channel, pack_label, unit_price, min_qty, description, in_stock
                    FROM products WHERE channel = ? AND published = 1 ORDER BY sort, id', [$channel]);
}
