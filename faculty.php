<?php
session_start();
include "connect.php";

/* FACE RECOGNITION TRIGGER */
if(isset($_POST['start_face'])){
    $output = shell_exec("C:\\Users\\Hp\\AppData\\Local\\Programs\\Python\\Python310\\python.exe C:\\xampp\\htdocs\\FaceAttendance\\main.py 2>&1");
    echo "<pre>$output</pre>";
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != "faculty") {
    header("Location: login.php");
    exit();
}

$today = date("Y-m-d");
$students = $conn->query("SELECT id, name FROM users WHERE role='student'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Faculty Portal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="header">
    <h2>Attendance Portal</h2>
    <p><?php echo $_SESSION['user_name']; ?> (Faculty)</p>
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

<div id="attendance" class="section">
    <div class="card">
        <h3>Mark Attendance – DBMS</h3>

        <!-- ✅ FACE RECOGNITION BUTTON -->
        <form method="post" style="margin-bottom:10px;">
            <button type="submit" name="start_face"
                style="background:#9b59b6; color:white; padding:8px 12px; border:none; border-radius:5px;">
                Start Face Attendance
            </button>
        </form>

        <!-- EXPORT -->
        <a href="export_excel.php" 
           style="background:#2ecc71;
                  padding:8px 12px;
                  color:white;
                  text-decoration:none;
                  border-radius:5px;
                  display:inline-block;
                  margin-bottom:10px;">
           Export to Excel
        </a>

        <!-- MESSAGE -->
        <?php
        if (isset($_GET['msg'])) {
            if ($_GET['msg'] == "success") {
                echo "<p style='color:green;'>✔ Attendance marked successfully</p>";
            } elseif ($_GET['msg'] == "already") {
                echo "<p style='color:orange;'>⚠ Attendance already marked today</p>";
            } else {
                echo "<p style='color:red;'>❌ Error saving attendance</p>";
            }
        }
        ?>

        <table border="1" cellpadding="10">
            <tr>
                <th>ID</th>
                <th>Student Name</th>
                <th>Status</th>
            </tr>

            <?php while($row = $students->fetch_assoc()) {

                $check = $conn->query("
                    SELECT status FROM attendance
                    WHERE student_id='{$row['id']}'
                    AND date='$today'
                    AND subject='DBMS'
                ");

                $alreadyMarked = $check->num_rows > 0;
                $currentStatus = $alreadyMarked ? $check->fetch_assoc()['status'] : "";
            ?>

            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td>

                <?php if ($alreadyMarked) {

                    $color = "green";
                    if ($currentStatus == "Absent") $color = "red";
                    if ($currentStatus == "OD") $color = "blue";
                ?>

                    <b style="color:<?php echo $color; ?>;">
                        <?php echo $currentStatus; ?>
                    </b>

                <?php } else { ?>

                    <form method="post" action="save_attendance.php" style="display:inline;">
                        <input type="hidden" name="student_id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="subject" value="DBMS">
                        <input type="hidden" name="status" value="Present">
                        <button type="submit">Present</button>
                    </form>

                    <form method="post" action="save_attendance.php" style="display:inline;">
                        <input type="hidden" name="student_id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="subject" value="DBMS">
                        <input type="hidden" name="status" value="Absent">
                        <button type="submit" style="background:#e74c3c;">Absent</button>
                    </form>

                    <form method="post" action="save_attendance.php" style="display:inline;">
                        <input type="hidden" name="student_id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="subject" value="DBMS">
                        <input type="hidden" name="status" value="OD">
                        <button type="submit" style="background:#3498db;">OD</button>
                    </form>

                <?php } ?>

                </td>
            </tr>

            <?php } ?>

        </table>
    </div>
</div>

</div>
</div>

</body>
</html>