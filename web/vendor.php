<?php
session_start();
require 'db.php'; // PDO connection

// Verify Authenication
if (!isset($_SESSION["auth"]) || $_SESSION["auth"] == false) {
    header("Location: login.php");
    exit();
}
// Log User In 
$user = htmlspecialchars($_SESSION["user"] ?? "Unknown");

// View Vendors
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["vendor_name"])) {
    $name = $_POST["vendor_name"];
    $city = $_POST["vendor_city"];
    // Run a SELECT query to find matching part
    $stmt = $pdo->prepare("SELECT id, vendorname FROM vendor WHERE vendorname = ?");
    $stmt->execute([$name]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // Message duplicate error
        $_SESSION['message'] = [
            'type' => 'error',
            'text' => "<b>$name</b> already exists in the system."
        ];
    } else {
        // Insert new vendor
        $stmt = $pdo->prepare("INSERT INTO vendor (vendorname, vendorcity) VALUES (?, ?)");
        $stmt->execute([$name, $city]);
        $_SESSION['message'] = [
            'type' => 'success',
            'text' => "<b>$name</b> added successfully."
        ];
    }

    header("Location: vendor.php");
    exit();
}

// Display Vendors
$stmt = $pdo->query("SELECT id, vendorname, vendorcity FROM vendor ORDER BY id ASC");
$vendor = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Management</title>
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
        .vendor-table {
            border-collapse: collapse;
            width: 95%;
            margin: 20px auto;
        }
        .vendor-table th, .vendor-table td {
            border: 1px solid #ddd;
            padding: 12px 15px;
            text-align: left;
        }
        .vendor-table th {
            background-color: #0D3B66;
            color: white;
            text-transform: uppercase;
        }
        .vendor-table tr:nth-child(even) { background-color: #f9f9f9; }
        .vendor-table tr:hover { background-color: #e1f0ff; }
        .message {
            text-align: center;
            font-weight: bold;
            margin: 15px 0;
        }
        .message.error { color: red; }
        .message.success { color: green; }
        #vendor {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
    </style>
</head>
<body>
    <h1>Vendor Management</h1>
    <p>Logged in as: <?= $user ?></p> <form action="logout.php" method="post" style="display:inline;">
    <input type="submit" value="Logout">
    </form>
    <a href="index.php"><button type="button">Dashboard</button></a>
    <?php 
    if (isset($_SESSION['message'])): 
    ?>
        <p class="message <?= $_SESSION['message']['type'] ?>">
            <?= $_SESSION['message']['text'] ?>
        </p>
        <?php 
        unset($_SESSION['message']); 
        ?>
    <?php 
    endif; 
    ?>

    <div id='vendor'>
        <!-- Vendor -->
        <fieldset>
            <legend>Create a Vendor</legend>
            <form action="vendor.php" method="post">
                <label>Vendor Name:</label><br>
                <input type="text" name="vendor_name" required><br>
                <label>Vendor City:</label><br>
                <input type="text" name="vendor_city" required><br>
                <input type="submit" value="Add">
            </form>
        </fieldset>

    </div>

    <!-- Vendor Table -->
    <fieldset>
        <legend>Vendor List</legend>
        <table class="vendor-table">
            <tr>
                <th>Vendor ID</th>
                <th>Vendor Name</th>
                <th>Vendor City</th>
            </tr>
            <?php 
            // Display each line row in a table foreach
            foreach ($vendor as $item): 
            ?>
                <tr>
                    <td><?= htmlspecialchars($item['id']) ?></td>
                    <td><?= htmlspecialchars($item['vendorname']) ?></td>
                    <td><?= htmlspecialchars($item['vendorcity']) ?></td>
                </tr>
            <?php 
            endforeach; 
            ?>
        </table>
    </fieldset>
    <br><br>

    <div id='foot'>
        <p>Damon Morgan © 2025</p>
        <p><a href="https://www.linkedin.com/in/damon-morgan/">LinkedIn</a></p>
    </div>
</body>

</html>