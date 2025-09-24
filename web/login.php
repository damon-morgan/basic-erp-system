<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login to IMS</title>
    <style>
        body {
            font-family: 'Verdana', sans-serif;
            background-color: #f4f7fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }


        .login-container {
            background-color: #ffffff;
            padding: 40px 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            width: 350px;
        }


        .login-container h2 {
            text-align: center;
            color: #0D3B66;
            margin-bottom: 30px;
        }

        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 10px 12px;
            margin: 10px 0 20px 0;
            border: 1px solid #A9C5EB;
            border-radius: 5px;
            font-size: 14px;
            outline: none;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: border 0.3s, box-shadow 0.3s;
        }

        .login-container input[type="text"]:focus,
        .login-container input[type="password"]:focus {
            border-color: #0D3B66;
            box-shadow: 0 0 5px rgba(13, 59, 102, 0.3);
        }

        .login-container input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #0D3B66;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        .login-container input[type="submit"]:hover {
            background-color: #145DA0;
            transform: translateY(-2px);
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div id="Header">
            <h2>Please Authenticate Below</h2>
        </div>

        <div id="LoginForm">
            <form action="login.php" method="post">
                <label>Username:</label><br>
                <input type="text" name="username"><br>
                <label>Password:</label><br>
                <input type="text" name="password"><br>
                <input type="submit" value="Login">
                <br></br>
                <a href="register.php">Create an Account</a>
            </form>
        </div>
    </div>
</body>

</html>

<?php

session_start();
require 'db.php'; // PDO Connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    // Validate input
    if (empty($username) || empty($password)) {
        echo "Both fields are required.";
        exit();
    }

    // Look up user
    $stmt = $pdo->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Prevent session fixation
        session_regenerate_id(true);

        $_SESSION["auth"] = true;
        $_SESSION["user"] = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
        header("Location: index.php");
        exit();
    } else {
        $_SESSION["auth"] = false;
        echo "Incorrect username or password.";
    }
}

?>