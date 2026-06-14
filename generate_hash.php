<?php
// Hash generator for admin password
// Use this to get the correct bcrypt hash

$password = 'Admin@123';
$hash = password_hash($password, PASSWORD_BCRYPT);

echo "Password: " . $password . "\n";
echo "Bcrypt Hash: " . $hash . "\n";
echo "\n";
echo "SQL Command:\n";
echo "UPDATE users SET password = '" . $hash . "' WHERE email = 'admin@bookstore.com';\n";
?>
