<?php
session_start();
$host = "localhost";
$db = "form_submissions";
$user = "root";
$pass = "Dabonem123!";
$conn = new mysqli($host, $user, $pass, $db);

// Check DB connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$zipPath = "/Applications/XAMPP/htdocs/CMMDproj/army_man_text.zip";

// Sanitize and assign POST values
$C2_PER_OP        = $_POST['C2_PER_OP']        ?? '';
$C2_PER_N         = $_POST['C2_PER_N']         ?? '';
$C2_PER_EX        = $_POST['C2_PER_EX']        ?? '';
$SITUATIONS        = $_POST['SITUATIONS']        ?? '';
$CONDITIONS        = $_POST['CONDITIONS']        ?? '';
$FACTS        = $_POST['FACTS']        ?? '';
$RANK             = $_POST['RANK']             ?? '';
$DATE_OPER        = $_POST['DATE_OPER']        ?? '';
$LOC_OPER         = $_POST['LOC_OPER']         ?? '';
$C2_PER_IN        = $_POST['C2_PER_IN']        ?? '';
$C2_PER_COM       = $_POST['C2_PER_COM']       ?? '';
$C2_OPER_EX       = $_POST['C2_OPER_EX']       ?? '';
$C2_LESSONS       = $_POST['C2_LESSONS']       ?? '';
$TERR_WEA      = $_POST['TERR_WEA']       ?? '';
$NUM_ENEM       = $_POST['NUM_ENEM']       ?? '';
$TROOPS_OPER_EX       = $_POST['TROOPS_OPER_EX']       ?? '';
$CIVIL_CON       = $_POST['CIVIL_CON']       ?? '';
$TIMEDAYS      = $_POST['TIMEDAYS']       ?? '0.00';
$TIMEHOURS      = $_POST['TIMEHOURS']       ?? '0.00';
$TIMEMIN      = $_POST['TIMEMIN']       ?? '0.00';
$MISS_IMAGE       = $_POST['MISS_IMAGE']       ?? '';
$MISS_AUDIO       = $_POST['MISS_AUDIO']       ?? '';
$RISK_SCORE       = $_POST['RISK_SCORE']       ?? '';
$SEVERITY_SCORE   = $_POST['SEVERITY_SCORE']   ?? '';
$SINGLE_RISK_SCORE = $_POST['SINGLE_RISK_SCORE'] ?? '';
$NUM_HAZ     = $_POST['NUM_HAZ']     ?? '0.00';
$COMBINED_HAZARDS    = $_POST['COMBINED_HAZARDS']    ?? '';
$TEXT_FILES_CONTENT = $_POST['TEXT_FILES_CONTENT']    ?? '';
$FINAL_RISK_LEVEL = $_POST['FINAL_RISK_LEVEL'] ?? '';


// Prepare SQL insert with prepared statement
$sql = "INSERT INTO form_submissions9 (
    C2_PER_OP, C2_PER_N, C2_PER_EX, SITUATIONS, CONDITIONS, FACTS, RANK, DATE_OPER, LOC_OPER,
    C2_PER_IN, C2_PER_COM, C2_OPER_EX, C2_LESSONS, TERR_WEA, NUM_ENEM, TROOPS_OPER_EX, CIVIL_CON, TIMEDAYS, TIMEHOURS, TIMEMIN, 
    MISS_IMAGE, MISS_AUDIO,
    RISK_SCORE, SINGLE_RISK_SCORE, SEVERITY_SCORE, NUM_HAZ, COMBINED_HAZARDS, TEXT_FILES_CONTENT, FINAL_RISK_LEVEL
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL prepare failed: " . $conn->error);
}
$_SESSION['RISK_SCORE']        = $RISK_SCORE;
$_SESSION['SEVERITY_SCORE']    = $SEVERITY_SCORE;
$_SESSION['SINGLE_RISK_SCORE'] = $SINGLE_RISK_SCORE;
$_SESSION['FINAL_RISK_LEVEL']  = $FINAL_RISK_LEVEL;
$_SESSION['COMBINED_HAZARDS']        = $COMBINED_HAZARDS;
$_SESSION['C2_PER_N']        = $C2_PER_N;
$_SESSION['C2_PER_IN']        = $C2_PER_IN;
$_SESSION['MISS_IMAGE']        = $MISS_IMAGE;

$stmt->bind_param(
    "sssssssssssdddsssssssssssdsss",
    $C2_PER_OP, $C2_PER_N, $C2_PER_EX, $SITUATIONS, $CONDITIONS, $FACTS, $RANK, $DATE_OPER, $LOC_OPER,
    $C2_PER_IN, $C2_PER_COM, $C2_OPER_EX, $C2_LESSONS, $TERR_WEA, $NUM_ENEM, $TROOPS_OPER_EX, $CIVIL_CON, $TIMEDAYS, $TIMEHOURS, $TIMEMIN,
    $MISS_IMAGE, $MISS_AUDIO,
    $RISK_SCORE, $SINGLE_RISK_SCORE, $SEVERITY_SCORE, $NUM_HAZ, $COMBINED_HAZARDS, $TEXT_FILES_CONTENT, $FINAL_RISK_LEVEL
);

// Execute insert
if ($stmt->execute()) {
    header("Location: ARMOR_SIMU_OUTPUT.php");
    exit();
} else {
    echo "Submission failed: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
