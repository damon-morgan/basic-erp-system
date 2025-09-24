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


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["part_number"])) {
    $partnumber = $_POST["part_number"];
    $partdescription = $_POST["part_description"];
    $partquantity = $_POST["part_quantity"];
    $vendor = $_POST["vendor_id"];
    $desireddate = $_POST["desired_date"];
    // Create new purchase order
    $stmt = $pdo->prepare("INSERT INTO purchase (partnumber, partdescription, partquantity, vendorid, desireddate) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$partnumber, $partdescription, $partquantity, $vendor, $desireddate]);
    $_SESSION['message'] = [
        'type' => 'success',
        'text' => "Purchase Order added successfully."
    ];
    header("Location: purchase.php");
    exit();
}

// Display Purchase Order
$stmt = $pdo->query("SELECT 
    p.id AS purchase_id,
    v.vendorname,
    p.desireddate,
    p.partnumber,
    p.partdescription,
    p.partquantity
FROM purchase p
INNER JOIN vendor v ON p.vendorid = v.id
ORDER BY p.id ASC");
$purchase = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order Management</title>
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
        .purchase-table {
            border-collapse: collapse;
            width: 95%;
            margin: 20px auto;
        }
        .purchase-table th, .inventory-table td {
            border: 1px solid #ddd;
            padding: 12px 15px;
            text-align: left;
        }
        .purchase-table th {
            background-color: #0D3B66;
            color: white;
            text-transform: uppercase;
        }
        .purchase-table tr:nth-child(even) { background-color: #f9f9f9; }
        .purchase-table tr:hover { background-color: #e1f0ff; }
        .message {
            text-align: center;
            font-weight: bold;
            margin: 15px 0;
        }
        .message.error { color: red; }
        .message.success { color: green; }
        #purchase {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
    </style>
</head>
<body>
    <h1>Purchase Order Management</h1>
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

    <div id='purchase'>
        <!-- Purchase -->
        <fieldset>
            <legend>Create a Purchase Order</legend>
            <form action="purchase.php" method="post">
                <label>Part Number:</label><br>
                <input type="number" name="part_number" required><br>
                <label>Description:</label><br>
                <input type="text" name="part_description" required><br>
                <label>Quantity:</label><br>
                <input type="number" name="part_quantity" required min="1"><br>
                <label>Vendor:</label><br>
                <input type="number" name="vendor_id" required><br>
                <label>Desired Date:</label><br>
                <input type="date" name="desired_date" required><br>
                <input type="submit" value="Add">
            </form>
        </fieldset>
    </div>

    <!-- Purchase Table -->
    <fieldset>
        <legend>Incoming PO</legend>
        <table class="purchase-table">
            <tr>
                <th>PO Number</th>
                <th>Vendor</th>
                <th>Desired Date</th>
                <th>Part Number</th>
                <th>Description</th>
                <th>Quantity</th>
            </tr>

            <?php 
            // Display each line row in a table foreach
            foreach ($purchase as $item): 
            ?>
                <tr>
                    <td><?= htmlspecialchars($item['purchase_id']) ?></td>
                    <td><?= htmlspecialchars($item['vendorname']) ?></td>
                    <td><?= htmlspecialchars($item['desireddate']) ?></td>
                    <td><?= htmlspecialchars($item['partnumber']) ?></td>
                    <td><?= htmlspecialchars($item['partdescription']) ?></td>
                    <td><?= htmlspecialchars($item['partquantity']) ?></td>
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