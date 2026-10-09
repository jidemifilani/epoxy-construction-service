<?php
/**
 * Drawn artwork. Every illustration on the site is inline SVG, so the whole
 * thing renders with no photography and nothing to download.
 *
 * Shapes reference the palette through CSS custom properties, so a colour
 * change in main.css moves the artwork with it. Animation is driven by classes
 * the stylesheet owns -- nothing animates from inside these functions, which
 * keeps prefers-reduced-motion handling in one place.
 *
 * Replace any call with a real <img> once site photography exists; the markup
 * around each one already carries the right aspect ratio.
 */

declare(strict_types=1);

/** The wordmark glyph: a mason's arch over a trowel. */
function brand_mark(string $class = 'brand-mark'): string
{
    return <<<SVG
<svg class="{$class}" viewBox="0 0 48 48" role="img" aria-label="Logo" focusable="false">
  <circle cx="24" cy="24" r="22.5" fill="none" stroke="currentColor" stroke-width="1.25" opacity=".45"/>
  <circle cx="24" cy="24" r="19" fill="none" stroke="currentColor" stroke-width="2.5"/>
  <path d="M13 29.5V24a11 11 0 0 1 22 0v5.5" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round"/>
  <path d="M18.5 29.5 24 15l5.5 14.5z" fill="currentColor" opacity=".9"/>
  <path d="M13 33.5h22" stroke="currentColor" stroke-width="2.25" stroke-linecap="round"/>
</svg>
SVG;
}

/**
 * The home hero scene, in parallax layers.
 * data-depth is read by main.js; larger numbers move further.
 */
function hero_artwork(): string
{
    return <<<SVG
<svg class="hero-art" viewBox="0 0 900 620" role="img"
     aria-label="Illustration of a finished epoxy floor in a double-height space with a crane beyond the glazing"
     preserveAspectRatio="xMidYMid slice" focusable="false">
  <defs>
    <linearGradient id="haSky" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0" stop-color="var(--ink-900)"/>
      <stop offset=".62" stop-color="var(--blue-800)"/>
      <stop offset="1" stop-color="var(--blue-700)"/>
    </linearGradient>
    <linearGradient id="haFloor" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0" stop-color="#cfe3f5"/>
      <stop offset=".45" stop-color="#eaf2fa"/>
      <stop offset="1" stop-color="#ffffff"/>
    </linearGradient>
    <linearGradient id="haSheen" x1="0" y1="0" x2="1" y2="0">
      <stop offset="0" stop-color="#fff" stop-opacity="0"/>
      <stop offset=".5" stop-color="#fff" stop-opacity=".85"/>
      <stop offset="1" stop-color="#fff" stop-opacity="0"/>
    </linearGradient>
    <linearGradient id="haGreen" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="var(--green-500)"/>
      <stop offset="1" stop-color="var(--green-700)"/>
    </linearGradient>
    <pattern id="haGrid" width="45" height="45" patternUnits="userSpaceOnUse">
      <path d="M45 0H0v45" fill="none" stroke="#9ec6e8" stroke-width=".6" opacity=".35"/>
    </pattern>
    <clipPath id="haFrame"><rect width="900" height="620"/></clipPath>
  </defs>

  <g clip-path="url(#haFrame)">
    <rect width="900" height="620" fill="url(#haSky)"/>
    <rect width="900" height="620" fill="url(#haGrid)"/>

    <!-- Distant skyline -->
    <g data-depth="6" opacity=".4" fill="#0a1f3c">
      <rect x="40"  y="250" width="90"  height="170"/>
      <rect x="150" y="205" width="70"  height="215"/>
      <rect x="240" y="275" width="110" height="145"/>
      <rect x="600" y="230" width="85"  height="190"/>
      <rect x="705" y="285" width="130" height="135"/>
    </g>

    <!-- Tower crane -->
    <g data-depth="12" stroke="var(--green-500)" stroke-width="3" fill="none" stroke-linecap="round">
      <path d="M420 420V120"/>
      <path d="M300 136h250"/>
      <path d="M420 120 300 136M420 120l130 16"/>
      <path d="M408 420h24M405 300h30M405 220h30"/>
      <path d="M352 136v46" stroke-width="2.25"/>
      <circle cx="352" cy="192" r="9" fill="var(--green-500)" stroke="none"/>
    </g>

    <!-- Structural frame of the room -->
    <g data-depth="18" stroke="#bcd8f0" stroke-width="2.5" fill="none" opacity=".75">
      <path d="M95 60v360M805 60v360"/>
      <path d="M95 60h710"/>
      <path d="M250 60v360M450 60v360M650 60v360" stroke-width="1.25" opacity=".6"/>
      <path d="M95 150h710M95 240h710" stroke-width="1" opacity=".4"/>
    </g>

    <!-- The floor itself -->
    <g data-depth="26">
      <path d="M0 420h900v200H0z" fill="url(#haFloor)"/>
      <path d="M0 420h900v6H0z" fill="var(--green-500)" opacity=".85"/>
      <!-- reflected uprights -->
      <g stroke="#8fb6d8" stroke-width="2" opacity=".35">
        <path d="M95 426v120M805 426v120M250 426v86M450 426v104M650 426v86"/>
      </g>
      <!-- reflected crane -->
      <path d="M420 426v150M352 440v40" stroke="var(--green-600)" stroke-width="2.5" opacity=".3" fill="none"/>
      <!-- perspective joint lines -->
      <g stroke="#a9c9e6" stroke-width="1" opacity=".45">
        <path d="M0 470h900M0 520h900M0 575h900"/>
      </g>
      <rect class="hero-sheen" x="-320" y="420" width="320" height="200" fill="url(#haSheen)" opacity=".55"/>
    </g>

    <!-- Foreground: a trowel laid flat on the slab, blade pointing left -->
    <g data-depth="34">
      <path d="M636 528l118-26 92 10-88 24z" fill="url(#haGreen)"/>
      <path d="M700 507l104-9" fill="none" stroke="var(--ink-900)" stroke-width="3" opacity=".25"/>
      <path d="M846 512h44" fill="none" stroke="#8c6239" stroke-width="13" stroke-linecap="round"/>
      <path d="M838 512h10" fill="none" stroke="var(--ink-900)" stroke-width="9" stroke-linecap="round"/>
    </g>
  </g>
</svg>
SVG;
}

/**
 * Classic divider. The path is stroked with a dash offset the stylesheet
 * animates, so the rule draws itself outward from the centre diamond.
 */
function ornament(string $tone = 'ink'): string
{
    return <<<SVG
<div class="ornament ornament--{$tone}" aria-hidden="true">
  <svg viewBox="0 0 320 24" focusable="false">
    <path class="ornament__line" d="M4 12h120" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
    <path class="ornament__line" d="M316 12H196" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
    <path class="ornament__gem" d="M160 2l10 10-10 10-10-10z" fill="currentColor"/>
    <circle class="ornament__dot" cx="136" cy="12" r="2.5" fill="currentColor"/>
    <circle class="ornament__dot" cx="184" cy="12" r="2.5" fill="currentColor"/>
  </svg>
</div>
SVG;
}

/** Corner brackets that draw in on hover. Purely decorative. */
function corner_frame(): string
{
    return '<span class="frame" aria-hidden="true">'
        . '<i class="frame__c frame__c--tl"></i><i class="frame__c frame__c--tr"></i>'
        . '<i class="frame__c frame__c--br"></i><i class="frame__c frame__c--bl"></i>'
        . '</span>';
}

/**
 * Project artwork. Each key is an abstract portrait of that floor finish --
 * the swirl of a metallic pour, the bay markings of a warehouse, and so on.
 */
function project_art(string $key): string
{
    $scenes = [
        'metallic' => '
            <rect width="600" height="400" fill="#eef4fa"/>
            <g opacity=".95">
              <path d="M0 250c90-70 180 30 300-20s210 30 300-30v200H0z" fill="#9fc4e4"/>
              <path d="M0 300c110-60 190 40 310-10s190 20 290-40v150H0z" fill="#6fa7d4"/>
              <path d="M0 348c120-48 200 34 320-6s180 10 280-36v94H0z" fill="var(--blue-700)"/>
            </g>
            <g fill="none" stroke="#fff" stroke-width="2.5" opacity=".6">
              <path d="M0 262c92-66 182 28 302-22s208 28 298-28"/>
              <path d="M0 312c112-56 192 38 312-12s188 18 288-38"/>
            </g>
            <rect y="0" width="600" height="120" fill="#fff" opacity=".55"/>',
        'warehouse' => '
            <rect width="600" height="400" fill="#f3f6fa"/>
            <g stroke="#c3d6e8" stroke-width="2" fill="none">
              <path d="M0 150h600M0 230h600M0 310h600"/>
              <path d="M120 150V400M300 150V400M480 150V400"/>
            </g>
            <rect width="600" height="150" fill="var(--ink-900)" opacity=".92"/>
            <g fill="#8fb6d8" opacity=".5">
              <rect x="40" y="46" width="70" height="104"/><rect x="150" y="26" width="70" height="124"/>
              <rect x="260" y="56" width="70" height="94"/><rect x="370" y="36" width="70" height="114"/>
              <rect x="480" y="62" width="80" height="88"/>
            </g>
            <g stroke="var(--green-500)" stroke-width="9" stroke-linecap="square">
              <path d="M40 360h160M240 360h120M400 360h160"/>
            </g>
            <g stroke="#f0b429" stroke-width="5" stroke-dasharray="26 18">
              <path d="M0 270h600"/>
            </g>',
        'showroom' => '
            <rect width="600" height="400" fill="#eaf1f8"/>
            <rect width="600" height="170" fill="var(--blue-800)"/>
            <g fill="none" stroke="#7fb0da" stroke-width="2" opacity=".5">
              <path d="M70 0v170M190 0v170M310 0v170M430 0v170M550 0v170"/>
            </g>
            <ellipse cx="300" cy="250" rx="230" ry="52" fill="var(--green-600)" opacity=".2"/>
            <ellipse cx="300" cy="250" rx="150" ry="34" fill="var(--green-600)" opacity=".3"/>
            <ellipse cx="300" cy="250" rx="72"  ry="17" fill="var(--green-600)" opacity=".45"/>
            <g fill="#fff" opacity=".7">
              <path d="M0 300h600v8H0zM0 346h600v6H0z"/>
            </g>
            <path d="M120 400 300 300l180 100z" fill="#fff" opacity=".35"/>',
        'kitchen' => '
            <rect width="600" height="400" fill="#f6f9fc"/>
            <rect width="600" height="140" fill="#dce8f3"/>
            <g fill="#fff" stroke="#c3d6e8" stroke-width="2">
              <rect x="40" y="40" width="120" height="100" rx="4"/>
              <rect x="190" y="40" width="120" height="100" rx="4"/>
              <rect x="340" y="40" width="220" height="100" rx="4"/>
            </g>
            <path d="M0 140h600v26a26 26 0 0 1-26 26H26A26 26 0 0 1 0 166z" fill="var(--green-600)" opacity=".85"/>
            <rect y="192" width="600" height="208" fill="#e7f1f8"/>
            <g fill="var(--blue-700)" opacity=".18">
              <circle cx="90"  cy="250" r="4"/><circle cx="210" cy="286" r="4"/><circle cx="330" cy="240" r="4"/>
              <circle cx="450" cy="300" r="4"/><circle cx="540" cy="256" r="4"/><circle cx="160" cy="344" r="4"/>
              <circle cx="300" cy="360" r="4"/><circle cx="430" cy="350" r="4"/>
            </g>',
        'entry' => '
            <rect width="600" height="400" fill="#eef4fa"/>
            <!-- arched opening, with daylight beyond -->
            <path d="M170 300V170a130 130 0 0 1 260 0v130z" fill="var(--blue-800)"/>
            <path d="M200 300V172a100 100 0 0 1 200 0v128z" fill="#cfe3f5"/>
            <path d="M230 300V176a70 70 0 0 1 140 0v124z" fill="#e8f3fb"/>
            <!-- metallic flow across the floor -->
            <rect y="300" width="600" height="100" fill="#dfecf7"/>
            <path d="M0 322c86-30 150 26 242 6s184-34 358-10v82H0z" fill="#9fc4e4"/>
            <path d="M0 348c96-24 158 22 250 4s176-26 350-8v56H0z" fill="var(--blue-700)"/>
            <g fill="none" stroke="#fff" stroke-width="2" opacity=".55">
              <path d="M0 322c86-30 150 26 242 6s184-34 358-10"/>
              <path d="M0 348c96-24 158 22 250 4s176-26 350-8"/>
            </g>
            <!-- reflection of the arch in the finished floor -->
            <path d="M200 300h200v18a100 26 0 0 1-200 0z" fill="#fff" opacity=".35"/>',
        'garage' => '
            <rect width="600" height="400" fill="#f4f7fb"/>
            <rect width="600" height="160" fill="var(--ink-900)"/>
            <g stroke="#6f97bd" stroke-width="3" opacity=".6">
              <path d="M0 40h600M0 80h600M0 120h600"/>
            </g>
            <rect y="160" width="600" height="240" fill="#e9f0f7"/>
            <g opacity=".75">
              <g fill="var(--blue-700)">
                <rect x="60"  y="200" width="14" height="7" rx="3"/><rect x="180" y="238" width="14" height="7" rx="3"/>
                <rect x="320" y="206" width="14" height="7" rx="3"/><rect x="470" y="252" width="14" height="7" rx="3"/>
                <rect x="120" y="310" width="14" height="7" rx="3"/><rect x="380" y="330" width="14" height="7" rx="3"/>
                <rect x="520" y="340" width="14" height="7" rx="3"/>
              </g>
              <g fill="var(--green-600)">
                <rect x="240" y="286" width="14" height="7" rx="3"/><rect x="420" y="196" width="14" height="7" rx="3"/>
                <rect x="90"  y="360" width="14" height="7" rx="3"/><rect x="300" y="360" width="14" height="7" rx="3"/>
                <rect x="540" y="214" width="14" height="7" rx="3"/>
              </g>
              <g fill="#9aa8b5">
                <rect x="150" y="196" width="14" height="7" rx="3"/><rect x="270" y="330" width="14" height="7" rx="3"/>
                <rect x="470" y="310" width="14" height="7" rx="3"/><rect x="200" y="360" width="14" height="7" rx="3"/>
              </g>
            </g>',
    ];

    $scene = $scenes[$key] ?? $scenes['metallic'];
    return '<svg class="art" viewBox="0 0 600 400" role="img" aria-hidden="true" preserveAspectRatio="xMidYMid slice" focusable="false">'
        . $scene . '</svg>';
}

/** Line icons. One flat set, used for spaces, sectors and finishes. */
function icon(string $key, string $class = 'icon'): string
{
    $paths = [
        'garage'     => '<path d="M3 11 12 5l9 6v9h-4v-6H7v6H3z"/><path d="M7 17h10"/>',
        'basement'   => '<path d="M4 4h16v16H4z"/><path d="M4 12h16M12 4v16"/>',
        'patio'      => '<circle cx="12" cy="8" r="4"/><path d="M3 20h18M7 20l2-5M17 20l-2-5"/>',
        'kitchen'    => '<path d="M5 3v8a3 3 0 0 0 6 0V3"/><path d="M8 11v10"/><path d="M17 3c2 2 2 5 0 7v11"/>',
        'living'     => '<path d="M4 13h16v6H4z"/><path d="M6 13V8a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v5"/><path d="M4 19v2M20 19v2"/>',
        'workshop'   => '<path d="M14 7l3-3 4 4-3 3z"/><path d="M13 8 4 17v3h3l9-9"/>',
        'warehouse'  => '<path d="M3 20V9l9-5 9 5v11z"/><path d="M8 20v-7h8v7"/>',
        'factory'    => '<path d="M3 20V10l5 3V10l5 3V10l5 3v7z"/><path d="M19 10V4h2v6"/>',
        'food'       => '<path d="M6 3v7a3 3 0 0 0 6 0V3"/><path d="M9 10v11"/><circle cx="17" cy="8" r="4"/><path d="M17 12v9"/>',
        'pharma'     => '<rect x="4" y="4" width="16" height="16" rx="8"/><path d="M12 8v8M8 12h8"/>',
        'health'     => '<path d="M12 21s-7-4.6-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 11c0 5.4-7 10-7 10z"/>',
        'auto'       => '<path d="M4 16v-3l2-5h12l2 5v3"/><path d="M4 16h16v3H4z"/><circle cx="8" cy="19" r="1.6"/><circle cx="16" cy="19" r="1.6"/>',
        'retail'     => '<path d="M4 8h16l-1 12H5z"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/>',
        'hospitality'=> '<path d="M4 20h16"/><path d="M6 20V9h12v11"/><path d="M9 9V5h6v4"/>',
        'shield'     => '<path d="M12 3l8 3v6c0 5-4 8-8 9-4-1-8-4-8-9V6z"/><path d="m9 12 2.2 2.2L15.5 10"/>',
        'clock'      => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l4 2"/>',
        'doc'        => '<path d="M6 3h8l4 4v14H6z"/><path d="M14 3v4h4M9 13h6M9 17h6"/>',
        'crew'       => '<circle cx="9" cy="8" r="3"/><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6"/><path d="M16 11a3 3 0 1 0 0-6"/><path d="M18 20c0-2.2-.8-4.2-2-5.6"/>',
        'price'      => '<path d="M4 9h16v11H4z"/><path d="M8 9V6a4 4 0 0 1 8 0v3"/><path d="M12 12v5M10 14h4"/>',
        'grind'      => '<circle cx="12" cy="12" r="7"/><circle cx="12" cy="12" r="2.5"/><path d="M12 5V2M12 22v-3M5 12H2M22 12h-3"/>',
        'sparkle'    => '<path d="M12 3l2 6 6 2-6 2-2 6-2-6-6-2 6-2z"/>',
        'cube'       => '<path d="M12 3l8 4.5v9L12 21l-8-4.5v-9z"/><path d="m4 7.5 8 4.5 8-4.5M12 12v9"/>',
        'flake'      => '<path d="M12 3v18M3 12h18M6 6l12 12M18 6 6 18"/>',
        'marble'     => '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 14c4-3 7 1 11-2l7-4M3 19c3-2 6 1 9-1"/>',
        'quartz'     => '<circle cx="8" cy="9" r="2"/><circle cx="15" cy="7" r="1.5"/><circle cx="17" cy="14" r="2"/><circle cx="10" cy="16" r="1.5"/><circle cx="6" cy="15" r="1.2"/><rect x="3" y="3" width="18" height="18" rx="2"/>',
        'logofloor'  => '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 15l4-7 4 7z"/><path d="M7 18h10"/>',
        'terrazzo'   => '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="m7 8 2 2-2 2-2-2zM16 7l2 2-2 2-2-2zM11 13l2 2-2 2-2-2z"/>',
        'phone'      => '<path d="M5 3h4l2 5-2.5 1.5a11 11 0 0 0 6 6L16 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 5a2 2 0 0 1 2-2z"/>',
        'chat'       => '<path d="M4 5h16v11H9l-5 4z"/>',
        'mail'       => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
        'pin'        => '<path d="M12 21s-7-6-7-11a7 7 0 1 1 14 0c0 5-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/>',
        'check'      => '<path d="m4 13 5 5L20 6"/>',
        'arrow'      => '<path d="M5 12h14M13 6l6 6-6 6"/>',
        'star'       => '<path d="m12 3 2.8 6 6.2.9-4.5 4.4 1.1 6.2L12 17.6 6.4 20.5l1.1-6.2L3 9.9 9.2 9z"/>',
        'calendar'   => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/>',
        'ruler'      => '<path d="m3 15 12-12 6 6-12 12z"/><path d="m8 10 2 2M11 7l2 2M5 13l2 2"/>',
    ];

    $body = $paths[$key] ?? $paths['check'];
    return '<svg class="' . $class . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" '
        . 'stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">'
        . $body . '</svg>';
}

/** Decorative finish swatch, used on the decorative services page. */
function finish_swatch(string $key): string
{
    $scenes = [
        'metallic' => '<rect width="200" height="140" fill="#dbe9f6"/><path d="M0 70c40-34 70 18 110-6s60 12 90-16v92H0z" fill="var(--blue-700)"/><path d="M0 96c46-26 76 20 116-4s54 8 84-14v62H0z" fill="#8cb9de"/><path d="M0 70c40-34 70 18 110-6s60 12 90-16" fill="none" stroke="#fff" stroke-width="2" opacity=".7"/>',
        '3d'       => '<rect width="200" height="140" fill="#07314f"/><g fill="none" stroke="#2f9ad6" stroke-width="2" opacity=".8"><path d="M-10 110q50-40 100 0t110 0"/><path d="M-10 90q50-40 100 0t110 0"/><path d="M-10 130q50-40 100 0t110 0"/></g><circle cx="150" cy="38" r="16" fill="#9fe3c5" opacity=".8"/>',
        'flake'    => '<rect width="200" height="140" fill="#eaf1f8"/><g><rect x="20" y="30" width="11" height="5" rx="2" fill="var(--blue-700)"/><rect x="60" y="54" width="11" height="5" rx="2" fill="var(--green-600)"/><rect x="104" y="26" width="11" height="5" rx="2" fill="#94a3b0"/><rect x="146" y="60" width="11" height="5" rx="2" fill="var(--blue-700)"/><rect x="34" y="86" width="11" height="5" rx="2" fill="var(--green-600)"/><rect x="82" y="104" width="11" height="5" rx="2" fill="var(--blue-700)"/><rect x="128" y="96" width="11" height="5" rx="2" fill="#94a3b0"/><rect x="166" y="118" width="11" height="5" rx="2" fill="var(--green-600)"/><rect x="54" y="124" width="11" height="5" rx="2" fill="var(--blue-700)"/></g>',
        'marble'   => '<rect width="200" height="140" fill="#f7fafc"/><g fill="none" stroke="#9db4c7" stroke-width="2.5"><path d="M-5 46c40-20 60 18 100 4s60-22 110-6"/><path d="M-5 92c46-14 62 20 104 8s54-18 106-4"/></g><g fill="none" stroke="var(--green-600)" stroke-width="1.2" opacity=".6"><path d="M-5 60c44-18 66 16 104 2s56-18 106-6"/></g>',
        'quartz'   => '<rect width="200" height="140" fill="#e6eef6"/><g fill="var(--blue-700)" opacity=".55"><circle cx="30" cy="36" r="5"/><circle cx="76" cy="60" r="4"/><circle cx="130" cy="32" r="5"/><circle cx="168" cy="72" r="4"/><circle cx="46" cy="98" r="4"/><circle cx="104" cy="110" r="5"/><circle cx="156" cy="116" r="4"/></g><g fill="var(--green-600)" opacity=".5"><circle cx="58" cy="26" r="3"/><circle cx="112" cy="72" r="3"/><circle cx="24" cy="120" r="3"/><circle cx="184" cy="40" r="3"/></g>',
        'logo'     => '<rect width="200" height="140" fill="#0d3b61"/><path d="M70 96l30-52 30 52z" fill="none" stroke="var(--green-500)" stroke-width="4"/><path d="M58 108h84" stroke="#fff" stroke-width="3" fill="none"/>',
        'terrazzo' => '<rect width="200" height="140" fill="#f2f6fa"/><g><path d="m30 32 9 9-9 9-9-9z" fill="var(--blue-700)" opacity=".6"/><path d="m96 24 8 8-8 8-8-8z" fill="var(--green-600)" opacity=".6"/><path d="m158 44 9 9-9 9-9-9z" fill="#94a3b0" opacity=".6"/><path d="m58 84 8 8-8 8-8-8z" fill="var(--green-600)" opacity=".5"/><path d="m124 96 9 9-9 9-9-9z" fill="var(--blue-700)" opacity=".5"/><path d="m176 108 7 7-7 7-7-7z" fill="#94a3b0" opacity=".5"/></g>',
    ];
    $scene = $scenes[$key] ?? $scenes['metallic'];
    return '<svg class="swatch-art" viewBox="0 0 200 140" role="img" aria-hidden="true" preserveAspectRatio="xMidYMid slice" focusable="false">'
        . $scene . '</svg>';
}
