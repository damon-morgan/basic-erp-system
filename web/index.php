<?php
session_start();
require 'db.php'; // PDO connection

// Check to verify authenication
if (!isset($_SESSION["auth"]) || $_SESSION["auth"] == false) {
    header("Location: login.php");
    exit();
}
// Log user in and display
$user = htmlspecialchars($_SESSION["user"] ?? "Unknown");


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERP Basic Dashboard</title>
    <style>
        body {
            font-family: 'Verdana', sans-serif;
            font-size: 16px;
            background-color: #f4f7fa;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: left;
            color: #0D3B66;
        }
        fieldset {
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #A9C5EB;
            margin-bottom: 20px;
            background-color: #ffffff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        legend {
            font-size: 14px;
            font-weight: bold;
            color: #0D3B66;
        }
        input[type="text"], input[type="number"], input[type="submit"], button {
            font-size: 14px;
            padding: 8px 10px;
            margin: 5px 0;
            border: 1px solid #A9C5EB;
            border-radius: 5px;
            outline: none;
            background: #fff;
        }
        input[type="submit"], button {
            cursor: pointer;
            background-color: #0D3B66;
            color: white;
            border: none;
            transition: 0.3s;
        }
        input[type="submit"]:hover, button:hover {
            background-color: #145DA0;
        }
        .inventory-table {
            border-collapse: collapse;
            width: 95%;
            margin: 20px auto;
        }
        .inventory-table th, .inventory-table td {
            border: 1px solid #ddd;
            padding: 12px 15px;
            text-align: left;
        }
        .inventory-table th {
            background-color: #0D3B66;
            color: white;
            text-transform: uppercase;
        }
        .inventory-table tr:nth-child(even) { background-color: #f9f9f9; }
        .inventory-table tr:hover { background-color: #e1f0ff; }
        .message {
            text-align: center;
            font-weight: bold;
            margin: 15px 0;
        }
        .message.error { color: red; }
        .message.success { color: green; }
        #erp {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
    </style>
</head>
<body>
    <h1>Welcome to ERP Basic System</h1>
    <p>Logged in as: <?= $user ?></p> <form action="logout.php" method="post" style="display:inline;">
    <input type="submit" value="Logout">
    </form>


    <div id='erp'>
        <fieldset>
            <legend>Control Panel</legend>
            <label>Purchase Order Management</label>
                <a href="purchase.php"><button type="button">POM</button></a>
                <br>
            <label>Inventory Management</label>
                <a href="inventory.php"><button type="button">IM</button></a>
                <br>
            <label>Vendor Management</label>
                <a href="vendor.php"><button type="button">VM</button></a>
                <br>
        </fieldset>

    </div>
    <br><br>

    <div id='foot'>
        <p>Damon Morgan © 2025</p>
        <p><a href="https://www.linkedin.com/in/damon-morgan/">LinkedIn</a></p>
    </div>
</body>

</html>