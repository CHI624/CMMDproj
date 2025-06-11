<?php
$host = "localhost";
$db = "form_submissions";
$user = "root";
$pass = "Dabonem123!";
$conn = new mysqli($host, $user, $pass, $db);

// Check DB connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sanitize and assign POST values
$C2_PER_OP        = $_POST['C2_PER_OP']        ?? '';
$C2_PER_N         = $_POST['C2_PER_N']         ?? '';
$C2_PER_EX        = $_POST['C2_PER_EX']        ?? '';
$RANK             = $_POST['RANK']             ?? '';
$DATE_OPER        = $_POST['DATE_OPER']        ?? '';
$LOC_OPER         = $_POST['LOC_OPER']         ?? '';
$C2_PER_IN        = $_POST['C2_PER_IN']        ?? '';
$C2_PER_COM       = $_POST['C2_PER_COM']       ?? '';
$C2_OPER_EX       = $_POST['C2_OPER_EX']       ?? '';
$C2_LESSONS       = $_POST['C2_LESSONS']       ?? '';
$MISS_IMAGE       = $_POST['MISS_IMAGE']       ?? '';
$MISS_AUDIO       = $_POST['MISS_AUDIO']       ?? '';
$RISK_SCORE       = $_POST['RISK_SCORE']       ?? '0.00';
$SEVERITY_SCORE   = $_POST['SEVERITY_SCORE']   ?? '0.00';
$FINAL_RISK_LEVEL = $_POST['FINAL_RISK_LEVEL'] ?? '';

// Prepare SQL insert with prepared statement
$sql = "INSERT INTO form_submissions4 (
    C2_PER_OP, C2_PER_N, C2_PER_EX, RANK, DATE_OPER, LOC_OPER,
    C2_PER_IN, C2_PER_COM, C2_OPER_EX, C2_LESSONS,
    MISS_IMAGE, MISS_AUDIO,
    RISK_SCORE, SEVERITY_SCORE, FINAL_RISK_LEVEL
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL prepare failed: " . $conn->error);
}

$stmt->bind_param(
    "ssssssssssssdds",
    $C2_PER_OP, $C2_PER_N, $C2_PER_EX, $RANK, $DATE_OPER, $LOC_OPER,
    $C2_PER_IN, $C2_PER_COM, $C2_OPER_EX, $C2_LESSONS,
    $MISS_IMAGE, $MISS_AUDIO,
    $RISK_SCORE, $SEVERITY_SCORE, $FINAL_RISK_LEVEL
);

// Execute insert
if ($stmt->execute()) {
    header("Location: thankyou.html");
    exit();
} else {
    echo "Submission failed: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
