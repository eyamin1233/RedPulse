<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_bloodbanks.php");
    exit;
}

try {
    $conn = new PDO('mysql:host=localhost;dbname=blooddonationmanagementsystem', 'root', '');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch all blood banks
    $stmt = $conn->query("SELECT * FROM bloodbank ORDER BY BloodBankID ASC");
    $bloodbanks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all inventory grouped by BloodBankID
    $inventoryStmt = $conn->query("SELECT BloodBankID, BloodType, Units FROM inventory");
    $inventoryData = [];

    while ($row = $inventoryStmt->fetch(PDO::FETCH_ASSOC)) {
        $inventoryData[$row['BloodBankID']][] = [
            'type' => $row['BloodType'],
            'units' => $row['Units']
        ];
    }

} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Blood Banks - RedPulse</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: radial-gradient(circle, rgb(255, 99, 90) 0%, rgb(241, 33, 33) 100%);
            font-family: 'Segoe UI', sans-serif;
            padding: 40px 20px;
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

        .navbar-nav .nav-item{
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

        .navbar-toggler {
            border-color: rgba(0, 0, 0, 0.5);
        }

        .container {
            max-width: 1000px;
            margin: auto;
        }

        .card {
            position: relative;
            top: 100px;
            margin-bottom: 25px;
            border-radius: 5px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.07);
        }

        .card-header {
            background-color: #dc3545;
            color: white;
            font-weight: 600;
            font-size: 1.3rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .delete-btn {
            background-color: #ffffff;
            border: none;
            color: #dc3545;
            font-weight: bold;
            padding: 6px 12px;
            border-radius: 5px;
            transition: background 0.2s;
        }

        .delete-btn:hover {
            background-color: #ffe5e5;
            color: #a71d2a;
        }

        .inventory-table {
            width: 100%;
            margin-top: 15px;
        }

        .inventory-table th,
        .inventory-table td {
            text-align: center;
            padding: 8px;
            border: 1px solid #dee2e6;
        }

        .inventory-table th {
            background-color: #f8d7da;
            color: #721c24;
        }

        .inventory-table td {
            background-color: #fff;
        }
        .d-flex.gap-2 {
            gap: 10px;
        }
        .gap-2 > * {
            margin-right: 10px;
            margin-bottom: 10px;
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

        @keyframes slideDown {
            0% {
                opacity: 0;
                transform: translateY(-50px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes float {
            0% {
                transform: translateY(100vh);
                opacity: 0;
            }
            50% {
                opacity: 1;
            }
            100% {
                transform: translateY(-10vh);
                opacity: 0;
            }
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
            0% {
                stroke-dashoffset: 200;
            }
            100% {
                stroke-dashoffset: 0;
            }
        }

        @keyframes glow {
            0% {
                stroke-opacity: 0.8;
            }
            100% {
                stroke-opacity: 1;
            }
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

        .alert-success {
            position: relative;
            top: 50px;
            left: 400px;
            right: 20px;
            z-index: 1050;
            width: 300px;
        }

        #delete-alert {
    transition: opacity 0.5s ease;
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
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
    <li class="nav-item">
        <a class="nav-link" href="admin_profile.php">Dashboard</a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="amin_bloodbanks.php">Blood Banks</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="users.php">Users</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="signout.php">Sign Out</a>
    </li>
</ul>

        </div>
    </nav>

<div class="container">

<?php if (isset($_GET['deleted']) && $_GET['deleted'] == 1): ?>
    <div id="delete-alert" class="alert alert-success text-center" role="alert">
        ✅ Blood bank has been successfully deleted.
    </div>
<?php endif; ?>


    <h2 class="mb-4" style="color:rgb(255, 255, 255); position: relative; left: 350px; top: 70px; animation: slideDown 0.5s ease-in-out;">All Registered Blood Banks</h2>


    <?php foreach ($bloodbanks as $bank): ?>
        <div class="card">
            <div class="card-header">
                <?= htmlspecialchars($bank['Name']) ?>
                <form method="POST" action="delete_bank.php" onsubmit="return confirm('Are you sure you want to delete this blood bank?');">
                    <input type="hidden" name="BloodBankID" value="<?= $bank['BloodBankID'] ?>">
                    <button type="submit" class="delete-btn">Delete</button>
                </form>
            </div>
            <div class="card-body">
                <p><strong>Location:</strong> <?= htmlspecialchars($bank['Location']) ?></p>
                <p><strong> Contact:</strong> <?= htmlspecialchars($bank['ContactNumber']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($bank['email']) ?></p>
                <p><strong> Operating Hours:</strong> <?= htmlspecialchars($bank['OperatingHours']) ?></p>
                <p><strong> License File:</strong> <?= htmlspecialchars($bank['LicenseFile']) ?></p>

                <?php if (!empty($inventoryData[$bank['BloodBankID']])): ?>
    <div class="mt-3">
        <h6 class="text-dark mb-2"> Blood Inventory</h6>
        <div class="d-flex flex-wrap gap-2">
            <?php foreach ($inventoryData[$bank['BloodBankID']] as $item): ?>
                <div class="p-2 px-3 bg-light border rounded shadow-sm text-center small" style="min-width: 70px;">
                    <strong><?= htmlspecialchars($item['type']) ?></strong><br>
                    <?= htmlspecialchars($item['units']) ?> units
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php else: ?>
    <p class="text-muted mt-2">No inventory data available for this blood bank.</p>
<?php endif; ?>

            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
    // Hide the success alert after 3 seconds
    setTimeout(() => {
        const alertBox = document.getElementById('delete-alert');
        if (alertBox) {
            alertBox.style.transition = 'opacity 0.5s ease';
            alertBox.style.opacity = '0';
            setTimeout(() => alertBox.remove(), 500); // Remove from DOM after fade out
        }
    }, 3000);
</script>


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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
