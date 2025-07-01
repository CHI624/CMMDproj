<?php
session_start();
// store_control.php
$host = 'localhost';
$db   = 'form_submissions_quer'; // or your actual DB
$user = 'root';
$pass = 'Dabonem123!';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
  http_response_code(500);
  die("Connection failed: " . $conn->connect_error);
}
// extend for other severity metrics

// Hidden aggregated fields
$form1 = $_SESSION['form1'] ?? [];
$form2 = $_SESSION['form2'] ?? [];

$researcher   = $form1['researcher'] ?? '';
$job          = $form1['job']        ?? '';
$job_descr    = $form1['job_descr']  ?? '';
$quer_image   = $form1['quer_image'] ?? '';
$mainList = $form1['main_list'] ?? '';
$descList = $form1['desc_list'] ?? '';
$locList = $form1['loc_list'] ?? '';

$types = $descs = $locs = [];
if (!empty($form1['hazards']) && is_array($form1['hazards'])) {
    foreach ($form1['hazards'] as $hazard) {
        // each hazard is [ main_hazard, description, location ]
        list($type, $desc, $loc) = $hazard;
        $types[] = $type;
        $descs[] = $desc;
        $locs[]  = $loc;
    }
}
// Turn them into comma‑separated strings
$typesStr = implode(', ', $types);
$descsStr = implode(' || ', $descs);   // use a separator that won't conflict with commas
$locsStr  = implode(', ', $locs);

$descr_haz        = $form2['descr_haz']      ?? '';
$risk_score       = $form2['risk_score']     ?? '';
$single_risk      = $form2['single_risk']    ?? '';
$num_haz          = $form2['num_haz']        ?? '';
$severity_score   = $form2['severity_score'] ?? '';
$combined_hazards = $form2['combined_hazards'] ?? '';
$final_risk       = $form2['final_risk']     ?? '';




$data = json_decode(file_get_contents("php://input"), true);
$response = $data['response'] ?? '';

if ($response !== '') {
  $stmt = $conn->prepare("INSERT INTO form_submissions_full (response, researcher, job, job_descr, quer_image,
  descr_haz, risk_score, single_risk, num_haz, severity_score,
  combined_hazards, final_risk, hazard_types, hazard_descriptions, hazard_locations) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
  $stmt->bind_param("sssssssssssssss", $response,
$researcher,
    $job,
    $job_descr,
    $quer_image,
    $descr_haz,
    $risk_score,
    $single_risk,
    $num_haz,
    $severity_score,
    $combined_hazards,
    $final_risk,
    $mainList,
    $descList,
    $locList
);
  if (! $stmt->execute()) {
  http_response_code(500);
  die("Insert failed: " . htmlspecialchars($stmt->error));
}

$stmt->close();
$conn->close();

// Redirect and stop everything else
header("Location: thankyou.html");exit;
}
?>