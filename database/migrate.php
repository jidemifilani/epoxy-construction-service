<?php
/**
 * Applies any database/migrations/*.sql files that have not run yet, in name order.
 *
 *   php database/migrate.php
 *
 * Each applied file is recorded in schema_migrations, so running this again is
 * a no-op. database/install.php calls run_migrations() too, so a fresh install
 * always ends up fully migrated.
 */

require_once __DIR__ . '/../includes/functions.php';

function run_migrations(): array
{
    $pdo = db_connect();
    $pdo->exec('CREATE TABLE IF NOT EXISTS schema_migrations (
        name VARCHAR(190) NOT NULL PRIMARY KEY,
        applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

    $applied = $pdo->query('SELECT name FROM schema_migrations')->fetchAll(PDO::FETCH_COLUMN);
    $files = glob(__DIR__ . '/migrations/*.sql') ?: [];
    sort($files);

    $ran = [];
    foreach ($files as $file) {
        $name = basename($file);
        if (in_array($name, $applied, true)) {
            continue;
        }

        $sql = preg_replace('/^\s*--.*$/m', '', (string) file_get_contents($file)) ?? '';
        foreach (array_filter(array_map('trim', explode(";\n", $sql . "\n"))) as $statement) {
            $pdo->exec($statement);
        }

        $pdo->prepare('INSERT INTO schema_migrations (name) VALUES (?)')->execute([$name]);
        $ran[] = $name;
    }
    return $ran;
}

// Run directly from the command line; install.php includes this file and calls the function.
if (PHP_SAPI === 'cli' && realpath($argv[0] ?? '') === realpath(__FILE__)) {
    try {
        $ran = run_migrations();
    } catch (Throwable $e) {
        fwrite(STDERR, 'Migration failed: ' . $e->getMessage() . PHP_EOL);
        exit(1);
    }
    fwrite(STDOUT, $ran === [] ? "Database is up to date.\n" : 'Applied: ' . implode(', ', $ran) . "\n");
}
