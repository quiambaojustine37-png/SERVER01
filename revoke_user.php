<?php
include_once("./connection/connection.php");
$conn = connection();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
    $id = intval($_POST["id"]);

    $sql = "UPDATE account SET approved = 0 WHERE id = $id";
    if ($conn->query($sql)) {
        header("Location: admin_panel.php?message=revoke_success");
        exit;
    } else {
        echo "Error revoking user: " . $conn->error;
    }
} else {
    header("Location: admin_panel.php");
    exit;
}
?>
