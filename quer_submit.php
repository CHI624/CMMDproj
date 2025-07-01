<?php
session_start();
unset($_SESSION['chat']);

$host = "localhost";
$db   = "form_submissions_quer";
$user = "root";
$pass = "Dabonem123!";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ── 1) Decode the JSON‑encoded list of hazards from the form POST
$hazardsJson = $_POST['hazardsList'] ?? '[]';
$hazards     = json_decode($hazardsJson, true);

// ── 2) Store it back into session so your next page’s chat code still sees it
$_SESSION['hazards'] = $hazards;

// ── 3) Build three comma‑list strings out of that array
$mainArr = $descArr = $locArr = [];
foreach ($hazards as $h) {
    $mainArr[] = $h['main_hazard']   ?? '';
    $descArr[] = $h['description']   ?? '';
    $locArr[]  = $h['location']      ?? '';
}

$mainList = implode(', ', $mainArr);
$descList = implode(' || ', $descArr);
$locList  = implode(', ', $locArr);

// ── Your other scalar form fields
$RESEARCHER_N = $_POST['RESEARCHER_N'] ?? '';
$JOB          = $_POST['JOB']          ?? '';
$JOB_DESCR    = $_POST['JOB_DESCR']    ?? '';
$QUER_IMAGE   = $_POST['QUER_IMAGE']   ?? '';

// ── Single INSERT with the three list‑columns
$sql = "
  INSERT INTO form_submissions_quer1
    (RESEARCHER_N, JOB, JOB_DESCR, QUER_IMAGE, MAIN_HAZ, DESCR_MAIN, HAZ_LOC)
  VALUES
    (?, ?, ?, ?, ?, ?, ?)
";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL prepare failed: " . $conn->error);
}

$stmt->bind_param(
    "sssssss",
    $RESEARCHER_N,
    $JOB,
    $JOB_DESCR,
    $QUER_IMAGE,
    $mainList,
    $descList,
    $locList
);

if (!$stmt->execute()) {
    die("Insert failed: " . $stmt->error);
}
$_SESSION['form1'] = [
    'researcher' => $RESEARCHER_N,
    'job'        => $JOB,
    'job_descr'  => $JOB_DESCR,
    'quer_image' => $QUER_IMAGE,
    'main_list' => $mainList,
    'desc_list' => $descList,
    'loc_list' => $locList,
    'hazards'    => $hazards,
];
$stmt->close();
$conn->close();

// Now the chat page (ARMOR_QUER_INPUT.php) will see $_SESSION['hazards'] again
header("Location: ARMOR_QUER_INPUT.php");
exit;
?>
