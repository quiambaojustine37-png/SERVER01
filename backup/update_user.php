<?php
include_once("./connection/connection.php");
$conn = connection();

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM account WHERE id = $id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Update User</title></head>
<body>
    <h2>Update User</h2>
    <form action="save_update.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
        Fullname: <input type="text" name="fullname" value="<?php echo $row['fullname']; ?>"><br>
        Username: <input type="text" name="username" value="<?php echo $row['username']; ?>"><br>
        Password: <input type="text" name="password" value="<?php echo $row['password']; ?>"><br>
        <button type="submit">Save</button>
    </form>
</body>
</html>
