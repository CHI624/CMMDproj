<?php
session_start();
$host = "db-mysql-nyc3-19913-do-user-23595991-0.m.db.ondigitalocean.com";
$port = 25060; // usually this for MySQL on DigitalOcean
$db = "form_submissions_asess";
$user = "doadmin";
$pass = "AVNS__Tm7x5axtN8phP_O_m9";
$conn = new mysqli($host, $user, $pass, $db, $port);

// Check DB connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$zipPath = "/Applications/XAMPP/htdocs/CMMDproj/army_man_text.zip";

// Sanitize and assign POST values
$C2_PER_EX        = $_POST['C2_PER_EX']        ?? '';
$USER_RANK             = $_POST['USER_RANK']             ?? '';
$YEARS_EX       = $_POST['YEARS_EX']       ?? '';
$NUM_HAZ     = $_POST['NUM_HAZ']    ?? ''; // Count number of hazards
$COMBINED_HAZARDS    = $_POST['COMBINED_HAZARDS']    ?? '';
$RISK_SCORE     = $_POST['RISK_SCORE']             ?? '';
$SEVERITY_SCORE      = $_POST['SEVERITY_SCORE']         ?? '';
$SINGLE_RISK_SCORE   = $_POST['SINGLE_RISK_SCORE']      ?? '';
$DESCR_HAZ        = $_POST['DESCR_HAZ']        ?? '';
$EXPOSED_HAZ        = $_POST['EXPOSED_HAZ']        ?? '';
$PAST_HAZ           = $_POST['PAST_HAZ']           ?? '';
$IMMEDIATE_HAZ      = $_POST['IMMEDIATE_HAZ']      ?? '';
$COMPLETE_MISSION   = $_POST['COMPLETE_MISSION']   ?? '';
$LOSS_MISSION       = $_POST['LOSS_MISSION']       ?? '';
$DEATH              = $_POST['DEATH']              ?? '';
$PERM_DISABILITY    = $_POST['PERM_DISABILITY']    ?? '';
$LOSS_EQUIPMENT     = $_POST['LOSS_EQUIPMENT']     ?? '';
$PROPERTY_DAMAGE    = $_POST['PROPERTY_DAMAGE']    ?? '';
$FACILITY_DAMAGE    = $_POST['FACILITY_DAMAGE']    ?? '';
$COLLATERAL_DAMAGE  = $_POST['COLLATERAL_DAMAGE']  ?? '';
$USER_ID   = $_POST['USER_ID']   ?? '';
$ASSET_TAG = $_POST['ASSET_TAG'] ?? '';
// Prepare SQL insert with prepared statement
$sql = "INSERT INTO form_submissions (
    USER_ID, ASSET_TAG, C2_PER_EX, USER_RANK, YEARS_EX, DESCR_HAZ,
    RISK_SCORE, SINGLE_RISK_SCORE, SEVERITY_SCORE, EXPOSED_HAZ, PAST_HAZ, IMMEDIATE_HAZ,
    COMPLETE_MISSION, LOSS_MISSION, DEATH, PERM_DISABILITY, LOSS_EQUIPMENT, PROPERTY_DAMAGE, FACILITY_DAMAGE, COLLATERAL_DAMAGE, NUM_HAZ, COMBINED_HAZARDS
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?,?,?,?,?,?,?,?,?,?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
  http_response_code(500);
  echo "SQL prepare failed: " . $conn->error;
  exit;
}
    // Prepare the statement per loop (or just bind again if prepared outside)
    $stmt->bind_param(
        "ssssssssssssssssssssds",
        $USER_ID, $ASSET_TAG, $C2_PER_EX, $USER_RANK, $YEARS_EX, $DESCR_HAZ,
        $RISK_SCORE, $SINGLE_RISK_SCORE, $SEVERITY_SCORE, $EXPOSED_HAZ, $PAST_HAZ, $IMMEDIATE_HAZ, $COMPLETE_MISSION, 
        $LOSS_MISSION, $DEATH, $PERM_DISABILITY, $LOSS_EQUIPMENT, $PROPERTY_DAMAGE, 
        $FACILITY_DAMAGE, $COLLATERAL_DAMAGE, $NUM_HAZ, $COMBINED_HAZARDS
    );
if (!$stmt->execute()) {
  http_response_code(500);
  echo "Insert failed: " . $stmt->error;
  exit;
}

$stmt->close();
$conn->close();

header("Location: thankyou1.html");
exit;
?>
