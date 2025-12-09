<?php
/**
 * Final automated fix - This will work after MySQL restart
 */

echo "=== Final MySQL Fix ===\n\n";

$myIniPath = 'C:\\xampp\\mysql\\bin\\my.ini';

// Check if skip-grant-tables is enabled
$iniContent = file_get_contents($myIniPath);
$hasSkipGrant = preg_match('/skip-grant-tables/i', $iniContent);

if (!$hasSkipGrant) {
    echo "Adding skip-grant-tables...\n";
    $iniContent = preg_replace('/(\[mysqld\])/i', "$1\nskip-grant-tables", $iniContent, 1);
    file_put_contents($myIniPath, $iniContent);
    echo "✓ Added skip-grant-tables\n";
    echo "\n⚠ RESTART MySQL in XAMPP Control Panel NOW!\n";
    echo "Then run this script again.\n";
    exit(0);
}

echo "✓ skip-grant-tables is enabled\n";
echo "Checking if MySQL has been restarted...\n\n";

// Try to connect
$attempts = 0;
$maxAttempts = 5;
$connected = false;

while ($attempts < $maxAttempts && !$connected) {
    $attempts++;
    echo "Attempt $attempts/$maxAttempts: Trying to connect...\n";
    
    try {
        $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $connected = true;
        echo "✓ Connected!\n\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), '1130') !== false) {
            echo "✗ Access denied - MySQL needs restart\n";
        } else {
            echo "✗ Connection failed: " . $e->getMessage() . "\n";
        }
        
        if ($attempts < $maxAttempts) {
            echo "Waiting 3 seconds...\n\n";
            sleep(3);
        }
    }
}

if (!$connected) {
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "⚠ MySQL needs to be RESTARTED for skip-grant-tables to work!\n";
    echo str_repeat("=", 60) . "\n\n";
    echo "SOLUTION:\n\n";
    echo "OPTION 1 - Restart MySQL (Recommended):\n";
    echo "  1. Open XAMPP Control Panel\n";
    echo "  2. Click STOP next to MySQL\n";
    echo "  3. Wait 3 seconds\n";
    echo "  4. Click START next to MySQL\n";
    echo "  5. Run this script again: php final_fix.php\n\n";
    
    echo "OPTION 2 - Use phpMyAdmin (Easiest, always works):\n";
    echo "  1. Make sure Apache and MySQL are running\n";
    echo "  2. Open: http://localhost/phpmyadmin\n";
    echo "  3. Click 'SQL' tab\n";
    echo "  4. Open file: create_database.sql\n";
    echo "  5. Copy all SQL and paste in phpMyAdmin\n";
    echo "  6. Click 'Go'\n\n";
    
    exit(1);
}

// We're connected! Now fix everything
echo "Step 1: Creating database...\n";
try {
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `elibrary` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✓ Database 'elibrary' created!\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'already exists') === false) {
        echo "✗ Error: " . $e->getMessage() . "\n";
        exit(1);
    } else {
        echo "✓ Database already exists!\n";
    }
}

echo "\nStep 2: Fixing user permissions...\n";
try {
    $pdo->exec('USE mysql');
    
    // Update root@localhost to allow from any host
    $pdo->exec("UPDATE user SET Host='%' WHERE User='root'");
    
    // Flush privileges
    $pdo->exec('FLUSH PRIVILEGES');
    
    echo "✓ User permissions fixed!\n";
} catch (PDOException $e) {
    echo "⚠ Warning: Could not fix permissions: " . $e->getMessage() . "\n";
    echo "You may need to fix this manually in phpMyAdmin\n";
}

echo "\nStep 3: Removing skip-grant-tables...\n";
$iniContent = file_get_contents($myIniPath);
$iniContent = preg_replace('/skip-grant-tables\s*\n?/i', '', $iniContent);
file_put_contents($myIniPath, $iniContent);
echo "✓ Removed skip-grant-tables\n";

echo "\n" . str_repeat("=", 60) . "\n";
echo "✓✓✓ SUCCESS! ✓✓✓\n";
echo str_repeat("=", 60) . "\n\n";
echo "⚠ FINAL STEP: Restart MySQL in XAMPP Control Panel\n";
echo "   (STOP then START MySQL)\n\n";
echo "After restart, run:\n";
echo "  php artisan migrate\n";
echo "  php artisan db:seed\n\n";

