<?php
session_start();
include_once("./connection/connection.php");
$conn = connection();

$user_id = $_SESSION['user_id'];

if (isset($_POST['typing_to'])) {
    $typing_to = $_POST['typing_to'];
    $conn->query("UPDATE account SET typing_to = '$typing_to' WHERE id = '$user_id'");
} else {
    // Check who is typing to me
    $sql = "SELECT id FROM account WHERE typing_to = '$user_id'";
    $result = $conn->query($sql);
    $typingUsers = [];
    while ($row = $result->fetch_assoc()) {
        $typingUsers[] = $row['id'];
    }
    echo json_encode($typingUsers);
}
?>
