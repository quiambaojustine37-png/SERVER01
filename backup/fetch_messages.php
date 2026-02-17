<?php
session_start();
include_once("./connection/connection.php");
$conn = connection();

$current_id = $_SESSION['user_id'];
$receiver_id = $_GET['receiver_id'];

$sql = "SELECT * FROM messages 
        WHERE (sender_id='$current_id' AND receiver_id='$receiver_id') 
           OR (sender_id='$receiver_id' AND receiver_id='$current_id')
        ORDER BY timestamp ASC";
$result = $conn->query($sql);

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

// ✅ Mark messages as seen if the current user is receiver
$conn->query("UPDATE messages SET seen = 1 WHERE receiver_id = '$current_id' AND sender_id = '$receiver_id'");

echo json_encode($messages);
?>
