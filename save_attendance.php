<?php
session_start();
include "connect.php";

if (!isset($_POST['student_id'], $_POST['subject'], $_POST['status'])) {
    die("Invalid data");
}

$student_id = $_POST['student_id'];
$subject = $_POST['subject'];
$status = $_POST['status'];
$date = date("Y-m-d");

/* 🔥 STRONG DUPLICATE CHECK */
$check = $conn->query("
    SELECT id FROM attendance 
    WHERE student_id='$student_id'
    AND subject='$subject'
    AND date='$date'
    LIMIT 1
");

if ($check->num_rows > 0) {
    header("Location: faculty.php?msg=already");
    exit();
}

/* 🔥 INSERT ONLY IF NOT EXISTS */
$sql = "INSERT INTO attendance (student_id, subject, date, status)
        VALUES ('$student_id', '$subject', '$date', '$status')";

if ($conn->query($sql) === TRUE) {
    header("Location: faculty.php?msg=success");
    exit();
} else {
    header("Location: faculty.php?msg=error");
    exit();
}
?>