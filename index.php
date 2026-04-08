<!DOCTYPE html>
<html>
<head>
    <title>Attendance Portal Login</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .login-container {
            width: 900px;
            margin: 60px auto;
            display: flex;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .login-image {
            width: 50%;
            background: #1f4fd8;
            color: white;
            padding: 40px;
        }
        .login-image img {
            width: 100%;
            margin-top: 30px;
            border-radius: 8px;
        }
        .login-form {
            width: 50%;
            padding: 40px;
        }
        .login-form h2 {
            margin-bottom: 20px;
        }
        .login-form input, .login-form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
        }
        .login-form button {
            width: 100%;
            padding: 10px;
            background: #1f4fd8;
            color: white;
            border: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="login-container">

    <div class="login-image">
        <h2>Automated Attendance System</h2>
        <p>Secure • Role Based • Future Face Recognition</p>
        <img src="https://cdn-icons-png.flaticon.com/512/3135/3135755.png" alt="Education">
    </div>

    <div class="login-form">
        <h2>Login</h2>
        <form action="login.php" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>

            <select name="role" required>
                <option value="">Select Role</option>
                <option value="student">Student</option>
                <option value="faculty">Faculty</option>
                <option value="parent">Parent</option>
            </select>

            <button type="submit">Login</button>
        </form>
    </div>

</div>

</body>
</html>
