<?php
include "connect.php";

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=attendance_report.xls");

echo "Student Name\tSubject\tDate\tStatus\n";

$query = $conn->query("
    SELECT users.name, attendance.subject, attendance.date, attendance.status
    FROM attendance
    JOIN users ON attendance.student_id = users.id
    ORDER BY attendance.date DESC
");

while ($row = $query->fetch_assoc()) {
    echo $row['name'] . "\t" .
         $row['subject'] . "\t" .
         $row['date'] . "\t" .
         $row['status'] . "\n";
}
?>