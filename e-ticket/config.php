<?php
// Check if constants are already defined before defining them
if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
}
if (!defined('DB_USER')) {
    define('DB_USER', 'root');
}
if (!defined('DB_PASS')) {
    define('DB_PASS', 'July112001.yussy');
}
if (!defined('DB_NAME')) {
    define('DB_NAME', 'gabisan_dbms');
}

// Create connection using defined constants
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
