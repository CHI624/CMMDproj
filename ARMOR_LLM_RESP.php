<?php
session_start();
$SEVERITY_SCORE = $_SESSION['SEVERITY_SCORE'] ?? 'N/A';
$RISK_SCORE     = $_SESSION['RISK_SCORE'] ?? 'N/A';
$SINGLE_RISK_SCORE = $_SESSION['SINGLE_RISK_SCORE'] ?? 'N/A';
$FINAL_RISK_LEVEL    = $_SESSION['FINAL_RISK_LEVEL'] ?? 'N/A';
$COMBINED_HAZARDS = $_SESSION['COMBINED_HAZARDS']  ?? 'N/A';      
$C2_PER_N = $_SESSION['C2_PER_N'] ?? 'N/A';     
$C2_PER_IN = $_SESSION['C2_PER_IN'] ?? 'N/A';
$form1 = $_SESSION['form1'] ?? [];
$form2 = $_SESSION['form2'] ?? [];

// 1) Connect to DB
$host = 'localhost';
$db   = 'form_submissions_quer';
$user = 'root';
$pass = 'Dabonem123!';
$conn = new mysqli($host,$user,$pass,$db);
if($conn->connect_error) {
  die("DB Conn error: ".$conn->connect_error);
}

// 2) Fetch your historical controls
$sql = "SELECT response, researcher, job, final_risk, hazard_types
        FROM form_submissions_full
        ORDER BY id DESC
        LIMIT 3000";   // or whatever slice you want
$res = $conn->query($sql);

$historyText = "";
if ($res) {
  while ($row = $res->fetch_assoc()) {
    $historyText .= "— Session from {$row['researcher']} ({$row['job']})\n";
    $historyText .= "  • Final Risk Level: {$row['final_risk']}\n";
    $historyText .= "  • Hazards Described: {$row['hazard_types']}\n";
    $historyText .= "  • Controls Recommended: {$row['response']}\n\n";
  }
}
$conn->close();

// 3) Build your new prompt, injecting both the *entire history* and the *current* session summary
$fullPrompt = <<<EOT
You are a military operations analyst.

You are analyzing risk control effectiveness across missions. Your goal is to recommend risk mitigation strategies for the current report by comparing it to past hazard data and controls.

### PAST SESSION LOGS ###
Each past log includes:
- Final risk level
- Hazard descriptions
- Control actions taken

Matrix(For Severity Score, Risk Score and Final Risk:
Severity Level	Unlikely	Seldom	Occasional	Likely	Frequent
Negligible	L	L	L	L	M
Moderate	L	L	M	M	H
Critical	L	M	H	H	EH
Catastrophic	M	H	H	EH	EH

$historyText
### END OF HISTORY ###

### CURRENT USER SESSION ###
Final Risk Level: {$FINAL_RISK_LEVEL}
Reported Hazards:
{$COMBINED_HAZARDS}
{$C2_PER_IN}

Severity Score: {$SEVERITY_SCORE}
Risk Score: {$RISK_SCORE}
Single Risk Score: {$SINGLE_RISK_SCORE}

Based on similarity to previous hazard reports and final risk levels, recommend appropriate control or mitigation strategies tailored to the current session, also include {$form1['researcher']} name in your response (in under 200 words).
IF YOU DO NOT FIND DATA THAT IS SIMILAR TO CURRENT USER DATA, THEN ONLY OUTPUT, MESSAGE "WE DO NOT HAVE ANY DATA ON FILE" AND END YOUR RESPONSE.
EOT;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ARMOR Risk Summary</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400&display=swap" rel="stylesheet" />
  </head>
<body>
  <header class="site-header">
    <div class="header-content">
      <a href="index.html" class="logo">
        <img src="Photos_Videos/DOD logo.png" alt="CMMD Logo" class="logo-icon" />
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
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background-color: #121212;
    }

    header.site-header {
      background: #1f1f1f;
      padding: 1rem 2rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    header .logo {
      display: flex;
      align-items: center;
      color: #e1da0c;
      text-decoration: none;
    }

    header .logo img {
      height: 40px;
      margin-right: 1rem;
    }

    nav.nav-menu a {
      margin-left: 1rem;
      color: white;
      text-decoration: none;
    }

    .chat-interface {
      max-width: 800px;
      margin: 3rem auto;
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
      height: auto;
      background: #f5f8fa;
      border-radius: 6px;
      margin-bottom: 1rem;
    }

    .message {
      margin-bottom: 1rem;
      display: flex;
      flex-direction: column;
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

    h2 {
      color: #e1da0c;
      font-family: "Trebuchet MS", sans-serif;
      font-size: 30px;
      margin-bottom: 13px;
    }

    label {
      color:rgb(0, 0, 0);
      font-family: "Trebuchet MS", sans-serif, bold;
      font-size: 25px;
      margin-bottom: 0px;
    }
  button{
        padding: 0.7rem 1.2rem;
  font-size: 1rem;
  background-color:rgb(27, 129, 239);
  border: none;
  color: rgb(0, 0, 0);
  top: 30px;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.3s ease;
    }
button:hover{
      background-color: #0056b3;
    }
  </style>
  <main class="chat-interface">
    <h2>ARMOR RISK SUMMARY</h2>
    <div id="chat-box" class="chat-box">
      <div class="message bot"><strong>Severity Score(s):</strong> <?php echo htmlspecialchars($SEVERITY_SCORE); ?></div>
      <div class="message bot"><strong>Probability Score(s):</strong> <?php echo htmlspecialchars($RISK_SCORE); ?></div>
      <div class="message bot"><strong>Single Probability Score(s):</strong> <?php echo htmlspecialchars($SINGLE_RISK_SCORE); ?></div>
      <div class="message bot"><strong>Final Risk Level:</strong> <?php echo htmlspecialchars($FINAL_RISK_LEVEL); ?></div>
    </div>
    <a href="thankyou.html"><button id="button">NEXT</button></a>
  </main>
  <script>
      // 4) Embed the PHP prompt into JS
      const prompt = <?php echo json_encode($fullPrompt); ?>;

      window.addEventListener('DOMContentLoaded', async () => {
        const chatBox = document.getElementById('chat-box');
        const addMessage = (sender, text) => {
          const msg = document.createElement('div');
          msg.className = 'message bot';
          msg.innerHTML = `<strong>${sender}:</strong> ${text}`;
          chatBox.appendChild(msg);
        };

        addMessage('ARMOR', 'Analyzing historical controls and current risk...');

        // 5) Send just the prompt in "response-only" mode
        const res = await fetch('gemini-api.php', {
          method: 'POST',
          headers: {'Content-Type':'application/json'},
          body: JSON.stringify({ prompt, response_only: true })
        });
        const { reply } = await res.json();
        addMessage('ARMOR', reply || 'No response received.');
      });
    </script>
</body>
</html>
