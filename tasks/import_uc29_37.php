<?php
// ============================================================
//  Import UC29–UC37 new tables into hotel_db
//  Run once: php tasks/import_uc29_37.php
// ============================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'hotel_management');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$conn) {
    echo "Connection failed: " . mysqli_connect_error() . PHP_EOL;
    exit(1);
}

$sql = file_get_contents(__DIR__ . '/../database.sql');

// Find the start of UC29 section (everything we added)
$marker = '-- UC29';
$pos    = strpos($sql, $marker);
if ($pos === false) {
    echo "ERROR: Could not find UC29 marker in database.sql" . PHP_EOL;
    exit(1);
}

$newSql = substr($sql, $pos);

// Split on semicolons to get individual statements
$statements = array_filter(
    array_map('trim', explode(';', $newSql)),
    fn($s) => strlen($s) > 10 && !preg_match('/^--/', ltrim($s))
);

$ok  = 0;
$err = 0;

foreach ($statements as $stmt) {
    // Skip pure comment blocks
    $withoutComments = preg_replace('/--[^\n]*\n/', '', $stmt);
    if (strlen(trim($withoutComments)) < 5) continue;

    $result = mysqli_query($conn, $stmt);
    if ($result === false) {
        $errMsg = mysqli_error($conn);
        // Silently skip "already exists" errors — safe to re-run
        if (
            strpos($errMsg, 'already exists') !== false ||
            strpos($errMsg, 'Duplicate column') !== false ||
            strpos($errMsg, 'Duplicate entry') !== false
        ) {
            echo "  SKIP (already exists): " . substr(trim($stmt), 0, 60) . "…" . PHP_EOL;
        } else {
            echo "  ERROR: $errMsg" . PHP_EOL;
            echo "  SQL: " . substr(trim($stmt), 0, 80) . PHP_EOL;
            $err++;
        }
    } else {
        $ok++;
    }
}

echo PHP_EOL . "Done. Successful: $ok | Errors: $err" . PHP_EOL;
mysqli_close($conn);
