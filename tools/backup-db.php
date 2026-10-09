<?php
/**
 * Backs up the database to storage/backups/ and keeps the newest 14 copies.
 *
 *   php tools/backup-db.php
 *
 * Schedule it weekly (Windows Task Scheduler: program "C:\xampp\php\php.exe",
 * arguments "tools\backup-db.php", start in the project folder). Copy the
 * backups off the server too; a backup on the same disk does not protect
 * against losing the disk.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../includes/functions.php';

const BACKUPS_KEPT = 14;

$db       = config()['db'];
$dir      = __DIR__ . '/../storage/backups';
$mysqldump = is_file('C:/xampp/mysql/bin/mysqldump.exe') ? 'C:/xampp/mysql/bin/mysqldump.exe' : 'mysqldump';

if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
    fwrite(STDERR, "Could not create storage/backups.\n");
    exit(1);
}

$file = $dir . '/' . $db['name'] . '-' . date('Ymd-His') . '.sql';

// The password goes through the environment, not the command line, so it never
// appears in the process list.
putenv('MYSQL_PWD=' . $db['password']);

$command = escapeshellarg($mysqldump)
    . ' --host=' . escapeshellarg((string) $db['host'])
    . ' --port=' . (int) $db['port']
    . ' --user=' . escapeshellarg((string) $db['user'])
    . ' --single-transaction --routines --triggers --default-character-set=utf8mb4'
    . ' --databases ' . escapeshellarg((string) $db['name'])
    . ' --result-file=' . escapeshellarg($file)
    . ' 2>&1';

exec($command, $output, $code);
if ($code !== 0 || !is_file($file) || filesize($file) === 0) {
    @unlink($file);
    fwrite(STDERR, "Backup failed (exit $code): " . implode(' ', $output) . "\n");
    exit(1);
}

// Keep the newest copies and remove the rest.
$backups = glob($dir . '/*.sql') ?: [];
rsort($backups);
foreach (array_slice($backups, BACKUPS_KEPT) as $old) {
    unlink($old);
}

fwrite(STDOUT, 'Backed up to ' . basename($file) . ' (' . round(filesize($file) / 1024) . " KB). "
    . 'Keeping the newest ' . BACKUPS_KEPT . ".\n");
