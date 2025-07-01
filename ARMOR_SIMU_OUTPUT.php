<?php
session_start();
$SEVERITY_SCORE = $_SESSION['SEVERITY_SCORE'] ?? 'N/A';
$RISK_SCORE     = $_SESSION['RISK_SCORE'] ?? 'N/A';
$SINGLE_RISK_SCORE = $_SESSION['SINGLE_RISK_SCORE'] ?? 'N/A';
$FINAL_RISK_LEVEL    = $_SESSION['FINAL_RISK_LEVEL'] ?? 'N/A';
$isMissImageAvailable = !empty($_SESSION['MISS_IMAGE']) && $_SESSION['MISS_IMAGE'] !== 'N/A';
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

    .cmmd-btn.disabled {
  background-color: #444 !important;
  color: #999 !important;
  cursor: not-allowed;
  pointer-events: none;
  opacity: 0.6;
}

  </style>
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

  <main class="chat-interface">
    <h2>ARMOR RISK SUMMARY</h2>
    <di id="chat-box" class="chat-box">
      <div class="message bot"><strong>Severity Score(s):</strong> <?php echo htmlspecialchars($SEVERITY_SCORE); ?></div>
      <div class="message bot"><strong>Probability Score(s):</strong> <?php echo htmlspecialchars($RISK_SCORE); ?></div>
      <div class="message bot"><strong>Single Probability Score(s):</strong> <?php echo htmlspecialchars($SINGLE_RISK_SCORE); ?></div>
      <div class="message bot"><strong>Final Risk Level:</strong> <?php echo htmlspecialchars($FINAL_RISK_LEVEL); ?></div>
      <label>CHOICES:</label><br></br>
<?php if ($isMissImageAvailable): ?>
  <a href="thankyou.html"><button type="button" class="cmmd-btn">CMMD</button></a>
<?php else: ?>
  <button type="button" class="cmmd-btn disabled" disabled>CMMD (Image Required)</button>
<?php endif; ?>   
<a href="ARMOR_LLM_RESP.php"><button type="button">LLM(Large Language Model)</button></a>
    </div>
  </main>
</body>
</html>
