<?php
session_start();
include_once("./connection/connection.php");
$conn = connection();

if (!empty($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $status = $_POST['status']; // 1 = online, 0 = offline
    $sql = "UPDATE account SET online_status = '$status' WHERE id = '$user_id'";
    $conn->query($sql);
}
?>
