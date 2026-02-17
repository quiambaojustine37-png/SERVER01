<?php
session_start();
include_once("./connection/connection.php");
$conn = connection();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$currentUserId = $_SESSION['user_id'];

// Fetch user info
$stmt = $conn->prepare("SELECT fullname, profile_image, approved FROM account WHERE id = ?");
$stmt->bind_param("i", $currentUserId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || $user['approved'] != 1) {
    echo "<script>alert('Your account is not yet approved by the admin.'); window.location.href='index.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Faceboat - Main</title>
<div class="top-bar">
    <div class="user-info">
        <img src="uploads/<?php echo $user['profile_image'] ?: 'default.png'; ?>" alt="Profile">
        <span><?php echo htmlspecialchars($user['fullname']); ?></span>
    </div>
    <div style="display:flex; align-items:center; gap:15px;">
        <div class="chat-notification">
            Messages: <span id="msgCount">0</span>
        </div>
        <form action="logout.php" method="post" style="margin:0;">
            <button type="submit" style="
                padding: 5px 15px;
                border:none;
                border-radius:5px;
                background:#e74c3c;
                color:white;
                font-weight:bold;
                cursor:pointer;
            ">Log Out</button>
        </form>
    </div>
</div>

<style>
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f0f2f5;
}
/* Top bar */
.top-bar {
    width: 95%;
    margin: 10px auto;
    padding: 10px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #2c3e50;
    color: white;
    border-radius: 10px;
}
.user-info {
    display: flex;
    align-items: center;
}
.user-info img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    margin-right: 15px;
    object-fit: cover;
    border: 2px solid #27ae60;
}
.user-info span {
    font-size: 20px;
    font-weight: bold;
}
.chat-notification {
    background-color: #e74c3c;
    padding: 5px 10px;
    border-radius: 20px;
    font-weight: bold;
}

/* Chat layout */
.chat-container {
  display: flex;
  width: 90%;
  max-width: 1200px;
  margin: 20px auto;
  height: 600px;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 5px 15px rgba(0,0,0,0.2);
  background: white;
}
/* User list (left) */
.user-list {
  width: 30%;
  background: #f9f9f9;
  border-right: 1px solid #ddd;
  overflow-y: auto;
}
.user {
  display: flex;
  align-items: center;
  padding: 12px;
  cursor: pointer;
  transition: 0.2s;
}
.user:hover {
  background: #e8ffe8;
}
.user img {
  width: 45px;
  height: 45px;
  border-radius: 50%;
  object-fit: cover;
  margin-right: 10px;
}
.user span {
  font-weight: 600;
  color: #333;
}
.online-dot {
  width: 10px;
  height: 10px;
  background: #00c853;
  border-radius: 50%;
  margin-left: auto;
}
/* Chat area (right) */
.chat-box {
  flex: 1;
  display: flex;
  flex-direction: column;
  background: #fafafa;
}
.chat-header {
  background: #00b06b;
  color: white;
  padding: 15px;
  font-weight: 600;
}
.chat-messages {
  flex: 1;
  padding: 15px;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
}
.message {
  margin: 8px 0;
  max-width: 70%;
  padding: 10px 14px;
  border-radius: 12px;
  word-wrap: break-word;
}
.sent {
  align-self: flex-end;
  background: #00b06b;
  color: white;
}
.received {
  align-self: flex-start;
  background: #e0e0e0;
}
.chat-input {
  display: flex;
  border-top: 1px solid #ddd;
}
.chat-input input {
  flex: 1;
  padding: 15px;
  border: none;
  outline: none;
  font-size: 15px;
}
.chat-input button {
  background: #00b06b;
  color: white;
  border: none;
  padding: 0 20px;
  cursor: pointer;
  font-weight: bold;
}
</style>
</head>
<body>

<!-- Chat area -->
<div class="chat-container">
  <div class="user-list" id="userList"></div>

  <div class="chat-box">
    <div class="chat-header" id="chatHeader">Select a user to start chatting</div>
    <div class="chat-messages" id="chatMessages"></div>
    <div class="chat-input">
      <input type="text" id="messageInput" placeholder="Type a message..." disabled>
      <button id="sendBtn" disabled>Send</button>
    </div>
  </div>
</div>

<script>
// Current user ID from PHP
const currentUserId = <?php echo $_SESSION['user_id']; ?>;

// Current chat state
let currentReceiver = null;
let currentReceiverName = '';

// Fetch users every 3 seconds
function fetchUsers() {
  fetch('fetch_users.php')
    .then(res => res.json())
    .then(users => {
      const list = document.getElementById('userList');
      list.innerHTML = '';
      users.forEach(u => {
        const div = document.createElement('div');
        div.className = 'user';
        div.onclick = () => openChat(u.id, u.fullname);
        div.innerHTML = `
          <img src="uploads/${u.profile_image || 'default.png'}">
          <span>${u.fullname}</span>
          ${u.online_status == 1 ? '<div class="online-dot"></div>' : ''}
        `;
        list.appendChild(div);
      });
    });
}
setInterval(fetchUsers, 3000);
fetchUsers();

function openChat(id, name) {
  currentReceiver = id;
  currentReceiverName = name;
  document.getElementById('chatHeader').innerText = 'Chat with ' + name;
  document.getElementById('messageInput').disabled = false;
  document.getElementById('sendBtn').disabled = false;
  fetchMessages();
}

// Fetch messages every 2 seconds
function fetchMessages() {
  if (!currentReceiver) return;
  fetch(`fetch_messages.php?receiver_id=${currentReceiver}`)
    .then(res => res.json())
    .then(msgs => {
      const box = document.getElementById('chatMessages');
      box.innerHTML = '';
      msgs.forEach(m => {
        const div = document.createElement('div');
        div.className = 'message ' + (m.sender_id == currentUserId ? 'sent' : 'received');
        div.innerHTML = m.message + (m.sender_id == currentUserId && m.seen == 1 ? " <span style='font-size:10px;color:#00b06b;'>✔ Seen</span>" : "");
        box.appendChild(div);
      });
      box.scrollTop = box.scrollHeight;
    });
}
setInterval(fetchMessages, 2000);

// Send message
document.getElementById('sendBtn').onclick = () => {
  const msg = document.getElementById('messageInput').value.trim();
  if (!msg || !currentReceiver) return;
  fetch('send_message.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: `receiver_id=${currentReceiver}&message=${encodeURIComponent(msg)}`
  }).then(() => {
    document.getElementById('messageInput').value = '';
    fetchMessages();
  });
};

// Update online/offline
window.addEventListener('load', () => {
  fetch('update_status.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'status=1'
  });
});
window.addEventListener('beforeunload', () => {
  navigator.sendBeacon('update_status.php', 'status=0');
});

// Message count update
function updateMsgCount() {
  fetch('fetch_unread.php')
      .then(res => res.text())
      .then(count => {
          document.getElementById('msgCount').innerText = count;
      });
}
setInterval(updateMsgCount, 2000);
updateMsgCount();

// Typing indicator
setInterval(() => {
  if (!currentReceiver) return;
  fetch('typing_status.php')
    .then(res => res.json())
    .then(ids => {
      if (ids.includes(currentReceiver)) {
        document.getElementById('chatHeader').innerText = currentReceiverName + ' is typing... ✍️';
      } else if (currentReceiver) {
        document.getElementById('chatHeader').innerText = 'Chat with ' + currentReceiverName;
      }
    });
}, 1500);
</script>
</body>
</html>
