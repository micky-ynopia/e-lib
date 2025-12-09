<?php
/**
 * Web-based MySQL Access Fix
 * Access via: http://localhost:8000/fix-mysql-access.php
 * or: http://127.0.0.1/fix-mysql-access.php (if using Apache)
 */

header('Content-Type: text/html; charset=utf-8');

$success = false;
$error = null;
$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;

$host = '127.0.0.1';
$username = 'root';
$password = '';
$database = 'elibrary';

// Step 2: Try to fix permissions and create database
if ($step == 2) {
    try {
        // Try to connect - might fail due to permissions
        $conn = @new mysqli($host, $username, $password);
        
        if ($conn && !$conn->connect_error) {
            // Connected! Now fix permissions
            $conn->query("USE mysql");
            
            // Update root user to allow from all hosts
            $conn->query("UPDATE user SET Host='%' WHERE User='root'");
            $conn->query("FLUSH PRIVILEGES");
            
            // Create database
            $conn->query("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            $conn->close();
            $success = true;
        } else {
            $error = "Could not connect. MySQL may need skip-grant-tables mode.";
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix MySQL Access - eLibrary</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 { color: #333; }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        button, .btn {
            background: #007bff;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
            margin: 10px 5px;
        }
        button:hover, .btn:hover {
            background: #0056b3;
        }
        .btn-success {
            background: #28a745;
        }
        .btn-success:hover {
            background: #218838;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            display: block;
            padding: 10px;
            margin: 10px 0;
            overflow-x: auto;
        }
        .steps {
            margin-top: 20px;
            line-height: 1.8;
        }
        ol li {
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Fix MySQL Access Denied Error</h1>
        
        <?php if ($success): ?>
            <div class="success">
                <strong>✓✓✓ SUCCESS! ✓✓✓</strong><br>
                Database permissions have been fixed and the database has been created!
            </div>
            
            <div class="info">
                <strong>Next Steps:</strong>
                <ol class="steps">
                    <li><strong>Restart MySQL in XAMPP Control Panel</strong>
                        <ul>
                            <li>Open XAMPP Control Panel</li>
                            <li>Click <strong>STOP</strong> next to MySQL</li>
                            <li>Wait 2 seconds</li>
                            <li>Click <strong>START</strong> next to MySQL</li>
                        </ul>
                    </li>
                    <li><strong>Clear Laravel cache:</strong>
                        <code>php artisan config:clear</code>
                        <code>php artisan cache:clear</code>
                    </li>
                    <li><strong>Run migrations:</strong>
                        <code>php artisan migrate</code>
                    </li>
                    <li><strong>Refresh your browser</strong> - The error should be gone!</li>
                </ol>
            </div>
            
        <?php elseif ($error): ?>
            <div class="error">
                <strong>✗ Error:</strong> <?php echo htmlspecialchars($error); ?>
            </div>
            
            <div class="warning">
                <strong>⚠ Manual Fix Required</strong>
                <p>The automatic fix didn't work. Please use phpMyAdmin to fix this:</p>
                <ol class="steps">
                    <li>Open <a href="http://localhost/phpmyadmin" target="_blank">phpMyAdmin</a></li>
                    <li>Click on the <strong>SQL</strong> tab</li>
                    <li>Copy and paste this SQL:</li>
                </ol>
                <code>
USE mysql;<br>
UPDATE user SET Host='%' WHERE User='root';<br>
FLUSH PRIVILEGES;<br><br>
-- Create database if it doesn't exist<br>
CREATE DATABASE IF NOT EXISTS elibrary CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
                </code>
                <ol class="steps" start="4">
                    <li>Click <strong>Go</strong></li>
                    <li>Restart MySQL in XAMPP Control Panel</li>
                    <li>Refresh this page</li>
                </ol>
            </div>
            
        <?php else: ?>
            <div class="info">
                <p><strong>Problem:</strong> MySQL is blocking connections from 'localhost'. This script will fix the permissions.</p>
                <p><strong>Make sure MySQL is running in XAMPP Control Panel!</strong></p>
            </div>
            
            <div class="warning">
                <strong>⚠ Important:</strong> If this doesn't work, you'll need to use phpMyAdmin (see instructions below).
            </div>
            
            <form method="GET" action="">
                <input type="hidden" name="step" value="2">
                <button type="submit">🔧 Try Automatic Fix</button>
            </form>
            
            <hr style="margin: 30px 0;">
            
            <h2>Alternative: Use phpMyAdmin (Always Works)</h2>
            <ol class="steps">
                <li>Make sure <strong>Apache</strong> and <strong>MySQL</strong> are running in XAMPP</li>
                <li>Open <a href="http://localhost/phpmyadmin" target="_blank">http://localhost/phpmyadmin</a></li>
                <li>Click on the <strong>SQL</strong> tab</li>
                <li>Copy and paste this SQL:</li>
            </ol>
            <code>
USE mysql;<br><br>
-- Fix root user permissions<br>
UPDATE user SET Host='%' WHERE User='root';<br>
FLUSH PRIVILEGES;<br><br>
-- Create database<br>
CREATE DATABASE IF NOT EXISTS elibrary CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
            </code>
            <ol class="steps" start="5">
                <li>Click <strong>Go</strong></li>
                <li><strong>Restart MySQL</strong> in XAMPP Control Panel</li>
                <li>Clear Laravel cache: <code>php artisan config:clear</code></li>
                <li>Refresh your Laravel application</li>
            </ol>
        <?php endif; ?>
    </div>
</body>
</html>

