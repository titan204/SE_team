<?php
/**
 * verify_auth.php
 * ─────────────────────────────────────────────────────────────
 * Verifies that password_verify() succeeds for every demo/seed
 * account in the users table.  Also confirms the hash itself
 * does NOT pass verification (anti-double-hash protection).
 *
 * Run: C:\xampp\php\php.exe tasks/verify_auth.php
 * ─────────────────────────────────────────────────────────────
 */

// Avoid ROOT_PATH redefinition warning from config.php
if (!defined('ROOT_PATH')) define('ROOT_PATH', dirname(__DIR__));
require ROOT_PATH . '/config/config.php';

$db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
if (!$db) die("DB connection failed: " . mysqli_connect_error() . PHP_EOL);
mysqli_set_charset($db, DB_CHARSET);

// Known plain-text password for all seeded accounts
$PLAIN = 'password';

$result = mysqli_query($db,
    "SELECT u.id, u.name, u.email, u.password, u.is_active, r.name AS role_name
     FROM users u
     JOIN roles r ON u.role_id = r.id
     ORDER BY r.name, u.id"
);

echo PHP_EOL;
echo "=== Authentication Verification Report ===" . PHP_EOL;
echo str_repeat("─", 65) . PHP_EOL;
printf("%-4s %-18s %-34s %-14s %s\n", "ID", "Role", "Email", "Login OK?", "Hash-as-pwd rejected?");
echo str_repeat("─", 65) . PHP_EOL;

$totalPass = 0;
$totalFail = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $storedHash = $row['password'];

    // Test 1: plain password must succeed
    $loginOk = password_verify($PLAIN, $storedHash);

    // Test 2: hash itself must NOT work as a password (anti double-hash)
    $hashAsPasswordFails = !password_verify($storedHash, $storedHash);

    $loginLabel = $loginOk           ? "PASS ✓" : "FAIL ✗";
    $hashLabel  = $hashAsPasswordFails ? "PASS ✓" : "FAIL ✗";

    if ($loginOk && $hashAsPasswordFails) {
        $totalPass++;
    } else {
        $totalFail++;
    }

    printf("%-4s %-18s %-34s %-14s %s\n",
        $row['id'],
        substr($row['role_name'], 0, 17),
        substr($row['email'], 0, 33),
        $loginLabel,
        $hashLabel
    );
}

echo str_repeat("─", 65) . PHP_EOL;
echo PHP_EOL;
echo "  Total PASS: {$totalPass}" . PHP_EOL;
echo "  Total FAIL: {$totalFail}" . PHP_EOL;
echo PHP_EOL;

if ($totalFail === 0) {
    echo "  ✓ ALL accounts use secure bcrypt.  Login with: '{$PLAIN}'" . PHP_EOL;
} else {
    echo "  ✗ {$totalFail} account(s) need attention (see FAIL rows above)." . PHP_EOL;
}

echo PHP_EOL;
mysqli_close($db);
