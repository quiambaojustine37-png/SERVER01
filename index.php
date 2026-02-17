<?php
session_start();
include_once("./connection/connection.php");
$conn = connection();

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // ✅ ADMIN LOGIN
    if ($username === 'admin' && $password === '12345') {
        $_SESSION['admin'] = true;
        header('Location: admin_panel.php');
        exit;
    }

    // ✅ USER LOGIN
    $sql = "SELECT * FROM account WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if ($user['approved'] == 1) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: main.php");
            exit;
        } else {
            echo "<script>alert('Your account is not yet approved by the admin.');</script>";
        }
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Faceboat</title>
<style>
/* 🌈 General Reset */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: "Poppins", sans-serif;
}

/* 🌄 Background */
body {
  height: 100vh;
  background: url('./picture/baot.jpg') no-repeat center center/cover;
  display: flex;
  justify-content: center;
  align-items: center;
  animation: fadeIn 1.5s ease-in-out;
}

/* 🌟 Glass Effect Container */
.container {
  width: 400px;
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(100px);
  border-radius: 20px;
  padding: 40px;
  border: 1px solid rgba(255, 255, 255, 0.25);
  box-shadow: 0 8px 30px rgba(0,0,0,0.3);
  text-align: center;
  color: white;
  animation: slideUp 0.8s ease;
}

/* 💡 Faceboat Logo */
.logo {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  margin-bottom: 10px;
}
.logo img {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  object-fit: cover;
  animation: float 3s infinite ease-in-out;
}
.logo span {
  font-size: 28px;
  font-weight: 700;
  background: linear-gradient(135deg, #00ffa2, #00b05c);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  letter-spacing: 1px;
}

/* 📝 Header Text */
.container h2 {
  font-size: 22px;
  font-weight: 500;
  margin-bottom: 25px;
  color: #e0ffe0;
}

/* 💡 Input Fields */
form input[type="text"],
form input[type="password"],
form input[type="file"] {
  width: 100%;
  padding: 12px 15px;
  margin: 10px 0;
  border-radius: 10px;
  border: none;
  background: rgba(255, 255, 255, 0.25);
  color: white;
  font-size: 16px;
  outline: none;
  transition: 0.3s;
}

form input:focus {
  background: rgba(255,255,255,0.35);
  box-shadow: 0 0 10px #00b06b;
}

form input::placeholder {
  color: #e0e0e0;
}

/* ✨ Submit Button */
form input[type="submit"] {
  width: 100%;
  background: linear-gradient(135deg, #00b06b, #007a48);
  color: white;
  font-weight: bold;
  border: none;
  border-radius: 10px;
  padding: 12px;
  margin-top: 10px;
  cursor: pointer;
  transition: 0.3s;
}
form input[type="submit"]:hover {
  background: linear-gradient(135deg, #00d17a, #00a35e);
  transform: scale(1.03);
}

/* ⚠️ Error Message */
.error {
  color: #ff6b6b;
  font-size: 15px;
  margin-bottom: 10px;
}

/* ➕ Create Account Button */
.create-btn {
  width: 100%;
  background: rgba(255,255,255,0.2);
  border: 1px solid rgba(255,255,255,0.4);
  color: white;
  padding: 12px;
  border-radius: 10px;
  cursor: pointer;
  transition: 0.3s;
  font-weight: 500;
  margin-top: 15px;
}
.create-btn:hover {
  background: rgba(255,255,255,0.35);
  transform: scale(1.03);
}

/* 🪟 Create Account Modal */
.create {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.85);
  backdrop-filter: blur(10px);
  justify-content: center;
  align-items: center;
  animation: fadeIn 0.5s ease-in-out;
}

.create.active {
  display: flex;
}

.create-box {
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(20px);
  border-radius: 15px;
  padding: 30px;
  width: 450px;
  border: 1px solid rgba(255,255,255,0.3);
  color: white;
  text-align: center;
  animation: slideUp 0.6s ease;
}

.create-box h2 {
  margin-bottom: 10px;
}

.create-box button {
  margin-top: 15px;
  background: #e74c3c;
  border: none;
  color: white;
  border-radius: 10px;
  padding: 10px 20px;
  cursor: pointer;
  font-weight: 600;
  transition: 0.3s;
}
.create-box button:hover {
  background: #ff6b5b;
}

/* 🎬 Animations */
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideUp {
  from { transform: translateY(30px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

@keyframes float {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-8px); }
}

/* 🖼 Responsive */
@media (max-width: 480px) {
  .container, .create-box {
    width: 90%;
    padding: 25px;
  }
}
</style>
</head>
<body>

<div class="container">
  <div class="logo">
    <img src="./picture/logo.png" alt="Faceboat Logo" onerror="this.style.display='none';">
    <span>Faceboat</span>
  </div>
  <h2>Welcome Back 👋</h2>

  <form method="post">
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="submit" value="Log In">
  </form>

  <button class="create-btn" onclick="openCreate()">Create New Account</button>
</div>

<!-- 🪄 Create Account Modal -->
<div class="create" id="createModal">
  <div class="create-box">
    <h2>Create Your Account</h2>
    <hr style="margin-bottom:15px; opacity:0.5;">
    <form action="creating.php" method="POST" enctype="multipart/form-data">
      <input type="file" name="profile_image" accept="image/*" required><br>
      <label for="fullname"></label>
      <input type="text" name="fullname" placeholder="Full Name" required>
      <label for="useername"></label>
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <input type="submit" name="submit" value="Create Account">
    </form>
    <button onclick="closeCreate()">Close</button>
  </div>
</div>

<script>
function openCreate() {
  document.getElementById('createModal').classList.add('active');
}
function closeCreate() {
  document.getElementById('createModal').classList.remove('active');
}
</script>
</body>
</html>
