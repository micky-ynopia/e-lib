<?php

/**
 * Comprehensive script to fix MySQL permissions and create database
 */

echo "=== MySQL Database Setup for eLibrary ===\n\n";

$mysqlPath = 'C:\\xampp\\mysql\\bin\\mysql.exe';
$myIniPath = 'C:\\xampp\\mysql\\bin\\my.ini';
$mysqlDataPath = 'C:\\xampp\\mysql\\data\\mysql';

echo "Step 1: Checking MySQL configuration...\n";

// Check if my.ini exists
if (!file_exists($myIniPath)) {
    echo "✗ Cannot find my.ini at: $myIniPath\n";
    echo "Please ensure XAMPP is installed at C:\\xampp\n";
    exit(1);
}

echo "✓ Found my.ini\n";

// Read current my.ini
$iniContent = file_get_contents($myIniPath);

// Check if skip-grant-tables is already set
$hasSkipGrantTables = strpos($iniContent, 'skip-grant-tables') !== false;

if (!$hasSkipGrantTables) {
    echo "\nStep 2: Attempting to temporarily enable skip-grant-tables...\n";
    echo "This will allow us to connect without permission checks.\n";
    
    // Find [mysqld] section and add skip-grant-tables
    if (preg_match('/\[mysqld\]/i', $iniContent)) {
        $iniContent = preg_replace(
            '/(\[mysqld\])(.*?)(\n)/is',
            "$1\nskip-grant-tables\n$3",
            $iniContent,
            1
        );
        
        // Backup original
        copy($myIniPath, $myIniPath . '.backup');
        file_put_contents($myIniPath, $iniContent);
        
        echo "✓ Added skip-grant-tables to my.ini (backup saved)\n";
        echo "⚠ Please RESTART MySQL in XAMPP Control Panel, then run this script again!\n";
        exit(0);
    }
} else {
    echo "✓ skip-grant-tables is already enabled\n";
    echo "\nStep 3: Creating database...\n";
    
    // Now we should be able to connect
    try {
        $pdo = new PDO("mysql:host=127.0.0.1;port=3306", 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create database
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `elibrary` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "✓ Database 'elibrary' created!\n";
        
        // Fix user permissions
        echo "\nStep 4: Fixing user permissions...\n";
        $pdo->exec("USE mysql");
        $pdo->exec("UPDATE user SET Host='%' WHERE User='root'");
        $pdo->exec("FLUSH PRIVILEGES");
        echo "✓ User permissions fixed!\n";
        
        // Remove skip-grant-tables
        echo "\nStep 5: Removing skip-grant-tables from my.ini...\n";
        $iniContent = str_replace("skip-grant-tables\n", '', $iniContent);
        file_put_contents($myIniPath, $iniContent);
        echo "✓ Removed skip-grant-tables\n";
        echo "⚠ Please RESTART MySQL in XAMPP Control Panel to apply changes!\n";
        
        echo "\n✓✓✓ SUCCESS! Database setup complete! ✓✓✓\n";
        echo "\nNext steps:\n";
        echo "1. Restart MySQL in XAMPP\n";
        echo "2. Run: php artisan migrate\n";
        echo "3. (Optional) Run: php artisan db:seed\n";
        
    } catch (Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
        echo "\nPlease ensure MySQL is running in XAMPP!\n";
    }
}

