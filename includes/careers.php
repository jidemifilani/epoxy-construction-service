<?php
/**
 * Careers: job postings and applications.
 *
 * A CV is personal data, so it is never stored anywhere the web can reach.
 * It goes in storage/resumes/, which storage/.htaccess already denies all
 * access to; the only way to read one back is admin/resume.php, which
 * requires an admin sign-in and streams the file itself.
 */

declare(strict_types=1);

require_once __DIR__ . '/leads.php';     // posted(), posted_text(), submission_guard_failed()
require_once __DIR__ . '/notify.php';

const RESUME_DIR      = __DIR__ . '/../storage/resumes';
const RESUME_MAX_SIZE = 5 * 1024 * 1024;   // 5 MB is generous for a CV
const RESUME_MIME_EXT = [
    'application/pdf' => 'pdf',
    'application/msword' => 'doc',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
];

/** Open, published jobs, in the order the admin set. Shown on the careers page. */
function open_jobs(): array
{
    return db_rows("SELECT * FROM jobs WHERE published = 1 AND status = 'open' ORDER BY sort, id");
}

/** One job by slug, published or not, open or closed: a direct link still works either way. */
function job_find(string $slug): ?array
{
    return db_row('SELECT * FROM jobs WHERE slug = ? AND published = 1', [$slug]);
}

/** A newline-separated field (responsibilities, requirements) as a clean list. */
function job_lines(?string $text): array
{
    return array_values(array_filter(array_map('trim', explode("\n", (string) $text)), 'strlen'));
}

/**
 * Validates and stores an uploaded CV. Returns [relative path, original name, mime],
 * or throws InvalidArgumentException with a message the applicant can read.
 */
function resume_store_upload(array $file): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            throw new InvalidArgumentException('Attach your CV to apply.');
        }
        throw new InvalidArgumentException('Your CV could not be uploaded. Please try again.');
    }

    $tmp = (string) $file['tmp_name'];
    if ((int) filesize($tmp) > RESUME_MAX_SIZE) {
        throw new InvalidArgumentException('Your CV must be 5 MB or smaller.');
    }

    $mime = (string) (new finfo(FILEINFO_MIME_TYPE))->file($tmp);
    if (!isset(RESUME_MIME_EXT[$mime])) {
        throw new InvalidArgumentException('Attach your CV as a PDF or Word document (.pdf, .doc or .docx).');
    }

    if (!is_dir(RESUME_DIR) && !mkdir(RESUME_DIR, 0775, true) && !is_dir(RESUME_DIR)) {
        throw new RuntimeException('Could not create the resumes folder.');
    }

    $relative = 'storage/resumes/' . bin2hex(random_bytes(12)) . '.' . RESUME_MIME_EXT[$mime];
    $destination = __DIR__ . '/../' . $relative;
    if (!move_uploaded_file($tmp, $destination)) {
        throw new RuntimeException('Your CV could not be saved. Please try again.');
    }

    $originalName = basename((string) ($file['name'] ?? 'cv'));
    return [$relative, $originalName, $mime];
}

/** Validates the application form. Returns [data, errors]. */
function validate_application(): array
{
    $data = [];
    $errors = [];

    foreach (['applicant_name' => ['Your name', 160], 'email' => ['Email', 160], 'phone' => ['Phone / WhatsApp', 40]] as $field => [$label, $max]) {
        $value = posted($field);
        if ($value === '') {
            $errors[$field] = $label . ' is required.';
        } elseif (mb_strlen($value) > $max) {
            $errors[$field] = $label . ' is too long.';
        }
        $data[$field] = $value;
    }

    if ($data['email'] !== '' && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter a valid email address.';
    }
    if ($data['phone'] !== '' && preg_match_all('/[0-9]/', $data['phone']) < 7) {
        $errors['phone'] = 'Enter a phone or WhatsApp number we can reach you on.';
    }

    $data['cover_note'] = posted_text('cover_note');
    if (mb_strlen($data['cover_note']) > 2000) {
        $errors['cover_note'] = 'Keep this under 2000 characters.';
    }

    return [$data, $errors];
}

/** Statuses an application can be moved through in the admin. */
function application_statuses(): array
{
    return [
        'new'         => 'New',
        'reviewed'    => 'Reviewed',
        'shortlisted' => 'Shortlisted',
        'interview'   => 'Interview',
        'hired'       => 'Hired',
        'rejected'    => 'Not progressing',
    ];
}
