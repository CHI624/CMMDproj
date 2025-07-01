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
  <main id="main-content">
    <h2>Please Fill Out the Form</h2>
    <form id = "combinedForm" method="POST" action="Analysis_asess_submit.php">
    <section id="form2">
  <div class = "form2-wrapper">
  <div id="hazardsContainer">
    <div class="hazard-block" style="position:relative; margin-bottom:1rem;">
         <style> 
       #back-to-form1{
  margin-top: -90px;
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
      <a href="ARMOR_QUER_INPUT.php"><button type="button" id="back-to-form1" name="back-to-form1" class="nav-btn">← Back</button></a>
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