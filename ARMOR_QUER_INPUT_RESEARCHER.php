<?php
session_start();
$hazards     = $_SESSION['hazards'] ?? [];
$promptLines = [];

foreach ($hazards as $h) {
    $main = htmlspecialchars($h['main_hazard'], ENT_QUOTES, 'UTF-8');
    $desc = htmlspecialchars($h['description'],  ENT_QUOTES, 'UTF-8');
    $loc  = htmlspecialchars($h['location'],     ENT_QUOTES, 'UTF-8');
    $promptLines[] = "This user has encountered a {$main} hazard described as: \"{$desc}\" located in {$loc}.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Website Name | Home</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="site-header">
    <div class="header-content">
        <a href ="index.html" class="logo">
            <img src="Photos_Videos/DOD logo.png" alt="CMMD Logo" class="logo-icon">
        <h1>ARMOR</h1>
        </a>
        <nav class="nav-menu">
            <a href="index.html">Home</a>
            <a href="CMMD_risk_choice.php">ARMOR</a>
            <a href="ARMOR_QUER.html">ARMOR ANALYSIS</a>
            <a href="about.html">About</a>
            <a href="projects.html">Projects</a>
            </nav>
    </div>
</header>
<body>
    <!-- Preloader -->
<div id="preloader">
  <div class="loader-text">Loading...</div>
</div>
<style>
.chat-interface {
  max-width: 800px;
  margin: 3rem auto;
  font-family: 'Poppins', sans-serif;
  display: flex;
  flex-direction: column;
  align-items: stretch;
  padding: 1rem;
  border-radius: 8px;
  background-color: #282828;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.chat-box {
  border: 1px solid #ddd;
  padding: 1rem;
  height: 400px;
  overflow-y: auto;
  background: #f5f8fa;
  border-radius: 6px;
  margin-bottom: 1rem;
}

.message {
  margin-bottom: 1rem;
  display: flex;
  flex-direction: column;
}

.user {
  align-self: flex-end;
  background-color: #e1f5fe;
  color: #003c8f;
  padding: 0.6rem 1rem;
  border-radius: 18px 18px 0 18px;
  max-width: 75%;
  word-wrap: break-word;
}

.bot {
  align-self: flex-start;
  background-color: #e8eaf6;
  color: #1a237e;
  padding: 0.6rem 1rem;
  border-radius: 18px 18px 18px 0;
  max-width: 75%;
  word-wrap: break-word;
}

#chat-form {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin-top: 10px;
}
#user-input {
  flex: 1;
  padding: 10px;
  border: 1px solid #bbb;
  border-radius: 6px;
  width: 100%; /* Changed from fixed width */
  font-size: 1rem;
  outline: none;
  transition: border 0.2s ease;
}

#user-input:focus {
  border-color: #007BFF;
}

#chat-form button {
  padding: 0.7rem 1.2rem;
  font-size: 1rem;
  background-color: #e1da0c;
  border: none;
  color: rgb(0, 0, 0);
  top: 30px;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

#chat-form button:hover {
  background-color: #0056b3;
}

h2{
    color: #e1da0c;
    font: "Trebuchet MS", "sans-serif", "bold";
    font-size: 30px;
    margin-bottom: 13px;
}
label{
    color: #e1da0c;
    font: "Trebuchet MS", "sans-serif", "bold";
    font-size: 15px;
    margin-bottom: 13px;
}

</style>
<main class="chat-interface">
  <div id="notice" style="
  background: #fff8c6;
  border: 1px solid #ffe066;
  color: #856404;
  padding: 0.75rem;
  border-radius: 6px;
  margin-bottom: 1rem;
  font-size: 0.95rem;
">
  ⚠️ You can only ask up to <strong>2 queries</strong> in this session.
</div>
<h2>ARMOR ANALYSIS LLM</h2>
  <div id="chat-box" class="chat-box"></div>
<label>Query Input:</label>
  <form id="chat-form">
    <input type="text" id="user-input" placeholder="Ask ARMOR anything..." autocomplete="off" required /><br></br>
    <a href="ARMOR_ASESS_RESEARCHER.php"><button type="button">Next</button></a>
    <button type="submit">Send</button>
  </form>
</main>
<script>
  // Wait for all content (including images, fonts, etc.)
  window.addEventListener('load', function () {
    const preloader = document.getElementById('preloader');
    preloader.style.display = 'none';
  });
</script>
<script>
document.getElementById('chat-form').addEventListener('submit', async function (e) {
  e.preventDefault();
  const input = document.getElementById('user-input');
  const message = input.value.trim();
  if (!message) return;

  addMessage('You', message);
  input.value = '';

  // Send to server for Gemini response
  const response = await fetch('gemini-api.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ prompt: message })
  });

  const data = await response.json();
  addMessage('ARMOR', data.reply || 'Error: No response');
});

function addMessage(sender, text) {
  const chatBox = document.getElementById('chat-box');
  const msg = document.createElement('div');
  msg.className = 'message';
  msg.innerHTML = `<span class="${sender === 'You' ? 'user' : 'bot'}">${sender}:</span> ${text}`;
  chatBox.appendChild(msg);
  chatBox.scrollTop = chatBox.scrollHeight;
}

window.addEventListener('load', async function () {
  const preloader = document.getElementById('preloader');
  preloader.style.display = 'none';

  // Send structured hazard summary to LLM (from PHP)
  const initialPrompt = <?php echo json_encode($initialPrompt); ?>;

  if (initialPrompt.trim()) {
    addMessage('You', initialPrompt);

    const response = await fetch('gemini-api.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({ prompt: initialPrompt })
    });

    const data = await response.json();
    addMessage('ARMOR', data.reply || 'No initial response received.');
  }
});
</script>
<script>
  const initialPrompts = <?php echo json_encode($promptLines, JSON_UNESCAPED_UNICODE); ?>;
  const maxQueries     = 2;

  const chatForm   = document.getElementById('chat-form');
  const input      = document.getElementById('user-input');
  const submitBtn  = chatForm.querySelector('button[type="submit"]');
  const chatBox    = document.getElementById('chat-box');

  // How many “You:” messages we inject for the hazards
  let initialUserCount = 0;

  // Send each hazard as its own message (no counting)
  async function sendInitialPrompt() {
    if (!Array.isArray(initialPrompts)) return;
    for (const line of initialPrompts) {
      addMessage('You', line);
      const res = await fetch('gemini-api.php', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({ prompt: line, contextOnly: true })
      });
      const data = await res.json();
      addMessage('ARMOR', data.reply || 'No initial response.');
    }
    // Record how many user‑messages we’ve injected up front:
    initialUserCount = chatBox.querySelectorAll('.message .user').length;
  }

  // Disable further typing & submission
  function lockUserInput() {
    input.disabled = true;
    submitBtn.disabled = true;
    input.placeholder = "⚠️ Query limit reached";
    input.style.backgroundColor = "#eee";
    submitBtn.style.backgroundColor = "#aaa";
  }

  // Render a message
  function addMessage(sender, text) {
    const msg = document.createElement('div');
    msg.className = 'message';
    msg.innerHTML = `<span class="${sender==='You'?'user':'bot'}">${sender}:</span> ${text}`;
    chatBox.appendChild(msg);
    chatBox.scrollTop = chatBox.scrollHeight;
  }

  // On page load, fire off the hazards without bumping any counters
  window.addEventListener('DOMContentLoaded', () => {
    document.getElementById('preloader').style.display = 'none';
    sendInitialPrompt();
  });

  // Now the one submit‐handler that counts only *new* user queries
  chatForm.addEventListener('submit', async e => {
    e.preventDefault();

    // Recompute how many “You:” messages there are, subtract the initial ones
    const totalUserMsgs = chatBox.querySelectorAll('.message .user').length;
    const userQueries   = totalUserMsgs - initialUserCount;

    if (userQueries >= maxQueries) {
      addMessage('System', '⚠️ You have reached the maximum number of allowed queries.');
      lockUserInput();
      return;
    }

    const message = input.value.trim();
    if (!message) return;

    // Show it and send it
    addMessage('You', message);
    input.value = '';

    const res = await fetch('gemini-api.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({ prompt: message })
    });
    const data = await res.json();
    addMessage('ARMOR', data.reply || 'Error: No response');

    // If that was your second real query, lock it down now
    const newTotalUserMsgs = chatBox.querySelectorAll('.message .user').length;
    const newUserQueries   = newTotalUserMsgs - initialUserCount;
    if (newUserQueries >= maxQueries) {
      addMessage('System', '⚠️ Query limit reached — no more submissions allowed.');
      lockUserInput();
    }
  });
</script>
</body>
</body>
</html>