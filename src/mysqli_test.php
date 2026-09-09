<?php
$mysqli = @new mysqli("127.0.0.1", "root", "", "");

if ($mysqli->connect_error) {
    die("Connect Error: " . $mysqli->connect_error);
}

echo "Success: A proper connection to MySQL was made! The my_db database is great." . PHP_EOL;
echo "Host information: " . $mysqli->host_info . PHP_EOL;

$mysqli->close();
?>