<?php
$serverName = "DESKTOP-2VD03S5\\SQLEXPRESS";
$database = "demo";
$username = ""; // database username
$password = ""; // database password

try {
    $conn = new PDO("sqlsrv:server=$serverName;Database=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connection successful!<br>";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>