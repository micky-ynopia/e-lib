<?php
/**
 * Web interface to create the database
 * Access via: http://localhost/create-database.php
 */

header('Content-Type: text/html; charset=utf-8');

$host = '127.0.0.1';
$username = 'root';
$password = '';
$database = 'elibrary';
$success = false;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_db'])) {
    try {
        // Try multiple connection methods
        $conn = null;
        
        // Method 1: Try 127.0.0.1
        $conn = @new mysqli($host, $username, $password);
        
        // Method 2: Try localhost
        if (!$conn || $conn->connect_error) {
            $conn = @new mysqli('localhost', $username, $password);
        }
        
        if ($conn && !$conn->connect_error) {
            $sql = "CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
            
            if ($conn->query($sql)) {
                $success = true;
            } else {
                $error = $conn->error;
            }
            $conn->close();
        } else {
            $error = "Could not connect to MySQL. Error: " . ($conn ? $conn->connect_error : "Connection failed");
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Database - eLibrary</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
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
        button {
            background: #007bff;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #0056b3;
        }
        .steps {
            margin-top: 20px;
            line-height: 1.8;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🗄️ Create eLibrary Database</h1>
        
        <?php if ($success): ?>
            <div class="success">
                <strong>✓ Success!</strong> Database '<?php echo htmlspecialchars($database); ?>' has been created successfully!
            </div>
            <div class="info">
                <strong>Next steps:</strong>
                <ol class="steps">
                    <li>Open terminal/command prompt in the e-library directory</li>
                    <li>Run: <code>php artisan migrate</code></li>
                    <li>(Optional) Run: <code>php artisan db:seed</code></li>
                </ol>
            </div>
        <?php elseif ($error): ?>
            <div class="error">
                <strong>✗ Error:</strong> <?php echo htmlspecialchars($error); ?>
            </div>
            <div class="info">
                <strong>Alternative Solution - Use phpMyAdmin:</strong>
                <ol class="steps">
                    <li>Open <a href="http://localhost/phpmyadmin" target="_blank">phpMyAdmin</a></li>
                    <li>Click on the <strong>SQL</strong> tab</li>
                    <li>Copy and paste this command:</li>
                    <li><code>CREATE DATABASE IF NOT EXISTS elibrary CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;</code></li>
                    <li>Click <strong>Go</strong></li>
                </ol>
            </div>
        <?php else: ?>
            <div class="info">
                <p>This will create the database <code><?php echo htmlspecialchars($database); ?></code> for your eLibrary application.</p>
                <p><strong>Make sure MySQL is running in XAMPP Control Panel!</strong></p>
            </div>
            <form method="POST">
                <button type="submit" name="create_db">Create Database</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>

