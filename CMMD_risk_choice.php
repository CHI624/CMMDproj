<?php
session_start();
  // armor_form.php
  // 1) Read and combine .txt files:
$zipPath = "/Applications/XAMPP/htdocs/CMMDproj/army_man_text.zip";
$TEXT_FILES_CONTENT = file_get_contents($zipPath);
$_SESSION['TEXT_FILES_CONTENT'] = $TEXT_FILES_CONTENT;

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
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Risk Choice Simulator</title>
  <link rel="stylesheet" href="style.css" />
</head>

<body>
  <div id="welcome-screen" class="fade-screen">
    <h1>Welcome to the ARMOR RISK MANAGEMENT</h1>
  </div> 
  
  <main id="main-content" style="display: none;">
    <h2>Please Fill Out the Form</h2>
        <h2>HAZARD INFORMATION:</h2>
    <form id = "combinedForm" method="POST" action="/CMMDproj/final_submit.php">
     <section id = "form1">
    <label for="name">DATE OF OPERATION (MM/DD/YYYY):</label><br />
    <input type="text" id="C2_PER_OP" name="C2_PER_OP" required /><br /><br />

    <label for="name">C2 PERSONNEL NAME:</label><br />
      <input type="text" id="C2_PER_N" name="C2_PER_N" required /><br /><br />
    
<label for="rank">C2 PERSONNEL EXPERIENCE (MOS#/NAME):</label><br />
<select id="C2_PER_EX" name="C2_PER_EX" required>
  <option value="">-- Select MOS and Name --</option>
</select>

<script>
fetch('mos_table.csv')
  .then(response => {
    if (!response.ok) throw new Error('Network response was not ok');
    return response.text();
  })
  .then(text => {
    const lines = text.trim().split('\n');
    for(let i = 1; i < lines.length; i++) {
      // Split on comma, but trim spaces just in case
      const [Mos, Name] = lines[i].split(',').map(s => s.trim());
      if(Mos && Name) {
        const optionValue = `${Mos} ${Name}`;
        const option =  document.createElement('option');
        option.value = optionValue;
        option.textContent = optionValue;
        document.getElementById('C2_PER_EX').appendChild(option);
      }
    }
  })
  .catch(err => console.error('Error loading CSV:', err));
</script>


      <label for="rank">RANK(Enlisted, Officer, Warrant Officer):</label><br />
      <select id="RANK" name="RANK" required>
      <option value ="">-- Select Rank --</option>
      <option value="Private">Private</option>
      <option value="Private First Class">Private First Class</option>
      <option value="Specialist">Specialist</option>
      <option value="Corporal">Corporal</option>
      <option value="Sergeant">Sergeant</option>
      <option value="Staff Sergeant">Staff Sergeant</option>
      <option value="Seargent First Class">Seargent First Class</option>
      <option value="Master Sergeant">Master Sergeant</option>
      <option value="First Sergeant">First Sergeant</option>
      <option value="Seargent Major">Seargent Major</option>
      <option value="Command Sergeant Major">Command Sergeant Major</option>
      <option value="Seargent Major of the Army">Seargent Major of the Army</option>
      <option value="Second Lieutenant">Second Lieutenant</option>
      <option value="First Lieutenant">First Lieutenant</option>
      <option value="Captain">Captain</option>
      <option value="Major">Major</option>
      <option value="Lieutenant Colonel">Lieutenant Colonel</option>
      <option value="Colonel">Colonel</option>
      <option value="Brigadier General">Brigadier General</option>
      <option value="Major General">Major General</option>
      <option value="Lieutenant General">Lieutenant General</option>
      <option value="General">General</option>
      <option value="General of the Army">General of the Army</option>
      <option value="Warrant Officer 1">Warrant Officer 1</option>
      <option value="Chief Warrant Officer 2">Chief Warrant Officer 2</option>
      <option value="Chief Warrant Officer 3">Chief Warrant Officer 3</option>
      <option value="Chief Warrant Officer 4">Chief Warrant Officer 4</option>
      <option value="Chief Warrant Officer 5">Chief Warrant Officer 5</option>
      </select>
      <label for="unit">C2 OPERATIONAL EXPERIENCE(RANK 1 - 10):</label><br />
    <div class="radio-group" id="C2_OPER_EX">
      <label><input type="radio" name="C2_OPER_EX" value="1" required>1</label>
      <label><input type="radio" name="C2_OPER_EX" value="2"required>2</label>
      <label><input type="radio" name="C2_OPER_EX" value="3"required>3</label>
      <label><input type="radio" name="C2_OPER_EX" value="4"required>4</label>
      <label><input type="radio" name="C2_OPER_EX" value="5"required>5</label>
      <label><input type="radio" name="C2_OPER_EX" value="6"required>6</label>
      <label><input type="radio" name="C2_OPER_EX" value="7"required>7</label>
      <label><input type="radio" name="C2_OPER_EX" value="8"required>8</label>
      <label><input type="radio" name="C2_OPER_EX" value="9"required>9</label>
      <label><input type="radio" name="C2_OPER_EX" value="10"required>10</label>
    </div>    
    <label for="C2_PER_COM">C2 PERSONNEL TECHNICAL COMPETENCY (RANK 1 – 10):</label><br />
    <div class="radio-group" id="C2_PER_COM">
      <label><input type="radio" name="C2_PER_COM" value="1" required>1</label>
      <label><input type="radio" name="C2_PER_COM" value="2"required>2</label>
      <label><input type="radio" name="C2_PER_COM" value="3"required>3</label>
      <label><input type="radio" name="C2_PER_COM" value="4"required>4</label>
      <label><input type="radio" name="C2_PER_COM" value="5"required>5</label>
      <label><input type="radio" name="C2_PER_COM" value="6"required>6</label>
      <label><input type="radio" name="C2_PER_COM" value="7"required>7</label>
      <label><input type="radio" name="C2_PER_COM" value="8"required>8</label>
      <label><input type="radio" name="C2_PER_COM" value="9"required>9</label>
      <label><input type="radio" name="C2_PER_COM" value="10"required>10</label>
    </div>
      <br /><br />
      <h2>MISSION ANALYSIS</h2><br></br>
      <label for="rank">DATE OF OPERATION(DATE OF SPECIFIC INCIDENT MM/DD/YYYY):</label><br />
      <input type="text" id="DATE_OPER" name="DATE_OPER" required /><br /><br />
      <label for="rank">LOCATION OF OPERATION(Address, Town, State):</label><br />
      <input type="text" id="LOC_OPER" name="LOC_OPER" required /><br /><br />
      <label for="rank">C2 PERSONNEL EXPERIENCE INSIGHTS(Description of Mission):</label><br />
      <input type="text" id="C2_PER_IN" name="C2_PER_IN" required /><br /><br />
            <label for="rank">SITUATIONS:</label><br />
      <input type="text" id="SITUATIONS" name="SITUATIONS" required /><br /><br />
            <label for="rank">CONDITIONS:</label><br />
      <input type="text" id="CONDITIONS" name="CONDITIONS" required /><br /><br />
            <label for="rank">FACTS:</label><br />
      <input type="text" id="FACTS" name="FACTS" required /><br /><br />
        <label for="name">TERRAIN/WEATHER:</label><br />
    <input type="text" id="TERR_WEA" name="TERR_WEA" required /><br /><br />
            <label for="name">NUM OF ENEMIES:</label><br />
    <input type="text" id="NUM_ENEM" name="NUM_ENEM" required /><br /><br />
      <label for="unit">TROOPS OPERATIONAL EXPERIENCE(RANK 1 - 10):</label><br />
    <div class="radio-group" id="TROOPS_OPER_EX">
      <label><input type="radio" name="TROOPS_OPER_EX" value="1" required>1</label>
      <label><input type="radio" name="TROOPS_OPER_EX" value="2"required>2</label>
      <label><input type="radio" name="TROOPS_OPER_EX" value="3"required>3</label>
      <label><input type="radio" name="TROOPS_OPER_EX" value="4"required>4</label>
      <label><input type="radio" name="TROOPS_OPER_EX" value="5"required>5</label>
      <label><input type="radio" name="TROOPS_OPER_EX" value="6"required>6</label>
      <label><input type="radio" name="TROOPS_OPER_EX" value="7"required>7</label>
      <label><input type="radio" name="TROOPS_OPER_EX" value="8"required>8</label>
      <label><input type="radio" name="TROOPS_OPER_EX" value="9"required>9</label>
      <label><input type="radio" name="TROOPS_OPER_EX" value="10"required>10</label>
    </div>
          <label for="unit">CIVIL CONSIDERATIONS(IS THE AREA SPARSELY POPULATED/LOCALS ARE IN SUPPORT OF MISSION?):</label><br />
        <div class="radio-group" id="CIVIL_CON">
      <label><input type="radio" name="CIVIL_CON" value="Yes" required>Yes</label>
      <label><input type="radio" name="CIVIL_CON" value="No"required>No</label>
      </div>
      <style>
        .time-inputs {
  display: flex;
  gap: 0.75rem;
  margin-top: 0.5rem;
  margin-bottom: 1.5rem;
  justify-content: center;
}

.time-inputs input {
  width: 100px;
  padding: 0rem;
  font-size: 1rem;
  font:rgb(214, 187, 14);
  background-color: #1f1f1f;
  color: #ffffff;
  border: 1px solid #444;
  border-radius: 6px;
  text-align: center;
  transition: border-color 0.3s ease;
}

.time-inputs input:focus {
  border-color: #e1da0c;
  outline: none;
}
      </style>
    <label for="TIMEDAYS">TIME (LENGTH OF MISSION):</label><br />
<div class="time-inputs">
<input type="number" id="TIMEDAYS" name="TIMEDAYS" min="0" placeholder="Days" required />
<input type="number" id="TIMEHOURS" name="TIMEHOURS" min="0" max="23" placeholder="Hours" required />
<input type="number" id="TIMEMIN" name="TIMEMIN" min="0" max="59" placeholder="Minutes" required /><br /><br />
</div>
      <label for="name">C2 LESSONS LEARNED:</label><br />
      <input type="text" id="C2_LESSONS" name="C2_LESSONS" required /><br /><br />
    <br /><br />
    <label for="mission_image">Select an image of the Mission Location(OPTIONAL):</label><br />
        <input type="file" id="MISS_IMAGE" name="MISS_IMAGE" accept="image/*" /><br /><br />

        <!-- Audio Input -->
        <label for="mission_audio">Select an audio in relation to the mission insights(OPTIONAL):</label><br />
        <input type="file" id="MISS_AUDIO" name="MISS_AUDIO" accept="audio/*" /><br /><br />

        <button type="button" id="nextToForm2">Next</button>
</section>
  <script>
  // Validation function for Form 1
  function validateForm1() {
    const requiredRadios = document.querySelectorAll('#form1 [required]');
    const grouped = {};

    // Group radio buttons by their name
    requiredRadios.forEach(input => {
      if (input.type === 'radio') {
        if (!grouped[input.name]) grouped[input.name] = [];
        grouped[input.name].push(input);
      }
    });

    // Check if at least one radio is selected per group
    for (let name in grouped) {
      const group = grouped[name];
      const oneChecked = group.some(input => input.checked);
      if (!oneChecked) {
        alert('Please complete all required questions in Form 1.');
        return false;
      }
    }

    return true;
  }

  // Only add ONE event listener for the "Next" button
  document.getElementById('nextToForm2').addEventListener('click', function () {
    if (validateForm1()) {
      // Hide Form 1, show Form 2
      document.getElementById('form1').style.display = 'none';
      document.getElementById('form2').style.display = 'block';
    }
  });
</script>
  <section id="form2" style="display: none;">
  <div class = "form2-wrapper">
  <div id="hazardsContainer">
    <div class="hazard-block" style="position:relative; margin-bottom:1rem;">
      <style> 
       #back-to-form1{
  top: 20px;
  background-color: #005a9c;
  color: white;
  font-size: 1rem;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

#back-to-form1:hover {
  background-color: #004578;
}
      </style>
      <button type="button" id="back-to-form1" name="back-to-form1" class="nav-btn">← Back</button>
      <script> 
        document.getElementById('back-to-form1').addEventListener('click', function () {
      // Hide Form 1, show Form 2
      document.getElementById('form1').style.display = 'block';
      document.getElementById('form2').style.display = 'none';
  });
      </script>
      <h2>Hazard Description</h2>
    <textarea name="DESCR_HAZ" rows="3" required></textarea><br/><br />
    <!-- ... all your EXPOSED_HAZ, experienced_hazard, etc. radio groups ... -->

    <!-- Hidden Input for Combined Hazard List -->
    <h2>Risk Analysis (Probability)</h2>
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
<label for="unit">Complete Mission Failure:</label><br />
    <div class="radio-group" id="COMPLETE_MISSION">
      <label><input type="radio" name="COMPLETE_MISSION" value="1" required>(Highly Unlikely) 1</label>
      <label><input type="radio" name="COMPLETE_MISSION" value="2"required>2</label>
      <label><input type="radio" name="COMPLETE_MISSION" value="3"required>3</label>
      <label><input type="radio" name="COMPLETE_MISSION" value="4"required>(Highly Likely) 4</label>
    </div>
            <label for="unit">Loss of the ability to complete the mission:</label><br />
    <div class="radio-group" id="LOSS_MISSION">
      <label><input type="radio" name="LOSS_MISSION" value="1" required>(Highly Unlikely) 1</label>
      <label><input type="radio" name="LOSS_MISSION" value="2"required>2</label>
      <label><input type="radio" name="LOSS_MISSION" value="3"required>3</label>
      <label><input type="radio" name="LOSS_MISSION" value="4"required>(Highly Likely) 4</label>
    </div>
            <label for="unit">Death:</label><br />
    <div class="radio-group" id="DEATH">
      <label><input type="radio" name="DEATH" value="1" required>(Highly Unlikely) 1</label>
      <label><input type="radio" name="DEATH" value="2"required>2</label>
      <label><input type="radio" name="DEATH" value="3"required>3</label>
      <label><input type="radio" name="DEATH" value="4"required>(Highly Likely) 4</label>
    </div>
            <label for="unit">Permanant Disability:</label><br />
    <div class="radio-group" id="PERM_DISABILITY">
      <label><input type="radio" name="PERM_DISABILITY" value="1" required>(Highly Unlikely) 1</label>
      <label><input type="radio" name="PERM_DISABILITY" value="2"required>2</label>
      <label><input type="radio" name="PERM_DISABILITY" value="3"required>3</label>
      <label><input type="radio" name="PERM_DISABILITY" value="4"required>(Highly Likely) 4</label>
    </div>
            <label for="unit">Loss of Equipment:</label><br />
    <div class="radio-group" id="LOSS_EQUIPMENT">
      <label><input type="radio" name="LOSS_EQUIPMENT" value="1" required>(Highly Unlikely) 1</label>
      <label><input type="radio" name="LOSS_EQUIPMENT" value="2"required>2</label>
      <label><input type="radio" name="LOSS_EQUIPMENT" value="3"required>3</label>
      <label><input type="radio" name="LOSS_EQUIPMENT" value="4"required>(Highly Likely) 4</label>
    </div>
            <label for="unit">Property Damage:</label><br />
    <div class="radio-group" id="PROPERTY_DAMAGE">
      <label><input type="radio" name="PROPERTY_DAMAGE" value="1" required>(Highly Unlikely) 1</label>
      <label><input type="radio" name="PROPERTY_DAMAGE" value="2"required>2</label>
      <label><input type="radio" name="PROPERTY_DAMAGE" value="3"required>3</label>
      <label><input type="radio" name="PROPERTY_DAMAGE" value="4"required>(Highly Likely) 4</label>
    </div>
            <label for="unit">Facility Damage:</label><br />
    <div class="radio-group" id="FACILITY_DAMAGE">
      <label><input type="radio" name="FACILITY_DAMAGE" value="1" required>(Highly Unlikely) 1</label>
      <label><input type="radio" name="FACILITY_DAMAGE" value="2"required>2</label>
      <label><input type="radio" name="FACILITY_DAMAGE" value="3"required>3</label>
      <label><input type="radio" name="FACILITY_DAMAGE" value="4"required>(Highly Likely) 4</label>
    </div>
    <label for="unit">Collateral Damage:</label><br />
    <div class="radio-group" id="COLLATERAL_DAMAGE">
      <label><input type="radio" name="COLLATERAL_DAMAGE" value="1" required>(Highly Unlikely) 1</label>
      <label><input type="radio" name="COLLATERAL_DAMAGE" value="2"required>2</label>
      <label><input type="radio" name="COLLATERAL_DAMAGE" value="3"required>3</label>
      <label><input type="radio" name="COLLATERAL_DAMAGE" value="4"required>(Highly Likely) 4</label>
    </div>
    <!-- at the bottom of your form2 section, just before the closing </script> -->




    </div>    
</div><button type="button" id="addHazardBtn">Add Additional Hazard</button><br/><br />
    <input type="number" id="NUM_HAZ" name="NUM_HAZ" value="1" readonly hidden>
    <input type="hidden" id="COMBINED_HAZARDS" name="COMBINED_HAZARDS">
    <input type="hidden" id="SINGLE_RISK_SCORE"     name="SINGLE_RISK_SCORE">
    <input type="hidden" id="SEVERITY_SCORE"         name="SEVERITY_SCORE">
    <input type="hidden" id="SEVERITY_SCORE_LABEL"   name="SEVERITY_SCORE_LABEL">
    <input type="hidden" id="RISK_SCORE"             name="RISK_SCORE">
    <input type="hidden" id="RISK_SCORE_LABEL"       name="RISK_SCORE_LABEL">
    <input type="hidden" id="FINAL_RISK_LEVEL"       name="FINAL_RISK_LEVEL">
    <button type="submit">Submit Full Record</button>

  </div>
  </section>
  </form>   
  </main>
  <script src="script.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const container    = document.getElementById('hazardsContainer');
  const addBtn       = document.getElementById('addHazardBtn');
  const form         = document.getElementById('combinedForm');
  const numHazInput  = document.getElementById('NUM_HAZ');
  const combined     = document.getElementById('COMBINED_HAZARDS');
  const singleField  = document.getElementById('SINGLE_RISK_SCORE');
  const sevField     = document.getElementById('SEVERITY_SCORE');
  const sevLblField  = document.getElementById('SEVERITY_SCORE_LABEL');
  const riskField    = document.getElementById('RISK_SCORE');
  const riskLblField = document.getElementById('RISK_SCORE_LABEL');
  const finalField   = document.getElementById('FINAL_RISK_LEVEL');

  const severityLabels = {1:'Negligible',2:'Moderate',3:'Critical',4:'Catastrophic'};
  const riskLabels     = {1:'Unlikely',2:'Seldom',3:'Occasional',4:'Likely',5:'Frequent'};
  const riskMatrix     = {
    'Negligible':   {'Unlikely':'L','Seldom':'L','Occasional':'L','Likely':'L','Frequent':'M'},
    'Moderate':     {'Unlikely':'L','Seldom':'L','Occasional':'M','Likely':'M','Frequent':'H'},
    'Critical':     {'Unlikely':'L','Seldom':'M','Occasional':'H','Likely':'H','Frequent':'EH'},
    'Catastrophic': {'Unlikely':'M','Seldom':'H','Occasional':'H','Likely':'EH','Frequent':'EH'}
  };

  function updateCount() {
    numHazInput.value = container.querySelectorAll('.hazard-block').length;
  }

  function injectRemove(block) {
    // Only clone blocks get a remove button; skip the very first one
    if (!block.dataset.cloned) return;
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.innerHTML = '&times;';
    btn.style.cssText = 'position:absolute; top:0.5rem; right:0.5rem; color:red; background:none; border:none; font-size:1.2rem; cursor:pointer;';
    btn.addEventListener('click', () => {
      block.remove();
      wireAll(); // re-wire everything
    });
    block.style.position = 'relative';
    block.appendChild(btn);
  }


  function initPastHazard(block) {
    const radios      = block.querySelectorAll('input[name^="experienced_hazard"]');
    const pastSection = block.querySelector('#past_hazard_section');
    const pastInputs  = block.querySelectorAll('#past_hazard_section input');

    pastSection.style.display = 'none';
    pastInputs.forEach(i => i.required = false);

    radios.forEach(radio => {
      radio.addEventListener('change', () => {
        if (radio.value === 'yes') {
          pastSection.style.display = 'block';
          pastInputs.forEach(i => i.required = true);
        } else {
          pastSection.style.display = 'none';
          pastInputs.forEach(i => {
            i.required = false;
            if (i.type==='radio'||i.type==='checkbox') i.checked = false;
            else i.value = '';
          });
        }
      });
    });
  }

  //–– Re-wire everything (remove+past-logic+count) after add/remove
  function wireAll() {
    container.querySelectorAll('.hazard-block').forEach((blk,i) => {
      // mark clones
      if (i>0) blk.dataset.cloned = 'true';
      initPastHazard(blk);
      injectRemove(blk);
    });
    updateCount();
    updateHiddenScores();
  }

  //–– Score calculation across ALL blocks
  function recordBlock(block, arrS, arrSv, arrSvLbl, arrR, arrRLbl) {
    // severity
    const sevNames = ['COMPLETE_MISSION','LOSS_MISSION','DEATH','PERM_DISABILITY',
                      'LOSS_EQUIPMENT','PROPERTY_DAMAGE','FACILITY_DAMAGE','COLLATERAL_DAMAGE'];
    let sumSv=0, cntSv=0;
    sevNames.forEach(n => {
      const inp = block.querySelector(`input[name^="${n}"]:checked`);
      if (inp) { sumSv += +inp.value; cntSv++; }
    });
    const avgSv = cntSv? sumSv/cntSv : 0;
    const lblSv = severityLabels[Math.round(avgSv)]||'N/A';

    // risk
    const val = nm => {
      const i = block.querySelector(`input[name^="${nm}"]:checked`);
      return i? +i.value : null;
    };
    const ex  = val('EXPOSED_HAZ');
    const im  = val('IMMEDIATE_HAZ');
    const yes = !!block.querySelector(`input[name^="experienced_hazard"][value="yes"]:checked`);
    const pt  = yes? val('PAST_HAZ') : null;
    let arr = [ex,im].filter(v=>v!=null);
    if (pt!=null) arr.push(pt);
    const div = pt!=null? 3:2;
    const sumR = arr.reduce((a,b)=>a+b,0);
    const avgR = div? sumR/div : 0;
    const lblR = riskLabels[Math.round(avgR)]||'N/A';

    // single letter
    const single = (riskMatrix[lblSv]||{})[lblR]||'N/A';

    arrS.push(single);
    arrSv.push(avgSv.toFixed(2));
    arrSvLbl.push(lblSv);
    arrR.push(avgR.toFixed(2));
    arrRLbl.push(lblR);
  }

  function updateHiddenScores() {
    const S=[] , SV=[], SVL=[], R=[], RL=[];
    container.querySelectorAll('.hazard-block').forEach(bl=> recordBlock(bl,S,SV,SVL,R,RL));
    singleField.value  = S.join(', ');
    sevField.value     = SV.join(', ');
    sevLblField.value  = SVL.join(', ');
    riskField.value    = R.join(', ');
    riskLblField.value = RL.join(', ');
    // final collective
       const allDescriptions = Array.from(
      container.querySelectorAll('textarea[name^="DESCR_HAZ"]')
    )
    .map(t => t.value.trim())
    .filter(v => v)
    .map(v => `• ${v}`)
    .join('\n');

    combined.value = allDescriptions;
    const totSv = SV.reduce((a,v)=>a+parseFloat(v),0),
          totR  = R .reduce((a,v)=>a+parseFloat(v),0),
          avgSv = SV.length? totSv/SV.length : 0,
          avgR  = R .length? totR/R .length : 0,
          fSv   = severityLabels[Math.round(avgSv)] || 'N/A',
          fR    = riskLabels[Math.round(avgR)]     || 'N/A';
    finalField.value = (riskMatrix[fSv]||{})[fR]||'N/A';
    updateCount();
    
  }

  //–– Add hazard button
  addBtn.addEventListener('click', () => {
    const blocks   = container.querySelectorAll('.hazard-block');
    const last     = blocks[blocks.length-1];
    const clone    = last.cloneNode(true);
    const idx      = blocks.length + 1;

    // clear
    clone.querySelectorAll('input,textarea').forEach(el=>{
      if (el.type==='radio'||el.type==='checkbox') el.checked=false;
      else el.value='';
    });
    // rename
    clone.querySelectorAll('[name]').forEach(el=>{
      const o = el.getAttribute('name');
      el.setAttribute('name', `${o}_${idx}`);
    });

    last.after(clone);
    wireAll();
  });

  //–– Initial wire
  wireAll();

  //–– Submit hook
  form.addEventListener('submit', () => {
    updateHiddenScores();
    // now the hidden fields are up-to-date when PHP reads $_POST[…]
  });
});
</script>
</body>
</html>