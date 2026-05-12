<?php
/**
 * fix_passwords.php
 * ─────────────────────────────────────────────────────────────
 * One-shot migration: detects users whose stored password is NOT
 * a valid bcrypt hash and replaces it with a bcrypt hash of a
 * known fallback password.
 *
 * Run ONCE from the project root:
 *   php tasks/fix_passwords.php
 *
 * After running, tell affected users their temporary password and
 * have them change it on next login.
 * ─────────────────────────────────────────────────────────────
 */

define('ROOT_PATH', dirname(__DIR__));
require ROOT_PATH . '/config/config.php';

// ── 1. Connect ────────────────────────────────────────────────
$db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
if (!$db) {
    die("DB connection failed: " . mysqli_connect_error() . PHP_EOL);
}
mysqli_set_charset($db, DB_CHARSET);

// ── 2. Fallback password for broken accounts ──────────────────
//      Change this before running if you want a different default.
$FALLBACK_PASSWORD  = 'password';
$fallbackHash       = password_hash($FALLBACK_PASSWORD, PASSWORD_DEFAULT);

// ── 3. Load all users ─────────────────────────────────────────
$result = mysqli_query($db, "SELECT id, name, email, password FROM users");
if (!$result) {
    die("Query failed: " . mysqli_error($db) . PHP_EOL);
}

$fixed   = [];
$ok      = [];

while ($row = mysqli_fetch_assoc($result)) {
    $storedHash = $row['password'];

    // password_get_info() returns algo = 0 for anything that is NOT
    // a recognised hash (bcrypt, argon2, etc.).
    $info = password_get_info($storedHash);

    if ($info['algo'] === 0 || $info['algo'] === null || $info['algo'] === '') {
        // Not a valid bcrypt/modern hash — replace it
        $safeHash = mysqli_real_escape_string($db, $fallbackHash);
        $id       = (int) $row['id'];

        if (mysqli_query($db, "UPDATE users SET password = '$safeHash' WHERE id = $id")) {
            $fixed[] = [
                'id'    => $id,
                'name'  => $row['name'],
                'email' => $row['email'],
                'old'   => substr($storedHash, 0, 20) . '…',
            ];
        } else {
            echo "[ERROR] Could not update user ID {$id}: " . mysqli_error($db) . PHP_EOL;
        }
    } else {
        $ok[] = $row['email'];
    }
}

// ── 4. Report ─────────────────────────────────────────────────
echo PHP_EOL;
echo "=== Password Migration Report ===" . PHP_EOL;
echo PHP_EOL;

if (empty($fixed)) {
    echo "✓ No broken hashes found. All users already have valid bcrypt passwords." . PHP_EOL;
} else {
    echo "Fixed " . count($fixed) . " user(s) with broken/non-bcrypt hashes:" . PHP_EOL;
    foreach ($fixed as $u) {
        echo "  [ID {$u['id']}] {$u['name']} <{$u['email']}>" . PHP_EOL;
        echo "         Old hash prefix : {$u['old']}" . PHP_EOL;
        echo "         New password    : {$FALLBACK_PASSWORD}" . PHP_EOL;
    }
}

echo PHP_EOL;
echo count($ok) . " user(s) already had valid bcrypt hashes — untouched." . PHP_EOL;
echo PHP_EOL;
echo "Verification:" . PHP_EOL;
echo "  password_verify('{$FALLBACK_PASSWORD}', newHash) = " .
     (password_verify($FALLBACK_PASSWORD, $fallbackHash) ? 'PASS ✓' : 'FAIL ✗') . PHP_EOL;
echo PHP_EOL;
echo "DONE. Affected users must log in with password: '{$FALLBACK_PASSWORD}' and change it." . PHP_EOL;

mysqli_close($db);
