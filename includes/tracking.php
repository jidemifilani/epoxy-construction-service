<?php
/**
 * Campaign tracking for paid advertising and analytics.
 *
 *  - Captures utm_* and click IDs (gclid, fbclid) from the arrival URL and
 *    keeps them in the session, so an enquiry days later still knows which
 *    advert brought the visitor in.
 *  - Outputs the Google Analytics 4 / Google Ads and Meta Pixel tags, only for
 *    IDs that are set in Settings. Nothing is loaded until an ID exists.
 *  - Fires conversion events on the thank-you and order-confirmation pages.
 *
 * Consent: these tags set cookies. Before running paid campaigns in the UK or EU
 * audiences, add a consent banner; for Nigeria, state the cookies in the privacy policy.
 */

declare(strict_types=1);

const TRACKED_PARAMS = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'gclid', 'fbclid'];

/** Store campaign parameters from this request, if any. Called once per page, early. */
function tracking_capture(): void
{
    $found = [];
    foreach (TRACKED_PARAMS as $param) {
        $value = $_GET[$param] ?? null;
        if (is_string($value) && $value !== '') {
            $found[$param] = mb_substr(trim($value), 0, 120);
        }
    }
    if ($found === []) {
        return;
    }
    start_session_if_needed();
    // Last touch wins: the most recent advert a visitor clicked is the one credited.
    $_SESSION['campaign'] = $found + ['landed' => date('c'), 'landing_page' => current_path()];
}

/** The campaign captured earlier in this session, or []. */
function tracking_campaign(): array
{
    start_session_if_needed();
    return is_array($_SESSION['campaign'] ?? null) ? $_SESSION['campaign'] : [];
}

/** One line for an enquiry, so the admin can see where it came from. Empty if nothing was captured. */
function tracking_summary(): string
{
    $campaign = tracking_campaign();
    $parts = [];
    foreach (TRACKED_PARAMS as $param) {
        if (!empty($campaign[$param])) {
            $parts[] = $param . '=' . $campaign[$param];
        }
    }
    if ($parts === []) {
        return '';
    }
    return 'Campaign: ' . implode(' | ', $parts) . (isset($campaign['landing_page']) ? ' | Landed on: ' . $campaign['landing_page'] : '');
}

/** Head tags for analytics and ads. Output only what is configured. */
function tracking_head(): string
{
    $out = '';
    $ga4   = trim((string) cfg('ga4_measurement_id'));
    $adsId = trim((string) cfg('google_ads_id'));
    $pixel = trim((string) cfg('meta_pixel_id'));

    // One gtag.js loader serves GA4 and Google Ads. Only IDs that exist are configured.
    $gtagIds = array_values(array_filter([$ga4, $adsId]));
    if ($gtagIds !== []) {
        $out .= '<script async src="https://www.googletagmanager.com/gtag/js?id=' . e($gtagIds[0]) . '"></script>' . "\n";
        $config = "window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());";
        foreach ($gtagIds as $id) {
            $config .= "gtag('config'," . json_encode($id) . ");";
        }
        $out .= '<script>' . $config . '</script>' . "\n";
    }

    if ($pixel !== '') {
        $out .= '<script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?'
              . 'n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;'
              . 'n.push=n;n.loaded=!0;n.version="2.0";n.queue=[];t=b.createElement(e);t.async=!0;'
              . 't.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,"script",'
              . '"https://connect.facebook.net/en_US/fbevents.js");fbq("init",' . json_encode($pixel) . ');fbq("track","PageView");</script>' . "\n";
    }
    return $out;
}

/**
 * Conversion events for a page that marks a completed action. $event is 'lead'
 * (enquiry form) or 'purchase' (order placed), with an optional naira value.
 */
function tracking_conversion(string $event, float $value = 0.0, string $reference = ''): string
{
    $ga4Id  = trim((string) cfg('ga4_measurement_id'));
    $adsId  = trim((string) cfg('google_ads_id'));
    $label  = trim((string) cfg('google_ads_conversion_label'));
    $pixel  = trim((string) cfg('meta_pixel_id'));

    $js = '';
    $ga4Event = $event === 'purchase' ? 'purchase' : 'generate_lead';
    $params = ['currency' => 'NGN', 'value' => round($value, 2), 'transaction_id' => $reference];

    if ($ga4Id !== '' || $adsId !== '') {
        $js .= 'gtag("event",' . json_encode($ga4Event) . ',' . json_encode($params) . ');';
    }
    if ($adsId !== '' && $label !== '') {
        $js .= 'gtag("event","conversion",{"send_to":' . json_encode($adsId . '/' . $label) . ',"value":' . json_encode(round($value, 2)) . ',"currency":"NGN","transaction_id":' . json_encode($reference) . '});';
    }
    if ($pixel !== '') {
        $fbEvent = $event === 'purchase' ? 'Purchase' : 'Lead';
        $js .= 'fbq("track",' . json_encode($fbEvent) . ',' . json_encode(['value' => round($value, 2), 'currency' => 'NGN']) . ');';
    }
    if ($js === '') {
        return '';
    }
    return '<script>' . $js . '</script>' . "\n";
}
