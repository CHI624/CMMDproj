<?php
$host = "localhost";
$db = "form_submissions";
$user = "root";
$pass = "Dabonem123!";
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Collect form data
$C2_PER_OP = $_POST['C2_PER_OP'];
$C2_PER_N = $_POST['C2_PER_N'];
$C2_PER_EX = $_POST['C2_PER_EX'];
$C2_PER_IN = $_POST['C2_PER_IN'];
$C2_PER_COM = $_POST['C2_PER_COM'];
$C2_OPER_EX = $_POST['C2_OPER_EX'];
$C2_LESSONS = $_POST['C2_LESSONS'];

// Insert into your table
$sql = "INSERT INTO form_submissions1 (C2_PER_OP, C2_PER_N, C2_PER_EX, C2_PER_IN, C2_PER_COM, C2_OPER_EX, C2_LESSONS)
        VALUES ('$C2_PER_OP', '$C2_PER_N', '$C2_PER_EX', '$C2_PER_IN', '$C2_PER_COM', '$C2_OPER_EX', '$C2_LESSONS')";

if ($conn->query($sql) === TRUE) {
    header("Location: thankyou.html");
    exit();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>