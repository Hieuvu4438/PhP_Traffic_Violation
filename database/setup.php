<?php
/**
 * Script import database — chạy 1 lần để khởi tạo
 * Usage: php database/setup.php
 */

/**
 * Parse SQL file into executable statements, stripping comments and
 * filtering out CREATE DATABASE / USE statements.
 */
function parseSqlStatements(string $sql): array
{
    $chunks = explode(';', $sql);
    $result = [];

    foreach ($chunks as $chunk) {
        // Strip leading/trailing whitespace and comment lines
        $lines = explode("\n", trim($chunk));
        $cleanLines = [];
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '' || str_starts_with($trimmed, '--')) {
                continue;
            }
            $cleanLines[] = $line;
        }
        $statement = trim(implode("\n", $cleanLines));

        if ($statement === '') {
            continue;
        }

        $upper = strtoupper($statement);
        if (str_starts_with($upper, 'CREATE DATABASE') || str_starts_with($upper, 'USE ')) {
            continue;
        }

        $result[] = $statement;
    }

    return $result;
}

$host = 'localhost';
$user = 'root';
$pass = ''; // XAMPP mặc định = rỗng. Nếu có password thì điền vào đây
$dbName = 'traffic_violation_db';

echo "=== DATABASE SETUP ===\n\n";

try {
    // Kết nối không chọn database
    $pdo = new PDO("mysql:host={$host};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    // Tạo database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "[OK] Database '{$dbName}' created.\n";

    // Import schema
    $pdo->exec("USE `{$dbName}`");
    $schema = file_get_contents(__DIR__ . '/schema.sql');
    $statements = parseSqlStatements($schema);
    foreach ($statements as $sql) {
        $pdo->exec($sql);
    }
    echo "[OK] Schema imported (" . count($statements) . " tables).\n";

    // Import seed data
    $seed = file_get_contents(__DIR__ . '/seed.sql');
    $seedStatements = parseSqlStatements($seed);
    foreach ($seedStatements as $sql) {
        $pdo->exec($sql);
    }
    echo "[OK] Seed data imported.\n";

    // Verify
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "\n=== TABLES CREATED (" . count($tables) . ") ===\n";
    foreach ($tables as $table) {
        $count = $pdo->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();
        echo "  - {$table}: {$count} rows\n";
    }

    echo "\n=== SETUP COMPLETE ===\n";
    echo "Admin: admin@traffic.vn / admin123\n";
    echo "User: an.nguyen@gmail.com / 123456\n";

} catch (PDOException $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
    exit(1);
}
