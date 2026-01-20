<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: donations_list.php');
    exit();
}

try {
    // DB connection
    $conn = new PDO('mysql:host=localhost;dbname=blooddonationmanagementsystem', 'root', '');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get logged-in donor ID
    $user_id = $_SESSION['user_id'];

// Step 1: Get the donor's `id` from donors table
$stmt = $conn->prepare("SELECT id FROM donors WHERE user_id = :user_id");
$stmt->execute([':user_id' => $user_id]);
$donor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$donor) {
    // No donor found for this user_id
    $recipients = []; // Return an empty recipient list
} else {
    $donor_id = $donor['id'];

    // Fetch the recipients
    $stmt = $conn->prepare("
        SELECT 
            r.recipient_name,
            r.bloodtype,
            r.contact,
            r.location,
            r.received_date
        FROM donations d
        INNER JOIN recipient r ON d.request_id = r.request_id
        WHERE d.donor_id = :donor_id
        ORDER BY r.received_date DESC
    ");
    $stmt->execute([':donor_id' => $donor_id]);
    $recipients = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}
catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Recipient Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg,rgb(248, 35, 35));
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }
        h2 {
            margin-top: 100px;
            color: #fff;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            animation: slideDown 1s ease-in-out;
            font-size: 40px;
        }
        table {
            margin-top: 20px;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        th, td {
            text-align: center;
            vertical-align: middle;
            padding: 15px;
            font-size: 18px;
            color: #333;
        }
        th {
            background-color: rgba(0, 0, 0, 0.7);
            color: #fff;
            font-weight: normal;
        }
        .alert {
            margin-top: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .btn-primary {
            background-color: lightgreen;
            border: black 1px solid;
            border-radius: 20px;
            padding: 10px 20px;
            font-size: 20px;
            font-weight: medium;
            color: black;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color:rgb(0, 0, 0);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .navbar {
            background-color: rgba(0, 0, 0, 0.8) !important;
            padding: 3px 20px;
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
            width: 25px;
            height: 25px;
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
        <a class="nav-link" href="profile.php">Profile</a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="profile.php">History</a>
    </li>
</ul>

        </div>
    </nav>
    <div class="container mt-5">
    <h2 class="text-center mb-4">Blood Recipients (Fulfilled Requests)</h2>
    <?php if (empty($recipients)): ?>
        <div class="alert alert-warning text-center">No recipients found yet.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover bg-white">
                <thead class="table-dark">
                    <tr>
                        <th>Recipient Name</th>
                        <th>Blood Type</th>
                        <th>Contact</th>
                        <th>Location</th>
                        <th>Received Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recipients as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['recipient_name']) ?></td>
                            <td><?= htmlspecialchars($row['bloodtype']) ?></td>
                            <td><?= htmlspecialchars($row['contact']) ?></td>
                            <td><?= htmlspecialchars($row['location']) ?></td>
                            <td><?= htmlspecialchars($row['received_date']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

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
</body>
</html>
