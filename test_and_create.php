<?php
// Simple test and create script
sleep(2); // Wait a moment for MySQL

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $pdo->exec('CREATE DATABASE IF NOT EXISTS elibrary CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    
    echo "✓✓✓ SUCCESS! Database 'elibrary' created! ✓✓✓\n\n";
    echo "Next steps:\n";
    echo "1. Run: php artisan migrate\n";
    echo "2. (Optional) Run: php artisan db:seed\n";
    
} catch (PDOException $e) {
    if (strpos($e->getMessage(), '1130') !== false) {
        echo "⚠ MySQL permission issue detected.\n";
        echo "The my.ini has been modified to add skip-grant-tables.\n";
        echo "Please:\n";
        echo "1. Open XAMPP Control Panel\n";
        echo "2. STOP MySQL\n";
        echo "3. START MySQL again\n";
        echo "4. Run this script again: php test_and_create.php\n";
        echo "\nOR use phpMyAdmin:\n";
        echo "- Go to: http://localhost/phpmyadmin\n";
        echo "- Click SQL tab\n";
        echo "- Run: CREATE DATABASE IF NOT EXISTS elibrary CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\n";
    } else {
        echo "✗ Error: " . $e->getMessage() . "\n";
        echo "Make sure MySQL is running in XAMPP!\n";
    }
}

