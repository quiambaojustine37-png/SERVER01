<?php
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: admin_login.php');
    exit;
}
include_once("./connection/connection.php");
$conn = connection();

// Kunin lahat ng account data
$sql = "SELECT id, profile_image, fullname, username, password, approved FROM account";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin.css">
    <title>ADMIN PANEL</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fafafa;
            color: #333;
        }
        .top_container {
            text-align: center;
            margin-top: 20px;
        }
        .container {
            width: 95%;
            margin: 20px auto;
            overflow-x: auto;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #333;
            color: white;
        }
        img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
        }
        button {
            padding: 6px 10px;
            margin: 2px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .delete-btn { background-color: #e74c3c; color: white; }
        .update-btn { background-color: #3498db; color: white; }
        .toggle-btn { background-color: #2ecc71; color: white; }
        .approve-btn { background-color: #27ae60; color: white; }
        .approved-label { color: green; font-weight: bold; }
    </style>
</head>
<body>
    <div class="top_container">
        <h1>Welcome Administrator</h1>
        <form action="logout.php" method="POST" style="margin-top:10px;">
            <button type="submit">Logout</button>
        </form>
    </div>
    <hr>
    <div class="container">
        <?php if (isset($_GET['message'])): ?>
    <div style="background:#dff0d8; color:#3c763d; padding:10px; border-radius:5px; text-align:center;">
        <?php 
            if ($_GET['message'] == 'approved') echo "User approved successfully!";
            if ($_GET['message'] == 'revoke_success') echo "User access revoked successfully!";
        ?>  
    </div>
<?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Profile Image</th>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row["id"]); ?></td>
                            <td>
                                <?php if (!empty($row["profile_image"])): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($row["profile_image"]); ?>" alt="Profile">
                                <?php else: ?>
                                    <span>No image</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($row["fullname"]); ?></td>
                            <td><?php echo htmlspecialchars($row["username"]); ?></td>
                            <td>
                                <span class="password-text">••••••</span>
                                <span class="real-password" style="display:none;"><?php echo htmlspecialchars($row["password"]); ?></span>
                                <button class="toggle-btn" onclick="togglePassword(this)">👁️</button>
                            </td>
                            <td>
                                <?php if ($row['approved'] == 1): ?>
                                    <span class="approved-label">Approved ✅</span>
                                <?php else: ?>
                                    <span style="color:red;">Pending ⏳</span>
                                <?php endif; ?>
                            </td>
                            <td>
    <!-- Approve / Revoke Button -->
    <?php if ($row['approved'] == 0): ?>
        <form action="approve_user.php" method="POST" style="display:inline;">
            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
            <button type="submit" class="approve-btn">Approve</button>
        </form>
    <?php else: ?>
        <form action="revoke_user.php" method="POST" style="display:inline;">
            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
            <button type="submit" class="delete-btn" style="background-color:#f39c12;">Revoke</button>
        </form>
    <?php endif; ?>

    <!-- Update & Delete Buttons -->
    <form action="update_user.php" method="GET" style="display:inline;">
        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
        <button type="submit" class="update-btn">Update</button>
    </form>

    <form action="delete_user.php" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
        <button type="submit" class="delete-btn">Delete</button>
    </form>
</td>

                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7">No records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        function togglePassword(button) {
            const row = button.closest('td');
            const hidden = row.querySelector('.password-text');
            const real = row.querySelector('.real-password');

            if (real.style.display === 'none') {
                real.style.display = 'inline';
                hidden.style.display = 'none';
                button.textContent = '🙈';
            } else {
                real.style.display = 'none';
                hidden.style.display = 'inline';
                button.textContent = '👁️';
            }
        }
    </script>
</body>
</html>
