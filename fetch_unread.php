<?php
session_start();
include_once("./connection/connection.php");
$conn = connection();

$currentUserId = $_SESSION['user_id'];

$sql = "SELECT COUNT(*) as cnt FROM messages WHERE receiver_id='$currentUserId' AND seen=0";
$result = $conn->query($sql);
$data = $result->fetch_assoc();

echo $data['cnt'];
?>
