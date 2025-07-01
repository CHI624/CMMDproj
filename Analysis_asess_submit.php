<?php
session_start();
$host = "localhost";
$db   = "form_submissions_quer";
$user = "root";
$pass = "Dabonem123!";
$conn = new mysqli($host, $user, $pass, $db);

// Check DB connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Corrected key name
$DESCR_HAZ         = $_POST['DESCR_HAZ']          ?? '';
$RISK_SCORE        = $_POST['RISK_SCORE']         ?? '';
$SEVERITY_SCORE    = $_POST['SEVERITY_SCORE']     ?? '';
$NUM_HAZ           = $_POST['NUM_HAZ']            ?? '0.00';
$SINGLE_RISK_SCORE = $_POST['SINGLE_RISK_SCORE']  ?? '';
$COMBINED_HAZARDS  = $_POST['COMBINED_HAZARDS']   ?? '';
$FINAL_RISK_LEVEL  = $_POST['FINAL_RISK_LEVEL']   ?? '';

$sql = "INSERT INTO form_submissions_asess (
    DESCR_HAZ, RISK_SCORE, SINGLE_RISK_SCORE, NUM_HAZ, SEVERITY_SCORE, COMBINED_HAZARDS, FINAL_RISK_LEVEL
) VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL prepare failed: " . $conn->error);
}

$stmt->bind_param(
    "sssdsss",
    $DESCR_HAZ, $RISK_SCORE, $SINGLE_RISK_SCORE, $NUM_HAZ, $SEVERITY_SCORE, $COMBINED_HAZARDS, $FINAL_RISK_LEVEL
);
$_SESSION['form2'] = [
    'descr_haz'        => $DESCR_HAZ,
    'risk_score'       => $RISK_SCORE,
    'single_risk'      => $SINGLE_RISK_SCORE,
    'num_haz'          => $NUM_HAZ,
    'severity_score'   => $SEVERITY_SCORE,
    'combined_hazards' => $COMBINED_HAZARDS,
    'final_risk'       => $FINAL_RISK_LEVEL,
];

try {
    $stmt->execute();
    $stmt->close();
    $conn->close();
    header("Location: ARMOR_REPORT.php");
    exit();
} catch (mysqli_sql_exception $e) {
    die("Database error: " . htmlspecialchars($e->getMessage()));
}
?>
