<?php

/**
 * Script to fix MySQL user permissions
 * This will grant root user access from localhost
 */

// Try to connect using --skip-grant-tables approach or via socket
// First, let's try to connect and fix permissions

$config = [
    'host' => '127.0.0.1',
    'port' => '3306',
    'username' => 'root',
    'password' => '',
];

echo "Attempting to fix MySQL permissions...\n";

// Method 1: Try using XAMPP's MySQL admin directly
$mysqlPath = 'C:\\xampp\\mysql\\bin\\mysql.exe';
$adminPath = 'C:\\xampp\\mysql\\bin\\mysqladmin.exe';

if (file_exists($adminPath)) {
    echo "Found MySQL admin tool.\n";
}

// Create a SQL script file that will be executed
$sqlScript = <<<'SQL'
-- Fix root user permissions
CREATE DATABASE IF NOT EXISTS elibrary CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Grant all privileges to root from localhost and 127.0.0.1
GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' IDENTIFIED BY '';
GRANT ALL PRIVILEGES ON *.* TO 'root'@'127.0.0.1' IDENTIFIED BY '';
FLUSH PRIVILEGES;
SQL;

// Write SQL script to temp file
$tempFile = sys_get_temp_dir() . '\\fix_mysql_' . time() . '.sql';
file_put_contents($tempFile, $sqlScript);

echo "Created SQL script: $tempFile\n";
echo "\nPlease run this command manually in XAMPP's MySQL console or phpMyAdmin:\n\n";
echo file_get_contents($tempFile);
echo "\n\nOR run this command:\n";
echo "C:\\xampp\\mysql\\bin\\mysql.exe -u root < \"$tempFile\"\n";

// Try alternative: Check if database exists via phpMyAdmin API or file system
echo "\n--- Alternative Solution ---\n";
echo "1. Open phpMyAdmin: http://localhost/phpmyadmin\n";
echo "2. Click on 'SQL' tab\n";
echo "3. Paste and run this SQL:\n\n";
echo "CREATE DATABASE IF NOT EXISTS elibrary CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\n";

