<?php
$host='localhost';
require_once "dbpassagg.php";
$port=$DB_PORT;
$socket=$DB_SOCKET;
$user=$DB_USER;
$pass=$DB_PASS;
$db = $DB;
$mysqli = new mysqli($host, $user, $pass, $db,$port,$socket);
if ($mysqli->connect_errno) {
    echo "CONNECTION_FAIL://////$mysqli->connect_errno//////" . $mysqli->connect_error;
}
?>