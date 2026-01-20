<?php
session_start();

if (!isset($_SESSION['bloodbank_id']) || $_SESSION['role'] !== 'bloodbank') {
    header('Location: inventory.php');
    exit();
}

try {
    $conn = new PDO('mysql:host=localhost;dbname=blooddonationmanagementsystem', 'root', '');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $bloodBankID = $_SESSION['bloodbank_id'];

    // Define all standard blood types
    $allBloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
    $inventoryMap = [];

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        foreach ($_POST['units'] as $bloodType => $unit) {
            $unit = (int)$unit;

            // Check if record exists
            $checkStmt = $conn->prepare("SELECT * FROM inventory WHERE BloodBankID = :id AND BloodType = :type");
            $checkStmt->execute([
                ':id' => $bloodBankID,
                ':type' => $bloodType
            ]);

            if ($checkStmt->rowCount() > 0) {
                // Update
                $stmt = $conn->prepare("UPDATE inventory SET Units = :unit WHERE BloodBankID = :id AND BloodType = :type");
            } else {
                // Insert
                $stmt = $conn->prepare("INSERT INTO inventory (BloodBankID, BloodType, Units) VALUES (:id, :type, :unit)");
            }

            $stmt->execute([
                ':unit' => $unit,
                ':id' => $bloodBankID,
                ':type' => $bloodType
            ]);
        }

        header("Location: inventory.php?success=1");
        exit();
    }

    // Fetch existing inventory
    $stmt = $conn->prepare("SELECT BloodType, Units FROM inventory WHERE BloodBankID = :id");
    $stmt->execute([':id' => $bloodBankID]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rows as $row) {
        $inventoryMap[$row['BloodType']] = $row['Units'];
    }

} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Blood Inventory - RedPulse</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background: radial-gradient(circle,rgb(255, 99, 90) 0%,rgb(241, 33, 33) 100%);
            font-family: 'Arial', sans-serif;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }

        .navbar {
            background-color: rgba(0, 0, 0, 0.8) !important;
            padding: 5px 20px;
        }

        .navbar-brand {
            color: #fff !important;
            font-size: 22px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
        }

        .navbar-nav .nav-item .nav-link.active {
            color:rgb(253, 253, 253) !important;
            background-color: rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            font-weight: bold;
        }

        .navbar-nav .nav-item {
            padding: 0px 10px;
        }

        .navbar a {
            font-size: 20px;
        }

        .navbar-nav .nav-item .nav-link:hover {
            color:rgb(255, 255, 255) !important;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
        }

        .inventory-container {
            max-width: 960px;
            margin-top: 100px;
            background: #ffffff;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            color: #333;
        }

        .inventory-container h2 {
            color: #b30000;
            margin-bottom: 30px;
            font-weight: bold;
        }

        .form-control {
            border-radius: 10px;
        }

        .btn-update {
            background-color:rgb(254, 220, 220);
            color: red;
            border-radius: 30px;
            border: orangered 2px solid;
            padding: 10px 30px;
            font-weight: 600;
        }

        .btn-update:hover {
            background-color:rgb(213, 33, 33);
            color: #fff;
        }

        .form-group label {
            font-weight: 500;
        }

        .fade-alert {
            animation: fadeOut 3s ease-in-out forwards;
        }

        @keyframes fadeOut {
            0% {opacity: 1;}
            80% {opacity: 1;}
            100% {opacity: 0; display: none;}
        }

        .floating-drops {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }

        .drop {
            position: absolute;
            width: 15px;
            height: 15px;
            background-color: #ffcccb;
            border-radius: 50%;
            animation: float 10s infinite ease-in-out;
            opacity: 0.8;
        }

        @keyframes float {
            0% {transform: translateY(100vh); opacity: 0;}
            50% {opacity: 1;}
            100% {transform: translateY(-10vh); opacity: 0;}
        }

        .drop:nth-child(odd) {
            animation-duration: 10s;
        }

        .drop:nth-child(even) {
            animation-duration: 12s;
        }

        .logo-container {
            width: 180px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pulse-line {
            stroke: red;
            stroke-width: 3;
            fill: none;
            stroke-dasharray: 100;
            stroke-dashoffset: 200;
            animation: draw 2s infinite linear, glow 1.5s infinite alternate;
        }

        .pulse-text {
            font-family: Arial, sans-serif;
            font-size: 24px;
            fill: red;
            font-weight: bold;
            text-anchor: middle;
            animation: textGlow 1.5s infinite alternate;
        }

        @keyframes draw {
            0% {stroke-dashoffset: 200;}
            100% {stroke-dashoffset: 0;}
        }

        @keyframes glow {
            0% {stroke-opacity: 0.8;}
            100% {stroke-opacity: 1;}
        }

        @keyframes textGlow {
            0% {
                fill: rgb(255, 80, 80);
                text-shadow: 0 0 5px rgba(255, 80, 80, 0.5);
            }
            100% {
                fill: rgb(255, 0, 0);
                text-shadow: 0 0 10px rgba(255, 0, 0, 1);
            }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <a class="navbar-brand">
        <div class="logo-container">
            <svg width="180" height="60" viewBox="0 0 180 60" xmlns="http://www.w3.org/2000/svg">
                <text x="90" y="40" class="pulse-text">RedPulse</text>
                <polyline points="10,40 30,10 50,50 70,15 90,45 110,20 130,40 150,10 170,30"
                          class="pulse-line"
                          stroke-linecap="round"
                          stroke-linejoin="round">
                </polyline>
            </svg>
        </div>
    </a>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" href="bb_profile.php">Profile</a></li>
            <li class="nav-item"><a class="nav-link active" href="inventory.php">Inventory</a></li>
            <li class="nav-item"><a class="nav-link" href="events.php">Events</a></li>
            <li class="nav-item"><a class="nav-link" href="signout.php">Sign Out</a></li>
        </ul>
    </div>
</nav>

<div class="container inventory-container mt-5">
    <h2 class="text-center">Blood Bank Inventory Management</h2>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success text-center fade-alert">Inventory updated successfully!</div>
    <?php endif; ?>

    <form method="POST">
        <div class="row">
            <?php foreach ($allBloodTypes as $type): ?>
                <div class="form-group col-md-6">
                    <label><?= $type ?> (units):</label>
                    <input type="number" class="form-control" name="units[<?= $type ?>]" value="<?= isset($inventoryMap[$type]) ? $inventoryMap[$type] : 0 ?>" min="0" required>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <button type="submit" class="btn btn-update">Update Inventory</button>
        </div>
    </form>
</div>

<!-- Floating drops animation -->
<div class="floating-drops">
    <div class="drop" style="left: 10%; animation-delay: 0s;"></div>
    <div class="drop" style="left: 20%; animation-delay: 2s;"></div>
    <div class="drop" style="left: 30%; animation-delay: 4s;"></div>
    <div class="drop" style="left: 40%; animation-delay: 1s;"></div>
    <div class="drop" style="left: 50%; animation-delay: 3s;"></div>
    <div class="drop" style="left: 60%; animation-delay: 5s;"></div>
    <div class="drop" style="left: 70%; animation-delay: 0s;"></div>
    <div class="drop" style="left: 80%; animation-delay: 2s;"></div>
    <div class="drop" style="left: 90%; animation-delay: 4s;"></div>
</div>

</body>
</html>
