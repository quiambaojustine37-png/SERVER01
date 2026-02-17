<?php
include_once("./connection/connection.php");
$conn = connection();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
    $id = intval($_POST["id"]);

    $sql = "UPDATE account SET approved = 1 WHERE id = $id";
    if ($conn->query($sql)) {
        header("Location: admin_panel.php?message=approved");
        exit;
    } else {
        echo "Error approving user: " . $conn->error;
    }
} else {
    header("Location: admin_panel.php");
    exit;
}
?>
