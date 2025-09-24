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


// View Inventory
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["part_number"])) {
    $partnum = $_POST["part_number"];
    $desc = $_POST["part_description"];
    $quantity = (int) $_POST["add_quantity"];
    // Run a SELECT query to find matching part
    $stmt = $pdo->prepare("SELECT id, partquantity FROM inventory WHERE partnumber = ?");
    $stmt->execute([$partnum]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // Update existing part number quantity only
        $newQty = $row['partquantity'] + $quantity;
        $stmt = $pdo->prepare("UPDATE inventory SET partquantity = ?, partdescription = ? WHERE id = ?");
        $stmt->execute([$newQty, $desc, $row['id']]);
        $_SESSION['message'] = [
            'type' => 'success',
            'text' => "Part number <b>$partnum</b> already exists. Quantity updated to $newQty."
        ];
    } else {
        // Insert new part number into Inventory
        $stmt = $pdo->prepare("INSERT INTO inventory (partnumber, partdescription, partquantity) VALUES (?, ?, ?)");
        $stmt->execute([$partnum, $desc, $quantity]);
        $_SESSION['message'] = [
            'type' => 'success',
            'text' => "Part number <b>$partnum</b> added to Inventory."
        ];
    }

    header("Location: inventory.php");
    exit();
}

// Pick from Inventory
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["pick_partnumber"])) {
    $id = (int) $_POST["pick_partnumber"];
    $pickQty = (int) $_POST["pick_quantity"];

    $stmt = $pdo->prepare("SELECT partquantity FROM inventory WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Part not found.'];
    } else {
        $currentQty = (int)$row['partquantity'];

        if ($pickQty > $currentQty) {
            $_SESSION['message'] = ['type' => 'error', 'text' => "Cannot pick more than available quantity ($currentQty)."];
        } elseif ($pickQty === $currentQty) {
            $stmt = $pdo->prepare("DELETE FROM inventory WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Part picked completely and removed from inventory.'];
        } else {
            $newQty = $currentQty - $pickQty;
            $stmt = $pdo->prepare("UPDATE inventory SET partquantity = ? WHERE id = ?");
            $stmt->execute([$newQty, $id]);
            $_SESSION['message'] = ['type' => 'success', 'text' => "Quantity updated successfully. Remaining: $newQty"];
        }
    }

    header("Location: inventory.php");
    exit();
}

// Display Inventory
$stmt = $pdo->query("SELECT id, partnumber, partdescription, partquantity FROM inventory ORDER BY id ASC");
$inventory = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management</title>
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
        #invent {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
    </style>
</head>
<body>
    <h1>Welcome to Inventory Management System</h1>
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

    <div id='invent'>
        <!-- Receive -->
        <fieldset>
            <legend>Receive Inventory</legend>
            <form action="inventory.php" method="post">
                <label>Part Number:</label><br>
                <input type="number" name="part_number" required><br>
                <label>Description:</label><br>
                <input type="text" name="part_description" required><br>
                <label>Quantity:</label><br>
                <input type="number" name="add_quantity" required min="1"><br>
                <input type="submit" value="Add">
            </form>
        </fieldset>

        <!-- Pick -->
        <fieldset>
            <legend>Pick Inventory</legend>
            <form action="inventory.php" method="post">
                <label>ID Number:</label><br>
                <input type="number" name="pick_partnumber" required><br>
                <label>Quantity to Pick:</label><br>
                <input type="number" name="pick_quantity" required min="1"><br>
                <input type="submit" value="Pick">
            </form>
        </fieldset>
    </div>

    <!-- Inventory Table -->
    <fieldset>
        <legend>Inventory</legend>
        <table class="inventory-table">
            <tr>
                <th>ID</th>
                <th>Part Number</th>
                <th>Description</th>
                <th>Quantity</th>
            </tr>
            <?php 
            // Display each line row in a table foreach
            foreach ($inventory as $item): 
            ?>
                <tr>
                    <td><?= htmlspecialchars($item['id']) ?></td>
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