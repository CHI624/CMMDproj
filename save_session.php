<?php
session_start();

// Store all fields in session
$_SESSION['form1_data'] = [
    'C2_PER_OP' => $_POST['C2_PER_OP'] ?? '',
    'C2_PER_N' => $_POST['C2_PER_N'] ?? '',
    'C2_PER_EX' => $_POST['C2_PER_EX'] ?? '',
    'RANK' => $_POST['RANK'] ?? '',
    'DATE_OPER' => $_POST['DATE_OPER'] ?? '',
    'LOC_OPER' => $_POST['LOC_OPER'] ?? '',
    'C2_PER_IN' => $_POST['C2_PER_IN'] ?? '',
    'C2_PER_COM' => $_POST['C2_PER_COM'] ?? '',
    'C2_OPER_EX' => $_POST['C2_OPER_EX'] ?? '',
    'C2_LESSONS' => $_POST['C2_LESSONS'] ?? ''
];

// Handle file uploads (optional)
if ($_FILES['MISS_IMAGE']['error'] === UPLOAD_ERR_OK) {
    $tmpName = $_FILES['MISS_IMAGE']['tmp_name'];
    $filename = basename($_FILES['MISS_IMAGE']['name']);
    $targetPath = "uploads/" . $filename;
    move_uploaded_file($tmpName, $targetPath);
    $_SESSION['MISS_IMAGE'] = $targetPath;
}

if ($_FILES['MISS_AUDIO']['error'] === UPLOAD_ERR_OK) {
    $tmpName = $_FILES['MISS_AUDIO']['tmp_name'];
    $filename = basename($_FILES['MISS_AUDIO']['name']);
    $targetPath = "uploads/" . $filename;
    move_uploaded_file($tmpName, $targetPath);
    $_SESSION['MISS_AUDIO'] = $targetPath;
}

// Redirect to second form page
header("Location: Risk_Asess.php");
exit();
?>
