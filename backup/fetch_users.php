<?php
session_start();
include_once("./connection/connection.php");
$conn = connection();

$current_id = $_SESSION['user_id'];
$sql = "SELECT id, fullname, profile_image, online_status FROM account WHERE id != '$current_id' AND approved = 1";
$result = $conn->query($sql);

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode($users);
?>
