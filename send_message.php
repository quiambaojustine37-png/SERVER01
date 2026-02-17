<?php
session_start();
include_once("./connection/connection.php");
$conn = connection();

$sender = $_SESSION['user_id'];
$receiver = $_POST['receiver_id'];
$message = trim($_POST['message']);

if (!empty($message)) {
    $sql = "INSERT INTO messages (sender_id, receiver_id, message) VALUES ('$sender', '$receiver', '$message')";
    $conn->query($sql);

    // Stop typing indicator
    $conn->query("UPDATE account SET typing_to = NULL WHERE id = '$sender'");
}
?>
