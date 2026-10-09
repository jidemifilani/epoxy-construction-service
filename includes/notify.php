<?php
/**
 * WhatsApp alerts for new enquiries and orders.
 *
 * Uses the WhatsApp Business Cloud API from Meta. A new quote request, enquiry or
 * order is sent to the owner's WhatsApp as a template message, so it arrives on the
 * phone within seconds, whether or not the admin dashboard is open.
 *
 * Guarantees:
 *  - Nothing is sent until the access token, phone number ID and alert number are all set.
 *  - A failed send never fails the visitor's request. The enquiry is saved first,
 *    and the failure goes to the PHP error log.
 *  - The request has a short timeout, so a slow network cannot hold up the visitor.
 *
 * The template used must be created and approved in WhatsApp Manager. See the
 * setup steps in SEO-IMPLEMENTATION-STATUS.md under "WhatsApp alerts".
 */

declare(strict_types=1);

const WHATSAPP_PARAM_MAX = 300;   // characters per template variable

/** Digits only, with the country code. A leading 0 becomes 234 (Nigeria). */
function normalise_phone_digits(string $number): string
{
    $digits = preg_replace('/\D/', '', $number) ?? '';
    if ($digits === '') {
        return '';
    }
    if (str_starts_with($digits, '0')) {
        $digits = '234' . substr($digits, 1);
    }
    return $digits;
}

/** The owner's number, from Settings > Enquiry alerts. */
function whatsapp_alert_number(): string
{
    return normalise_phone_digits((string) cfg('whatsapp_alert_number'));
}

/** True only when everything needed to send is in place. */
function whatsapp_configured(): bool
{
    $wa = config()['whatsapp'] ?? [];
    return trim((string) ($wa['access_token'] ?? '')) !== ''
        && trim((string) ($wa['phone_number_id'] ?? '')) !== ''
        && whatsapp_alert_number() !== '';
}

/**
 * The JSON body for one template message. Pure: builds the request, sends nothing.
 * Empty variables are rejected by the API, so they are replaced with a dash.
 */
function whatsapp_template_payload(string $to, string $template, string $language, array $values): array
{
    $parameters = [];
    foreach (array_values($values) as $value) {
        $text = trim((string) preg_replace('/\s+/u', ' ', (string) $value));
        $parameters[] = [
            'type' => 'text',
            'text' => $text === '' ? '-' : mb_substr($text, 0, WHATSAPP_PARAM_MAX),
        ];
    }

    return [
        'messaging_product' => 'whatsapp',
        'to'                => $to,
        'type'              => 'template',
        'template'          => [
            'name'       => $template,
            'language'   => ['code' => $language],
            'components' => [['type' => 'body', 'parameters' => $parameters]],
        ],
    ];
}

/**
 * Sends one request to the Cloud API. Returns null when the API accepted it,
 * otherwise a short description of what went wrong. Never throws.
 */
function whatsapp_send(array $payload, string $token, string $phoneNumberId, string $version): ?string
{
    if (!function_exists('curl_init')) {
        return 'the PHP cURL extension is not enabled';
    }

    $url = 'https://graph.facebook.com/' . rawurlencode($version) . '/' . rawurlencode($phoneNumberId) . '/messages';
    $json = json_encode($payload, JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        return 'the message could not be encoded';
    }

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $json,
        CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $token, 'Content-Type: application/json'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 3,
        CURLOPT_TIMEOUT        => 6,
    ]);
    $body   = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $error  = curl_error($ch);

    if ($body === false) {
        return 'network error: ' . $error;
    }
    if ($status >= 200 && $status < 300) {
        return null;
    }

    $decoded = json_decode((string) $body, true);
    $message = is_array($decoded) ? ($decoded['error']['message'] ?? null) : null;
    return 'API responded HTTP ' . $status . ($message !== null ? ': ' . $message : '');
}

/**
 * Alerts the owner about one enquiry or order. The five template variables are:
 * what came in, who sent it, their phone, a short detail line, and a reference.
 */
function notify_owner_whatsapp(string $headline, string $name, string $phone, string $detail, string $reference): void
{
    if (!whatsapp_configured()) {
        return;
    }

    $wa = config()['whatsapp'];
    $payload = whatsapp_template_payload(
        whatsapp_alert_number(),
        (string) ($wa['template'] ?? 'new_enquiry'),
        (string) ($wa['template_language'] ?? 'en'),
        [$headline, $name, $phone, $detail, $reference]
    );

    $error = whatsapp_send(
        $payload,
        (string) $wa['access_token'],
        (string) $wa['phone_number_id'],
        (string) ($wa['graph_version'] ?? 'v21.0')
    );

    if ($error !== null) {
        error_log('[iris] WhatsApp alert not sent (' . $reference . '): ' . $error);
    }
}

/** One readable line for the owner's alert: the answers that matter most. */
function whatsapp_detail_line(array $parts): string
{
    return implode(' | ', array_filter(array_map(static fn ($p): string => trim((string) $p), $parts), 'strlen'));
}
