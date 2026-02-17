<?php
function connection() {
    $host = "localhost"; // palitan base sa MySQL Host sa InfinityFree
    $username = "root";       // iyong MySQL username
    $password = "";        // iyong MySQL password
    $database = "create_account"; // pangalan ng database mo

    $conn = new mysqli($host, $username, $password, $database);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } else {
        return $conn;
    }
}
?>
