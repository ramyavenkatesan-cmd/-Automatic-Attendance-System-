<?php
session_start();
include "connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != "parent") {
    header("Location: login.php");
    exit();
}

$parent_id = $_SESSION['user_id'];

/* Get Parent Details */
$parent_query = $conn->query("SELECT * FROM users WHERE id='$parent_id'");
$parent = $parent_query->fetch_assoc();

/* Get Child Details */
$child_id = $parent['child_id'];
$child_query = $conn->query("SELECT * FROM users WHERE id='$child_id'");
$child = $child_query->fetch_assoc();

/* Attendance Calculation */
$attendance_query = $conn->query("
    SELECT COUNT(DISTINCT date) as total,
    SUM(CASE WHEN status='Present' OR status='OD' THEN 1 ELSE 0 END) as attended
    FROM attendance WHERE student_id='$child_id'
");

$attendance = $attendance_query->fetch_assoc();

$total = $attendance['total'] ?? 0;
$attended = $attendance['attended'] ?? 0;
$percentage = $total > 0 ? round(($attended/$total)*100) : 0;


/* Timetable + Attendance Fetch */
$timetable_query = $conn->query("
    SELECT 
        t.day,
        t.time,
        t.subject,
        IFNULL(a.status, 'Not Marked') AS status
    FROM timetable t
    LEFT JOIN attendance a 
    ON t.student_id = a.student_id 
    AND t.subject = a.subject
    WHERE t.student_id = '$child_id'
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Parent Portal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="header">
    <h2>Attendance Portal</h2>
    <p><?php echo $parent['name']; ?> (Parent)</p>
</div>

<div class="container">

    <div class="sidebar">
        <a href="#" onclick="showSection('dashboard')">Dashboard</a>
        <a href="#" onclick="showSection('attendance')">Attendance</a>
        <a href="#" onclick="showSection('timetable')">Timetable</a>
        <a href="#" onclick="showSection('profile')">Profile</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="content">

        <!-- DASHBOARD -->
        <div id="dashboard" class="section">
            <div class="card">
                <h3>Parent Dashboard</h3>
                <p><b>Student Name:</b> <?php echo $child['name']; ?></p>
                <p><b>Class:</b> CSE – III</p>
                <p><b>Subject:</b> DBMS</p>
            </div>
        </div>

        <!-- ATTENDANCE -->
        <div id="attendance" class="section" style="display:none;">
            <div class="card">
                <h3>Attendance – DBMS</h3>
                <div>
    <p>Percentage: <?php echo $percentage; ?>%</p>

    <?php if($percentage < 75) { ?>
        <p style="color:red;">
            ⚠ Warning: Attendance below 75%!
        </p>
    <?php } ?>
</div>
                <table border="1" cellpadding="10">
                    <tr>
                        <th>Total Classes</th>
                        <th>Attended</th>
                        <th>Percentage</th>
                    </tr>
                    <tr>
                        <td><?php echo $total; ?></td>
                        <td><?php echo $attended; ?></td>
                        <td><?php echo $percentage; ?>%</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- TIMETABLE + ATTENDANCE -->
        <div id="timetable" class="section" style="display:none;">
            <div class="card">
                <h3>Child Timetable & Attendance</h3>

                <table border="1" cellpadding="10">
                    <tr>
                        <th>Day</th>
                        <th>Time</th>
                        <th>Subject</th>
                        <th>Status</th>
                    </tr>

                    <?php while($row = $timetable_query->fetch_assoc()) { 
                        
                        $color = "black";
                        if($row['status'] == "Present") $color = "green";
                        if($row['status'] == "Absent") $color = "red";
                        if($row['status'] == "OD") $color = "blue";
                    ?>

                    <tr>
                        <td><?php echo $row['day']; ?></td>
                        <td><?php echo $row['time']; ?></td>
                        <td><?php echo $row['subject']; ?></td>
                        <td style="color:<?php echo $color; ?>;">
                            <?php echo $row['status']; ?>
                        </td>
                    </tr>

                    <?php } ?>

                </table>
            </div>
        </div>

        <!-- PROFILE -->
        <div id="profile" class="section" style="display:none;">
            <div class="card">
                <h3>Parent Profile</h3>
                <p><b>Name:</b> <?php echo $parent['name']; ?></p>
                <p><b>Child:</b> <?php echo $child['name']; ?></p>
                <p><b>Email:</b> <?php echo $parent['email']; ?></p>
                <p><b>Role:</b> Parent</p>
                <p><b>Status:</b> Active</p>
            </div>
        </div>

    </div>
</div>

<script>
function showSection(id) {
    document.getElementById("dashboard").style.display = "none";
    document.getElementById("attendance").style.display = "none";
    document.getElementById("timetable").style.display = "none";
    document.getElementById("profile").style.display = "none";
    document.getElementById(id).style.display = "block";
}
</script>

</body>
</html>