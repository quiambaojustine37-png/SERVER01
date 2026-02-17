<?php
include_once("./connection/connection.php");
$conn = connection();

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM account WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: admin_panel.php");
        exit;
    } else {
        echo "Error deleting record: " . $stmt->error;
    }
}
?>
