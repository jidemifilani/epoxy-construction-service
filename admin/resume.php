<?php
/**
 * Streams one applicant's CV. Requires an admin sign-in; the file itself lives
 * under storage/, which is never reachable by a direct URL.
 */

require_once __DIR__ . '/includes/admin.php';

require_admin();

$id = (int) ($_GET['id'] ?? 0);
$application = $id > 0 ? db_row('SELECT resume_path, resume_original_name, resume_mime, applicant_name FROM job_applications WHERE id = ?', [$id]) : null;

if ($application === null) {
    http_response_code(404);
    exit('Not found.');
}

$relative = (string) $application['resume_path'];
// Only ever serve a file inside storage/resumes/, whatever the database says.
if (!str_starts_with($relative, 'storage/resumes/') || str_contains($relative, '..')) {
    http_response_code(404);
    exit('Not found.');
}

$full = __DIR__ . '/../' . $relative;
if (!is_file($full)) {
    http_response_code(404);
    exit('That CV is no longer on the server.');
}

$mime = (string) $application['resume_mime'] ?: 'application/octet-stream';
$extension = match ($mime) {
    'application/pdf' => '.pdf',
    'application/msword' => '.doc',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => '.docx',
    default => '',
};
$downloadName = $application['resume_original_name'] !== ''
    ? $application['resume_original_name']
    : preg_replace('/[^A-Za-z0-9 _-]/', '', (string) $application['applicant_name']) . '-cv' . $extension;

while (ob_get_level() > 0) {
    ob_end_clean();
}

header('Content-Type: ' . $mime);
header('Content-Disposition: attachment; filename="' . str_replace('"', '', $downloadName) . '"');
header('Content-Length: ' . filesize($full));
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, no-store');

readfile($full);
exit;
