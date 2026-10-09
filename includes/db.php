<?php
/**
 * Database access. One PDO connection per request, prepared statements
 * everywhere, and exceptions turned into a plain 503 page rather than a
 * stack trace on a public site.
 */

declare(strict_types=1);

/**
 * The shared connection, opened on first use. Throws if it cannot connect;
 * callers that must keep working without a database catch that.
 */
function db_connect(): PDO
{
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $cfg = config()['db'];
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        $cfg['host'], (int) $cfg['port'], $cfg['name'], $cfg['charset']
    );

    $pdo = new PDO($dsn, $cfg['user'], $cfg['password'], [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_TIMEOUT            => 3,
    ]);
    return $pdo;
}

/**
 * The connection for page code. If the database is down the visitor gets a
 * short, honest 503 instead of a fatal error.
 */
function db(): PDO
{
    try {
        return db_connect();
    } catch (Throwable $e) {
        http_response_code(503);
        header('Content-Type: text/html; charset=utf-8');
        echo '<!doctype html><meta charset="utf-8"><title>Temporarily unavailable</title>'
           . '<body style="font-family:system-ui;max-width:560px;margin:12vh auto;padding:0 16px;color:#0b2545">'
           . '<h1>We are updating the site</h1>'
           . '<p>The website is briefly unavailable. Please try again in a few minutes, '
           . 'or call the office directly.</p></body>';
        error_log('[iris] database unavailable: ' . $e->getMessage());
        exit;
    }
}

/** All rows of a query. */
function db_rows(string $sql, array $params = []): array
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/** The first row of a query, or null. */
function db_row(string $sql, array $params = []): ?array
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch();
    return $row === false ? null : $row;
}

/** A single value from the first row of a query, or null. */
function db_value(string $sql, array $params = [])
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $value = $stmt->fetchColumn();
    return $value === false ? null : $value;
}

/** Run a statement that does not return rows. Returns the affected row count. */
function db_exec(string $sql, array $params = []): int
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->rowCount();
}

/**
 * Insert a row. $table and the column names come from our own code, never
 * from the request, so they are checked against a strict identifier pattern
 * rather than quoted as user data.
 */
function db_insert(string $table, array $data): int
{
    assert_identifier($table);
    $columns = array_keys($data);
    foreach ($columns as $column) {
        assert_identifier($column);
    }

    $sql = sprintf(
        'INSERT INTO `%s` (`%s`) VALUES (%s)',
        $table,
        implode('`, `', $columns),
        implode(', ', array_fill(0, count($columns), '?'))
    );
    db_exec($sql, array_values($data));
    return (int) db()->lastInsertId();
}

/** Update a row by id. Same identifier rule as db_insert(). */
function db_update(string $table, int $id, array $data): void
{
    assert_identifier($table);
    $assignments = [];
    foreach (array_keys($data) as $column) {
        assert_identifier($column);
        $assignments[] = '`' . $column . '` = ?';
    }

    $sql = sprintf('UPDATE `%s` SET %s WHERE id = ?', $table, implode(', ', $assignments));
    db_exec($sql, [...array_values($data), $id]);
}

function assert_identifier(string $name): void
{
    if (!preg_match('/^[a-z_][a-z0-9_]*$/', $name)) {
        throw new InvalidArgumentException('Unsafe identifier: ' . $name);
    }
}
