<?php
/**
 * Enquiry handling for the contact, quote and trade forms, and the form-reading
 * helpers the shop uses too.
 *
 * Every enquiry is stored in the leads table, where the admin dashboard lists
 * it. Email notification is opt-in via config('notify') and a failure to send
 * never loses the enquiry, because it is already in the database.
 */

declare(strict_types=1);

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/tracking.php';

/** Trim and collapse whitespace on a posted value. */
function posted(string $key, string $default = ''): string
{
    $value = $_POST[$key] ?? $default;
    if (!is_string($value)) {
        return $default;
    }
    return trim(preg_replace('/\s+/u', ' ', $value) ?? '');
}

/** Multi-line posted value (the message textarea), newlines preserved. */
function posted_text(string $key): string
{
    $value = $_POST[$key] ?? '';
    return is_string($value) ? trim($value) : '';
}

/**
 * Validate a submission.
 *
 * $rules maps field => 'required|email|phone|max:500'. Returns [data, errors].
 */
function validate_submission(array $rules): array
{
    $data = [];
    $errors = [];

    foreach ($rules as $field => $ruleString) {
        $value = $field === 'message' ? posted_text($field) : posted($field);
        $checks = explode('|', $ruleString);
        $label = ucwords(str_replace('_', ' ', $field));

        foreach ($checks as $check) {
            if ($check === 'required' && $value === '') {
                $errors[$field] = $label . ' is required.';
                continue 2;
            }
            if ($value === '') {
                continue;
            }
            if ($check === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[$field] = 'Enter a valid email address.';
                continue 2;
            }
            if ($check === 'phone' && preg_match_all('/[0-9]/', $value) < 7) {
                $errors[$field] = 'Enter a phone or WhatsApp number we can reach you on.';
                continue 2;
            }
            if (str_starts_with($check, 'max:')) {
                $max = (int) substr($check, 4);
                if (mb_strlen($value) > $max) {
                    $errors[$field] = $label . ' must be ' . $max . ' characters or fewer.';
                    continue 2;
                }
            }
            if (str_starts_with($check, 'in:')) {
                $allowed = explode(',', substr($check, 3));
                if (!in_array($value, $allowed, true)) {
                    $errors[$field] = 'Choose one of the listed options.';
                    continue 2;
                }
            }
        }
        $data[$field] = $value;
    }

    return [$data, $errors];
}

/**
 * Guards that apply to every form: CSRF, the honeypot, and a per-session
 * throttle so a single visitor cannot hammer the endpoint.
 */
function submission_guard_failed(): ?string
{
    if (!csrf_valid($_POST['csrf'] ?? null)) {
        return 'Your session expired before the form was sent. Please try again.';
    }

    // Bots fill hidden fields; people do not.
    if (posted('website') !== '') {
        return 'That submission looked automated. Please call or WhatsApp us instead.';
    }

    start_session_if_needed();
    $last = $_SESSION['last_submission'] ?? 0;
    if (time() - (int) $last < 20) {
        return 'That came through twice. Give it a moment before sending again.';
    }

    return null;
}

/**
 * Save one enquiry. The three forms ask different questions, so the fields
 * every form shares get their own column and anything else is kept in
 * 'details' as "Label: value" lines rather than dropped.
 */
function record_lead(string $kind, array $data): int
{
    // The trade form names its contact fields differently; map them across.
    $name  = $data['name'] ?? $data['contact_name'] ?? '';
    $extra = $data;
    unset($extra['name'], $extra['contact_name'], $extra['email'], $extra['phone'], $extra['location']);

    $details = [];
    foreach ($extra as $field => $value) {
        if ($value === '') {
            continue;
        }
        $details[] = ucwords(str_replace('_', ' ', $field)) . ': ' . $value;
    }

    // Which advert brought this enquiry in, if the visitor came from one.
    $campaign = tracking_summary();
    if ($campaign !== '') {
        $details[] = $campaign;
    }

    try {
        $leadId = db_insert('leads', [
            'form'       => $kind,
            'name'       => (string) $name,
            'email'      => (string) ($data['email'] ?? ''),
            'phone'      => (string) ($data['phone'] ?? ''),
            'location'   => (string) ($data['location'] ?? ''),
            'details'    => implode("\n", $details),
            'ip_address' => substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45),
        ]);
    } catch (Throwable $e) {
        error_log('[iris] could not save enquiry: ' . $e->getMessage());
        return 0;
    }

    start_session_if_needed();
    $_SESSION['last_submission'] = time();
    return $leadId;
}

/** The short description the owner sees first in a WhatsApp alert. */
function lead_headline(string $kind): string
{
    return match (true) {
        $kind === 'quote'           => 'quote request',
        $kind === 'trade'           => 'trade enquiry',
        str_starts_with($kind, 'landing-') => 'landing page enquiry',
        default                     => 'enquiry',
    };
}

/** Optional email notification. Never fatal. */
function notify_lead(string $kind, array $data): void
{
    if (!cfg('notify')) {
        return;
    }

    $lines = [];
    foreach ($data as $field => $value) {
        $lines[] = ucwords(str_replace('_', ' ', $field)) . ': ' . $value;
    }

    $subject = 'New ' . $kind . ' enquiry - ' . cfg('company_name');
    $body    = implode("\n", $lines) . "\n\nReceived: " . date('r');
    $headers = 'From: ' . cfg('company_name') . ' <' . cfg('email') . ">\r\n"
             . 'Reply-To: ' . ($data['email'] ?? cfg('email')) . "\r\n"
             . 'Content-Type: text/plain; charset=UTF-8';

    @mail(cfg('notify_to'), $subject, $body, $headers);
}

/**
 * Run a form end to end. On success it redirects to /thank-you and exits;
 * on failure it returns the errors for the page to re-render.
 */
function handle_submission(string $kind, array $rules): array
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        return [[], []];
    }

    $guard = submission_guard_failed();
    if ($guard !== null) {
        return [$_POST, ['_form' => $guard]];
    }

    [$data, $errors] = validate_submission($rules);
    if (!empty($errors)) {
        return [$_POST, $errors];
    }

    $leadId = record_lead($kind, $data);
    if ($leadId === 0) {
        return [$_POST, ['_form' => 'We could not save that enquiry. Please call ' . cfg('phone') . ' and we will take the details directly.']];
    }

    notify_lead($kind, $data);
    notify_owner_whatsapp(
        lead_headline($kind),
        (string) ($data['name'] ?? $data['contact_name'] ?? ''),
        (string) ($data['phone'] ?? ''),
        whatsapp_detail_line([
            $data['you_are'] ?? $data['enquiring_as'] ?? '',
            $data['space_type'] ?? $data['project_type'] ?? '',
            isset($data['size']) ? $data['size'] . ' m2' : '',
            isset($data['floor_area']) ? $data['floor_area'] . ' m2' : '',
            $data['location'] ?? '',
            $data['timeline'] ?? '',
        ]),
        'Lead #' . $leadId
    );

    start_session_if_needed();
    $_SESSION['lead_kind'] = $kind;
    $_SESSION['lead_name'] = $data['name'] ?? '';

    header('Location: ' . url('thank-you'), true, 303);
    exit;
}

/** Print the error for one field, if any. */
function field_error(array $errors, string $field): string
{
    return '<span class="field__error" role="alert">' . e($errors[$field] ?? '') . '</span>';
}

/** Repopulate a field after a failed submission. */
function old(array $data, string $field, string $default = ''): string
{
    $value = $data[$field] ?? $default;
    return is_string($value) ? $value : $default;
}

/** Whether a radio/select option should be marked selected after a failure. */
function was_chosen(array $data, string $field, string $value): bool
{
    return ($data[$field] ?? '') === $value;
}
