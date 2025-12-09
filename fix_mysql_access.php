<?php

/**
 * Comprehensive MySQL Access Fix
 * This will fix the "access denied" issue and create the database
 */

echo "=== Fixing MySQL Access and Creating Database ===\n\n";

$myIniPath = 'C:\\xampp\\mysql\\bin\\my.ini';
$mysqlPath = 'C:\\xampp\\mysql\\bin\\mysql.exe';

// Step 1: Ensure skip-grant-tables is enabled
echo "Step 1: Checking MySQL configuration...\n";

if (!file_exists($myIniPath)) {
    echo "✗ Cannot find my.ini. Please ensure XAMPP is installed at C:\\xampp\n";
    exit(1);
}

$iniContent = file_get_contents($myIniPath);
$hasSkipGrant = strpos($iniContent, 'skip-grant-tables') !== false;

if (!$hasSkipGrant) {
    echo "Adding skip-grant-tables to my.ini...\n";
    
    // Add skip-grant-tables after [mysqld]
    if (preg_match('/\[mysqld\]/i', $iniContent)) {
        // Check if it's already there with different case/spacing
        if (!preg_match('/skip-grant-tables/i', $iniContent)) {
            $iniContent = preg_replace(
                '/(\[mysqld\])/i',
                "$1\nskip-grant-tables",
                $iniContent,
                1
            );
            
            // Backup
            $backupPath = $myIniPath . '.backup.' . date('YmdHis');
            copy($myIniPath, $backupPath);
            echo "✓ Backup saved to: $backupPath\n";
            
            file_put_contents($myIniPath, $iniContent);
            echo "✓ Added skip-grant-tables to my.ini\n";
        }
    }
    
    echo "\n⚠⚠⚠ IMPORTANT ⚠⚠⚠\n";
    echo "1. Please RESTART MySQL in XAMPP Control Panel NOW\n";
    echo "   - Click STOP next to MySQL\n";
    echo "   - Wait 2 seconds\n";
    echo "   - Click START next to MySQL\n";
    echo "2. Then run this script again: php fix_mysql_access.php\n";
    echo "\nPress Enter after restarting MySQL, or Ctrl+C to exit...\n";
    readline();
}

echo "\nStep 2: Attempting to connect with skip-grant-tables...\n";

// Wait a moment
sleep(2);

try {
    // Connect without authentication (skip-grant-tables mode)
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Connected to MySQL!\n";
    
    // Switch to mysql database
    echo "\nStep 3: Fixing user permissions...\n";
    $pdo->exec('USE mysql');
    
    // Fix root user - allow from all hosts
    $queries = [
        "UPDATE user SET Host='%' WHERE User='root' AND Host='localhost'",
        "INSERT IGNORE INTO user (Host, User, Select_priv, Insert_priv, Update_priv, Delete_priv, Create_priv, Drop_priv, Reload_priv, Shutdown_priv, Process_priv, File_priv, Grant_priv, References_priv, Index_priv, Alter_priv, Show_db_priv, Super_priv, Create_tmp_table_priv, Lock_tables_priv, Execute_priv, Repl_slave_priv, Repl_client_priv, Create_view_priv, Show_view_priv, Create_routine_priv, Alter_routine_priv, Create_user_priv, Event_priv, Trigger_priv, ssl_cipher, x509_issuer, x509_subject, max_questions, max_updates, max_connections, max_user_connections, plugin, authentication_string, password_expired, password_last_changed, password_lifetime, account_locked) SELECT '%', 'root', Select_priv, Insert_priv, Update_priv, Delete_priv, Create_priv, Drop_priv, Reload_priv, Shutdown_priv, Process_priv, File_priv, Grant_priv, References_priv, Index_priv, Alter_priv, Show_db_priv, Super_priv, Create_tmp_table_priv, Lock_tables_priv, Execute_priv, Repl_slave_priv, Repl_client_priv, Create_view_priv, Show_view_priv, Create_routine_priv, Alter_routine_priv, Create_user_priv, Event_priv, Trigger_priv, ssl_cipher, x509_issuer, x509_subject, max_questions, max_updates, max_connections, max_user_connections, plugin, authentication_string, password_expired, password_last_changed, password_lifetime, account_locked FROM user WHERE User='root' AND Host='localhost' LIMIT 1",
    ];
    
    foreach ($queries as $query) {
        try {
            $pdo->exec($query);
        } catch (PDOException $e) {
            // Ignore if already exists
            if (strpos($e->getMessage(), 'Duplicate entry') === false) {
                echo "  Note: " . $e->getMessage() . "\n";
            }
        }
    }
    
    // Flush privileges
    $pdo->exec('FLUSH PRIVILEGES');
    echo "✓ User permissions fixed!\n";
    
    // Create database
    echo "\nStep 4: Creating database 'elibrary'...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `elibrary` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✓✓✓ Database 'elibrary' created successfully! ✓✓✓\n";
    
    // Remove skip-grant-tables
    echo "\nStep 5: Removing skip-grant-tables from my.ini...\n";
    $iniContent = file_get_contents($myIniPath);
    $iniContent = preg_replace('/skip-grant-tables\s*\n?/i', '', $iniContent);
    file_put_contents($myIniPath, $iniContent);
    echo "✓ Removed skip-grant-tables\n";
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "✓✓✓ SETUP COMPLETE! ✓✓✓\n";
    echo str_repeat("=", 50) . "\n\n";
    echo "⚠ FINAL STEP: Please RESTART MySQL in XAMPP Control Panel\n";
    echo "   (STOP and then START MySQL)\n\n";
    echo "After restarting, you can run:\n";
    echo "  php artisan migrate\n";
    echo "  php artisan db:seed\n\n";
    
} catch (PDOException $e) {
    if (strpos($e->getMessage(), '2002') !== false || strpos($e->getMessage(), 'Can\'t connect') !== false) {
        echo "✗ Cannot connect to MySQL server.\n";
        echo "Please ensure:\n";
        echo "1. MySQL is running in XAMPP Control Panel\n";
        echo "2. You have RESTARTED MySQL after adding skip-grant-tables\n";
        echo "3. Port 3306 is not blocked\n";
    } else {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
    exit(1);
}

