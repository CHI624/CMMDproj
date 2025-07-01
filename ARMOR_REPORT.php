<?php
session_start();

// Pull in the two pieces of session data
$form1 = $_SESSION['form1'] ?? [];
$form2 = $_SESSION['form2'] ?? [];

if (empty($form1) || empty($form2)) {
    die("Missing session data from previous forms.");
}

// Build the hazards section
$hazardText = '';
if (!empty($form1['hazards']) && is_array($form1['hazards'])) {
    foreach ($form1['hazards'] as $hazard) {
        // each $hazard is [ main_hazard, description, location ]
        list($type, $desc, $loc) = $hazard;
        $hazardText .= "Hazard: $type\n";
        $hazardText .= "Description: $desc\n";
        $hazardText .= "Location: $loc\n\n";
    }
}

// Now assemble your LLM prompt
$prompt = <<<EOT
You are a military operations analyst. Based on the following input, provide a brief risk control or mitigation recommendation.

--- Personnel Info ---
Researcher: {$form1['researcher']}
Job: {$form1['job']}
Job Description: {$form1['job_descr']}

Matrix(For Severity Score, Risk Score and Final Risk:
Severity Level	Unlikely	Seldom	Occasional	Likely	Frequent
Negligible	L	L	L	L	M
Moderate	L	L	M	M	H
Critical	L	M	H	H	EH
Catastrophic	M	H	H	EH	EH

--- Hazards Identified ---
$hazardText
--- Risk Assessment Summary ---
Risk Score: {$form2['risk_score']}
Severity Score: {$form2['severity_score']}
Combined Hazards: {$form2['combined_hazards']}
Final Risk Level: {$form2['final_risk']}

Your response should be under 200 words.
EOT;

// (Optional) store back in session for your chat code
$_SESSION['llm_prompt'] = $prompt;

// From here you can include your HTML / chat‐UI which will read $_SESSION['llm_prompt']
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
<style>
    /* Base reset and typography */
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}
body {
  font-family: 'Poppins', sans-serif;
  background: #282828;
  color: #333;
  line-height: 1.5;
}

/* Header */
.site-header {
  background: #1a1a1a;
  padding: 1rem 2rem;
  box-shadow: 0 2px 6px rgb(225, 218, 11);
}
.site-header .header-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.site-header .logo {
  display: flex;
  align-items: center;
  text-decoration: none;
}
.site-header .logo-icon {
  height: 40px;
  margin-right: 0.75rem;
}
.site-header h1 {
  font-family: 'Playfair Display', serif;
  color: #e1da0c;
  font-size: 1.75rem;
}
.next-button {
  color: #ddd;
  margin-left: 1.5rem;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.2s;
}
.next-button :hover {
  color: #e1da0c;
}

/* Chat interface container */
.chat-interface {
  margin-top: 90px;
  max-width: 800px;
  margin: 2rem auto;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  padding: 1.5rem;
}
.chat-interface h2 {
  font-family: 'Playfair Display', serif;
  color: #1a1a1a;
  text-align: center;
  margin-bottom: 1rem;
}

/* Chat box styling */
.chat-box {
  margin-top: 20px;
  border: 1px solid #ccc;
  border-radius: 6px;
  height: 400px;
  padding: 1rem;
  overflow-y: auto;
  background:rgb(255, 255, 255);
}
.chat-box .message {
  margin-bottom: 1rem;
  display: flex;
}
main.chat-interface {
  margin-top: 4rem;
  background-color:rgb(163, 163, 163);
}
main.chat-interface h2{
  color:rgb(255, 255, 255);
}
.chat-box .user,
.chat-box .bot {
  display: inline-block;
  padding: 0.6rem 1rem;
  border-radius: 18px;
  max-width: 75%;
  word-wrap: break-word;
}

.chat-box .user {
  align-self: flex-end;
  background:rgb(13, 163, 232);
  color: #003c8f;
  border-top-right-radius: 0;
}
.chat-box .bot {
  align-self: flex-start;
  background: #e8eaf6;
  color: #1a237e;
  border-top-left-radius: 0;
}

/* Utility */
.hidden {
  display: none !important;
}
#button {
  display: inline-block;
  margin-top: 20px;
  padding: 12px 24px;
  background-color: #1e3a8a; /* Navy blue */
  color: white;
  text-decoration: none;
  font-weight: bold;
  border-radius: 8px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
  transition: background-color 0.3s ease;
}

#button:hover {
  background-color: #3b82f6; /* Lighter blue on hover */
}

</style>
<main class="chat-interface">
  <h2>ARMOR Risk Report</h2>

  <div style="background:rgb(117, 117, 117); color: #f0f0f0; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
    <p><strong>Severity Score:</strong> <?= htmlspecialchars($form2['severity_score']) ?></p>
    <p><strong>Risk Score:</strong> <?= htmlspecialchars($form2['risk_score']) ?></p>
    <p><strong>Final Risk Level:</strong> <?= htmlspecialchars($form2['final_risk']) ?></p>
  </div>
  <div id="chat-box" class="chat-box"></div>
  <a href="thankyou.html" id="button" method="POST">NEXT</a>
</main>

<script>
const prompt = <?php echo json_encode($prompt); ?>;

window.addEventListener('DOMContentLoaded', async () => {
  const chatBox = document.getElementById('chat-box');
  const nextButton = document.getElementById('next-button');
  let redirectURL = null; // Store redirect here

  const addMessage = (sender, text) => {
    const msg = document.createElement('div');
    msg.className = 'message';
    msg.innerHTML = `<span class="${sender === 'You' ? 'user' : 'bot'}">${sender}:</span> ${text}`;
    chatBox.appendChild(msg);
    chatBox.scrollTop = chatBox.scrollHeight;
  };

  addMessage('ARMOR', 'Analyzing your mission data...');

  const response = await fetch('gemini-api.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ prompt })
  });

  const data = await response.json();
  const reply = data.reply || 'No response received.';
  addMessage('ARMOR', reply);

  const dbResponse = await fetch('ARMOR_COND.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ response: reply })
  });

  const dbData = await dbResponse.json();

  if (dbData.status === 'success' && dbData.redirect) {
    redirectURL = dbData.redirect;
    nextButton.style.display = 'inline-block'; // Show the button
  } else {
    console.error('DB submission failed or redirect missing:', dbData);
  }

  nextButton.addEventListener('click', () => {
    if (redirectURL) {
      window.location.href = redirectURL;
    }
  });
});
</script>
</body>
</html>