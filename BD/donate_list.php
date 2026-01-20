<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: posts.php');
    exit;
}

if (!isset($_GET['request_id']) || empty($_GET['request_id'])) {
    die("Error: request_id is missing.");
}

$request_id = $_GET['request_id'];

try {
    $conn = new PDO('mysql:host=localhost;dbname=blooddonationmanagementsystem', 'root', '');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("
    SELECT d.user_id 
    FROM donations dn
    JOIN donors d ON dn.donor_id = d.id
    WHERE dn.request_id = :request_id
");
$stmt->execute([':request_id' => $request_id]);
$selected_donors = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);  // Now user_ids!



    $stmt = $conn->prepare("SELECT da.user_id, da.health_issue, da.medical_conditions, da.medications, da.hospital_visits, da.fatigue, 
                                   u.name, u.contact, u.location 
                            FROM donation_answers da
                            JOIN user u ON da.user_id = u.id
                            WHERE da.request_id = :request_id");
    $stmt->execute([':request_id' => $request_id]);
    $donations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Responses | RedPulse</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- RedPulse Custom Styles -->
    <style>
        body {
            background: linear-gradient(135deg,rgb(248, 35, 35));
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }
        h2 {
            text-align: center;
            margin-top: 100px;
            font-weight: 600;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            animation: slideDown 0.5s ease-in-out;
        }
        table {
            margin-top: 40px;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        th, td {
            text-align: center;
            vertical-align: middle;
            padding: 15px;
            font-size: 18px;
            color: black;
        }
        th {
            background-color: rgba(0, 0, 0, 0.7);
            color: #fff;
            font-weight: bold;
        }
        .alert {
            margin-top: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        thead {
        background-color: rgba(0, 0, 0, 0.7);
        color: white; /* To make text visible on black background */
    }
        .btn-primary {
            background-color:rgb(236, 80, 80);
            border: none;
            transition: background 0.3s;
        }
        .btn-primary:hover {
            background-color: lightgreen;
            color: black;
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

        .navbar a {
            font-size: 20px;
        }


        .navbar-nav .nav-item .nav-link:hover {
            color:rgb(255, 255, 255) !important;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
        }

        .navbar-nav .nav-item{
            padding: 0px 10px;
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
    </style>

    <script>
        function showSelectionMessage() {
            alert("Donor is selected successfully!");
        }
    </script>
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
        <a class="nav-link" href="posts.php">Posts</a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="donate_list.php?request_id=3">Responses</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="bloodbank.php">Blood Bnaks</a>
    </li>
</ul>

        </div>
    </nav>

       <!-- Floating Drops Animation -->
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

<div class="container">
    <h2>Donation Responses</h2>

    <?php if (empty($donations)): ?>
        <div class="alert alert-warning text-center">No responses found for this request ID.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Location</th>
                        <th>Fever/Cough</th>
                        <th>Medical Conditions</th>
                        <th>Medications</th>
                        <th>Hospital Visits</th>
                        <th>Fatigue</th>
                        <th>Select</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($donations as $donation): ?>
                        <tr>
                            <td><?= htmlspecialchars($donation['name']) ?></td>
                            <td><?= htmlspecialchars($donation['contact']) ?></td>
                            <td><?= htmlspecialchars($donation['location']) ?></td>
                            <td><?= htmlspecialchars($donation['health_issue']) ?></td>
                            <td><?= htmlspecialchars($donation['medical_conditions']) ?></td>
                            <td><?= htmlspecialchars($donation['medications']) ?></td>
                            <td><?= htmlspecialchars($donation['hospital_visits']) ?></td>
                            <td><?= htmlspecialchars($donation['fatigue']) ?></td>
                            <td>
    <?php if (in_array($donation['user_id'], $selected_donors)): ?>
    <button class="btn btn-secondary btn-sm" disabled>Selected</button>
<?php else: ?>
    <form action="select_donor.php" method="POST" onsubmit="showSelectionMessage()">
        <input type="hidden" name="user_id" value="<?= $donation['user_id'] ?>">
        <input type="hidden" name="request_id" value="<?= $request_id ?>">
        <button type="submit" class="btn btn-primary btn-sm">Select</button>
    </form>
<?php endif; ?>

</td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
