<?php var_dump($_SESSION['form1_data']); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Your Website Name | Home</title>

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400&display=swap" rel="stylesheet" />

  <link rel="stylesheet" href="style.css" />

  <style>
    body {
  font-family: 'Futura', Tahoma, Geneva, Verdana, sans-serif;
  background-color: #282828;
  margin: 0;
  padding: 2rem;
  color: #333;
}

h2 {

  color: #e4dd0d;
  font: 700 2rem 'Trebuchet MS', serif;
  margin-top: 3rem;
  border-bottom: 2px solid #ccc;
  padding-bottom: 0.5rem;
}

form {
  background-color: #282828;
  border-radius: 8px;
  bottom: 10px;
  padding: 2rem;
  box-shadow: 0 2px 8px rgba(34, 34, 34, 0.1);
}
h3 {
  color: #e4dd0d;
  font: 17px 'Futura','bold', 'serif';
  margin-top: 3rem;
    border-bottom: 1px solid #ccc;
  padding-bottom: 0.5rem;
}
label {
  display: block;
  font-weight: 600;
  font: 1rem 'Poppins', sans-serif;
  margin: 1.2rem 0 0.5rem;
}

.radio-group {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 1rem;
  padding: 0.5rem 0 1.5rem;
}

.radio-group label {
  font-weight: normal;
  background-color: #454545;
  border: 1px solid #cbd6e2;
  border-radius: 6px;
  padding: 0.4rem 0.8rem;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.radio-group label:hover {
  background-color: #515151;
}

.radio-group input[type="radio"] {
  margin-right: 0.5rem;
}

button[type="submit"] {
  margin-top: 2rem;
  background-color: #005a9c;
  color: white;
  font-size: 1rem;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

button[type="submit"]:hover {
  background-color: #004578;
}

/* Optional responsive enhancement */
@media (max-width: 600px) {
  form {
    padding: 1rem;
  }

  .radio-group {
    flex-direction: column;
  }
}
  </style>
</head>
<body>

<header class="site-header">
  <div class="header-content">
    <a href="index.html" class="logo">
      <img src="Photos_Videos/DOD logo.png" alt="CMMD Logo" class="logo-icon" />
      <span><h1>ARMOR</h1></span>
    </a>
    <nav class="nav-menu">
      <a href="index.html">Home</a>
      <a href="CMMD_risk_choice.php">ARMOR</a>
      <a href="about.html">About</a>
      <a href="projects.html">Projects</a>
    </nav>
  </div>
</header>

<main>
  <h2>Risk Analysis (Probability)</h2>
  <form id="combinedForm" method="POST" action="/CMMDproj/final_submit.php">
    <!-- Probability Section -->
    <label for="EXPOSED_HAZ">Likelihood of exposure to the hazard:</label>
    <div class="radio-group" id="EXPOSED_HAZ">
      <label><input type="radio" name="EXPOSED_HAZ" value="1" required>(Highly Unlikely) 1</label>
      <label><input type="radio" name="EXPOSED_HAZ" value="2">2</label>
      <label><input type="radio" name="EXPOSED_HAZ" value="3">3</label>
      <label><input type="radio" name="EXPOSED_HAZ" value="4">4</label>
      <label><input type="radio" name="EXPOSED_HAZ" value="5">(Highly Likely) 5</label>
    </div>

    <label>Have you experienced a similar hazard before?</label>
    <div class="radio-group" id="experienced_hazard">
      <label><input type="radio" name="experienced_hazard" value="yes" required>Yes</label>
      <label><input type="radio" name="experienced_hazard" value="no">No</label>
    </div>

    <div id="past_hazard_section" style="display: none;">
      <label for="PAST_HAZ">How problematic was the previous hazard?</label>
      <div class="radio-group" id="PAST_HAZ">
        <label><input type="radio" name="PAST_HAZ" value="1">(Not Problematic) 1</label>
        <label><input type="radio" name="PAST_HAZ" value="2">2</label>
        <label><input type="radio" name="PAST_HAZ" value="3">3</label>
        <label><input type="radio" name="PAST_HAZ" value="4">4</label>
        <label><input type="radio" name="PAST_HAZ" value="5">(Very Problematic) 5</label>
      </div>
    </div>

    <label for="IMMEDIATE_HAZ">Immediate threat to mission:</label>
    <div class="radio-group" id="IMMEDIATE_HAZ">
      <label><input type="radio" name="IMMEDIATE_HAZ" value="1" required>(Highly Unlikely) 1</label>
      <label><input type="radio" name="IMMEDIATE_HAZ" value="2">2</label>
      <label><input type="radio" name="IMMEDIATE_HAZ" value="3">3</label>
      <label><input type="radio" name="IMMEDIATE_HAZ" value="4">4</label>
      <label><input type="radio" name="IMMEDIATE_HAZ" value="5">(Highly Likely) 5</label>
    </div>

    <!-- Severity Section -->
    <h2>Risk Analysis (Severity)</h2>
    <h3>Indicate the likelihood of the present hazard potentially resulting in 
        each of the following(1-4):</h3>

    <!-- Repeatable block for each consequence -->
    <script>
      const severityQuestions = [
        ['COMPLETE_MISSION', 'Complete Mission Failure'],
        ['LOSS_MISSION', 'Loss of the ability to complete the mission'],
        ['DEATH', 'Death'],
        ['PERM_DISABILITY', 'Permanent Disability'],
        ['LOSS_EQUIPMENT', 'Loss of Equipment'],
        ['PROPERTY_DAMAGE', 'Property Damage'],
        ['FACILITY_DAMAGE', 'Facility Damage'],
        ['COLLATERAL_DAMAGE', 'Collateral Damage']
      ];
    </script>

    <div id="severityFields"></div>

    <!-- Hidden Inputs -->
    <input type="hidden" id="RISK_SCORE" name="RISK_SCORE" />
    <input type="hidden" id="RISK_SCORE_LABEL" name="RISK_SCORE_LABEL" />
    <input type="hidden" id="SEVERITY_SCORE" name="SEVERITY_SCORE" />
    <input type="hidden" id="SEVERITY_SCORE_LABEL" name="SEVERITY_SCORE_LABEL" />
    <input type="hidden" id="FINAL_RISK_LEVEL" name="FINAL_RISK_LEVEL" />

    <button type="submit">Submit Full Record</button>
  </form>
</main>

<script>
  // Show/hide past hazard question
  const hazardRadios = document.getElementsByName('experienced_hazard');
  const pastHazardSection = document.getElementById('past_hazard_section');
  const pastHazardInputs = document.getElementsByName('PAST_HAZ');

  hazardRadios.forEach(radio => {
    radio.addEventListener('change', () => {
      if (radio.value === 'yes') {
        pastHazardSection.style.display = 'block';
        pastHazardInputs.forEach(input => input.required = true);
      } else {
        pastHazardSection.style.display = 'none';
        pastHazardInputs.forEach(input => {
          input.required = false;
          input.checked = false;
        });
      }
    });
  });

  // Generate severity radio groups dynamically
  const severityContainer = document.getElementById('severityFields');
  severityQuestions.forEach(([name, labelText]) => {
    const label = document.createElement('label');
    label.textContent = labelText;
    const div = document.createElement('div');
    div.className = 'radio-group';
    div.id = name;
    for (let i = 1; i <= 4; i++) {
      const lbl = document.createElement('label');
      lbl.innerHTML = `<input type="radio" name="${name}" value="${i}" required>${i === 1 ? '(Negligible)' : i === 4 ? '(Catastrophic)' : ''} ${i}`;
      div.appendChild(lbl);
    }
    severityContainer.appendChild(label);
    severityContainer.appendChild(div);
  });

  // Risk calculation on submit
  document.getElementById('combinedForm').addEventListener('submit', function (e) {
    const severityFields = severityQuestions.map(q => q[0]);

    let totalSeverity = 0;
    let countSeverity = 0;

    severityFields.forEach(name => {
      const selected = document.querySelector(`input[name="${name}"]:checked`);
      if (selected) {
        totalSeverity += parseInt(selected.value);
        countSeverity++;
      }
    });

    const avg = (countSeverity > 0) ? (totalSeverity / countSeverity).toFixed(2) : 0;
    const rounded = Math.round(avg);

    const severityLabels = {
      4: 'Catastrophic',
      3: 'Critical',
      2: 'Moderate',
      1: 'Negligible'
    };

    const severityLabel = severityLabels[rounded] || 'N/A';
    document.getElementById('SEVERITY_SCORE').value = avg;
    document.getElementById('SEVERITY_SCORE_LABEL').value = severityLabel;

    const riskLabel = document.getElementById('RISK_SCORE_LABEL').value || 'Unlikely';

    const riskMatrix = {
      'Negligible': { 'Unlikely': 'L', 'Seldom': 'L', 'Occasional': 'L', 'Likely': 'L', 'Frequent': 'M' },
      'Moderate': { 'Unlikely': 'L', 'Seldom': 'L', 'Occasional': 'M', 'Likely': 'M', 'Frequent': 'H' },
      'Critical': { 'Unlikely': 'L', 'Seldom': 'M', 'Occasional': 'H', 'Likely': 'H', 'Frequent': 'EH' },
      'Catastrophic': { 'Unlikely': 'M', 'Seldom': 'H', 'Occasional': 'H', 'Likely': 'EH', 'Frequent': 'EH' }
    };

    document.getElementById('FINAL_RISK_LEVEL').value = (riskMatrix[severityLabel] && riskMatrix[severityLabel][riskLabel]) || 'N/A';
  });
</script>

</body>
</html>