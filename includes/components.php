<?php
/**
 * Render helpers for the blocks that repeat across pages.
 *
 * Each one echoes markup. They exist so a page file reads as content and
 * structure rather than as a wall of divs, and so a change to (say) the
 * project card shape lands everywhere at once.
 */

declare(strict_types=1);

/**
 * Section heading group: small eyebrow, serif H2, optional lede.
 *
 * $eyebrow is PLAIN TEXT -- it is escaped here, so pass "Finish & colour",
 * not "Finish &amp; colour". $heading and $lede are MARKUP and echoed as-is,
 * so entities and inline tags belong there.
 */
function section_head(string $eyebrow, string $heading, string $lede = '', string $align = 'center'): void
{
    $alignClass = $align === 'left' ? ' section-head--left' : '';
    echo '<header class="section-head' . $alignClass . ' reveal">';
    if ($eyebrow !== '') {
        echo '<p class="eyebrow">' . e($eyebrow) . '</p>';
    }
    echo '<h2 class="section-head__title">' . $heading . '</h2>';
    echo ornament($align === 'left' ? 'left' : 'ink');
    if ($lede !== '') {
        echo '<p class="section-head__lede">' . $lede . '</p>';
    }
    echo '</header>';
}

/** Primary / secondary / ghost button. */
function button(string $label, string $href, string $variant = 'primary', array $attrs = []): string
{
    $attrString = '';
    foreach ($attrs as $key => $value) {
        $attrString .= ' ' . $key . '="' . e($value) . '"';
    }
    return '<a class="btn btn--' . e($variant) . '" href="' . e($href) . '"' . $attrString . '>'
        . '<span>' . e($label) . '</span>' . icon('arrow', 'btn__arrow') . '</a>';
}

/** The quote / call / WhatsApp trio the script ends most pages with. */
function cta_row(string $primaryLabel = 'Request My Free Quote', string $primaryHref = ''): void
{
    $primaryHref = $primaryHref !== '' ? $primaryHref : url('quote');
    echo '<div class="cta-row reveal">';
    echo button($primaryLabel, $primaryHref, 'primary');
    echo button('Call ' . cfg('phone'), tel_link(), 'secondary');
    echo button('WhatsApp Us', whatsapp_link('Hello ' . cfg('company_name') . ', I would like a quote for an epoxy floor.'), 'ghost', ['target' => '_blank', 'rel' => 'noopener']);
    echo '</div>';
}

/**
 * Trust bar. Counts animate up from zero when the bar scrolls into view;
 * the final value is in the markup so it reads correctly with JS disabled.
 */
function trust_bar(bool $marquee = false): void
{
    $items = [
        ['value' => cfg('years'),              'suffix' => '+', 'label' => 'Years in Business'],
        ['value' => cfg('projects_completed'), 'suffix' => '+', 'label' => 'Projects Completed'],
        ['value' => cfg('warranty_years'),     'suffix' => '',  'label' => 'Year Written Warranty'],
    ];
    echo '<div class="trust-bar' . ($marquee ? ' trust-bar--marquee' : '') . '">';
    echo '<div class="trust-bar__inner">';
    foreach ($items as $item) {
        echo '<div class="trust-stat reveal">';
        echo '<span class="trust-stat__num"><span class="count" data-count="' . e((string) $item['value']) . '">'
            . e(num($item['value'])) . '</span>' . e($item['suffix']) . '</span>';
        echo '<span class="trust-stat__label">' . e($item['label']) . '</span>';
        echo '</div>';
    }
    echo '<div class="trust-stat trust-stat--text reveal">' . icon('shield', 'trust-stat__icon')
        . '<span class="trust-stat__label">Fully Licensed &amp; Insured</span></div>';
    echo '<div class="trust-stat trust-stat--text reveal">' . icon('check', 'trust-stat__icon')
        . '<span class="trust-stat__label">' . e(cfg('cert_1')) . ' Certified &middot; ' . e(cfg('cert_2')) . ' Member</span></div>';
    echo '</div></div>';
}

/** A single portfolio card. */
function project_card(array $project, bool $linked = true): void
{
    $href = url('portfolio') . '#' . $project['slug'];
    echo '<article class="card project-card reveal" data-tag="' . e($project['tag']) . '" id="' . e($project['slug']) . '">';
    echo '<div class="project-card__art">' . project_visual($project)
        . '<span class="tag tag--' . e($project['tag']) . '">' . e(ucfirst($project['tag'])) . '</span></div>';
    echo '<div class="project-card__body">';
    echo '<h3 class="project-card__title">' . e($project['name']) . '</h3>';
    echo '<p class="project-card__meta">' . e($project['location']) . ' &middot; ' . e($project['size']) . '</p>';
    echo '<p class="project-card__summary">' . e($project['summary']) . '</p>';
    echo '<dl class="spec">';
    foreach ([
        'Space'    => $project['space'],
        'System'   => $project['system'],
        'Finish'   => $project['finish'],
        'Timeline' => $project['timeline'],
    ] as $label => $value) {
        echo '<div class="spec__row"><dt>' . e($label) . '</dt><dd>' . e($value) . '</dd></div>';
    }
    echo '</dl>';
    if ($linked) {
        echo '<a class="link-arrow" href="' . e($href) . '">Discuss a similar project' . icon('arrow', 'link-arrow__icon') . '</a>';
    }
    echo '</div>' . corner_frame() . '</article>';
}

/** A single review card. */
function review_card(array $review): void
{
    echo '<figure class="card review-card reveal" data-group="' . e($review['group']) . '">';
    echo '<div class="review-card__stars" aria-label="Five out of five stars">';
    for ($i = 0; $i < 5; $i++) {
        echo icon('star', 'star');
    }
    echo '</div>';
    echo '<blockquote>' . e($review['quote']) . '</blockquote>';
    echo '<figcaption><strong>' . e($review['name']) . '</strong><span>' . e($review['role']) . '</span>'
        . '<span class="review-card__project">Project: ' . e($review['project']) . '</span></figcaption>';
    echo corner_frame() . '</figure>';
}

/**
 * Accordion. $groups is either a flat list of [question, answer] pairs or a
 * map of group name => list of pairs.
 */
function accordion(array $items, bool $grouped = false): void
{
    if (!$grouped) {
        $items = ['' => $items];
    }
    foreach ($items as $groupName => $pairs) {
        if ($groupName !== '') {
            // The id lets a jump list link straight to the group; slug() is
            // shared with whatever builds that list so the two always agree.
            echo '<h3 class="accordion__group reveal" id="' . e(slug($groupName)) . '">' . $groupName . '</h3>';
        }
        echo '<div class="accordion reveal">';
        foreach ($pairs as $pair) {
            [$question, $answer] = $pair;
            echo '<details class="accordion__item">';
            echo '<summary><span>' . $question . '</span><i class="accordion__plus" aria-hidden="true"></i></summary>';
            echo '<div class="accordion__body"><p>' . $answer . '</p></div>';
            echo '</details>';
        }
        echo '</div>';
    }
}

/** Numbered step list that draws its connecting rule on scroll. */
function step_list(array $steps, bool $compact = false): void
{
    echo '<ol class="steps' . ($compact ? ' steps--compact' : '') . '">';
    foreach ($steps as $index => $step) {
        [$title, $body] = is_array($step) ? $step : [$step, ''];
        echo '<li class="steps__item reveal">';
        echo '<span class="steps__num" aria-hidden="true">' . str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) . '</span>';
        echo '<div class="steps__content"><h3>' . $title . '</h3>';
        if ($body !== '') {
            echo '<p>' . $body . '</p>';
        }
        echo '</div></li>';
    }
    echo '</ol>';
}

/** Icon + title + copy card, optionally with a link. */
function feature_card(string $iconKey, string $title, string $body, string $linkLabel = '', string $linkHref = ''): void
{
    echo '<article class="card feature-card reveal">';
    echo '<span class="feature-card__icon">' . icon($iconKey, 'icon icon--lg') . '</span>';
    echo '<h3>' . $title . '</h3><p>' . $body . '</p>';
    if ($linkLabel !== '') {
        echo '<a class="link-arrow" href="' . e($linkHref) . '">' . e($linkLabel) . icon('arrow', 'link-arrow__icon') . '</a>';
    }
    echo corner_frame() . '</article>';
}

/** Numbered reason card, for the "six reasons" style lists. */
function reason_card(int $number, string $title, string $body): void
{
    echo '<article class="card reason-card reveal">';
    echo '<span class="reason-card__num">' . str_pad((string) $number, 2, '0', STR_PAD_LEFT) . '</span>';
    echo '<h3>' . $title . '</h3><p>' . $body . '</p>';
    echo corner_frame() . '</article>';
}

/** Tick list. */
function tick_list(array $items, string $class = ''): void
{
    echo '<ul class="ticks ' . e($class) . ' reveal">';
    foreach ($items as $item) {
        echo '<li>' . icon('check', 'ticks__icon') . '<span>' . $item . '</span></li>';
    }
    echo '</ul>';
}

/** Cross list, for the "what we never do" block. */
function cross_list(array $items): void
{
    echo '<ul class="ticks ticks--cross reveal">';
    foreach ($items as $item) {
        echo '<li><svg class="ticks__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" '
            . 'stroke-linecap="round" aria-hidden="true" focusable="false"><path d="M6 6l12 12M18 6 6 18"/></svg>'
            . '<span>' . $item . '</span></li>';
    }
    echo '</ul>';
}

/** The closing CTA band every page ends with. */
function cta_band(string $heading, string $body = '', string $primaryLabel = 'Request My Free Quote', string $primaryHref = ''): void
{
    echo '<section class="band">';
    echo '<div class="band__grid" aria-hidden="true"></div>';
    echo '<div class="wrap band__inner">';
    echo '<p class="eyebrow eyebrow--light reveal">Next step</p>';
    echo '<h2 class="band__title reveal">' . $heading . '</h2>';
    echo ornament('light');
    if ($body !== '') {
        echo '<p class="band__lede reveal">' . $body . '</p>';
    }
    cta_row($primaryLabel, $primaryHref);
    echo '</div></section>';
}

/**
 * Breadcrumb trail. Also emits BreadcrumbList schema.
 *
 * Labels are PLAIN TEXT -- they are escaped for the markup and fed raw to the
 * schema, so an entity written here would surface in both.
 */
function breadcrumbs(array $trail): void
{
    echo '<nav class="crumbs" aria-label="Breadcrumb"><ol>';
    echo '<li><a href="' . e(url()) . '">Home</a></li>';
    $items = [[
        '@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => absolute_url(),
    ]];
    $position = 2;
    foreach ($trail as $label => $path) {
        if ($path === null) {
            echo '<li aria-current="page">' . e($label) . '</li>';
            $items[] = ['@type' => 'ListItem', 'position' => $position, 'name' => $label, 'item' => absolute_url(trim($_SERVER['REQUEST_URI'] ?? '', '/'))];
        } else {
            echo '<li><a href="' . e(url($path)) . '">' . e($label) . '</a></li>';
            $items[] = ['@type' => 'ListItem', 'position' => $position, 'name' => $label, 'item' => absolute_url($path)];
        }
        $position++;
    }
    echo '</ol></nav>';
    echo '<script type="application/ld+json">'
        . json_encode(['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $items], JSON_UNESCAPED_SLASHES)
        . '</script>';
}

/** FAQPage schema for a page that renders an accordion. */
function faq_schema(array $pairs): void
{
    $entities = [];
    foreach ($pairs as $pair) {
        $entities[] = [
            '@type' => 'Question',
            'name'  => html_entity_decode(strip_tags($pair[0]), ENT_QUOTES, 'UTF-8'),
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text'  => html_entity_decode(strip_tags($pair[1]), ENT_QUOTES, 'UTF-8'),
            ],
        ];
    }
    echo '<script type="application/ld+json">'
        . json_encode(['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $entities], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        . '</script>';
}

/** Flatten a grouped FAQ into pairs, for schema. */
function flatten_faq(array $groups): array
{
    $pairs = [];
    foreach ($groups as $group) {
        foreach ($group as $pair) {
            $pairs[] = $pair;
        }
    }
    return $pairs;
}
