<?php
echo "Laravel Public Directory is Accessible!<br>";
echo "Current Directory: " . __DIR__ . "<br>";
echo "Laravel index.php exists: " . (file_exists(__DIR__ . '/index.php') ? 'YES' : 'NO') . "<br>";
echo "<br>";
echo "If you see this page, your Apache is pointing to the right directory!<br>";
echo "<a href='index.php'>Click here to access Laravel</a>";



