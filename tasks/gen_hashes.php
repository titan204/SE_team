<?php
$plainPassword = 'password';
$hash = password_hash($plainPassword, PASSWORD_DEFAULT);

echo "Plain password : " . $plainPassword . PHP_EOL;
echo "Bcrypt hash    : " . $hash . PHP_EOL;
echo "Verify test    : " . (password_verify($plainPassword, $hash) ? 'PASS' : 'FAIL') . PHP_EOL;
echo PHP_EOL;

// Verify the existing seed hash still works (Laravel well-known hash for 'password')
$seedHash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
echo "Seed hash verify ('password'): " . (password_verify($plainPassword, $seedHash) ? 'PASS - VALID bcrypt' : 'FAIL - BROKEN') . PHP_EOL;

// Verify the SHA-256 hex (broken Revenue Manager hash)
$sha256hex = '3fe20d68a85f0ca590301eb12d95603bc1bc3bc42907d22503fe06bc03000782';
echo "SHA-256 hex verify ('password'): " . (password_verify($plainPassword, $sha256hex) ? 'PASS' : 'FAIL - raw SHA-256, NOT bcrypt - users 17+18 broken') . PHP_EOL;
echo PHP_EOL;

// Hash for Revenue Manager fix
$rmHash = password_hash('password', PASSWORD_DEFAULT);
echo "Revenue Manager fix hash : " . $rmHash . PHP_EOL;
echo "Verify RM hash           : " . (password_verify('password', $rmHash) ? 'PASS' : 'FAIL') . PHP_EOL;
