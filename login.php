<?php
session_start();
include "connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $sql = "SELECT * FROM users 
            WHERE email='$email' 
            AND password='$password' 
            AND role='$role'";

    $result = $conn->query($sql);

    if ($result->num_rows == 1) {

        $row = $result->fetch_assoc();

        $_SESSION['user_id'] = $row['id'];
        $_SESSION['user_name'] = $row['name'];
        $_SESSION['user_role'] = $row['role'];
        $_SESSION['user_email'] = $row['email'];

        if ($role == "student") {
            header("Location: student.php");
            exit();
        } elseif ($role == "faculty") {
            header("Location: faculty.php");
            exit();
        } else {
            header("Location: parent.php");
            exit();
        }

    } else {
        echo "Invalid Login Credentials";
    }
}
?>