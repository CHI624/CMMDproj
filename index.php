<?php
session_start();
// 🔹 Generate a unique ID per user session if not set
if (!isset($_SESSION['USER_ID'])) {
    $_SESSION['USER_ID'] = uniqid('user_', true); // e.g. user_662bc9f18b3227.51912434
}

// 🔹 Define asset tags
$assetTags = [
    'FI_1.jpeg'             => 'FP',
    'GroundMine.jpg'        => 'GMP',
    'Tactical_Road_M_text.png' => 'GMT',
    'Fires_text.png'        => 'FT',
    'GROUND_MINES.png'      => 'GMPT',
    'CALI_FIRES.png'        => 'FPT',
];

// 1) Define each asset → [ caption, [ five hazards ] ]
$assets = [
    __DIR__ . '/Photos_Videos/FI_1.jpeg' => [
      'caption' => 'Apple: The universal symbol of knowledge.',
      'hazards' => [
        'Flames',
        'Smoke',
        'Wind',
        'Low Visibility',
        'Falling Debris'
      ]
    ],
    __DIR__ . '/Photos_Videos/GroundMine.jpg' => [
      'caption' => 'Orange: Bright and full of vitamin C.',
      'hazards' => [
        'Land Mines',
        'Limited Visibility(Night Operation)',
        'Surface Traction Capability',
        'Road Width',
        'Rain and Cold'
      ]
    ],
    __DIR__ . '/Photos_Videos/Tactical_Road_M_text.png' => [
      'caption' => 'ADP 2-0: Discusses intelligence in operations.',
      'hazards' => [
        'Land Mines',
        'Limited Visibility(Night Operation)',
        'Surface Traction Capability',
        'Road Width',
        'Rain and Cold'
      ]
    ],
    __DIR__ . '/Photos_Videos/Fires_text.png' => [
      'caption' => 'ADP 1: Introduces Army Doctrine and fundamentals.',
      'hazards' => [
        'Flames',
        'Smoke',
        'Wind',
        'Low Visibility',
        'Falling Debris'
      ]
    ],
    __DIR__ . '/Photos_Videos/GROUND_MINES.png' => [
      'caption' => 'ADP 1-01: Outlines the Army Profession.',
      'hazards' => [
        'Land Mines',
        'Limited Visibility(Night Operation)',
        'Surface Traction Capability',
        'Road Width',
        'Rain and Cold'
      ]
    ],
    __DIR__ . '/Photos_Videos/CALI_FIRES.png' => [
      'caption' => 'ADP 2-0: Discusses intelligence in operations.',
      'hazards' => [
        'Flames',
        'Smoke',
        'Wind',
        'Low Visibility',
        'Falling Debris'
      ]
    ],
];
if (empty($_SESSION['serve_queue'])) {
    $queue = [];
    // Repeat each asset key 9 times
    foreach (array_keys($assets) as $path) {
        for ($i = 0; $i < 9; $i++) {
            $queue[] = $path;
        }
    }
    shuffle($queue);
    $_SESSION['serve_queue'] = $queue;
}
// 2) Pick one asset at random
$randomAsset = array_shift($_SESSION['serve_queue']);
$_SESSION['serve_queue'] = $_SESSION['serve_queue'];
$info        = $assets[$randomAsset];
$caption     = $info['caption'];
$hazardList  = $info['hazards'];

$filename = basename($randomAsset);
$assetTag = $assetTags[$filename] ?? 'UN'; // UN = Unknown


// 3) Expose for JS
$_SESSION['RANDOM_ASSET']  = $randomAsset;
$_SESSION['ASSET_CAPTION'] = $caption;
$_SESSION['ASSET_TAG']      = $assetTag;
$_SESSION['HAZARD_LIST']    = $hazardList;

$ext = strtolower(pathinfo($randomAsset, PATHINFO_EXTENSION));
$publicPath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $randomAsset);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ARMOR Form</title>
  <link rel="stylesheet" href="style.css">
  <script>
  const userId   = "<?= $_SESSION['USER_ID']   ?? '' ?>";
  const assetTag = "<?= $_SESSION['ASSET_TAG'] ?? '' ?>";
  sessionStorage.setItem('USER_ID', userId);
sessionStorage.setItem('ASSET_TAG', assetTag);
</script>
</head>
<body>
  <main id="main-content">
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
<main id="main-content">
  <style>
    /* Next button styling */
button {
  display: inline-block;
  padding: 0.75rem 1.5rem;
  font-family: 'Poppins', sans-serif;
  font-size: 1rem;
  font-weight: 500;
  color: #fff;
  background: linear-gradient(135deg, #4a90e2, #007aff);
  border: none;
  border-radius: 0.5rem;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  cursor: pointer;
  transition: transform 0.15s ease, box-shadow 0.15s ease;
  text-transform: uppercase;
}

button:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}

button:active {
  transform: translateY(0);
  box-shadow: 0 3px 6px rgba(0,0,0,0.1);
}

button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
  </style>
<form id = "combinedForm" action="thankyou1.html">
  <section id = "Acceptance">
     <div style="text-align:center; margin-bottom:1em;">
    <img src="Photos_Videos/info_page.jpg" alt="Consent Form" style="max-width:100%; height:auto;">
  </div>
        <button type="button" id="Thankyou_button">Next</button>
  </section>
  <section id="thankyou" style="display:none">
    <h2>Thank you for agreeing to take part in the following survey. For this survey, you will be 
      presented with an image, a short vignette, or both which will then be followed by a hazard that is
      present within. Please answer each of the questions pertaining to the hazard. After answering all
      questions, press the buttton to continue on to the next hazard. A total of 5 hazards will be    
      presented to you for the image or text. Additionally, we will be collecting a few minor
      demographic items for use in our analysis.

      Thank you again for your participation in this experiment.
    </h2><br></br>
    <button type="button" id="nextToForm1">Next</button>
  </section>
 <section id = "form1" style="display:none">

  <h2>PERSONAL INFORMATION:</h2>
<label for="rank">C2 PERSONNEL EXPERIENCE (MOS#/NAME):</label><br />
<select id="C2_PER_EX" name="C2_PER_EX" required>
  <option value="">-- Select MOS and Name --</option>
</select>

<script>
fetch('organized_civ_rank1.csv')
  .then(response => {
    if (!response.ok) throw new Error('Network response was not ok');
    return response.text();
  })
  .then(text => {
    const lines = text.trim().split('\n');
    for(let i = 1; i < lines.length; i++) {
      // Split on comma, but trim spaces just in case
      const [Mos, Name] = lines[i].split('|').map(s => s.trim());
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
<style>
  label[for="rank"] {
    display: block;
    font-weight: bold;
    margin-bottom: 6px;
    font-family: Arial, sans-serif;
    font-size: 14px;
    color: #333;
  }

  #USER_RANK {
    width: 100%;
    max-width: 400px;
    padding: 10px 12px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 6px;
    background-color: #f9f9f9;
    color: #333;
    font-family: Arial, sans-serif;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: url("data:image/svg+xml;utf8,<svg fill='gray' height='16' viewBox='0 0 24 24' width='16' xmlns='http://www.w3.org/2000/svg'><path d='M7 10l5 5 5-5z'/></svg>");
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 16px;
    cursor: pointer;
    transition: border-color 0.3s ease;
  }

  #USER_RANK:focus {
    outline: none;
    border-color: #007bff;
    background-color: #fff;
  }
</style>
      <label for="rank">RANK(Enlisted, Officer, Warrant Officer):</label><br />
      <select id="USER_RANK" name="USER_RANK" required>
      <option value ="">-- Select Rank --</option>
      <option value="Civilian">Civilian(No Rank)</option>
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
      <style>
        .form-group {
  margin: 2rem 0;
  padding: 0.3rem;
  background-color: #f0f8ff;
  border-left: 6px solidrgb(210, 220, 24);
  border-radius: 8px;
  font-size: 1.2rem;
}

.form-group label {
  font-weight: bold;
  display: block;
  margin-bottom: 0.5rem;
  color:rgb(213, 216, 18);
}

.form-group input[type="number"] {
  width: 100%;
  padding: 0.6rem;
  font-size: 1.1rem;
  border: 2px solidrgb(220, 217, 27);
  border-radius: 5px;
  outline: none;
  box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

      </style>
      <label for="unit">YEARS OF EXPERIENCE:</label><br />
    <div class="form-group" id="YEARS_EX">
        <input type="number" name="YEARS_EX" id="YEARS_EX" min="0" max="100" required>
    </div> 
    <style>
#backToForm1 {
  display: inline-block;
  padding: 0.75rem 1.5rem;
  font-family: 'Poppins', sans-serif;
  font-size: 1rem;
  font-weight: 500;
  color: #fff;
  background: linear-gradient(135deg, #4a90e2, #007aff);
  border: none;
  border-radius: 0.5rem;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  cursor: pointer;
  transition: transform 0.15s ease, box-shadow 0.15s ease;
  text-transform: uppercase;
}

#backToForm1:hover {
  background:rgb(10, 89, 180);
}
    </style>
    <button type="button" id="nextToForm2">Next</button>   
</section>
<section id="form2" style="display:none" >
  <div class = "form2-wrapper">
        <button type="button" id="backToForm1" style="margin-bottom: 1rem;">← Back</button>
  <div id="hazardsContainer">
    <div class="hazard-block" data-index="1" style="position:relative; margin-bottom:1rem;">
      <div id="hidden-lists-container"></div>
      <h2>Based on the prompt below, answer the questions:</h2>

    <!--  Display the randomly picked asset -->
    <?php if (in_array($ext, ['jpg','jpeg','png','gif'])): ?>
      <img src="<?= htmlspecialchars($publicPath) ?>"
           alt="Random asset"
           style="max-width:100%;display:block;margin:1rem auto;">
    <?php elseif ($ext==='txt'): ?>
      <pre style="background:#f4f4f4;padding:1rem;border-radius:4px;max-height:100%;margin:1rem auto;">
<?= htmlspecialchars(file_get_contents($randomAsset)) ?>
      </pre>
    <?php endif; ?>
      <h2>HAZARD QUESTIONS:</h2>
      <h2>Hazard Description</h2>
      <style>
        textarea[readonly] {
            background-color:rgb(255, 255, 255);
            font: "Futura", "sans-serif", "bold";
            font-size: 20px;
            width: 400px;
            justify-content: center;
            color: #333;
            cursor: default;
            resize: none;
        }
        textarea[name="DESCR_HAZ[]"] {
  background-color: #fef9e7; /* soft yellow highlight */
  border: 2px solid #f1c40f; /* bold yellow border */
  border-radius: 8px;
  padding: 1rem;
  font-family: 'Segoe UI', 'Poppins', sans-serif;
  font-size: 1.1rem;
  font-weight: 600;
  color: #333;
  resize: none;
  width: 100%;
  max-width: 600px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  margin-bottom: 1rem;
  cursor: default;
}
 textarea[name="DESCR_HAZ"] {
  background-color: #fef9e7; /* soft yellow highlight */
  border: 2px solid #f1c40f; /* bold yellow border */
  border-radius: 8px;
  padding: 1rem;
  font-family: 'Segoe UI', 'Poppins', sans-serif;
  font-size: 1.1rem;
  font-weight: 600;
  color: #333;
  resize: none;
  width: 100%;
  max-width: 600px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  margin-bottom: 1rem;
  cursor: default;
}
      </style>
      <textarea name="DESCR_HAZ[]" rows="2" required readonly></textarea>

    <!-- Hidden Input for Combined Hazard List -->
    <h2>Risk Analysis (Probability)</h2>
    <label for="EXPOSED_HAZ">How likely is it that an individual(s) will be exposed to the hazard?:</label>
    <div class="radio-group" id="EXPOSED_HAZ">
      <label><input type="radio" name="EXPOSED_HAZ" value="1" required>(Highly Unlikely) 1</label>
      <label><input type="radio" name="EXPOSED_HAZ" value="2">2</label>
      <label><input type="radio" name="EXPOSED_HAZ" value="3">3</label>
      <label><input type="radio" name="EXPOSED_HAZ" value="4">4</label>
      <label><input type="radio" name="EXPOSED_HAZ" value="5">(Highly Likely) 5</label>
    </div>
          <label>How problematic has a hazard of a similar nature been in the past?</label>
      <div class="radio-group" id="PAST_HAZ">
        <label><input type="radio" name="PAST_HAZ" value="1">(Not Problematic) 1</label>
        <label><input type="radio" name="PAST_HAZ" value="2">2</label>
        <label><input type="radio" name="PAST_HAZ" value="3">3</label>
        <label><input type="radio" name="PAST_HAZ" value="4">4</label>
        <label><input type="radio" name="PAST_HAZ" value="5">(Very Problematic) 5</label>
      </div>
    <label for="IMMEDIATE_HAZ">How much of an immediate threat does the hazard pose to the current mission?</label>
    <div class="radio-group" id="IMMEDIATE_HAZ">
      <label><input type="radio" name="IMMEDIATE_HAZ" value="1" required>(No Immediate Concern) 1</label>
      <label><input type="radio" name="IMMEDIATE_HAZ" value="2">2</label>
      <label><input type="radio" name="IMMEDIATE_HAZ" value="3">3</label>
      <label><input type="radio" name="IMMEDIATE_HAZ" value="4">4</label>
      <label><input type="radio" name="IMMEDIATE_HAZ" value="5">(Highly Concerning) 5</label>
    </div>
    <h2>Risk Analysis (Severity)</h2>
    <h3>Given the present hazard, please indicate the level of damage this hazard poses to each of the following:</h3>
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
    <h2>Given the present hazard, please indicate the level of damage this hazard poses to each of the following:</h2>
    <label for="unit">Property Damage:</label><br />
    <div class="radio-group" id="PROPERTY_DAMAGE">
      <label><input type="radio" name="PROPERTY_DAMAGE" value="1" required>(No Damage) 1</label>
      <label><input type="radio" name="PROPERTY_DAMAGE" value="2"required>2</label>
      <label><input type="radio" name="PROPERTY_DAMAGE" value="3"required>3</label>
      <label><input type="radio" name="PROPERTY_DAMAGE" value="4"required>(A lot of Damage) 4</label>
    </div>
    <label for="unit">Facility Damage:</label><br />
    <div class="radio-group" id="FACILITY_DAMAGE">
      <label><input type="radio" name="FACILITY_DAMAGE" value="1" required>(No Damage) 1</label>
      <label><input type="radio" name="FACILITY_DAMAGE" value="2"required>2</label>
      <label><input type="radio" name="FACILITY_DAMAGE" value="3"required>3</label>
      <label><input type="radio" name="FACILITY_DAMAGE" value="4"required>(A lot of Damage) 4</label>
    </div>
    <label for="unit">Collateral Damage:</label><br />
    <div class="radio-group" id="COLLATERAL_DAMAGE">
      <label><input type="radio" name="COLLATERAL_DAMAGE" value="1" required>(No Damage) 1</label>
      <label><input type="radio" name="COLLATERAL_DAMAGE" value="2"required>2</label>
      <label><input type="radio" name="COLLATERAL_DAMAGE" value="3"required>3</label>
      <label><input type="radio" name="COLLATERAL_DAMAGE" value="4"required>(A lot of Damage) 4</label>
    </div>  
    </div>
    </div>
  </div>
    <!-- at the bottom of your form2 section, just before the closing </script> -->
    <input type="number" id="NUM_HAZ" name="NUM_HAZ" value="1" readonly hidden>
    <input type="hidden" name="EXPOSED_HAZ_LIST[]"   id="EXPOSED_HAZ_LIST">
<input type="hidden" name="experienced_hazard_LIST[]"   id="experienced_hazard_LIST">
    <input type="hidden" name="PAST_HAZ_LIST[]"   id="PAST_HAZ_LIST">
    <input type="hidden" name="IMMEDIATE_HAZ_LIST[]"   id="IMMEDIATE_HAZ_LIST">
    <input type="hidden" name="COMPLETE_MISSION_LIST[]" id="COMPLETE_MISSION_LIST">
    <input type="hidden" name="LOSS_MISSION_LIST[]" id="LOSS_MISSION_LIST">
    <input type="hidden" name="DEATH_LIST[]" id="DEATH_LIST">
    <input type="hidden" name="PERM_DISABILITY_LIST[]" id="PERM_DISABILITY_LIST">
    <input type="hidden" name="LOSS_EQUIPMENT_LIST[]" id="LOSS_EQUIPMENT_LIST">
        <input type="hidden" name="PROPERTY_DAMAGE_LIST[]"   id="PROPERTY_DAMAGE_LIST">
    <input type="hidden" name="FACILITY_DAMAGE_LIST[]"   id="FACILITY_DAMAGE_LIST">
    <input type="hidden" name="COLLATERAL_DAMAGE_LIST[]"   id="COLLATERAL_DAMAGE_LIST">
    <input type="hidden" id="COMBINED_HAZARDS" name="COMBINED_HAZARDS">
    <input type="hidden" id="SINGLE_RISK_SCORE"     name="SINGLE_RISK_SCORE">
    <input type="hidden" id="SEVERITY_SCORE"         name="SEVERITY_SCORE">
    <input type="hidden" id="SEVERITY_SCORE_LABEL"   name="SEVERITY_SCORE_LABEL">
    <input type="hidden" id="RISK_SCORE"             name="RISK_SCORE">
    <input type="hidden" id="RISK_SCORE_LABEL"       name="RISK_SCORE_LABEL">
    <input type="hidden" id="FINAL_RISK_LEVEL"       name="FINAL_RISK_LEVEL">
  <style>
    /* Next button styling */
  #WNEXT {
  display: inline-block;
  padding: 0.75rem 1.5rem;
  font-family: 'Poppins', sans-serif;
  font-size: 1rem;
  font-weight: 500;
  color: #fff;
  background: linear-gradient(135deg, #4a90e2, #007aff);
  border: none;
  border-radius: 0.5rem;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  cursor: pointer;
  transition: transform 0.15s ease, box-shadow 0.15s ease;
  text-transform: uppercase;
}

#WNEXT:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}

#WNEXT:active {
  transform: translateY(0);
  box-shadow: 0 3px 6px rgba(0,0,0,0.1);
}

#WNEXT:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
  </style>
    <button type="button" id="WNEXT" >Next</button> 
  <script>
  document.getElementById('WNEXT').addEventListener('click', () => {
    window.scrollTo({ top: 19, behavior: 'smooth' });
  });
</script>
  </section>

  <button type="submit" id = "submitBtn" style="display:none">Submit Full Record</button>
  </form>   
  </main>    

  <script src="script.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const container    = document.getElementById('hazardsContainer');
  const form         = document.getElementById('combinedForm');
  const numHazInput  = document.getElementById('NUM_HAZ');
  const combined     = document.getElementById('COMBINED_HAZARDS');
  const singleField  = document.getElementById('SINGLE_RISK_SCORE');
  const sevField     = document.getElementById('SEVERITY_SCORE');
  const sevLblField  = document.getElementById('SEVERITY_SCORE_LABEL');
  const riskField    = document.getElementById('RISK_SCORE');
  const riskLblField = document.getElementById('RISK_SCORE_LABEL');
  const finalField   = document.getElementById('FINAL_RISK_LEVEL');
  const templateHTML = container.querySelector('.hazard-block').outerHTML;
  const acceptSec = document.getElementById('Acceptance');
  const nextBtn = document.getElementById('WNEXT');
    const form1Sec  = document.getElementById('form1');
    const form2Sec  = document.getElementById('form2');
    const thankyou  = document.getElementById('thankyou');
    const btn       = document.getElementById('Thankyou_button');
    const btn1       = document.getElementById('nextToForm1');
    const btn2       = document.getElementById('nextToForm2');
    const submitBtn = document.getElementById('submitBtn');
    const hazards = <?= json_encode($hazardList, JSON_UNESCAPED_SLASHES); ?>;
    const TOTAL_HAZ = hazards.length;
    const listTypes = [
    'EXPOSED_HAZ','experienced_hazard','PAST_HAZ','IMMEDIATE_HAZ',
    'COMPLETE_MISSION','LOSS_MISSION','DEATH','PERM_DISABILITY',
    'LOSS_EQUIPMENT','PROPERTY_DAMAGE','FACILITY_DAMAGE','COLLATERAL_DAMAGE'
  ];
const userId   = sessionStorage.getItem('USER_ID') || Math.floor(Math.random() * 900000).toString();
const assetTag = sessionStorage.getItem('ASSET_TAG');
const backBtn = document.getElementById('backToForm1');  
numHazInput.value = 1; // Initialize to 1 when form2 begins
backBtn.style.display = 'inline-block'; // Initially visible on first block

    btn.addEventListener('click', () => {
      // grab the selected radio
        acceptSec.style.display = 'none';
        thankyou.style.display = 'block';
    });
    btn1.addEventListener('click', () => {
      // grab the selected radio
        thankyou.style.display = 'none';
        form1Sec.style.display = 'block';
    });
btn2.addEventListener('click', () => {
  // validate Form 2 before moving on
  const form1Sec = document.getElementById('form1');
  const groups   = form1Sec.querySelectorAll('.radio-group');
      const per = document.querySelector('select[name="C2_PER_EX"]');
if (!per || !per.value.trim()) {
  alert('Please select your C2_PER_EX value.');
  return;
}
backBtn.addEventListener('click', () => {
  document.getElementById('form2').style.display = 'none';
  document.getElementById('form1').style.display = 'block';
});
const rank = document.querySelector('select[name="USER_RANK"]');
if (!rank || !rank.value.trim()) {
  alert('Please select your rank.');
  return;
}
      const years = document.querySelector('input[name="YEARS_EX"]');
if (!years || years.value.trim() === "") {
  alert('Please enter your years of experience.');
  return;
}
  for (let group of groups) {
    // each group div wraps a set of radios
    const anyChecked = group.querySelector('input[type="radio"]:checked');
    if (!anyChecked) {
      const label = group.previousElementSibling?.textContent.trim() || 'one of the questions';
      alert(`Please select an option for "${label}".`);
      return;
    }
  }

  // if you have any textareas you also want required:
  const textareas = form1Sec.querySelectorAll('textarea[required]');
  for (let ta of textareas) {
    if (!ta.value.trim()) {
      alert('Please fill out all descriptions.');
      ta.focus();
      return;
    }
  }
        form1Sec.style.display = 'none';  
        form2Sec.style.display = 'block';
        nextBtn.style.display = 'inline-block'; 
        submitBtn.style.display   = 'none';
   
});         


  const severityLabels = {1:'Negligible',2:'Moderate',3:'Critical',4:'Catastrophic'};
  const riskLabels     = {1:'Unlikely',2:'Seldom',3:'Occasional',4:'Likely',5:'Frequent'};
  const riskMatrix     = {
    'Negligible':   {'Unlikely':'L','Seldom':'L','Occasional':'L','Likely':'L','Frequent':'M'},
    'Moderate':     {'Unlikely':'L','Seldom':'L','Occasional':'M','Likely':'M','Frequent':'H'},
    'Critical':     {'Unlikely':'L','Seldom':'M','Occasional':'H','Likely':'H','Frequent':'EH'},
    'Catastrophic': {'Unlikely':'M','Seldom':'H','Occasional':'H','Likely':'EH','Frequent':'EH'}
  };

    // clear container, then rebuild 5 blocks
container.innerHTML = ''; // Clear initial block


// 1) empty the container
container.innerHTML = '';
const blocks = hazards.map(label => {
  const wrap  = document.createElement('div');
  wrap.innerHTML = templateHTML;
  const blk = wrap.firstElementChild;
  const formData = new FormData(document.getElementById('combinedForm'));

  // clear old radios
  blk.querySelectorAll('input[type="radio"]').forEach(r => r.checked = false);

  // set new description
  const ta = blk.querySelector('textarea[name="DESCR_HAZ[]"]');
  if (ta) ta.value = label;

  // hide
  blk.style.display = 'none';
  container.appendChild(blk);
  return blk;
});
// show only the first
let current = 0;
blocks[current].style.display = 'block';
submitBtn.style.display = 'none';

// Store them so they persist across navigation

// 2) Next‑button cycles & AJAX‑submits
nextBtn.addEventListener('click', async () => {
  const blk = blocks[current];

  // a) validation
  for (let grp of blk.querySelectorAll('.radio-group')) {
  if (!grp.querySelector('input:checked')) {
    const label = grp.previousElementSibling?.textContent?.trim() || 'one of the questions';
    alert(`Please answer "${label}"`);
    return;
  }
}

  // b) collect form data
  const formData = new FormData(document.getElementById('combinedForm'));

  // b1) global fields (CONSENT, RANK, etc.)
  ['C2_PER_EX','USER_RANK','YEARS_EX','NUM_HAZ','COMBINED_HAZARDS']
    .forEach(name => {
      const el = document.querySelector(`[name="${name}"]`);
      if (el) formData.append(name, el.value);
    });
formData.append('USER_ID', userId);
  formData.append('ASSET_TAG', assetTag);
  // b2) this block’s description
  const desc = blk.querySelector('textarea[name="DESCR_HAZ[]"]');
  if (desc) formData.append('DESCR_HAZ', desc.value);

  // b3) this block’s radios
  // b3) this block’s radios — handle PAST_HAZ exception
listTypes.forEach(type => {
  const sel = blk.querySelector(`input[name^="${type}"]:checked`);

  if (type === 'PAST_HAZ') {
    // Always append the selected PAST_HAZ value (or an explicit “0” if none)
    formData.append('PAST_HAZ', sel ? sel.value : '0');
  } else if (sel) {
    formData.append(type, sel.value);
  }
});

// b4) calculate scores for this block
let sumSv = 0, cntSv = 0;
const severityNames = [
  'COMPLETE_MISSION','LOSS_MISSION','DEATH','PERM_DISABILITY',
  'LOSS_EQUIPMENT','PROPERTY_DAMAGE','FACILITY_DAMAGE','COLLATERAL_DAMAGE'
];

severityNames.forEach(name => {
  const sel = blk.querySelector(`input[name^="${name}"]:checked`);
  if (sel) {
    sumSv += parseFloat(sel.value);
    cntSv++;
  }
});

const avgSv = cntSv ? sumSv / cntSv : 0;
const lblSv = severityLabels[Math.round(avgSv)] || 'N/A';

// risk
const getVal = name => {
  const inp = blk.querySelector(`input[name^="${name}"]:checked`);
  return inp ? parseFloat(inp.value) : null;
};

const ex = getVal('EXPOSED_HAZ');
const im = getVal('IMMEDIATE_HAZ');
const pt = getVal('PAST_HAZ');

const riskVotes = [ex, im];
if (pt !== null) riskVotes.push(pt);
const avgR = riskVotes.reduce((a, b) => a + b, 0) / riskVotes.length;
const lblR = riskLabels[Math.round(avgR)] || 'N/A';

const single = (riskMatrix[lblSv] || {})[lblR] || 'N/A';

// ✅ Add to form data
formData.append('SEVERITY_SCORE', avgSv.toFixed(2));
formData.append('SEVERITY_SCORE_LABEL', lblSv);
formData.append('RISK_SCORE', avgR.toFixed(2));
formData.append('RISK_SCORE_LABEL', lblR);
formData.append('SINGLE_RISK_SCORE', single);

  // c) send to PHP
  try {
    const resp = await fetch('/ASESS_SUBMIT.php', {
      method:'POST',
      body:  formData
    });
    if (!resp.ok) throw new Error(resp.statusText);
  } catch (err) {
    return alert('Error saving hazard #'+(current+1)+': '+err);
  }

  // d) advance UI
  // d) advance UI
blk.style.display = 'none';
current++;
numHazInput.value = parseInt(numHazInput.value) + 1;
if (current < blocks.length) {
  blocks[current].style.display = 'block';

  // ✅ Hide the back button after moving past first block
  if (current > 0) {
    backBtn.style.display = 'none';
  }

  // show submit button on final block
  if (current === blocks.length - 1) {
    nextBtn.style.display   = 'none';
    submitBtn.style.display = 'inline-block';
  }
}
});

// 3) Final “Submit” falls back to normal POST if needed:
submitBtn.addEventListener('click', async () => {
  const blk = blocks[current]; // current is already pointing at the last block

  // a) validate current block
  for (let grp of blk.querySelectorAll('.radio-group')) {
  if (!grp.querySelector('input:checked')) {
    const label = grp.previousElementSibling?.textContent?.trim() || 'one of the questions';
    alert(`Please answer "${label}"`);
    return;
  }
}

  // b) collect form data
  const formData = new FormData(document.getElementById('combinedForm'));

  // Global fields
  ['C2_PER_EX','USER_RANK','YEARS_EX','NUM_HAZ','COMBINED_HAZARDS'].forEach(name => {
    const el = document.querySelector(`[name="${name}"]`);
    if (el) formData.append(name, el.value);
  });
formData.append('USER_ID', userId);
formData.append('ASSET_TAG', assetTag);
  // This block's textarea
  const desc = blk.querySelector('textarea[name="DESCR_HAZ[]"]');
  if (desc) formData.append('DESCR_HAZ', desc.value);

    // b3) this block’s radios
  // b3) this block’s radios — handle PAST_HAZ exception
listTypes.forEach(type => {
  const sel = blk.querySelector(`input[name^="${type}"]:checked`);

  if (type === 'PAST_HAZ') {
    // Always append the selected PAST_HAZ value (or an explicit “0” if none)
    formData.append('PAST_HAZ', sel ? sel.value : '0');
  } else if (sel) {
    formData.append(type, sel.value);
  }
});


// b4) calculate scores for this block
let sumSv = 0, cntSv = 0;
const severityNames = [
  'COMPLETE_MISSION','LOSS_MISSION','DEATH','PERM_DISABILITY',
  'LOSS_EQUIPMENT','PROPERTY_DAMAGE','FACILITY_DAMAGE','COLLATERAL_DAMAGE'
];

severityNames.forEach(name => {
  const sel = blk.querySelector(`input[name^="${name}"]:checked`);
  if (sel) {
    sumSv += parseFloat(sel.value);
    cntSv++;
  }
});

const avgSv = cntSv ? sumSv / cntSv : 0;
const lblSv = severityLabels[Math.round(avgSv)] || 'N/A';

// risk
const getVal = name => {
  const inp = blk.querySelector(`input[name^="${name}"]:checked`);
  return inp ? parseFloat(inp.value) : null;
};

const ex = getVal('EXPOSED_HAZ');
const im = getVal('IMMEDIATE_HAZ');
const pt = getVal('PAST_HAZ');

const riskVotes = [ex, im];
if (pt !== null) riskVotes.push(pt);

const avgR = riskVotes.reduce((a, b) => a + b, 0) / riskVotes.length;
const lblR = riskLabels[Math.round(avgR)] || 'N/A';

const single = (riskMatrix[lblSv] || {})[lblR] || 'N/A';

// ✅ Add to form data
formData.append('SEVERITY_SCORE', avgSv.toFixed(2));
formData.append('SEVERITY_SCORE_LABEL', lblSv);
formData.append('RISK_SCORE', avgR.toFixed(2));
formData.append('RISK_SCORE_LABEL', lblR);
formData.append('SINGLE_RISK_SCORE', single);
  // c) POST to backend
  try {
    const resp = await fetch('/ASESS_SUBMIT.php', {
      method: 'POST',
      body:   formData
    });
    if (!resp.ok) throw new Error(resp.statusText);

    // d) After success, redirect to thank you
    window.location.href = 'thankyou1.html';

  } catch (err) {
    alert('Error saving final hazard: ' + err);
  }
});


function updateCount() {
    const count = container.querySelectorAll('.hazard-block').length;
    numHazInput.value = count;
  }

  // 5) wire up your yes/no → show/hide follow-up
 function initPast() {
  // If your blocks use .haz_b instead, replace '.hazard-block' below
  document.querySelectorAll('.hazard-block').forEach(block => {
    const idx = block.dataset.index; // e.g. "1", "2", …

    const pastSection = block.querySelector('.past-hazard-section');
    if (!pastSection) return;

    // All the radios inside that follow-up section
    const pastRadios = pastSection.querySelectorAll('input[type="radio"]');
    pastSection.style.display = 'none';
    pastRadios.forEach(r => r.required = false);

    // Target the wrapper div by id
    const experiencedGroup = block.querySelector(`#experienced_hazard_${idx}`);
    if (!experiencedGroup) return;

    // Inside that, find the yes/no buttons
    const yesBtn = experiencedGroup.querySelector('input[value="yes"]');
    const noBtn  = experiencedGroup.querySelector('input[value="no"]');
    if (!yesBtn || !noBtn) return;

    yesBtn.addEventListener('change', () => {
      pastSection.style.display = '';       // show it
      pastRadios.forEach(r => r.required = true);
    });

    noBtn.addEventListener('change', () => {
      pastSection.style.display = 'none';   // hide
      pastRadios.forEach(r => {
        r.required = false;
        r.checked  = false;
      });
    });
  });
}

  //–– Re-wire everything (remove+past-logic+count) after add/remove
  function wireAll() {
    container.querySelectorAll('.hazard-block').forEach((blk, i) => {
      blk.dataset.index = i+1;                     // set data-index
      renameFields(blk, i+1);                      // rename inside
      initPast(blk);                         // re-attach yes/no logic
    });
  }

  function renameFields(block, idx) {
  block.querySelectorAll('[name]').forEach(el => {
    let name = el.getAttribute('name');
    // 1) don’t rename the DESCR_HAZ[] textarea:
    if (name === 'DESCR_HAZ[]') return;

    // 2) detect if it’s an array:
    const isArray = name.endsWith('[]');
    // strip off any existing _n suffix or []:
    let base = name
      .replace(/\[\]$/, '')    // remove trailing []
      .replace(/_\d+$/, '');   // remove trailing _n

    // 3) rebuild the new name: base_IDX plus back the [] if needed
    el.name = `${base}_${idx}${isArray? '[]' : ''}`;
  });

  // do the same for IDs if you need them unique
  block.querySelectorAll('[id]').forEach(el => {
    const base = el.id.replace(/_\d+$/, '');
    el.id = `${base}_${idx}`;
  });
}
  // … inside your DOMContentLoaded handler, *after* you’ve generated your 5 .hazard-block’s …

// 1) Updated recordBlock

// 3) Hook it all up
// …after you build your 5 blocks…
wireAll();            // if you still need to wire show/hide pastHazard
updateHiddenScores(); // populate hidden fields immediately
// 4) On submit

});
</script>
</body>
</html>
