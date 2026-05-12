<?php
if (!defined('ROOT_PATH')) define('ROOT_PATH', dirname(__DIR__));
require ROOT_PATH . '/config/config.php';

$db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
mysqli_set_charset($db, DB_CHARSET);

// Check user 19 specifically
$r = mysqli_query($db, "SELECT id, name, email, password FROM users WHERE id = 19");
$row = mysqli_fetch_assoc($r);

echo "User ID 19:" . PHP_EOL;
echo "  Name  : " . $row['name'] . PHP_EOL;
echo "  Email : " . $row['email'] . PHP_EOL;
echo "  Hash starts with: " . substr($row['password'], 0, 7) . PHP_EOL;

// Is it a valid bcrypt hash?
$info = password_get_info($row['password']);
echo "  Hash algorithm  : " . ($info['algoName'] ?? 'unknown') . PHP_EOL;
echo "  Is valid bcrypt : " . ($info['algo'] !== 0 ? 'YES - user registered normally with their own password' : 'NO - broken') . PHP_EOL;

mysqli_close($db);
