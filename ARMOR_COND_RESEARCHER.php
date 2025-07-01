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
$form_res = $_SESSION['form_res'] ?? [];
$form_res1 = $_SESSION['form_res1'] ?? [];

$researcher   = $form_res['researcher'] ?? '';
$job          = $form_res['job']        ?? '';
$job_descr    = $form_res['job_descr']  ?? '';
$quer_image   = $form_res['quer_image'] ?? '';
$mainList = $form_res['main_list'] ?? '';
$descList = $form_res['desc_list'] ?? '';
$locList = $form_res['loc_list'] ?? '';

$types = $descs = $locs = [];
if (!empty($form_res['hazards']) && is_array($form_res['hazards'])) {
    foreach ($form_res['hazards'] as $hazard) {
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

$descr_haz        = $form_res1['descr_haz']      ?? '';
$risk_score       = $form_res1['risk_score']     ?? '';
$single_risk      = $form_res1['single_risk']    ?? '';
$num_haz          = $form_res1['num_haz']        ?? '';
$severity_score   = $form_res1['severity_score'] ?? '';
$combined_hazards = $form_res1['combined_hazards'] ?? '';
$final_risk       = $form_res1['final_risk']     ?? '';




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